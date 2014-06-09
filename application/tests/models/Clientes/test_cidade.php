<?php
class Test_Cidade extends CI_TestCase{

	protected $cidade = NULL;

	public function setUp(){

		parent::setUp();

		$this->CI->load->model("Clientes/cidade");

		$this->cidade = new Cidade();

		$this->assertInstanceOf("Cidade", $this->cidade);
			
	}

	public function ProviderTestGettersAndSetters(){
		return Array(
					array(1119, "Cidade"),
					array(1118, "São Paulo"),
					array(1117, "Rio de Janeiro"),
					array(1116, "Curitiba")
			   );
	}

	/**
	 *@dataProvider ProviderTestGettersAndSetters 	 
	 */
	public function testGettersAndSetters( $id, $nome ){

		$this->assertTrue($this->cidade->setId((int)$id));
		$this->assertEquals($id, $this->cidade->getId());

		$this->assertTrue($this->cidade->setNome($nome));
		$this->assertEquals($nome, $this->cidade->getNome());

	}
	
	public function ProviderTestFindById(){
		return Array(
					array(1119, TRUE),
					array(1118, TRUE),
					array(1117, TRUE),
					array(1116, TRUE),
					array(1, FALSE),
			   );	
	} 

	/**
	 *@dataProvider ProviderTestFindById
	 */
	public function testFindById( $id, $retorno_esperado ){

		$this->cidade->setId((int)$id);
		$this->cidade->setNome(NULL);

		$retorno_real = $this->cidade->findById();

		$this->assertEquals($retorno_esperado, $retorno_real);

		if( $retorno_real === TRUE )
		{	
			$this->assertNotNull($this->cidade->getNome());
		}
		else
		{
			$this->assertNull($this->cidade->getNome());
		}	

	}

}//END CLASS