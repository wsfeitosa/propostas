<?php
include "/var/www/html/allink/Conexao/conecta.inc";

class Test_Gera_Numero_Acordo extends CI_TestCase{
	
	protected $object;
	protected $numero_gerado;
	protected $conn;
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/gera_numero_acordo");
		$this->object = new Gera_Numero_Acordo();
		$_SESSION['matriz'][1] = "SP";
		$this->conn = Zend_Conn();
	}
	
	public function tearDown()
	{
		parent::tearDown();
		//unset($_SESSION['matriz']);
		//$this->conn->query("DELETE FROM CLIENTES.acordos_taxas_locais_globais WHERE numero = '".$this->numero_gerado."'");
	}	
	
	public function testGerarPrimeiroNumeroAcordoShouldPass()
	{
		$numero_acordo = $this->object->gerarNumeroAcordo();
		
		$this->assertEquals(13, strlen($numero_acordo));
		
		$this->assertTrue(is_string($numero_acordo));
		
		$sigla_acordo = substr($numero_acordo, 0, 2);
		
		$this->assertEquals("TX", $sigla_acordo);
		
		/** Insere o numero do acordo na tabela para realizar teste da função seguinte **/
		$sql = "INSERT 
					INTO CLIENTES.acordos_taxas_locais_globais
				SET
					numero = '".$numero_acordo."',
					sentido = 'IMP',
					observacoes_internas = 'TESTE',
					data_inicial = '".date('Y-m-d')."',
					validade = '".date('Y-m-d')."',
					registro_ativo = 'S'";
		
		$this->conn->query($sql);
		
		$this->numero_gerado = $numero_acordo;
		
		return $numero_acordo;
		
	}
	
	/**
	 * @depends testGerarPrimeiroNumeroAcordoShouldPass
	 */
	public function testGerarNumeroSegundoAcordoShouldPass($numero_primeiro_acordo)
	{
		$numero = $this->object->gerarNumeroAcordo();
		
		$this->assertEquals(13, strlen($numero));
		
		$this->assertTrue(is_string($numero));
		
		$sigla_acordo = substr($numero, 0, 2);
		
		$this->assertEquals("TX", $sigla_acordo);
		
		$this->assertNotEquals($numero_primeiro_acordo, $numero);					
	}
	
}//END CLASS