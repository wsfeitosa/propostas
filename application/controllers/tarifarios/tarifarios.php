<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

/**
* @package  Controllers/Tarifarios
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 30/01/2013
* @version  1.0
* Controla o fluxo da aplicação para os tarifarios de importação
*/
class Tarifarios extends CI_Controller {

	public function __construct()
	{		
		parent::__construct();
		$this->load->helper(Array("html","form","url"));			
		$this->output->enable_profiler(FALSE);
		$this->load->model("Tarifario/Facade/tarifario_facade");
	}
	
	public function index()
	{
		
	}

	public function find($clientes = NULL, $origem = NULL, $embarque = NULL, $desembarque = NULL, $destino = NULL, $imo = "N", $modalidade = NULL, $modulo = "proposta", $sentido = NULL, $inicio = NULL, $validade = NULL, $tipo_proposta = NULL)
	{		
		
		if( is_null($sentido) )
		{
			log_message('error',"Não foi possível consultar o tarifário, pois o sentido (IMP ou EXP) não foi informado");
			show_error("Não foi possível consultar o tarifário, pois o sentido (IMP ou EXP) não foi informado");
		}

		if( is_null($inicio) )
		{
			$inicio = date('d-m-Y');
		}

		if( is_null($validade) )
		{
			$validade = date('d-m-Y');
		}	
		
		if( is_null($tipo_proposta) )
		{
			show_error("Tipo de proposta informado não é valido.");exit;
		}	

		/** Chama o façade que vai buscar os tarifários **/
        $dados_recebidos = new ArrayObject();
        
        $dados_recebidos->offsetSet("clientes", $clientes);
        $dados_recebidos->offsetSet("origem", $origem);
        $dados_recebidos->offsetSet("embarque",$embarque);
        $dados_recebidos->offsetSet("desembarque", $desembarque);
        $dados_recebidos->offsetSet("destino", $destino);
        $dados_recebidos->offsetSet("imo", $imo);
        $dados_recebidos->offsetSet("modalidade", $modalidade);
        $dados_recebidos->offsetSet("modulo", $modulo);
        $dados_recebidos->offsetSet("sentido", $sentido);
        $dados_recebidos->offsetSet("inicio", $inicio);
        $dados_recebidos->offsetSet("validade", $validade);
        $dados_recebidos->offsetSet("tipo_proposta", $tipo_proposta);
        
        $facade = new Tarifario_Facade();
        
        $tarifarios = $facade->ListarTarifarios($dados_recebidos);
		
		$header['form_title'] = 'Scoa - Tarifários';
		$header['form_name'] = 'SELECIONAR TARIFÁRIO';
		$header['css'] = '';
		
		/** Decide qual o javascrip certo a carregar baseado no módulo **/
		switch($modulo)
		{
				
			case "proposta":
				$js_file  = "proposta.js";
			break;
					
			case "routing_order":
				$js_file = "routing_order.js";
			break;
				
			case "house":
				$js_file = "house.js";
			break;
					
			default:
				show_error("Impossivel determinar o modulo selecionado!");
					
		}
		
		$header['js'] = load_js(array('tarifarios/'.$js_file));
		
		$data["tarifarios"] = $tarifarios;
		
		$imagens = '';
			
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Tarifarios/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}//END FUNCTION
	
	public function fill( $id_tarifario = NULL, $sentido = NULL, $clientes = NULL, $inicio = NULL, $validade = NULL, $imo = "N", $pp = NULL, $cc = NULL, $id_item_proposta = "0" )
	{		
		
		if( empty($id_tarifario) )
		{
			log_message('error','O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
			show_error('O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
		}	
							
		try{
			
            $facade = new Tarifario_Facade();
            
            $tarifario = $facade->BuscarTarifarioPeloId( $id_tarifario, $sentido, $clientes, $id_item_proposta, new DateTime($inicio), new DateTime($validade), $imo, $pp, $cc );
			
			$this->load->view("Tarifarios/xml_tarifario",Array("tarifario" => $tarifario));
			
		} catch (Exception $e) {
			log_message('error',$e->getMessage());
			show_error($e->getMessage()." - ".$e->getFile());
		}	
		
	}//END FUNCTION
	
	/**
	 * listFound
	 *
	 * Lista todos os tarifários encontrados para para que o usuário possa selecionalos e criar uma nova proposta
	 *
	 * @name listFound
	 * @access public	
	 */	
	public function listFound( $clientes = NULL, $origem = NULL, $embarque = NULL, $desembarque = NULL, $destino = NULL, $sentido = NULL, $modalidade = NULL, $inicio = NULL, $validade = NULL, $imo = NULL, $tipo_proposta = "proposta_tarifario" )
	{
		
		if( is_null($sentido) )
		{
			log_message('error',"Não foi possível consultar o tarifário, pois o sentido (IMP ou EXP) não foi informado");
			show_error("Não foi possível consultar o tarifário, pois o sentido (IMP ou EXP) não foi informado");
		}
		
		if( is_null($inicio) )
		{
			$inicio = date('d-m-Y');
		}
		
		if( is_null($validade) )
		{
			$validade = date('d-m-Y');
		}
		
		/** Chama o façade que vai buscar os tarifários **/
		$dados_recebidos = new ArrayObject();
		
		$dados_recebidos->offsetSet("clientes", $clientes);
		$dados_recebidos->offsetSet("origem", $origem);
		$dados_recebidos->offsetSet("embarque",$embarque);
		$dados_recebidos->offsetSet("desembarque", $desembarque);
		$dados_recebidos->offsetSet("destino", $destino);
		$dados_recebidos->offsetSet("imo", $imo);
		$dados_recebidos->offsetSet("modalidade", $modalidade);
		$dados_recebidos->offsetSet("modulo", $modulo);
		$dados_recebidos->offsetSet("sentido", $sentido);
		$dados_recebidos->offsetSet("inicio", $inicio);
		$dados_recebidos->offsetSet("validade", $validade);
		$dados_recebidos->offsetSet("tipo_proposta", $tipo_proposta);
		
		$facade = new Tarifario_Facade();
		
		$tarifarios = $facade->ListarTarifarios($dados_recebidos);
		
		$header['js'] = load_js(array('tarifarios/list_found'));
		
		$header['form_title'] = 'Scoa - Tarifários';
		$header['form_name'] = 'SELECIONAR TARIFÁRIO';
		$header['css'] = '';
		
		$data["tarifarios"] = $tarifarios;
		
		$imagens = '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/adicionar.jpg', 'id' => 'adicionar' , 'border' => 0)).'</a>';
			
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Tarifarios/list_found",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function fillAndPutOnSession( $id_tarifario = NULL, $sentido = NULL, $clientes = NULL, $inicio = NULL, $validade = NULL, $imo = "N" ,$id_item_proposta = "0" )
	{
		
		if( empty($id_tarifario) )
		{
			log_message('error','O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
			show_error('O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
		}

		$this->load->library("Scoa/url");
		$this->load->model("Adaptadores/sessao");
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Taxas/serializa_taxas");
		
		try{
				
			$facade = new Tarifario_Facade();
		
			$tarifario = $facade->BuscarTarifarioPeloId( $id_tarifario, $sentido, $clientes, $id_item_proposta, new DateTime($inicio), new DateTime($validade), $imo );
									
			/** Cria um item de proposta para serializar às taxas da proposta e guardar na sessão **/
			$item_proposta = new Item_Proposta($tarifario);
				
			$serializador = new Serializa_Taxas();
				
			$taxas_serializadas = $serializador->serializaTaxasProposta($item_proposta);
			
			$sessao = new Sessao();
			
			if( $id_item_proposta == "null" || $id_item_proposta == "NULL" )
			{
				$id_item_proposta = NULL;
			}
									
			$sessao
			->setIdItem($id_item_proposta)
			->setCc(true)
			->setPp(true)
			->setImo($imo)
			->setPeso((float)0.00)
			->setCubagem((float)0.00)
			->setVolumes((int)0)
			->setOrigem($tarifario->getRota()->getPortoOrigem()->getNome())
			->setEmbarque($tarifario->getRota()->getPortoEmbarque()->getNome())
			->setDesembarque($tarifario->getRota()->getPortoDesembarque()->getNome())
			->setDestino($tarifario->getRota()->getPortoFinal()->getNome())
			->setUnOrigem($tarifario->getRota()->getPortoOrigem()->getUnCode())
			->setUnEmbarque($tarifario->getRota()->getPortoEmbarque()->getUnCode())
			->setUnDesembarque($tarifario->getRota()->getPortoDesembarque()->getUnCode())
			->setUnDestino($tarifario->getRota()->getPortoFinal()->getUnCode())
			->setIdTarifario((int)$tarifario->getId())
			->setMercadoria("")
			->setObservacaoCliente("")
			->setObservacaoInterna("")
			->setLabelsFretesAdicionais($taxas_serializadas['label_taxas_adicionais'])
			->setLabelsTaxasLocais($taxas_serializadas['label_taxas_locais'])
			->setFreteAdicionais($taxas_serializadas['value_taxas_adicionais'])
			->setTaxasLocais($taxas_serializadas['value_taxas_locais'])
			->setInicio($inicio)
			->setValidade($validade)
			->setAntiCache(time());
								
			$id_item_sessao = $sessao->inserirItemNaSessao();
			
			$data["id_item_sessao"] = $id_item_sessao;
			$data['rota'] = $tarifario->getRota();
			
			$this->load->view("propostas/xml_item_proposta_tarifario",$data);
										
		} catch (Exception $e) {
			log_message('error',$e->getMessage());
			show_error($e->getMessage());
		}
		
	}
		
}//END CLASS	
