<?php
class Test_Portos_Acordos_Model extends CI_TestCase {
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/portos_acordos_Model");
		$this->CI->load->model("Taxas_Locais_Acordadas/portos_acordos_entity");
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		$this->CI->load->model("Tafifario/porto");
	}
	
	public function tearDown()
	{
		parent::tearDown();
		//$this->CI->db->delete("CLIENTES.portos_x_acordos_taxas_globais","id > 0");
	}
		
	public function testAssetPreConditions()
	{
		$this->assertTrue(class_exists("Portos_Acordos_Model"));
		$this->assertTrue(class_exists("Portos_Acordos_Entity"));		
		$this->assertTrue(class_exists("Acordo_Taxas_Entity"));
		$this->assertTrue(class_exists("Porto"));
	}
	
	public function providerTestSave()
	{
		return Array(
						array(1,12),
						array(20,15),
						array(89,98),
						array(91,3),
				);
	}
	
	/**
	 * @depends testAssetPreConditions
	 * @dataProvider providerTestSave
	 */
	public function testSave($id_acordo, $id_porto)
	{
		$porto = new Porto();
		$porto->setId((int)$id_porto);
		
		$acordo = new Acordo_Taxas_Entity();
		$acordo->setId((int)$id_acordo);
		
		$entity = new Portos_Acordos_Entity();
		$entity->setAcordo($acordo);
		$entity->setPorto($porto);
		
		$model = new Portos_Acordos_Model();
		
		$registro_salvo = $model->save($entity);
		
		$this->assertTrue($registro_salvo);
		
		/** Verifica se o registro está mesmo salvo na tabela **/
		$this->CI->db->where("id_acordo", $id_acordo);
		$this->CI->db->where("id_porto", $id_porto);
		$rs = $this->CI->db->get("CLIENTES.portos_x_acordos_taxas_globais");
		
		$this->assertGreaterThanOrEqual(1, $rs->num_rows());
		
		return $id_acordo;
		
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFindByIdShouldNotPass()
	{
		
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		
		$acordo = new Acordo_Taxas_Entity();
		$model = new Portos_Acordos_Model();
		$model->findById($acordo);		
	}
	
	/**
	 * @depends testSave
	 */
	public function testfindById($id_acordo)
	{		
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		
		$acordo = new Acordo_Taxas_Entity();
		$acordo->setId((int)$id_acordo);
		
		$model = new Portos_Acordos_Model();
		
		try{
			$portos_ancontrados = $model->findById($acordo);
			
			$this->assertInstanceOf("ArrayObject", $portos_ancontrados);
			$this->assertContainsOnlyInstancesOf("Porto", $portos_ancontrados);
		} catch (Exception $e) {
			
		}
	}
}//END CLASS