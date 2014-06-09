<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Descrição Curta
 *
 * Descrição Longa da classe 
 *
 * @package solicitacao_taxa.php
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 05/06/2013
 * @version  versao 1.0
*/
include_once APPPATH . "models/Desbloqueios/solicitacao.php";

class Solicitacao_Periodo extends CI_Model implements Solicitacao {
	
	public function __construct() 
	{
		parent::__construct();		
	}
	
	public function solicitar_desbloqueio_grupo($solicitacoes)
	{

		/** Adiciona o(s) email(s) do gestor(es) que vai(ão) desbloquear **/
		include "/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php";	
		$this->load->model("Usuarios/gestor_filial");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Email/envio");
		$this->load->model("Desbloqueios/verifica_filial_item");		
		$this->load->model("Email/email");
		$this->load->model("Email/email_model");

		$verificador_filial = new Verifica_Filial_Item();

		$envio = new Envio();

		$usuario_model = new Usuario_Model();

		$usuario_solicitacao = new Usuario();
		$usuario_model = new Usuario_Model();
		
		$usuario_solicitacao->setId((int)$_SESSION['matriz'][7]);
		$usuario_model->findById($usuario_solicitacao);

		$envio->adicionarNovoEmail($usuario_solicitacao->getEmail());

		$gestores_desbloqueio = Array();

		foreach( $solicitacoes as $entity )
		{

			$item = $verificador_filial->buscarDadosDoItemPeloId($entity->getIdItem(), $entity->getModulo());

			/** Verifica quem são às pessoas que vão desbloquear às solicitações e obtem os emails para enviar a solicitação **/		
			switch( $entity->getModulo() )
			{
				case "proposta":
			
					if( $item->modulo == "EXP" )
					{
						$filial_solicitacao = $item->id_place_receipt;
					}
					else
					{
						$filial_solicitacao = $item->id_place_delivery;
					}
													
					break;
			
				case "taxa_local":
			
					$porto = $item->getPortos();
			
					$filial_solicitacao = $porto[0]->getId();
			
					break;
			
				default:
					show_error("Impossivel determinar o módulo correto!");
			}

			$usuarios_acesso_filial = $acessos_filiais[$filial_solicitacao];
						
			if(!is_array($usuarios_acesso_filial))
			{
				echo "Impossivel encontrar os responsaveis pelos desbloqueios!<p />";
				var_dump($usuarios_acesso_filial);exit;
			}

			foreach( $usuarios_acesso_filial as $usuario_acesso )
			{
				array_push($gestores_desbloqueio, $usuario_acesso);
			}	

			$entity->setUsuarioSolicitacao($usuario_solicitacao);

		}//END FOREACH	

		$gestores_desbloqueio = array_unique($gestores_desbloqueio);

		foreach( $gestores_desbloqueio as $usuario_desbloqueio )
		{
			$gestor_desbloqueio = new Gestor_Filial();
			$gestor_desbloqueio->setId((int)$usuario_desbloqueio);
			$usuario_model->findById($gestor_desbloqueio);
		
			$email_gestor = new Email();
			$email_gestor->setEmail($gestor_desbloqueio->getEmail()->getEmail());
		
			$envio->adicionarNovoEmail($email_gestor);
		}

		$corpo_mensagem = $this->formatar_corpo_email_grupo($solicitacoes);
		
		$mensagem_enviada = $envio->enviarMensagem($corpo_mensagem, "SOLICITAÇÃO DE DESBLOQUEIO DE VALIDADE");
		
		if( ! $mensagem_enviada )
		{
			echo "<script language='javascript'>
					alert('Não foi possivel enviar a mensagem de solicitação de desbloqueio mas, a solicitação foi salva com sucesso!');
					window.parent.document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/';
					window.parent.document.getElementById('pop').style.display = 'none';
				   </script>";
			exit;
		}

	}

	public function solicitar_desbloqueio( Solicitacao_Entity $entity)
	{
		
		/** Adiciona o(s) email(s) do gestor(es) que vai(ão) desbloquear **/
		include "/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php";	
		$this->load->model("Usuarios/gestor_filial");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Email/envio");
		$this->load->model("Desbloqueios/verifica_filial_item");		
		$this->load->model("Email/email");
		$this->load->model("Email/email_model");
					
		$verificador_filial = new Verifica_Filial_Item();
		
		$item = $verificador_filial->buscarDadosDoItemPeloId($entity->getIdItem(), $entity->getModulo());

		$envio = new Envio();
		
		$usuario_solicitacao = new Usuario();
		$usuario_model = new Usuario_Model();
		
		$usuario_solicitacao->setId((int)$_SESSION['matriz'][7]);
		$usuario_model->findById($usuario_solicitacao);
		
		$entity->setUsuarioSolicitacao($usuario_solicitacao);
		
		$envio->adicionarNovoEmail($usuario_solicitacao->getEmail());
				
		/** Verifica quem são às pessoas que vão desbloquear às solicitações e obtem os emails para enviar a solicitação **/		
		switch( $entity->getModulo() )
		{
			case "proposta":
		
				if( $item->modulo == "EXP" )
				{
					$filial_solicitacao = $item->id_place_receipt;
				}
				else
				{
					$filial_solicitacao = $item->id_place_delivery;
				}
												
				break;
		
			case "taxa_local":
		
				$porto = $item->getPortos();
		
				$filial_solicitacao = $porto[0]->getId();
		
				break;
		
			default:
				show_error("Impossivel determinar o módulo correto!");
		}
		
		$usuarios_acesso_filial = $acessos_filiais[$filial_solicitacao];
						
		if(!is_array($usuarios_acesso_filial))
		{
			echo "Impossivel encontrar os responsaveis pelos desbloqueios!<p />";
			var_dump($usuarios_acesso_filial);exit;
		}	
		
		foreach( $usuarios_acesso_filial as $usuario_desbloqueio )
		{
			$gestor_desbloqueio = new Gestor_Filial();
			$gestor_desbloqueio->setId((int)$usuario_desbloqueio);
			$usuario_model->findById($gestor_desbloqueio);
		
			$email_gestor = new Email();
			$email_gestor->setEmail($gestor_desbloqueio->getEmail()->getEmail());
		
			$envio->adicionarNovoEmail($email_gestor);
		}
						
		$corpo_mensagem = $this->formatar_corpo_email($entity);
		
		$mensagem_enviada = $envio->enviarMensagem($corpo_mensagem, "SOLICITAÇÃO DE DESBLOQUEIO DE VALIDADE");
		
		if( ! $mensagem_enviada )
		{
			echo "<script language='javascript'>
					alert('Não foi possivel enviar a mensagem de solicitação de desbloqueio mas, a solicitação foi salva com sucesso!');
					window.parent.document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/';
					window.parent.document.getElementById('pop').style.display = 'none';
				   </script>";
			exit;
		}
				
	}
	
	protected function formatar_corpo_email(Solicitacao_Entity $entity)
	{
						
		/** Obtem o número do Item da proposta ou do acordo de taxas **/
		if( $entity->getModulo() == "proposta" )
		{
			$this->db->select("itens_proposta.numero_proposta as numero");
			$rs = $this->db->get_where("CLIENTES.itens_proposta","id_item_proposta = {$entity->getIdItem()}");
			$modulo = "PROPOSTA";
		}
		else
		{
			$this->db->select("acordos_taxas_locais_globais.numero");
			$rs = $this->db->get_where("CLIENTES.acordos_taxas_locais_globais","id = {$entity->getIdItem()}");
			$modulo = "ACORDO TAXA LOCAL";
		}
	
		if( $rs->num_rows() < 1 )
		{
			show_error("Impossivel enviar o email da solicitação de desbloqueio!");
		}
		else
		{
			$numero = $rs->row()->numero;
		}
	
		$mensagem = '
	    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	    "http://www.w3.org/TR/html4/loose.dtd">
	    <html>
	    <head>
	    <title>SCOA - ENVIO DE E-MAILS</title>
	    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	    <style type="text/css">
	    .titulo_tabela
	    {
	        background-color:#4682B4;
	        color:#FFFFFF;
	        font-family:Verdana;
	        font-size:11px;
	        text-align:center;
	    }
	    .texto_pb1
	    {
	        font-family:Verdana;
	        font-size: 9px;
	        color: #000000;
	        background: #FFFFFF;
	    }
	    .texto_pb2
	    {
	        font-family:Verdana;
	        font-size: 9px;
	        color: #000000;
	    }
	    .padrao
	    {
	        background: #DBEAF5;
	    }
	    .tabela_azul
	    {
	        background: #FFFFFF;
	        border:1px solid #4682B4;
	    }
	    .alerta
	    {
	        color: red;
	    }
	    </style>
	    </head>
	    <body>
	    <table border="0" cellpadding="1" cellspacing="1" width="100%" align="center" class="tabela_azul">
	    <tr>
	        <td>
	            <br>
	                <table border="0" cellpadding="1" cellspacing="1" width="97%" class="padrao" align="center">
	                    <tr>
	                        <td class="titulo_tabela">
	                            '.$modulo.'
	                        </td>
	                        <td class="titulo_tabela">
	                            MENSAGEM
	                        </td>                     
	                    </tr>
	                    <tr>
	                        <td class="texto_pb1" align="center">
	                            '.$numero.'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            O USUÁRIO '.strtoupper($entity->getUsuarioSolicitacao()->getNome()).' SOLICITOU O DESBLOQUEIO
	                            DE VALIDADE PARA '.$modulo.' '.$numero.', O DESBLOQUEIO AGUARDA UMA DECISÃO SUA.		
	                        </td>                        
	                    </tr>	                            			                            		
	                </table>	                            			                            		
	              <br>
	            </td>	                            		
	        </tr>	                            			        	                            		
	    </table>
	    <tr>
            <td class="texto_pb1" align="rigth" colspan="4">
                <i>Documento gerado em '.date("d-m-Y H:i:s").'</i>
			</td>
		</tr>
	    </body>
	    </html>';
				
		return $mensagem;
	}

	public function formatar_corpo_email_grupo(Array $solicitacoes)
	{
			
		$mensagem = '
	    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	    "http://www.w3.org/TR/html4/loose.dtd">
	    <html>
	    <head>
	    <title>SCOA - ENVIO DE E-MAILS</title>
	    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	    <style type="text/css">
	    .titulo_tabela
	    {
	        background-color:#4682B4;
	        color:#FFFFFF;
	        font-family:Verdana;
	        font-size:11px;
	        text-align:center;
	    }
	    .texto_pb1
	    {
	        font-family:Verdana;
	        font-size: 9px;
	        color: #000000;
	        background: #FFFFFF;
	    }
	    .texto_pb2
	    {
	        font-family:Verdana;
	        font-size: 9px;
	        color: #000000;
	    }
	    .padrao
	    {
	        background: #DBEAF5;
	    }
	    .tabela_azul
	    {
	        background: #FFFFFF;
	        border:1px solid #4682B4;
	    }
	    .alerta
	    {
	        color: red;
	    }
	    </style>
	    </head>
	    <body>
	    <table border="0" cellpadding="1" cellspacing="1" width="100%" align="center" class="tabela_azul">
	    <tr>
	        <td>
	            <br>
	                <table border="0" cellpadding="1" cellspacing="1" width="97%" class="padrao" align="center">
	                	<tr>
	                        <td class="titulo_tabela">
	                            NÚMERO
	                        </td>
	                        <td class="titulo_tabela">
	                            MENSAGEM
	                        </td>                     
	                    </tr>';

	    foreach( $solicitacoes as $entity )
	    {	
	    	
		    /** Obtem o número do Item da proposta ou do acordo de taxas **/
			if( $entity->getModulo() == "proposta" )
			{
				$this->db->select("itens_proposta.numero_proposta as numero");
				$rs = $this->db->get_where("CLIENTES.itens_proposta","id_item_proposta = {$entity->getIdItem()}");
				$modulo = "PROPOSTA";
			}
			else
			{
				$this->db->select("acordos_taxas_locais_globais.numero");
				$rs = $this->db->get_where("CLIENTES.acordos_taxas_locais_globais","id = {$entity->getIdItem()}");
				$modulo = "ACORDO TAXA LOCAL";
			}
		
			if( $rs->num_rows() < 1 )
			{
				show_error("Impossivel enviar o email da solicitação de desbloqueio!");
			}
			else
			{
				$numero = $rs->row()->numero;
			}

			$mensagem .='
	                    <tr>
	                        <td class="texto_pb1" align="center">
	                            '.$numero.'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            O USUÁRIO '.strtoupper($entity->getUsuarioSolicitacao()->getNome()).' SOLICITOU O DESBLOQUEIO
	                            DE VALIDADE PARA '.$modulo.' '.$numero.', O DESBLOQUEIO AGUARDA UMA DECISÃO SUA.		
	                        </td>                        
	                    </tr>';

		}

	    $mensagem .='                	                            			                            		
	                </table>	                            			                            		
	              <br>
	            </td>	                            		
	        </tr>	                            			        	                            		
	    </table>
	    <tr>
            <td class="texto_pb1" align="rigth" colspan="4">
                <i>Documento gerado em '.date("d-m-Y H:i:s").'</i>
			</td>
		</tr>
	    </body>
	    </html>';
				
		return $mensagem;

	}

}