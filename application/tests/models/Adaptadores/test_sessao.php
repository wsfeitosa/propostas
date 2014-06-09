<?php
class Test_Sessao extends CI_TestCase{
	
	protected $sessao = NULL;

	public function setUp()
	{
		parent::setUp();

		$this->CI->load->model("Adaptadores/sessao");

		$this->sessao = new Sessao();

		$this->assertInstanceOf("Sessao", $this->sessao);

	}

	public function providerSetValues(){

		return Array(
					array(1574, "Chinelos", "PP", "CC"),
					array(1111, "Ancião", "PP", ""),
					array(789, "cação", "", "CC"),
			   );

	}

	/**
	 *@dataProvider providerSetValues
	 */
	public function testSetters( $id_tarifario, $mercadoria, $pp, $cc){

		$this->assertInstanceOf("Sessao", $this->sessao->setIdTarifario($id_tarifario));
		$this->assertEquals($id_tarifario, $this->sessao->getIdTarifario());
		
		$this->assertInstanceOf("Sessao", $this->sessao->setMercadoria($mercadoria));
		$this->assertEquals($mercadoria, $this->sessao->getMercadoria());

		$this->assertInstanceOf("Sessao", $this->sessao->setPp($pp));
		$this->assertEquals($pp, $this->sessao->getPp());

		$this->assertInstanceOf("Sessao", $this->sessao->setCc($cc));
		$this->assertEquals($cc, $this->sessao->getCc());

	}
	
	public function testIncludeSessionItem(){

		$item_index = $this->sessao->inserirItemNaSessao();

		$this->assertNotNull($item_index);

		$this->assertTrue(is_int($item_index));

		$this->assertTrue( isset($_SESSION['itens_proposta'][$item_index]) );

		return $item_index;

	}

	/**
	 *@depends testIncludeSessionItem
	 */
	public function testRecuperarItemDaSessao( $item_index ){

		$itens_recuperados = $this->sessao->recuperarItemDaSessao($item_index);

		$this->assertTrue( is_array($itens_recuperados) );

	}

	public function tearDown(){

		$this->sessao->excluirItemDaSessao();
		
	}

}//END CLASS