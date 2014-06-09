<?php
class Test_Porto_Taxas extends CI_TestCase{
	
	protected $object;
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/portos_taxas");
		$this->object = new Portos_Taxas();
	}
	
	public function testObterPortosDasTaxasLocais()
	{
		
		$portos_encontrados = $this->object->obterPortosDasTaxasLocais();
		
		$this->assertTrue(is_array($portos_encontrados));
		$this->assertGreaterThan(0, count($portos_encontrados));
			
	}
	
}//END CLASS