<?php
class Test_Clientes_Acordos_Taxas_Model extends CI_TestCase{
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/clientes_acordos_taxas_model");
		$this->CI->load->model("Taxas_Locais_Acordadas/cliente_acordo_entity");
	}
	
	public function tearDown()
	{
		parent::tearDown();
		//$this->CI->db->delete("CLIENTES.clientes_x_acordos_taxas_locais_globais","id > 0");
	}
	
	public function testAssertPreConditions()
	{
		$this->assertTrue(class_exists("Clientes_Acordos_Taxas_model"));
		$this->assertTrue(class_exists("Cliente_Acordo_Entity"));
	}
	
	public function providerTestSave()
	{
		return Array(
						array(1,2),
						array(3,4),
						array(5,6),
						array(7,8),
				);
	}
	
	/**
	 * @depends testAssertPreConditions
	 * @dataProvider providerTestSave
	 */	
	public function testSave( $id_cliente, $id_acordo )
	{
		
		$entity = new Cliente_Acordo_Entity();
		$entity->setIdCliente($id_cliente);
		$entity->setIdAcordo($id_acordo);
		
		$dataBaseModel = new Clientes_Acordos_Taxas_Model();
		
		$entity_saved = $dataBaseModel->save($entity);
		
		$this->assertTrue($entity_saved);
		
		/** Verifica se o registro foi mesmo salvo na tabela **/
		$this->CI->db->where("id_cliente",$entity->getIdCliente());
		$this->CI->db->where("id_acordos_taxas_locais",$entity->getIdAcordo());
		$rs = $this->CI->db->get("CLIENTES.clientes_x_acordos_taxas_locais_globais",1);
		
		$this->assertNotNull($rs->num_rows());
		$this->assertGreaterThanOrEqual(1, $rs->num_rows());
		
	}
	
	/**
	 *@expectedException InvalidArgumentException 
	 */
	public function testFindByIdShouldNotPass()
	{
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		
		$acordo_entity = new Acordo_Taxas_Entity();
		
		$cliente_acordo_model = new Clientes_Acordos_Taxas_Model();
		
		$clientes_encontrados = $cliente_acordo_model->findById($acordo_entity);
	}
	
	/**
	 * @depends testSave
	 * @dataProvider providerTestSave
	 */
	public function testFindById( $id_cliente, $id_acordo )
	{
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		
		$acordo_entity = new Acordo_Taxas_Entity();
		$acordo_entity->setId((int)$id_acordo);
		
		$cliente_acordo_model = new Clientes_Acordos_Taxas_Model();
		
		$clientes_encontrados = $cliente_acordo_model->findById($acordo_entity);
		
		$this->assertInstanceOf("ArrayObject", $clientes_encontrados);
				
		$this->assertGreaterThan(0, $clientes_encontrados->count());
		
		$this->assertContainsOnlyInstancesOf("Cliente_Acordo_Entity", $clientes_encontrados);
		
	}
	
}//END CLASS