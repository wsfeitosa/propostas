<?php
class Test_Acordo_Taxas_Locais_Model extends CI_TestCase{
	
	public function setUp()
	{
		parent::setUp();		
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		$_SESSION['matriz'][1] = "SP";
		$_SESSION['matriz'][7] = 147;
	}
	
	public function tearDown()
	{
		parent::tearDown();
		//unset($_SESSION['matriz']);		
	}
	
	public function testAssertPreConditionsShouldPass()
	{		
		$this->assertTrue(class_exists("Acordo_Taxas_Locais_Model"));
		$this->assertTrue(class_exists("Acordo_Taxas_Entity"));
	}
			
	public function providerFindByIdPass()
	{
		return Array(
						array(1),
						array(2),
						array(3),
				);
	}
	
	/**
	 *@depends testAssertPreConditionsShouldPass
	 *@dataProvider providerFindByIdPass
	 */
	public function testFindByIdShouldPass( $id_acordo )
	{
		
		$entity = new Acordo_Taxas_Entity();
		$entity->setId((int)$id_acordo);
		
		$model = new Acordo_Taxas_Locais_Model();
		
		$model->findById($entity);
		
		$this->assertEquals($id_acordo, $entity->getId());		
		$this->assertInstanceOf("DateTime", $entity->getInicio());
		$this->assertInstanceOf("DateTime", $entity->getValidade());
		$this->assertEquals(3, strlen($entity->getSentido()));
		$this->assertNotNull($entity->getNumero());
		$this->assertEquals(1, strlen($entity->getRegistroAtivo()));
		$this->assertInstanceOf("Usuario", $entity->getUsuarioInclusao());
		$this->assertInstanceOf("DateTime", $entity->getDataInclusao());
		
	}
	
	/**
	 * @depends testAssertPreConditionsShouldPass
	 * @expectedException InvalidArgumentException
	 */
	public function testFindByIdShouldNotPass()
	{						
		$entity = new Acordo_Taxas_Entity();
				
		$model = new Acordo_Taxas_Locais_Model();
		
		$model->findById($entity);
	}
}//END CLASS