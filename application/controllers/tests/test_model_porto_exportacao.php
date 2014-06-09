<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Testes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @version  1.0
 * Classes de testes dos portos.
 */
class Test_Model_Porto_Exportacao extends CI_Controller{
	
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
		$this->load->model("Tarifario/porto");
		$this->load->model("Tarifario/Factory/concrete_factory");		
		$this->load->model("Tarifario/Factory/concrete_exportacao_factory");	
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
	
	public function testFindByIdOrigemShouldPass()
	{
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
		
		$porto = new Porto();
		
		/** Id do porto de itajai **/
		$porto->setId(1);
		
		try{
		
			$model->findById($porto,"origem");
			
		} catch ( RuntimeException $e ) {
			show_error($e->getMessage());
		}
		
		$this->unit->run($porto->getNome(),"ITAJAI","Testa o nome do porto (Origem)","Testa se o nome do porto retornou corretamente");
		
		$this->unit->run($porto->getPais(),"BRASIL","Testa o Pais do porto (Origem)","Testa o nome do pais do porto");
		
		$this->unit->run($porto->getUnCode(),"BRITJ","Testa o un code do porto (Origem)","Testa o un code do porto");
		
		$this->unit->run($porto->getId(),1,"Testa o id do porto (Origem)","Testa se o id do porto retornou corretamente");
		
	}
	
	public function testFindByIdEmbarqueShouldPass()
	{
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
		
		$porto = new Porto();
		
		/** Id do porto de Santos **/
		$porto->setId(3);
		
		try{
		
			$model->findById($porto,"embarque");
				
		} catch ( RuntimeException $e ) {
			show_error($e->getMessage());
		}
			
		$this->unit->run($porto->getNome(),"SANTOS","Testa o nome do porto (Embarque)","Testa se o nome do porto retornou corretamente");
		
		$this->unit->run($porto->getPais(),"BRASIL","Testa o Pais do porto (Embarque)","Testa o nome do pais do porto: ".$porto->getPais());
		
		$this->unit->run($porto->getUnCode(),"BRSSZ","Testa o un code do porto (Embarque)","Testa o un code do porto");
		
		$this->unit->run($porto->getId(),3,"Testa o id do porto (Embarque)","Testa se o id do porto retornou corretamente");
		
	}
	
	public function testFindByIdDesembarqueShouldPass()
	{
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
		
		$porto = new Porto();
		
		/** Id do porto de Santos **/
		$porto->setId(55);
		
		try{
		
			$model->findById($porto,"desembarque");
		
		} catch ( RuntimeException $e ) {
			show_error($e->getMessage());
		}
		
		$this->unit->run($porto->getNome(),"JEBEL ALI","Testa o nome do porto (Desembarque)","Testa se o nome do porto retornou corretamente");
		
		$this->unit->run($porto->getPais(),"EMIRADOS ÁRABES UNIDOS","Testa o Pais do porto (Desembarque)","Testa o nome do pais do porto: ".$porto->getPais());
		
		$this->unit->run($porto->getUnCode(),"AEJEA","Testa o un code do porto (Desembarque)","Testa o un code do porto");
		
		$this->unit->run($porto->getId(),55,"Testa o id do porto (Desembarque)","Testa se o id do porto retornou corretamente");
		
	}
	
	public function testFindByIdDestinoShouldPass()
	{
	
		$exportacao_factory = new Concrete_Exportacao_Factory();
	
		$factory = new Concrete_Factory();
	
		$model = $factory->CreatePortoModel($exportacao_factory);
	
		$porto = new Porto();
	
		/** Id do porto de Santos **/
		$porto->setId(1567);
	
		try{
	
			$model->findById($porto,"destino");
	
		} catch ( RuntimeException $e ) {
			show_error($e->getMessage());
		}
	
		$this->unit->run($porto->getNome(),"ILO","Testa o nome do porto (Destino)","Testa se o nome do porto retornou corretamente");
	
		$this->unit->run($porto->getPais(),"PERU","Testa o Pais do porto (Desembarque)","Testa o nome do pais do porto: ".$porto->getPais());
	
		$this->unit->run($porto->getUnCode(),"PEILQ","Testa o un code do porto (Desembarque)","Testa o un code do porto");
	
		$this->unit->run($porto->getId(),1567,"Testa o id do porto (Desembarque)","Testa se o id do porto retornou corretamente");
	
	}
	
	public function testFindByNameShouldPass()
	{
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
				
		$portos_encontrados = $model->findByName( "a", "origem" );
		
		$this->unit->run($portos_encontrados, 'is_array', "Testa a busca pelo nome do porto (Origem)", "Verifica so o retornado foi um array");
		
		$encontrou_porto = FALSE;
		
		if( count($portos_encontrados) > 0 )
		{
			$encontrou_porto = TRUE;
		}	
		
		$this->unit->run($encontrou_porto,TRUE, "Testa a busca pelo nome do porto (Origem)","Testa se ao menos um porto foi retornado");
		
		$this->unit->run(get_class($portos_encontrados[0]),"Porto","Testa a busca pelo nome do porto (Origem)","Verifica se o tipo de objeto retornado está correto");
		
	}
	
	public function testFindByNameLoadingShouldPass()
	{
	
		$exportacao_factory = new Concrete_Exportacao_Factory();
	
		$factory = new Concrete_Factory();
	
		$model = $factory->CreatePortoModel($exportacao_factory);
	
		$portos_encontrados = $model->findByName( "a", "embarque" );
	
		$this->unit->run($portos_encontrados, 'is_array', "Testa a busca pelo nome do porto (Embarque)", "Verifica so o retornado foi um array");
	
		$encontrou_porto = FALSE;
	
		if( count($portos_encontrados) > 0 )
		{
			$encontrou_porto = TRUE;
		}
	
		$this->unit->run($encontrou_porto,TRUE, "Testa a busca pelo nome do porto (Embarque)","Testa se ao menos um porto foi retornado");
	
		$this->unit->run(get_class($portos_encontrados[0]),"Porto","Testa a busca pelo nome do porto (Embarque)","Verifica se o tipo de objeto retornado está correto");
	
	}
	
	public function testFindByNameDischargeShouldPass()
	{
	
		$exportacao_factory = new Concrete_Exportacao_Factory();
	
		$factory = new Concrete_Factory();
	
		$model = $factory->CreatePortoModel($exportacao_factory);
	
		$portos_encontrados = $model->findByName( "a", "desembarque" );
	
		$this->unit->run($portos_encontrados, 'is_array', "Testa a busca pelo nome do porto (Desembarque)", "Verifica so o retornado foi um array");
		
		$encontrou_porto = FALSE;
	
		if( count($portos_encontrados) > 0 )
		{
			$encontrou_porto = TRUE;
		}
	
		$this->unit->run($encontrou_porto,TRUE, "Testa a busca pelo nome do porto (Desembarque)","Testa se ao menos um porto foi retornado");
	
		$this->unit->run(get_class($portos_encontrados[0]),"Porto","Testa a busca pelo nome do porto (Desembarque)","Verifica se o tipo de objeto retornado está correto");
	
	}
	
	public function testFindByNameDestinationShouldPass()
	{
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
		
		$portos_encontrados = $model->findByName( "a", "destino" );
		
		$this->unit->run($portos_encontrados, 'is_array', "Testa a busca pelo nome do porto (Destino)", "Verifica so o retornado foi um array");
		
		$encontrou_porto = FALSE;
		
		if( count($portos_encontrados) > 0 )
		{
			$encontrou_porto = TRUE;
		}
		
		$this->unit->run($encontrou_porto,TRUE, "Testa a busca pelo nome do porto (Destino)","Testa se ao menos um porto foi retornado");
		
		$this->unit->run(get_class($portos_encontrados[0]),"Porto","Testa a busca pelo nome do porto (Destino)","Verifica se o tipo de objeto retornado está correto");
		
	}
	
	public function testFindByUnCodeShouldPass()
	{
		
		$exportacao_factory = new Concrete_Exportacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$model = $factory->CreatePortoModel($exportacao_factory);
		
		$porto = new Porto();
		
		$porto->setUnCode("DEHAM");
					
		$model->findByUnCode( $porto, "DESTINO" );		
						
		$this->unit->run($porto->getId(),'is_int','Testa a busca por uncode',"Verifica se o id do porto foi preenchido: {$porto->getId()}");
		
		$this->unit->run($porto->getNome(),"HAMBURG", "Testa a busca por uncode", "Verifica se o nome do porto foi setado corretamente");
		
	}
	
}//END CLASS