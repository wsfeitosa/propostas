<?php
class Test_Desbloqueios extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Propostas/proposta_tarifario", "proposta", TRUE);
		$this->load->model("Clientes/concorrente");
		$this->load->model("Desbloqueios/Solicitacao_Desbloqueio_Taxa","desbloqueio_taxa");
		$this->load->model("Desbloqueios/Solicitacao_Desbloqueio_Periodo","debloqueio_data");		
		$this->load->model("Tarifario/Tarifario_exportacao");
		$this->load->model("Taxas/taxa_adicional","taxa");
		$this->load->model("Desbloqueios/solicitacao_desbloqueio_taxa_model");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/unidade");
		
		include_once APPPATH . "/models/Propostas/item_proposta.php";
	
	}
	
	/** Função que vai executar todos os testes **/
	public function index()
	{
		try {
	
			/** Testes à serem rodados **/
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
	
	public function test_DesbloqueioTaxa()
	{
		$solicitacao_taxa = new Solicitacao_Desbloqueio_Taxa();
		
		$tarifario = new Tarifario_Exportacao();
		
		$item_propostas = new Item_Proposta($tarifario);
		
		$concorrente = new Concorrente();
		
		$this->unit->run($solicitacao_taxa->setItem($item_propostas),TRUE,"Atribui um item ao desbloqueio");
		
		$this->unit->run($solicitacao_taxa->getItem(),'is_object',"Obtem o item atribuido");
		
		$this->unit->run(get_class($solicitacao_taxa->getItem()),get_class($item_propostas),"testa o tipo de objeto do retorno");		
		
		$this->unit->run($solicitacao_taxa->setConcorrente($concorrente),TRUE,"Atribui um concorrente");
		
		$this->unit->run($solicitacao_taxa->getConcorrente(),'is_object',"Obtem o concorrente");
		
		$this->unit->run(get_class($solicitacao_taxa->getConcorrente()),get_class($concorrente),"Testa o tipo do objeto de retorno");
		
		/** Criando a taxa para o teste **/		
		$taxa = new Taxa_Adicional();
		
		$unidade = new Unidade(3);
						
		$moeda = new Moeda(42);
		
		$taxa->setUnidade($unidade);
		
		$taxa->setMoeda($moeda);
		
		$taxa->setId(8);
		
		$taxa->setValor(15.25);
		
		$taxa->setValorMinimo(1.01);
		
		$taxa->setValorMaximo(1.01);
				
		$solicitacao = new Solicitacao_Desbloqueio_Taxa();
		
		$solicitacao->setItem($taxa);
				
		$taxa_model = new Solicitacao_Desbloqueio_Taxa_Model();
		
		$taxa_model->setSolicitacao($solicitacao);
				
		$this->unit->run($taxa_model->salvar(),TRUE,"Salva uma solicitação de desbloqueio de taxa");
		
	}
	
}//END CLASS