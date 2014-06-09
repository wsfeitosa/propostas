<?php
class Test_Tarifario_importacao extends CI_TestCase{
	
	protected $factory = NULL;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->CI->load->model("Tarifario/Factory/concrete_factory");
		$this->CI->load->model("Tarifario/Factory/concrete_importacao_factory");
		$this->CI->load->model("Tarifario/rota");
		$this->CI->load->model("Tarifario/porto");			
		
		$this->factory = new Concrete_Factory();
		
	}
	
	public function testAssertPreConditions()
	{
		$this->assertTrue(class_exists("Concrete_Factory"));
		$this->assertTrue(class_exists("Concrete_Importacao_Factory"));
		$this->assertTrue(class_exists("Rota"));
		$this->assertTrue(class_exists("Porto"));			
	}
	
	public function providerTestObterTarifarios()
	{
		return Array(
						array("CNBJO","BRSSZ","BRSSZ"),
						array("DEHAM","BRSSZ","BRSSZ"),
						array("CNSHA","BRSSZ","BRSSZ"),
						array("FRPAR","BRSSZ","BRSSZ"),
						array("KRPUS","BRSSZ","BRSSZ"),
						array("USNYC","BRSSZ","BRSSZ"),
				);
	}
	
	/**
	 * @depends testAssertPreConditions
	 * @dataProvider providerTestObterTarifarios
	 */
	public function testObterTarifarios($un_origem, $un_desembarque, $un_destino)
	{
		$concrete_importacao = new Concrete_Importacao_Factory();

		$origem = new Porto();		
		$embarque = new Porto();
		$desembarque = new Porto();				
		$destino = new Porto();
		
		$porto_model = $this->factory->CreatePortoModel($concrete_importacao);
		$tarifario_model = $this->factory->CreateTarifarioModel($concrete_importacao);
		$tarifario = $this->factory->CreateTarifarioObject($concrete_importacao);
		
		$origem->setUnCode($un_origem);		
		$desembarque->setUnCode($un_desembarque);
		$destino->setUnCode($un_destino);
		
		$porto_model->findByUnCode($origem,"origem");
		$porto_model->findByUnCode($desembarque,"desembarque");
		$porto_model->findByUnCode($destino,"destino");
		
		$rota = new Rota();
		
		$rota->setPortoOrigem($origem);
		$rota->setPortoEmbarque($embarque);
		$rota->setPortoDesembarque($desembarque);
		$rota->setPortoFinal($destino);
		
		$tarifarios_encontrados = $tarifario_model->obterTarifarios($rota);		
		
		$this->assertTrue(is_array($tarifarios_encontrados));
		
		$this->assertGreaterThan(0, count($tarifarios_encontrados));

		if( count($tarifarios_encontrados) > 0 )
		{
			$this->assertInstanceOf("Tarifario", $tarifarios_encontrados[0]);
		}	
		
	}
	
	
		
}//END CLASS

