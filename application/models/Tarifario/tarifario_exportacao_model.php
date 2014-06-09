<?php
include_once APPPATH."/models/Tarifario/porto.php";
include_once APPPATH."/models/Tarifario/porto_exportacao_model.php";
include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
include_once APPPATH."/models/Taxas/taxa_tarifario_model.php";
include_once APPPATH."/models/Taxas/taxa_local_model.php";
include_once APPPATH."/models/Tarifario/interface_tarifario_model.php";
include_once APPPATH."/models/Clientes/agente.php";
include_once APPPATH."/models/Tarifario/rota.php";
include_once APPPATH."/models/Tarifario/breakdown.php";

class Tarifario_Exportacao_Model extends CI_Model implements Interface_Tarifario_Model{

	public function __construct()
	{
		parent::__construct();		
	}
	
	const sentido = 'EXP';
		
	public function obterTarifarios(Rota $rota, DateTime $inicio, DateTime $validade)
	{
		
		/** Converte o un_code do porto de destino **/
		$porto_model = new Porto_Exportacao_Model();			
		
		$this->db->
				select("
						tarifarios_pricing.id_place_receipt, tarifarios_pricing.id_port_loading,
					    tarifarios_pricing.id_via, tarifarios_pricing.id_place_delivery, tarifarios_pricing.id_via_adicional,
						tarifarios_pricing.id_tarifario_pricing, tarifarios_pricing.id_agente_via,
						tarifarios_pricing.id_agente, transit_receipt_x_loading, transit_loading_x_via,
						transit_via_x_place_delivery, transit_via_x_via_adicional, transit_via_adicional_x_place_delivery,
						tarifarios_pricing.aceita_imo, tarifarios_pricing.aceita_frete_cc, rota_principal,
						frete_compra, frete_compra_minimo,autonomia_frete						
					   ")->
				from("FINANCEIRO.tarifarios_pricing")->
				join("GERAIS.destinos","destinos.id_destino = tarifarios_pricing.id_place_delivery")->
				join("FINANCEIRO.tarifarios_taxas_pricing","tarifarios_taxas_pricing.id_tarifario_pricing = tarifarios_pricing.id_tarifario_pricing");
				
		if( ! is_null($rota->getPortoOrigem()->getId()) && $rota->getPortoOrigem()->getId() != 0 )
		{
			$this->db->where("id_place_receipt",$rota->getPortoOrigem()->getId());
		}	
		
		if( ! is_null($rota->getPortoEmbarque()->getId()) && $rota->getPortoEmbarque()->getId() != 0 )
		{
			$this->db->where("id_port_loading",$rota->getPortoEmbarque()->getId());
		}	

		if( ! is_null($rota->getPortoDesembarque()->getId()) && $rota->getPortoDesembarque()->getId() != 0 )
		{
			$this->db->where("id_via", $rota->getPortoDesembarque()->getId());
		}	
		
		if( ! is_null($rota->getPortoFinal()->getId()) && $rota->getPortoFinal()->getId() != 0 )
		{
			$this->db->where("id_place_delivery",$rota->getPortoFinal()->getId());
		}	

		$this->db->
		where("modulo", self::sentido)->							
		where("tarifarios_pricing.ativo","S")->
		order_by("destinos.destino","ASC")->
		group_by("tarifarios_pricing.id_tarifario_pricing");
				
		$rotas_encontradas = Array();
				
		$rs = $this->db->get();
				
		$linhas = $rs->num_rows();
		
		if( $linhas < 1 )
		{
			return $rotas_encontradas;
		}	
		
		$this->load->helper(array('intervalo_datas'));
		
		$taxa_model = new Taxa_Tarifario_Model();

		$result = $rs->result();
		
		foreach( $result as $tarifarios )
		{						
			
			$tarifario = new Tarifario_Exportacao();
			
			$tarifario->setId((int)$tarifarios->id_tarifario_pricing);
			$tarifario->setInicio($inicio);
			$tarifario->setValidade($validade);
			$tarifario->setRota($rota);
			$tarifario->setSentido(self::sentido);
			$tarifario->setAceitaImo($tarifarios->aceita_imo);
			$tarifario->setAceitaFreteCc($tarifarios->aceita_frete_cc);
			$tarifario->setRotaPrincipal($tarifarios->rota_principal);
			$tarifario->setFreteCompra($tarifarios->frete_compra);
			$tarifario->setFreteCompraMinimo($tarifarios->frete_compra_minimo);
			$tarifario->setAutonomiaFrete((float)$tarifarios->autonomia_frete);
						
			/** Preenche os portos **/			
			$rota = new Rota();
						
			/** Carrega os dados da origem **/
			$porto_origem = new Porto();
			$porto_origem->setId((int)$tarifarios->id_place_receipt);
			
			$porto_model->findById($porto_origem, 'origem');
			
			$rota->setPortoOrigem($porto_origem);
			
			/** Carrega os dados do porto de embarque **/
			$porto_embarque = new Porto();
			$porto_embarque->setId((int)$tarifarios->id_port_loading);
			
			$porto_model->findById($porto_embarque, 'embarque');
			
			$rota->setPortoEmbarque($porto_embarque);
			
			/** Carrega os dados do porto de desembarque **/
			$porto_desembarque = new Porto();
			$porto_desembarque->setId((int)$tarifarios->id_via);
						
			$porto_model->findById($porto_desembarque, 'desembarque');
			
			$rota->setPortoDesembarque($porto_desembarque);
			
			/** Carrega os dados do destino final **/
			$porto_final = new Porto();
			$porto_final->setId((int)$tarifarios->id_place_delivery);
			
			$porto_model->findById($porto_final, 'destino');
			
			$rota->setPortoFinal($porto_final);

			/** Carrega os dados da via adicional, se houver **/
			$porto_via_adicional = new Porto();
			
			if( ! empty($tarifarios->id_via_adicional) )
			{	
				$porto_via_adicional->setId((int)$tarifarios->id_via_adicional);
				
				$porto_model->findById($porto_via_adicional,'destino');
			}
			
			$rota->setPortoViaAdicional($porto_via_adicional);
			
			$tarifario->setRota($rota);
									
			/** Obtem o agente e o subagente do tarifario **/
			$agente = new Agente();
			$agente->setId((int)$tarifarios->id_agente_via);
			
			if( empty($row->id_agente_via) )
			{
				$agente->setRazao("Não Informado");
			}
			else
			{			
				$agente->findById();
			}
			
			$tarifario->setAgente($agente);
			
			$sub_agente = new Agente();
			$sub_agente->setId((int)$tarifarios->id_agente);
			
			if( empty($tarifarios->id_agente) )
			{
				$sub_agente->setRazao("Não Informado");
			}
			else
			{
				$sub_agente->findById();
			}
			
			$tarifario->setSubAgente($sub_agente);
			
			/** Informa o transit time total **/
			$transit_time = (int)$tarifarios->transit_receipt_x_loading + (int)$tarifarios->transit_loading_x_via +
			(int)$tarifarios->transit_via_x_place_delivery + (int)$tarifarios->transit_via_x_via_adicional +
			(int)$tarifarios->transit_via_adicional_x_place_delivery;
				
			$tarifario->setTransitTime($transit_time);
			
			/** Breakdown do tarifário **/
			$breakdown = new Breakdown($tarifario);												
			$tarifario->setBreakDown(trim($breakdown->formatarBreakDown()));
			
			/** Obtem as taxas do tarifario **/						
			try{
				
				$taxa_model->obterTaxasRotaTarifario($tarifario, $inicio, $validade);
				
			} catch(Exception $e) {
				show_error($e->getMessage());exit;
			}
			
			$rotas_encontradas[] = $tarifario;
		}	
		
		return $rotas_encontradas;
		
	}
	
	/**
	  * Find By Id
	  * 
	  * Busca o tarifário pelo id
	  * 
	  * @name findById
	  * @access public
	  * @param integer
	  * @return array
	  */
	public function findById( Tarifario $tarifario, $classificacao_cliente = "A", DateTime $inicio, DateTime $validade )
	{
				
		$this->db->
				select("tarifarios_pricing.id_place_receipt, tarifarios_pricing.id_port_loading,
					    tarifarios_pricing.id_via, tarifarios_pricing.id_place_delivery, tarifarios_pricing.id_via_adicional,
						tarifarios_pricing.id_tarifario_pricing, tarifarios_pricing.id_agente_via,
						tarifarios_pricing.id_agente, transit_receipt_x_loading, transit_loading_x_via,
						transit_via_x_place_delivery, transit_via_x_via_adicional, transit_via_adicional_x_place_delivery,
						obs, obs_origem, obs_porto_embarque, obs_via, obs_pais_destino, obs_destino, obs_via_adicional,
						tarifarios_pricing.aceita_imo, tarifarios_pricing.aceita_frete_cc, rota_principal,
						frete_compra, frete_compra_minimo,autonomia_frete 						
					   ")->
				from("FINANCEIRO.tarifarios_pricing")->
				where("id_tarifario_pricing",$tarifario->getId())->		
				where("modulo",self::sentido)->				
				where("ativo","S");
		
		$rs = $this->db->get();
			
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
				
		$row = $rs->row();
						
		$tarifario->setId((int)$row->id_tarifario_pricing);
		$tarifario->setInicio($inicio);
		$tarifario->setValidade($validade);		
		$tarifario->setSentido(self::sentido);
		$tarifario->setAceitaImo($row->aceita_imo);
		$tarifario->setAceitaFreteCc($row->aceita_frete_cc);
		$tarifario->setRotaPrincipal($row->rota_principal);
		$tarifario->setFreteCompra($row->frete_compra);
		$tarifario->setFreteCompraMinimo($row->frete_compra_minimo);
		$tarifario->setAutonomiaFrete((float)$row->autonomia_frete);
		
		/** Preenche os portos para criar a rota **/
		$porto_origem = new Porto();
		$porto_desembarque = new Porto();
		$porto_embarque = new Porto();
		$porto_destino = new Porto();
		$porto_via_adicional = new Porto();
		$porto_model = new Porto_Exportacao_Model();
				
		$porto_origem->setId((int)$row->id_place_receipt);
		$porto_model->findById($porto_origem, "origem");
		
		$porto_embarque->setId((int)$row->id_port_loading);
		$porto_model->findById($porto_embarque, "embarque");
		
		$porto_desembarque->setId((int)$row->id_via);
		$porto_model->findById($porto_desembarque, "desembarque");
		
		$porto_destino->setId((int)$row->id_place_delivery);
		$porto_model->findById($porto_destino, "destino");
		
		if( ! empty($row->id_via_adicional) )
		{	
			$porto_via_adicional->setId((int)$row->id_via_adicional);
			$porto_model->findById($porto_via_adicional,'destino');
		}
		
		/** Cria rota e atribui os portos **/
		$rota = new Rota();
		
		$rota->setPortoOrigem($porto_origem);
		$rota->setPortoEmbarque($porto_embarque);
		$rota->setPortoDesembarque($porto_desembarque);		
		$rota->setPortoFinal($porto_destino);
		$rota->setPortoViaAdicional($porto_via_adicional);
		
		$tarifario->setRota($rota);
			
		/** Obtem o agente e o subagente do tarifario **/
		$agente = new Agente();
		$agente->setId($row->id_agente_via);
		
		if( empty($row->id_agente_via) )
		{
			$agente->setRazao("Não Informado");
		}
		else
		{			
			$agente->findById();
		}
		
		$tarifario->setAgente($agente);
		
		$sub_agente = new Agente();
		$sub_agente->setId($row->id_agente);
		
		if( empty($row->id_agente) )
		{
			$sub_agente->setRazao("Não Informado");
		}
		else
		{
			$sub_agente->findById();
		}		
						
		$tarifario->setSubAgente($sub_agente);
		
		/** Informa o transit time total **/
		$transit_time = (int)$row->transit_receipt_x_loading + (int)$row->transit_loading_x_via +
		(int)$row->transit_via_x_place_delivery + (int)$row->transit_via_x_via_adicional +
		(int)$row->transit_via_adicional_x_place_delivery;
		
		$tarifario->setTransitTime($transit_time);
		
		/** Breakdown do tarifário **/
		$breakdown = new Breakdown($tarifario);												
		$tarifario->setBreakDown(trim($breakdown->formatarBreakDown()));
		
		/** Formata às observações e cria o BreakDown do Tarifário **/
		$observacoes_tarifario = $this->concatenaObservacoesDoTarifario($row);
		
		$tarifario->setObservacao($observacoes_tarifario);

		/** Obtem as taxas do tarifario **/
		$taxa_model = new Taxa_Tarifario_Model();

		$taxa_model->obterTaxasRotaTarifario($tarifario, $inicio, $validade);
				
		/** Busca às taxas locais da rota **/
		$taxas_locais_model = new Taxa_Local_Model();
			
		$taxas_locais = $taxas_locais_model->ObterTaxasLocais("EXP","LCL",$classificacao_cliente,$porto_origem->getId());
			 
		/** Insere as taxas locais no tariário **/
		foreach( $taxas_locais as $taxa_local )
		{
			$tarifario->adicionarNovaTaxa($taxa_local);			
		}	
		
		return $tarifario;
		
	}//END FUNCTION
	
	/**
	 * concatenaObservacoesDoTarifario
	 *
	 * Concatena às observações do tarifário para enviar a tela
	 *
	 * @name concatenaObservacoesDoTarifario
	 * @access public
	 * @param DataMapper $this
	 * @return string $observacao_concatenada;
	 */
	protected function concatenaObservacoesDoTarifario( stdClass $tarifario  )
	{
	
		$obs_tarifario = $tarifario->obs . "\n" . $tarifario->obs_origem . "\n" .
				$tarifario->obs_porto_embarque . "\n" . $tarifario->obs_via . "\n" .
				$tarifario->obs_pais_destino . "\n" . $tarifario->obs_destino . "\n" .
				$tarifario->obs_via_adicional;
		
		$disallowed_chars = Array("/", "/\/", "-","!");
		
		$obs_tarifario = str_replace($disallowed_chars, " ", utf8_encode($obs_tarifario));
		
		return $obs_tarifario;
	
	}
        
}//END CLASS