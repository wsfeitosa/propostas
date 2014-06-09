<?php
include_once APPPATH."/models/Taxas/taxa_adicional.php";
include_once APPPATH."/models/Taxas/taxa_model.php";
include_once APPPATH."/models/Taxas/moeda.php";
include_once APPPATH."/models/Taxas/moeda_model.php";
include_once APPPATH."/models/Taxas/unidade.php";
include_once APPPATH."/models/Taxas/unidade_model.php";

class Taxa_Tarifario_Model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array('intervalo_datas'));
		//$this->output->enable_profiler(FALSE);
	}

	/**
	  * obterTaxasRotaTarifario
	  * 
	  * Obtem as taxas da rota de um tarifario
	  * 
	  * @name obterTaxasRotaTarifario
	  * @access public
	  * @param Tarifario_Importacao
	  * @return void
	  */
	public function obterTaxasRotaTarifario( Tarifario $tarifario, DateTime $inicio, DateTime $validade )
	{
			
		$this->db->
				select("id_taxa, valor, valor_minimo, valor_maximo, id_unidade, id_moeda, data_inicio, validade, ppcc")->
				from("FINANCEIRO.tarifarios_taxas_pricing")->
				where("id_tarifario_pricing",$tarifario->getId());				
		
		$rs = $this->db->get();
						
		if( $rs->num_rows() < 1 )
		{
			log_message('error','Não foi possivel recuperar as taxas do tarifario selecionado');
			throw new Exception('Não foi possivel recuperar as taxas do tarifario selecionado'.pr($this->db));
		}	
		
		/** Cria os objetos com as taxas e passa ao objeto tarifário **/
		$result = $rs->result();
		
		$model_taxa_geral = new Taxa_Model();
		$moeda_model = new Moeda_Model();
		$unidade_model = new Unidade_Model();
		
		foreach ($result as $taxas) 
		{
			
			/** Verifica se esta taxa é valida para o periodo da proposta **/
			$taxa_dentro_da_validade = verifica_intervalo($taxas->data_inicio,$taxas->validade,$inicio->format('Y-m-d'),$validade->format('Y-m-d'));
			/**
			if( ! $taxa_dentro_da_validade )
			{
				continue;
			}
			**/
			$taxa = new Taxa_Adicional();
			
			$taxa->setId((int)$taxas->id_taxa);
			$taxa->setValor((float)$taxas->valor);
			$taxa->setValorMinimo((float)$taxas->valor_minimo);
			$taxa->setValorMaximo((float)$taxas->valor_maximo);			
			$taxa->setPPCC($taxas->ppcc);
			
			/** Obtem o nome da taxa **/						
			$model_taxa_geral->obterNomeTaxaAdicional($taxa);

			/** Obtem a moeda da taxa **/
			$moeda = new Moeda();
			
			$moeda->setId((int)$taxas->id_moeda);
									
			$moeda_model->findById($moeda);
			
			$taxa->setMoeda($moeda);
									
			/** Obtem a unidade da taxa **/								
			$unidade = new Unidade();

			$unidade->setId((int)$taxas->id_unidade);
				
			$unidade_model->findById($unidade);

			$taxa->setUnidade($unidade);
			
			$tarifario->adicionarNovaTaxa($taxa);
						
		}
		
	}//END FUNCTION
	
}//END CLASS