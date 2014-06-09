<?php
class Test_Buscador_Taxas_Locais extends CI_TestCase{
	
	protected $finder;
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/buscador_taxas_locais");
		$this->finder = new Buscador_Taxas_Locais();
	}
	
	public function testAssertPreConditions()
	{
		
		$this->assertTrue(class_exists("Buscador_Taxas_Locais"));
		$this->assertInstanceOf("Buscador_Taxas_Locais", $this->finder);
		
	}
	
	public function providerFindRates()
	{
		return Array(
						array( 3, "1874:2349:3730:4829:4934:5119:5305:5601:7827:", "IMP" ),
						array( 3, "224:1077:2055:2634:3640:8988:", "IMP" ),
						array( 1, "832:1695:1771:1919:2108:4437:6318:", "EXP" ),
						//array( 0, "832:1695:1771:1919:2108:4437:6318:", "EXP" ), //TODO Criar um teste para quando a opção todos os portos for selecionada
				);
	}
	
	/**
	 * @depends testAssertPreConditions
	 * @dataProvider providerFindRates
	 */
	public function testBuscarTaxasLocais( $porto, $clientes, $sentido )
	{
		
		$taxas_encontradas = $this->finder->buscarTaxasLocais( $porto, $clientes, $sentido );
		
		$this->assertTrue($taxas_encontradas instanceof ArrayObject);
		$this->assertGreaterThan(0, $taxas_encontradas->count());
		$this->assertInstanceOf("Taxa_Local", $taxas_encontradas->offsetGet(0));
		
	}
			
}//END CLASS