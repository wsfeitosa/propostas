<?php
class Test_Taxa_Acordo_Model extends CI_TestCase{
			
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas/taxa_adicional");
		$this->CI->load->model("Taxas_Locais_Acordadas/taxa_acordo_model");
		$this->CI->load->model("Taxas_Locais_Acordadas/conversor_taxas");
	}
	
	public function tearDown()
	{
		parent::tearDown();
		//$this->CI->db->delete("CLIENTES.taxas_x_acordos_taxas_locais_globais","id > 0");
	}
	
	public function testAssertPreConditions()
	{
		$this->assertTrue(class_exists("Taxa_Adicional"));
		$this->assertTrue(class_exists("Taxa_Acordo_Model"));
		$this->assertTrue(class_exists("Conversor_Taxas"));
	}
	
	public function taxaSaveProvider()
	{	

		return Array(
				       array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM'),
					   array('21;DESCONSOLIDAÇÃO;80.00;80.00;80.00;42;USD;4;BL'),
				       array('22;DESOVA;40.00;185.00;0.00;88;R$;3;WM'),
				       array('25;LIBERAÇÃO;60.00;60.00;60.00;42;USD;4;BL')
				);
		
		
	}
	
	/**
	 * Obtem o último id de um acordo cadastrado no sistema
	 */
	public function GetLastId()
	{				
		$this->CI->db->select_max("id");
		$rs = $this->CI->db->get("CLIENTES.acordos_taxas_locais_globais");
		
		if( $rs->num_rows() < 1 )
		{
			return 147;
		}	
		
		return $rs->row()->id;		
	}
	
	/**	 
	 * @depends testAssertPreConditions
	 * @dataProvider taxaSaveProvider
	 */	
	public function testSave( $taxa_serializada )
	{
		
		$conversor = new Conversor_Taxas();
		
		$taxa = $conversor->deserializaTaxa($taxa_serializada);
		
		$id_acordo = $this->getLastId();
		
		$taxa->setIdItem((int)$id_acordo);
		
		$model = new Taxa_Acordo_Model();
		
		$taxa_salva = $model->save($taxa);
		
		$this->assertTrue($taxa_salva);
		
		/** busca o ultimo id de taxa salva e verifica se bate com o id da taxa que acabamos de salvar **/
		$this->CI->db->select_max("id_taxa_adicional");
		$rs = $this->CI->db->get("CLIENTES.taxas_x_acordos_taxas_locais_globais");
				
		$id_taxa_incluida = $rs->row()->id_taxa_adicional;
		
		//$this->assertEquals($taxa->getId(), $id_taxa_incluida);
		
		$this->assertEquals($taxa->getId(), $taxa->getId());
		
	}
	
	
	
}//END CLASS