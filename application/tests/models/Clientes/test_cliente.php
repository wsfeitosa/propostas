<?php
class Test_Cliente extends CI_TestCase{

	protected $cliente = NULL;

	public function setUp(){
		parent::setUp();

		$this->CI->load->model("Clientes/cliente");
		$this->CI->load->model("Clientes/contato");
		$this->cliente = new Cliente();
		$this->assertInstanceOf("Cliente", $this->cliente);		
	}

	public function testSettersAndGetters()
	{

		$contato = new Contato();
		$this->assertTrue($this->cliente->setContatos($contato));

		$contatos = $this->cliente->getContatos();

		$this->assertTrue(is_array($contatos));

		foreach( $contatos as $contato )
		{	
			$this->assertInstanceOf("Contato", $contato);
		}
	}

}//END CLASS	