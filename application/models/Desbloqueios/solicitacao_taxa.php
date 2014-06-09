<?php
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

class Solicitacao_Taxa extends CI_Model implements Solicitacao {
	
	protected $entidades = Array();
	protected $acessos = Array();

	public function __construct() 
	{
		parent::__construct();		
		$this->acessos = $acessos_filiais;		
	}
	
	public function solicitar_desbloqueio(Solicitacao_Entity $entity)
	{
		
		$dados_para_salvar = Array(
									'id_taxa_item' => $entity->getTaxa()->getIdItem(),
									'id_taxa' => $entity->getTaxa()->getId(),
									'id_unidade' => $entity->getTaxa()->getUnidade()->getId(),
									'id_moeda' => $entity->getTaxa()->getMoeda()->getId(),
									'valor' => $entity->getTaxa()->getValor(),
									'valor_minimo' => $entity->getTaxa()->getValorMinimo(),
									'valor_maximo' => $entity->getTaxa()->getValorMaximo(),
									'id_nota_importacao' => $entity->getNotaImportacao(),
									'id_nota_exportacao' => $entity->getNotaExportacao(),
									'observacao' => $entity->getObservacao(),
									'status' => $entity->getStatus(),
									'id_usuario_solicitacao' => $entity->getUsuarioSolicitacao()->getId(),
									//'data_solicitacao' => $entity->getDataSolicitacao()->format('Y-m-d H:i:s'),
									'data_solicitacao' => date('Y-m-d H:i:s'),
									'modulo' => $entity->getModulo(), 
							 );
		
		$salvou = $this->db->insert("CLIENTES.desbloqueios_taxas",$dados_para_salvar);
		
		/** Muda o status do item da proposta **/
		$status = $this->db->update("CLIENTES.itens_proposta",Array('id_status_item' => '2'),"id_item_proposta = ".$entity->getTaxa()->getIdItem());
		
		$entity->setIdDesbloqueio((int)$this->db->insert_id());

		$this->entidades[] = $entity;

		//Envia o email da solicitação
		//$this->enviar_email_solicitacao($entity);

		return $salvou;
		
	}
	
	
	public function enviar_grupo_solicitacoes()
	{
		$this->load->model("Email/envio");		
		$this->load->model("Desbloqueios/verifica_filial_item");		
		$this->load->model("Email/email");
		$this->load->model("Email/email_model");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/gestor_filial");
		
		$acessos = $acessos_filiais;

		$solicitacoes = $this->entidades;

		$mensagem = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
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
		a:link, a:visited {
			text-decoration: none;
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
	                            SOLICITANTE
	                        </td>
	                        <td class="titulo_tabela">
	                            TAXA
	                        </td>
	                        <td class="titulo_tabela">
	                            VALOR SOLICITADO
	                        </td>
	                        <td class="titulo_tabela">
	                            DATA
	                        </td>
	                    </tr>';

		foreach ($solicitacoes as $key => $entity) 
		{
			/** Obtem o número do Item da proposta ou do acordo de taxas **/
			if( $entity->getModulo() == "proposta" )
			{
				$this->db->select("itens_proposta.numero_proposta as numero");
				$rs = $this->db->get_where("CLIENTES.itens_proposta","id_item_proposta = {$entity->getTaxa()->getIdItem()}");	
				$modulo = "PROPOSTA";					
			}
			else
			{
				$this->db->select("acordos_taxas_locais_globais.numero");
				$rs = $this->db->get_where("CLIENTES.acordos_taxas_locais_globais","id = {$entity->getTaxa()->getIdItem()}");
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

			$mensagem .='<tr>
	                        <td class="texto_pb1" align="center">
								<a href="http://'.$_SERVER["HTTP_HOST"].'/Clientes/desbloqueio_propostas/" >
	                            '.$numero.'
	                            </a>		
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.strtoupper($entity->getUsuarioSolicitacao()->getnome()).'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.strtoupper($entity->getTaxa()->getNome()).'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.$entity->getTaxa()->getMoeda()->getSigla().' | '.sprintf( "%02.2f", $entity->getTaxa()->getValor() ).' | '.$entity->getTaxa()->getUnidade()->getUnidade().'
	                        </td>
	                        <td colspan="5" class="texto_pb1" align="center">
	                            '.date('d/m/Y H:i:s').'
	                        </td>
	                    </tr>';	                    

		}

		$mensagem .= '</table>
		              <br>
		            </td>
		        </tr>
		    </table>
		    </body>
		    </html>';

		//echo $mensagem;exit;    

		$envio = new Envio();
		
		$envio->adicionarNovoEmail($entity->getUsuarioSolicitacao()->getEmail());
				
		$entity = $solicitacoes[0];	

		$verificador_filial = new Verifica_Filial_Item();
		
		$item = $verificador_filial->buscarDadosDoItemPeloId($entity->getTaxa()->getIdItem(),$entity->getModulo());

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
				show_error("Impossilvel determinar o módulo correto!");
		}
				
		$usuario_model = new Usuario_Model();
		
		/** Adiciona o(s) email(s) do gestor(es) que vai(ão) desbloquear **/
		include_once "/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php";

		foreach( $acessos_filiais[$filial_solicitacao] as $usuario_desbloqueio )
		{				
			$gestor_desbloqueio = new Gestor_Filial();
			$gestor_desbloqueio->setId((int)$usuario_desbloqueio);
			$usuario_model->findById($gestor_desbloqueio);
		
			$email_gestor = new Email();
			$email_gestor->setEmail($gestor_desbloqueio->getEmail()->getEmail());
		
			$envio->adicionarNovoEmail($email_gestor);			
		}	
		
		$this->entidades = NULL;

		$mensagem_enviada = $envio->enviarMensagem($mensagem, "SOLICITAÇÃO DE DESBLOQUEIO DE TAXA");
		
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

	public function enviar_email_solicitacao(Solicitacao_Entity $entity)
	{
		
		$this->load->model("Email/envio");		
		$this->load->model("Desbloqueios/verifica_filial_item");		
		$this->load->model("Email/email");
		$this->load->model("Email/email_model");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/gestor_filial");
		
		$corpo_mensagem = $this->formatar_corpo_email($entity);
		
		$envio = new Envio();
		
		$envio->adicionarNovoEmail($entity->getUsuarioSolicitacao()->getEmail());
		
		/** Adiciona o(s) email(s) do gestor(es) que vai(ão) desbloquear **/
		include_once "/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php";
						
		$verificador_filial = new Verifica_Filial_Item();
		
		$item = $verificador_filial->buscarDadosDoItemPeloId($entity->getTaxa()->getIdItem(),$entity->getModulo());

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
				show_error("Impossilvel determinar o módulo correto!");
		}
				
		$usuario_model = new Usuario_Model();
		
		foreach( $acessos_filiais[$filial_solicitacao] as $usuario_desbloqueio )
		{				
			$gestor_desbloqueio = new Gestor_Filial();
			$gestor_desbloqueio->setId((int)$usuario_desbloqueio);
			$usuario_model->findById($gestor_desbloqueio);
		
			$email_gestor = new Email();
			$email_gestor->setEmail($gestor_desbloqueio->getEmail()->getEmail());
		
			$envio->adicionarNovoEmail($email_gestor);			
		}	
				
		$mensagem_enviada = $envio->enviarMensagem($corpo_mensagem, "SOLICITAÇÃO DE DESBLOQUEIO DE TAXA");
		
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
			$rs = $this->db->get_where("CLIENTES.itens_proposta","id_item_proposta = {$entity->getTaxa()->getIdItem()}");	
			$modulo = "PROPOSTA";					
		}
		else
		{
			$this->db->select("acordos_taxas_locais_globais.numero");
			$rs = $this->db->get_where("CLIENTES.acordos_taxas_locais_globais","id = {$entity->getTaxa()->getIdItem()}");
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
	                        <td class="titulo$this->load->model("Desbloqueios/solicitacao_periodo_entity");
_tabela">
	                            SOLICITANTE
	                        </td>
	                        <td class="titulo_tabela">
	                            TAXA
	                        </td>
	                        <td class="titulo_tabela">
	                            VALOR SOLICITADO
	                        </td>
	                        <td class="titulo_tabela">
	                            DATA
	                        </td>
	                    </tr>
	                    <tr>
	                        <td class="texto_pb1" align="center">
	                            '.$numero.'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.strtoupper($entity->getUsuarioSolicitacao()->getnome()).'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.strtoupper($entity->getTaxa()->getNome()).'
	                        </td>
	                        <td class="texto_pb1" align="center">
	                            '.$entity->getTaxa()->getMoeda()->getSigla().' | '.sprintf( "%02.2f", $entity->getTaxa()->getValor() ).' | '.$entity->getTaxa()->getUnidade()->getUnidade().'
	                        </td>
	                        <td colspan="5" class="texto_pb1" align="center">
	                            '.date('d/m/Y H:i:s').'
	                        </td>
	                    </tr>	                                      
	                </table>
	              <br>
	            </td>
	        </tr>
	    </table>
	    </body>
	    </html>';
						
		return $mensagem;
	}
	
}