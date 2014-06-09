<?php
class Test_Portos_Acordos_Entity extends CI_TestCase {
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/portos_acordos_entity");
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		$this->CI->load->model("Tarifario/porto");
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	public function testAssetPreConditions()
	{
		$this->assertTrue(class_exists("Portos_Acordos_Entity"));
		$this->assertTrue(class_exists("Porto"));
		$this->assertTrue(class_exists("Acordo_Taxas_Entity"));
	}
	
	/**
	 * @depends testAssetPreConditions
	 */
	public function testEntitySetters()
	{
		$porto = new Porto();
		$acordo = new Acordo_Taxas_Entity();
		$entity = new Portos_Acordos_Entity();
		
		$porto->setId((int)12);
		$acordo->setId((int)1);
		
		$entity->setPorto($porto);
		
		$this->assertTrue($entity->getPorto() instanceof Porto);
		$this->assertEquals(12, $entity->getPorto()->getId());
		
		$entity->setAcordo( $acordo );
		
		$this->assertTrue($entity->getAcordo() instanceof Acordo_Taxas_Entity);
		$this->assertEquals(1, $entity->getAcordo()->getId());
		
	}
	
}//END CLASS