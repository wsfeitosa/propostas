<?php
class Test_Busca_Acordo_Cliente extends CI_TestCase{
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente");
		$this->CI->load->model("Clientes/cliente");
		$this->CI->load->model("Tarifario/porto");
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	public function testAssertPreConditions()
	{
		$this->assertTrue(class_exists("Busca_Acordo_Taxas_Locais_Cliente"));
		$this->assertTrue(class_exists("Cliente"));
		$this->assertTrue(class_exists("Porto"));
	}
	
	public function providerBuscarAcordo()
	{
		return Array(
						array("EXP", 534, 3, date('d-m-y'), date('d-m-y')),
						array("IMP", 534, 3, date('d-m-y'), date('d-m-y')),
						array("EXP", 266, 3, date('d-m-y'), date('d-m-y')),						
				);
	}
	
	/**
	 * @depends testAssertPreConditions
	 * @dataProvider providerBuscarAcordo
	 */
	public function testBuscarAcordo( $sentido, $id_cliente, $id_porto, $data_inicial, $data_final )
	{
		
		$cliente = new Cliente();
		$cliente->setId((int)$id_cliente);
		
		$porto = new Porto();
		$porto->setId((int)$id_porto);
		
		$inicial = new DateTime($data_inicial);
		
		$validade = new DateTime($data_final);
		
		$finder = new Busca_Acordo_Taxas_Locais_Cliente();
		
		$acordo_encontrado = $finder->buscarAcordoTaxasCliente( $sentido, $cliente, $porto, $inicial, $validade );
		
		if( ! $acordo_encontrado )
		{
			$this->assertFalse($acordo_encontrado);
		}	
		else
		{	
			$this->assertInstanceOf("Acordo_Taxas_Entity", $acordo_encontrado);
			
			$clientes_acordo = $acordo_encontrado->getClientes();
			/** Verifica se pelo menos um (e somente um) cliente foi encontrado **/
			$this->assertEquals(1, count($clientes_acordo));
			/** Verifica se o retornado pelo acordo foi uma instancia da classe Cliente **/ 
			$this->assertInstanceOf(Cliente, $clientes_acordo[0]);
			
			/** Verifica se o acordo tem pelo menos uma taxa **/
			$taxas_acordo = $acordo_encontrado->getTaxas();
			$this->assertGreaterThan(0, count($taxas_acordo));
			/** Verifica se são objetos do tipo correto (Taxa) **/
			$this->assertInstanceOf("Taxa", $taxas_acordo[0]);
			
			/** Verifica se o sentido do acordo encontrado é o mesmo do acordo informado **/
			$this->assertEquals($sentido, $acordo_encontrado->getSentido());
			
			/** Verifica se o porto informado está entre os portos encontrados **/
			$portos_acordo = $acordo_encontrado->getPortos();
		}		
	}
	
}//END CLASS