<?php
class Test_Model_Tarifario_Exportacao extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->output->enable_profiler(TRUE);
		$this->load->model("Tarifario/rota");
		$this->load->model("Tarifario/porto");
		$this->load->model("Tarifario/tarifario_exportacao");
		$this->load->model("Tarifario/tarifario_exportacao_model");
			
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
	
	public function testFindTarifarioByIdShouldPass()
	{
		
		include_once APPPATH . "/models/Tarifario/Factory/concrete_exportacao_factory.php";
		include_once APPPATH . "/models/Tarifario/Factory/concrete_importacao_factory.php";
		include_once APPPATH . "/models/Tarifario/Factory/concrete_factory.php";
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$importacao_factory = new Concrete_Importacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$tarifario = $factory->CreateTarifarioObject($exportacao_factory);
		
		$model = $factory->CreateTarifarioModel($exportacao_factory);
		
		$this->unit->run($tarifario instanceof Tarifario, TRUE,"Testa se o objeto é uma instancia da classe tarifario");

		$this->unit->run($model instanceof Interface_Tarifario_Model,TRUE,"Testa o tipo do objeto de model","Tem de ser do tipo Interface_Tarifario_Model");
		
		$tarifario->setId((int)1);
		
		$model->findById($tarifario);
		
		$this->unit->run(is_null($tarifario->getRota()),FALSE,'Testa se a rota foi encontrada');
		$this->unit->run($tarifario->getRota() instanceof Rota,TRUE,"Testa o tipo da rota","Tem de ser do tipo Rota");
		$this->unit->run($tarifario->getSentido(),"EXP","Testa o sentido do tarifario","Tem de ser IMP ou EXP");
		$this->unit->run(is_array($tarifario->getTaxa()),TRUE,"Testa se a propriedade que armazena às taxas é um Array");
		
		$encontrou_taxas = FALSE;
		
		if( count($tarifario->getTaxa()) > 0 )
		{
			$encontrou_taxas = TRUE;
		}	

		$this->unit->run($encontrou_taxas,TRUE,"Testa se foram encontradas taxas para este tarifário");
		
	}
	
	public function testObterRotas()
	{
		
		/** Cria os objetos dos portos **/
		$porto_origem = new Porto();
		$porto_desembarque = new Porto();
		$porto_embarque = new Porto();
		$porto_destino = new Porto();
		
		$porto_origem->setUnCode("BRSSZ");
		$porto_embarque->setUnCode("BRSSZ");
		$porto_destino->setUnCode("CNSHA");
		
		/** Model dos portos de importação **/
		$porto_model = new Porto_Exportacao_Model();
		
		/** Cria a rota **/
		$rota = new Rota();
		$rota->setPortoOrigem($porto_origem);
		$rota->setPortoEmbarque($porto_embarque);
		$rota->setPortoDesembarque($porto_desembarque);
		$rota->setPortoFinal($porto_destino);
		
		$porto_model->findByUnCode($porto_origem, 'origem');
		$porto_model->findByUnCode($porto_embarque, 'embarque');
		$porto_model->findByUnCode($porto_destino, 'destino');
			
		$tarifario_model = new Tarifario_Exportacao_Model();
		
		$tarifarios = $tarifario_model->obterTarifarios($rota);
		
		$this->unit->run($tarifarios,'is_array',"Testa a busca por rotas do tarifário de exportação","Tem de ser um Array");
		
		$encontrou_rotas = FALSE;
		
		if( count($tarifarios) > 0 )
		{
			$encontrou_rotas = TRUE;
		}	
		
		$this->unit->run($encontrou_rotas,TRUE,"Testa a busca por rotas do tarifário de exportação","Testa se pelo menos uma (1) rota foi encontrada");

		$this->unit->run($tarifarios[0] instanceof Tarifario,TRUE,"Testa a busca por rotas do tarifário de exportação","Testa se o objeto é do tipo correto: Tarifário");
		pr($tarifarios);
	}
	
}//END CLASS