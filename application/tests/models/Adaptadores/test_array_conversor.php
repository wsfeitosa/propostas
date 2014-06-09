<?php

class Test_Array_Conversor extends CI_TestCase{

	protected $conversor = NULL;

	public function setUp()
	{
		parent::setUp();

		$this->CI->load->model("Adaptadores/array_conversor");
		$this->CI->load->model("Clientes/cliente");
		$this->CI->load->model("Clientes/cliente_model");

		$this->conversor = new Array_Conversor();

		$this->assertInstanceOf("Array_Conversor", $this->conversor);

	}

	public function providerTestConverter() 
	{ 
		return Array(
					array("allink", array('value'=>'id', 'label'=>'razao')),
					array("panalpina", array('value'=>'id', 'label'=>'razao')),
					array("dhl", array('value'=>'id', 'label'=>'razao')),
			   );	
	}

	/**
	 * @dataProvider providerTestConverter
	 */
	public function testConverter($razao, $parametros)
	{

		$cliente_model = new CLiente_Model();

		$clientes = $cliente_model->findByName($razao);

		$dados_convertidos = $this->conversor->converter($clientes, $parametros);

		$this->assertTrue(is_array($dados_convertidos));

		$this->assertGreaterThan(0, count($dados_convertidos));

	}

}//END CLASS