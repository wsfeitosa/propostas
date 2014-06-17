<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Upload_Proposta_Tarifario
 *
 * Lê a tabela excel enviada pelo usuário e cria altera os valores ou cria às solicitações de 
 * debloqueio necessárias. 
 *
 * @package models/Propostas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 01/07/2013
 * @version  versao 1.0
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/Libs/leitor_excel/leitor_excel/reader.php");
require_once APPPATH . "models/Desbloqueios/notas.php";

class Upload_Proposta_Tarifario extends CI_Model {
	
	const upload_dir = "/var/www/html/allink/Clientes/propostas/application/cache";
	
	protected $allowed_units = Array( "WM" => 3, "TON" => 2, "M3" => 1, "BL" => 4, "%" => 5, "CNTR" => 7, "SU" => 8, "CNTR20" => 9, "CNTR40" => 10 );
	
	protected $allowed_money = Array( "USD" => 42, "BRL" => 88, "EUR" => 113 );
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * ImportarPlanilha
	 *
	 * Importa os dados da planilha enviada pelo usuário
	 *
	 * @name ImportarPlanilha
	 * @access public
	 * @param string $file
	 * @return void
	 */ 	
	public function ImportarPlanilha( $file = NULL )
	{
		if( is_null($file) )
		{
			throw new InvalidArgumentException("Nenhum arquivo informado para realizar o upload!");
		}	
		
		unset($_SESSION['Desbloqueios']);
			
		$data = new Spreadsheet_Excel_Reader();
		$data->read(Upload_Proposta_Tarifario::upload_dir . "/" . $file);
		$linhas  = count($data->sheets[0]['cells']);
		$colunas = count($data->sheets[0]['cells'][1]);
		
		$this->load->model("Propostas/item_proposta");		
		$this->load->model("Propostas/item_proposta_model");			
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Desbloqueios/verifica_filial_item");

		$solicitacoes_taxa = Array();
		$solicitacoes_validade = Array();
		
		for($i=2;$i <= @$linhas; $i++)
		{            
			$id_taxa = (int)$data->sheets[0]['cells'][$i][1];
			$id_item = (int)$data->sheets[0]['cells'][$i][2];
			$sentido = (string)$data->sheets[0]['cells'][$i][3];
			$numero_proposta = (string)$data->sheets[0]['cells'][$i][4];
			$clientes = (string)$data->sheets[0]['cells'][$i][5];
			$origem = (string)$data->sheets[0]['cells'][$i][6];
			$uncode_origem = (string)$data->sheets[0]['cells'][$i][7];
			$embarque = (string)$data->sheets[0]['cells'][$i][8];
			$uncode_embarque = (string)$data->sheets[0]['cells'][$i][9];
			$desembarque = (string)$data->sheets[0]['cells'][$i][10];
			$uncode_desembarque = (string)$data->sheets[0]['cells'][$i][11];
			$destino = (string)$data->sheets[0]['cells'][$i][12];
			$uncode_destino = (string)$data->sheets[0]['cells'][$i][13];
			$via_adicional = (string)$data->sheets[0]['cells'][$i][14];
			$uncode_via_adicional = (string)$data->sheets[0]['cells'][$i][15];			
			$taxa = (string)$data->sheets[0]['cells'][$i][16];
			$valor = (float)str_replace(",",".",$data->sheets[0]['cells'][$i][17]);
			$valor_minimo = (float)str_replace(",",".",$data->sheets[0]['cells'][$i][18]);
			$valor_maximo = (float)str_replace(",",".",$data->sheets[0]['cells'][$i][19]);
			$unidade = (string)$data->sheets[0]['cells'][$i][20];
			$moeda = (string)$data->sheets[0]['cells'][$i][21];
			$inicio = str_replace("/", "-", $data->sheets[0]['cells'][$i][22]);
			$validade = str_replace("/", "-", $data->sheets[0]['cells'][$i][23]);
			
			/** Verifica se a unidade é de algum dos tipos permitidos **/		
			if( ! array_key_exists(strtoupper($unidade), $this->allowed_units) )
			{				
				show_error("Unidade desconhecida informada na planilha na linha: ".$i." -> ".$unidade); exit;
			}	
			
			/** Verifica se a moeda informa é de algum dos tipos permitidos **/
			if( ! array_key_exists(strtoupper($moeda), $this->allowed_money) )
			{
				show_error("Moeda desconhecida informada na planilha na linha: ".$i." -> ".$moeda); exit;
			}	
			
			try {

				$data_inicial = new DateTime($inicio);
				$data_final = new DateTime($validade);

			} catch (Exception $e) {
				echo $e->getMessage();exit();
			}

			/** Busca o item da proposta pelo id do item **/
			$item = new Item_Proposta();
			$item->setId($id_item);
			
			$item_model = new Item_Proposta_Model();
			
			$taxa_planilha = new Taxa_Adicional();
			
			$taxa_planilha->setId($id_taxa);
			$taxa_planilha->setValor((float)$valor);
			$taxa_planilha->setValorMinimo((float)$valor_minimo);
			$taxa_planilha->setValorMaximo((float)$valor_maximo);
			
			$taxa_model = new Taxa_Model();
			
			$taxa_model->obterNomeTaxaAdicional($taxa_planilha);
			
			$moeda_planilha = new Moeda();
			$moeda_planilha->setId((int)$this->allowed_money[$moeda]);
			
			$moeda_model = new Moeda_Model();
			$moeda_model->findById($moeda_planilha);
			
			$unidade_planilha = new Unidade();
			$unidade_planilha->setId((int)$this->allowed_units[$unidade]);
			
			$unidade_model = new Unidade_Model();
			$unidade_model->findById($unidade_planilha);

			$taxa_planilha->setUnidade($unidade_planilha);
			$taxa_planilha->setMoeda($moeda_planilha);
			
			try {
				$item = $item_model->buscarItemPorIdDoItem($item, $sentido);
			} catch (Exception $e) {
				continue;
			}	
			
			$taxa_planilha->setIdItem((int)$item->getId());
			
			/** Compara o valor do frete e verifica se foi reduzido ou se a moeda foi alterada **/
			$valor_foi_alterado = $this->verificarSeFreteFoiAlterado($item->getTarifario()->getTaxa(), $taxa_planilha);			
			
			/** Se o valor foi alterado então solicita o desbloqueio da taxa **/
			if( $valor_foi_alterado === TRUE )
			{				
				$dados_para_salvar = Array(
											'id_taxa_item' => $item->getId(),
											'id_taxa' => $taxa_planilha->getId(),
											'id_unidade' => $taxa_planilha->getUnidade()->getId(),
											'id_moeda' => $taxa_planilha->getMoeda()->getId(),
											'valor' => $taxa_planilha->getValor(),
											'valor_minimo' => $taxa_planilha->getValorMinimo(),
											'valor_maximo' => $taxa_planilha->getValorMaximo(),
											'id_nota_importacao' => 1,
											'id_nota_exportacao' => 1,
											'observacao' => '',
											'status' => 'P',
											'id_usuario_solicitacao' => $_SESSION['matriz'][7],
											'data_solicitacao' => date('Y-m-d H:i:s'),
											'modulo' => 'proposta', 
				);
				
				$this->db->insert("CLIENTES.desbloqueios_taxas",$dados_para_salvar);
				
				/** Muda o status do item da proposta **/
				$this->db->update("CLIENTES.itens_proposta",Array('id_status_item' => '2'),"id_item_proposta = ".$item->getId());

				$solicitacoes_taxa[$numero_proposta][] = $taxa_planilha; 	
				
			}	
			
			/** Verifica se às validades foram alteradas **/
			$validade_alterada = $this->verificaSeValidadeFoiAlterada($data_inicial, $data_final, $item->getInicio(), $item->getValidade());
			
			/** Se a validade foi alterada então solicita o desbloqueio da mesma **/
			if( $validade_alterada === TRUE )
			{				
				$dados_para_salvar = Array(
											'id_item' =>  $id_item,
											'validade_solicitada' => $data_final->format('Y-m-d'),
											'status' => 'P',
											'id_usuario_solicitacao' => $_SESSION['matriz'][7],
											'data_solicitacao' => date('Y-m-d H:i:s'),
											'modulo' => "proposta",
								 );
		
				$incluido = $this->db->insert("CLIENTES.desbloqueios_validades",$dados_para_salvar);
			
				/** Muda o status do item da proposta **/
				$status = $this->db->update("CLIENTES.itens_proposta",Array("id_status_item" => '2'),"id_item_proposta = ".$id_item);

				$solicitacoes_validade[$numero_proposta][] = $data_final;			
			}	
		}

		$usuario_solicitante = new Usuario();
		$usuario_solicitante->setId((int)$_SESSION['matriz'][7]);

		$usuario_model = new Usuario_Model();

		$usuario_model->findById($usuario_solicitante);
		
		/** Adiciona o(s) email(s) do gestor(es) que vai(ão) desbloquear **/
		include_once "/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php";
		include_once $_SERVER['DOCUMENT_ROOT']."/Libs/envia_msg.php";

		$verificador_filial = new Verifica_Filial_Item();

	    foreach( $solicitacoes_taxa as $numero=>$solicitacao )
	    {

	    	$emails_gestores = Array();

	    	$item = $verificador_filial->buscarDadosDoItemPeloId($solicitacao[0]->getIdItem(),"proposta");

			if( $item->modulo == "EXP" )
			{
				$filial_solicitacao = $item->id_place_receipt;
			}
			else
			{
				$filial_solicitacao = $item->id_place_delivery;
			}		
			
			foreach( $acessos_filiais[$filial_solicitacao] as $usuario_desbloqueio )
			{				
				$gestor_desbloqueio = new Usuario();
				$gestor_desbloqueio->setId((int)$usuario_desbloqueio);				
				$usuario_model->findById($gestor_desbloqueio);		

				$emails_gestores[] = $gestor_desbloqueio->getEmail()->getEmail();		
			}
	    	
			$emails_gestores[] = $usuario_solicitante->getEmail()->getEmail();

			$emails_gestores = array_unique($emails_gestores);
			
	    	/** Verifica se existem solicitações pendentes e às envia **/
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
		                            PROPOSTA
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


	    	foreach ($solicitacao as $taxa) 
	    	{    	
		    	$mensagem .='<tr>
		                        <td class="texto_pb1" align="center">
		                            '.$numero.'
		                        </td>
		                        <td class="texto_pb1" align="center">
		                            '.strtoupper($usuario_solicitante->getnome()).'
		                        </td>
		                        <td class="texto_pb1" align="center">
		                            '.strtoupper($taxa->getNome()).'
		                        </td>
		                        <td class="texto_pb1" align="center">
		                            '.$taxa->getMoeda()->getSigla().' | '.sprintf( "%02.2f", $taxa->getValor() ).' | '.$taxa->getUnidade()->getUnidade().'
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
			
			$enviado = envia_email(
									$usuario_solicitante->getEmail()->getEmail(), 
									$usuario_solicitante->getNome(), 
									implode(";",$emails_gestores), "", 
									"wellington.feitosa@allink.com.br", 
									$usuario_solicitante->getEmail()->getEmail(), 
									"SOLICITAÇÃO DE ALTERAÇÃO DE VALORES: PROPOSTA - ".$numero, 
									$mensagem, $anexo="", $nome_anexo="");			
			
			if( ! $enviado )
			{
				show_error("Não foi possivel enviar a mensagem, porem o desbloqueio foi salvo com sucesso!");
			}
			
	    }   

	    echo "<script language='javascript'>
				alert('Desbloqueios enviados com sucesso!');
				window.close();						
			  </script>";
		exit;     

	}
	
	/**
	 * verificaSeValidadeFoiAlterada
	 *
	 * Verifica se a validade da proposta foi alterada
	 *
	 * @name verificaSeValidadeFoiAlterada
	 * @access public
	 * @param DateTime $inicio
	 * @param DateTime $validade
	 * @param DateTime $inicio_item
	 * @param DateTime $validade_item
	 * @return boolean
	 */ 	
	public function verificaSeValidadeFoiAlterada(DateTime $inicio, DateTime $validade, DateTime $inicio_item, DateTime $validade_item)
	{
		
		/** Verifica se a validade foi extendida além da data original do item **/
		if( $validade->format('Y-m-d') > $validade_item->format('Y-m-d') )	
		{
			return TRUE;
		}
		
		if( $inicio->format('Y-m-d') > $validade_item->format('Y-m-d') )
		{
			return TRUE;
		}	
		
		/** Verifica se a data de validade é maior que o fim do mês atual **/
		include_once $_SERVER['DOCUMENT_ROOT'] . "/Libs/ultimo_dia_mes.php";

		$ultimo_dia_mes = ultimo_dia_mes(date('m'),date('Y'));
		
		$date_ultimo_dia_mes = new DateTime($ultimo_dia_mes);
		
		if( $validade->format('Y-m-d') > $date_ultimo_dia_mes->format('Y-m-d') )
		{
			return TRUE;
		}	
		
		return FALSE;
		
	}
	
	/**
	 * verificarSeFreteFoiAlterado
	 *
	 * verifica se o frete foi alterado e se necessita de desbloqueio
	 *
	 * @name verificarSeFreteFoiAlterado
	 * @access public
	 * @param Array $taxas
	 * @param Taxa_Adicional $frete
	 * @return boolean
	 */ 	
	public function verificarSeFreteFoiAlterado (Array $taxas, Taxa $frete)
	{
				
		foreach( $taxas as $taxa )
		{
			
			if( $taxa->getId() == $frete->getId() )
			{
				
				/** Verifica se os valores foram alterados para menor na planilha **/
				if( ( $taxa->getValor() != $frete->getValor() ) || ( $taxa->getValorMinimo() != $frete->getValorMinimo() ) || ( $taxa->getValorMaximo() != $frete->getValorMaximo() ) )
				{
					return TRUE;
				}
				
				/** Verifica se a moeda foi alterada na planiha **/
				if( $taxa->getMoeda()->getId() != $frete->getMoeda()->getId() )
				{
					return TRUE;
				}	
				
				/** Verfica se a unidade foi alterada na planilha **/
				if( $taxa->getUnidade()->getId() != $frete->getUnidade()->getId() )
				{
					return TRUE;
				}	
				
			}
						
		}	

		return FALSE;
		
	}
	
}//END FUNCTION