<?php
class Tarifas extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
						
		$this->load->helper(Array("html","form","url"));
		$this->load->model("Tarifario/Facade/tarifario_facade");
		$this->load->model("Api/api_facade");		
	}
	
	/**
	 * buscar
	 *
	 * busca às tarifas de um determinado cliente em uma determinada rota
	 *
	 * @name Buscar
	 * @access public
	 * @param int $id_cliente 
	 * @param bool $imo
	 * @param string $ppcc
	 * @param string $sentido
	 * @param string $origem
	 * @param string $embarque
	 * @param string $desembarque
	 * @param string $destino
	 * @return xml $tarfifas_encontradas
	 */ 	
	public function buscar($id_cliente = NULL, $imo = FALSE, $ppcc = NULL, $sentido = NULL, $origem = "NULL", $embarque = "NULL", $desembarque = "NULL", $destino = "NULL" ) 
	{				
		$api = new Api_Facade();
		
		$parametros = new ArrayObject(Array());
						
		/** Valida os dados recebidos **/
		$this->load->library('zend');
		$zend = new CI_Zend();
		
		$zend->load("Zend/Validate");
		
		if( ! Zend_Validate::is($id_cliente, "Digits") && ! Zend_Validate::is($id_cliente, "GreaterThan", array('min' => 1)) )
		{			 
			 $this->load->view("Api/xml_error_message",Array('error' => "Id do cliente inválido"));
		}
		else if( is_null($ppcc) || ! in_array(strtoupper($ppcc), Array("PP","CC")) )
		{			
			$this->load->view("Api/xml_error_message",Array('error' => "Modalidade de embarque informada inválida"));
		}	
		else if( is_null($sentido) || ! in_array(strtoupper($sentido), Array("IMP","EXP")) )
		{
			$msg = "O sentido do embarque informado está incorreto, precisa ser IMP ou EXP";
			$this->load->view("Api/xml_error_message",Array('error' => $msg));
		}	
		else
		{	
			$parametros->offsetSet("id_cliente", $id_cliente);
			$parametros->offsetSet("imo", $imo);
			$parametros->offsetSet("ppcc", strtoupper($ppcc));
			$parametros->offsetSet("sentido", strtoupper($sentido));
			$parametros->offsetSet("origem", strtoupper($origem));
			$parametros->offsetSet("embarque", strtoupper($embarque));
			$parametros->offsetSet("desembarque", strtoupper($desembarque));
			$parametros->offsetSet("destino", strtoupper($destino));
					
			$api->ListarTarifarios($parametros);
		}
			
	}
	
	/**
	 * buscarTarifarioCompleto
	 *
	 * Busca o tarifário completo baseado no id do tarifário informado no momento da consulta
	 *
	 * @name buscarTarifarioCompleto
	 * @access public
	 * @param int $id_tarifario
	 * @return xml $tarifario
	 */ 	
	public function buscarTarifarioCompleto($id_tarifario = NULL, $id_cliente = NULL, $imo = "N", $ppcc = NULL)
	{		
		if( empty($id_tarifario) )
		{
			log_message('error','O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
			show_error('O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
		}
		else
		{		
			try{
					
				$api = new Api_Facade();
	
				$tarifario = $api->BuscarTarifarioPorId($id_tarifario, $id_cliente, $imo, $ppcc);
				
				$this->load->view("Tarifarios/xml_tarifario",Array("tarifario" => $tarifario));
					
			} catch (Exception $e) {
				log_message('error',$e->getMessage());
				$this->load->view("Api/xml_error_message",Array('error' => $e->getMessage()));	
			}
		}
		
	}
	
	/**
	 * buscarTarifarioCompleto
	 *
	 * Busca o tarifário completo baseado no id do tarifário informado no momento da consulta
	 *
	 * @name buscarTarifarioCompleto
	 * @access public
	 * @param int $id_tarifario
	 * @return xml $tarifario
	 */
	public function buscarTarifarioNacCompleto($id_tarifario = NULL, $id_cliente = NULL, $imo = "N", $ppcc = NULL)
	{
		if( empty($id_tarifario) )
		{
			log_message('error','O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
			show_error('O id do tarifário não foi informado corretamente para efetuar a busca do tarifário');
		}
		else
		{
			try{
					
				$api = new Api_Facade();
	
				$tarifario = $api->BuscarTarifarioNacPorId($id_tarifario, $id_cliente, $imo, $ppcc);
				
				$this->load->view("Tarifarios/xml_tarifario",Array("tarifario" => $tarifario, "id_item" => $tarifario->id_item));
					
			} catch (Exception $e) {
				log_message('error',$e->getMessage());
				$this->load->view("Api/xml_error_message",Array('error' => $e->getMessage()));
			}
		}
	
	}
	
	public function buscarPropostaSpot($numero = NULL)
	{
		
		if( is_null($numero) )
		{			
			$msg = "Numero da proposta spot informado invalido!";
			$this->load->view("Api/xml_error_message",Array('error' => $msg));
		}	
		else
		{
			
			$this->load->model("Clientes/cliente_model");
			$this->load->model("Propostas/proposta_spot");

			try{
			
				$api = new Api_Facade();
				
				$item = $api->BuscarPropostaSpot($numero);
				
				/** Busca o cliente da proposta **/
				$this->db->select("id_proposta")->from("CLIENTES.itens_proposta")->where("id_item_proposta",$item->getId());
				
				$rs = $this->db->get();
				
				$id_proposta = $rs->row()->id_proposta;
				
				$proposta = new Proposta_Spot();
				$proposta->setId((int)$id_proposta);
								
				$this->cliente_model->findByIdDaProposta($proposta);
				$cliente = $proposta->getClientes();				
				
				if( ! $item->getTarifario() instanceof Tarifario )
				{
					$this->load->view("Api/xml_error_message",Array('error' => 'Nenhuma proposta encontrada'));				
				}	
				else
				{	
					$this->load->view("Api/xml_proposta_spot",Array("item" => $item, "cliente" => $cliente[0]));
				}
				
			} catch (Exception $e) {
				log_message('error',$e->getMessage());			
				$this->load->view("Api/xml_error_message",Array('error' => $e->getMessage()));			
			}
		}
	}
		
	public function listarPropostasSpot($id_cliente = NULL) 
	{
		
		if( is_null($id_cliente) )
		{
			$msg = "Cliente informado invalido!";
			$this->load->view("Api/xml_error_message",Array('error' => $msg));
		}	
		else
		{
			
			try{
					
				$api = new Api_Facade();
					
				$spots = $api->ListarPropostasSpotAtivasPorCliente($id_cliente);
							
				$this->load->view("Api/xml_listar_propostas_spot",Array("spots" => $spots));				
			
			} catch (Exception $e) {
				log_message('error',$e->getMessage());
				$this->load->view("Api/xml_error_message",Array('error' => $e->getMessage()));
			}
			
		}	
	}
	
	/**
	 * buscarTaxasLocais
	 *
	 * busca apenas às taxas locais de um determinado cliente
	 *
	 * @name buscarTaxasLocais
	 * @access public
	 * @param int $id_cliente
	 * @param string $sentido
	 * @param string $modalidade
	 * @param int $id_porto
	 * @return xml xml_taxas_locais
	 */ 	
	public function buscarTaxasLocais($id_cliente = NULL, $sentido = NULL, $modalidade = NULL, $id_origem = NULL, $id_destino = NULL) 
	{				
		$api = new Api_Facade();

		if( $modalidade == "COL" )
		{
			$modalidade = "LCL";
		}	

		$taxas_locais_encontradas = $api->BuscaTaxasLocais($id_cliente, $sentido, $modalidade, $id_origem, $id_destino);
				
		$this->load->view('Api/xml_taxas_locais',Array('taxas_locais' => $taxas_locais_encontradas));
	}
	
}//END CLASS