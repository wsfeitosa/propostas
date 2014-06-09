<?php

class Test_Taxa extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->use_strict(TRUE);
		$this->unit->active(TRUE);
	
		/** Carrega a library de debug **/
		$this->output->enable_profiler(TRUE);
	
		/** O models a serem testados **/
		$this->load->model("Taxas/taxa_model","taxa");
		$this->load->model("Tarifario/tarifario_importacao_model");
		$this->load->model("Taxas/taxa_local_model");
		include_once APPPATH."/models/Tarifario/rota.php";
		include_once APPPATH."/models/Tarifario/porto.php";
	}
	
	public function index()
	{
		try{
	
			foreach (get_class_methods($this) as $method)
			{
				if( strpos($method, "test") !== FALSE )
				{
					$this->$method();
				}
	
			}
	
		} catch (Exception $e) {
			show_error($e->getMessage());
		}
		echo $this->unit->report();
	
	}
			
	public function testBuscarTaxasLocais()
	{
		
		$taxas_locais_model = new Taxa_Local_Model();
		
		$taxas_locais = $taxas_locais_model->ObterTaxasLocais("IMP","FCL","F",1);
		
        //pr($taxas_locais);
        
        $this->unit->run(is_array($taxas_locais), TRUE, "Buscar Taxas Locais", "Busca às taxas padrões dos portos");
        
        $retornou_taxas = FALSE;
        
        if( count($taxas_locais) > 0 )
        {
            $retornou_taxas = TRUE;
        }    
        
        $this->unit->run($retornou_taxas,TRUE,"Verifica se foram encontradas Taxas","Verifica se ao menos uma taxa foi retornada");
        
        $this->unit->run(get_class($taxas_locais[0]), "Taxa_Local", "Verifica o tipo do objeto", "Verifica se os objetos retornados são do tipo correto: Taxa_Local()");
               
	}
	
    public function testBuscarTaxasDeUmaPropostaCadastradaDeveriaPassar()
    {
        
        $this->load->model("Propostas/item_proposta");
        
        $item = new Item_Proposta();
        
        $item->setId((int)47);
        
        $taxa_model = new Taxa_Model();
        
        $taxas_encontradas = $taxa_model->retornaTaxasDaProposta($item);
                
        $this->unit->run(get_class($taxas_encontradas), "ArrayObject", "Testa a busca das taxas de uma proposta já cadastrada", "Testa se o retornado foi um array");
        
        $encontrou_taxas = FALSE;
        
        if( count($taxas_encontradas) > 0 )
        {
            $encontrou_taxas = TRUE;
        }    
        
        $this->unit->run($encontrou_taxas, TRUE, "Testa a busca das taxas de uma proposta já cadastrada", "Testa a quantidade de taxas encontradas, ao menos uma deveria ser retornada");
               
        if( $encontrou_taxas )
        {
            
            $this->unit->run(is_subclass_of($taxas_encontradas->offsetGet(0),"Taxa"),TRUE,"Testa a busca das taxas de uma proposta já cadastrada","Testa o tipo das taxas encontrada: Taxa_Local ou Taxa_Adicional");
            
            $moeda = $taxas_encontradas->offsetGet(0)->getMoeda();
            
            $this->unit->run(get_class($moeda),"Moeda","Testa a busca das taxas de uma proposta já cadastrada","Testa se o objeto moeda foi definido para às taxas encontradas");
         
            $unidade = $taxas_encontradas->offsetGet(0)->getUnidade();
            
            $this->unit->run(get_class($unidade),"Unidade","Testa a busca das taxas de uma proposta já cadastrada","Testa se o objeto unidade foi definido para às taxas encontradas");
        }    
        
    }        
    
}//END CLASS