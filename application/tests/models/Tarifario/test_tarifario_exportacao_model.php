<?php
class Test_Tarifario_Exportacao_Model extends CI_TestCase{
	
	protected $model = NULL;	
		
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Tarifario/tarifario_exportacao_model");
		$this->CI->load->model("Tarifario/tarifario_exportacao");			
		$this->CI->load->model("Tarifario/porto_exportacao_model");
		$this->CI->load->model("Tarifario/rota");
		$this->CI->load->model("Tarifario/porto");
		
		$this->model = new Tarifario_Exportacao_Model();	
		
		$porto = new Porto();
		
		$this->assertInstanceOf("Tarifario_Exportacao_Model", $this->model);
		$this->assertInstanceOf("Porto", $porto);	
	}	
		
	public function testObterTarifarios()
	{
		
		//$this->markTestSkipped("Consertar este teste está com erro!");
		
		$origem = new Porto();
		$embarque = new Porto();
		$desembarque = new Porto();
		$destino = new Porto();
		
		$origem->setUnCode("BRSSZ");
		$embarque->setUnCode("BRSSZ");
		$destino->setUnCode("CNSHA");
		
		$porto_model = new Porto_Exportacao_Model();
		
		$porto_model->findByUnCode($origem,"origem");
		$porto_model->findByUnCode($embarque,"embarque");
		$porto_model->findByUnCode($destino,"destino");
				
		$rota = new Rota();
		
		$rota->setPortoOrigem($origem);
		$rota->setPortoEmbarque($embarque);
		$rota->setPortoDesembarque($desembarque);		
		$rota->setPortoFinal($destino);
				
		$rotas_encontradas = $this->model->obterTarifarios($rota);
		
		$this->assertTrue(is_array($rotas_encontradas));
		
		$this->assertGreaterThan(0, count($rotas_encontradas));
		
		if( is_array($rotas_encontradas) && count($rotas_encontradas) > 1 )
		{
			$this->assertInstanceOf("Tarifario", $rotas_encontradas[0]);
		}	
		
	}
	
}//END CLASS


