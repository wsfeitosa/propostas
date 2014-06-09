<?php
class Test_Compara_Taxas extends CI_TestCase{
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas/compara_taxas");
		$this->CI->load->model("Taxas/taxa_local");
		$this->CI->load->model("Taxas/taxa_adicional");
		$this->CI->load->model("Taxas_Locais_Acordadas/conversor_taxas");
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	public function testAssertPreConditions()
	{
		$this->assertTrue(class_exists("Compara_Taxas"));
		$this->assertTrue(class_exists("Taxa_Local"));
		$this->assertTrue(class_exists("Taxa_Adicional"));
		$this->assertTrue(class_exists("Conversor_Taxas"));
	}
	
	public function providerTaxas()
	{
		
		return Array(
						 Array(
						 		Array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM',
						 			   '21;DESCONSOLIDA플O;80.00;80.00;80.00;42;USD;4;BL',
						 			   '22;DESOVA;40.00;185.00;0.00;88;R$;3;WM',
						 			   '25;LIBERA플O;60.00;60.00;60.00;42;USD;4;BL'),
						 		
						 		Array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM',
									   '21;DESCONSOLIDA플O;80.00;80.00;80.00;42;USD;4;BL',
									   '22;DESOVA;35.00;185.00;0.00;88;R$;3;WM',
									   '25;LIBERA플O;59.00;60.00;60.00;42;USD;4;BL'),
						 ),		
				 		 Array(
				 				Array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM',
								 	   '21;DESCONSOLIDA플O;80.00;80.00;80.00;42;USD;4;BL',
								 	   '22;DESOVA;40.00;185.00;0.00;88;R$;3;WM',
								 	   '25;LIBERA플O;60.00;60.00;60.00;42;USD;4;BL'),
				 		
				 				Array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM',
									   '21;DESCONSOLIDA플O;80.00;80.00;80.00;42;USD;4;BL',
									   '22;DESOVA;40.00;185.00;0.00;88;R$;3;WM'),
				 		 ),		
		);
						
	}
	
	/**
	 * @depends testAssertPreConditions
	 * @dataProvider providerTaxas
	 */
	public function testCompararTaxas( $taxas_originais, $taxas_comparacao )
	{

		/** Converte os valores em taxas para realizar a compara豫o **/
		/** Deserializa e cria os objetos do tipo Taxa_Adicional **/		
		$conversor = new Conversor_Taxas();

		$taxas_originais_serializadas = Array();
		$taxas_comparacao_serializadas = Array();		
		
		foreach( $taxas_originais as $taxa_original )
		{				
			$taxa = $conversor->deserializaTaxa($taxa_original);
			
			array_push($taxas_originais_serializadas, $taxa);
		}
		
		foreach( $taxas_comparacao as $taxa_comparacao )
		{
			$taxa = $conversor->deserializaTaxa($taxa_comparacao);
				
			array_push($taxas_comparacao_serializadas, $taxa);
		}	
					
		$comparador = new Compara_Taxas($taxas_originais_serializadas, $taxas_comparacao_serializadas);
		
		$resultado = $comparador->comparar_taxas();
						
		$this->assertTrue(is_array($resultado));
		$this->assertGreaterThan(0,$resultado);
		$this->assertContainsOnlyInstancesOf("Taxa_Adicional", $resultado);
		
	}
	
}//END CLASS