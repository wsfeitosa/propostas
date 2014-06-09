<?php
class Test_Proposta_Model extends CI_TestCase{
	
	protected $instance;
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Propostas/proposta_model");	
		$this->instance = new Proposta_Model();	
		
		$_SESSION["matriz"][1] = "SP";				
	}
	
	public function testInitialConditions()
	{
		$this->assertTrue(class_exists("Proposta_Model"));
		$this->assertInstanceOf("Proposta_Model", $this->instance);	
	}
	
	public function providerNumberTypes()
	{
		return Array(
						array("proposta_cotacao", "PC"),
						array("proposta_tarifario", "PT"),
						array("proposta_especial", "PE"),
						array("proposta_spot", "PS"),
						array("proposta_nac", "NC"),
				);
	}	

	/**
	 * @depends testInitialConditions
	 * @dataProvider providerNumberTypes	 
	 */
	public function testGerarNumero($tipo_de_proposta, $prefixo)
	{
		
		$numero_gerado = $this->instance->gerarNumero($tipo_de_proposta);
		
		$this->assertNotEmpty($numero_gerado);
		$this->assertNotNull($numero_gerado);
		$this->assertStringStartsWith($prefixo, $numero_gerado);		
		
		$numero_de_caracteres = strlen($numero_gerado);
						
		$this->assertEquals(15, $numero_de_caracteres);
						
	}
	
}//END CLASS

