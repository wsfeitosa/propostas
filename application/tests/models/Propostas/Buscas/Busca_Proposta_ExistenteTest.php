<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-05-08 at 14:45:18.
 */
class Busca_Proposta_ExistenteTest extends CI_TestCase {

    /**
     * @var Busca_Proposta_Existente
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->CI->load->model("Propostas/Buscas/busca_proposta_existente");
        $this->CI->load->model("Propostas/item_proposta");
        $this->CI->load->model("Propostas/item_proposta_model");
        $this->CI->load->model("Tarifario/Factory/concrete_factory");
        $this->CI->load->model("Tarifario/Factory/concrete_importacao_factory");
        $this->CI->load->model("Tarifario/Factory/concrete_exportacao_factory");
        $this->CI->load->model("Propostas/proposta_cotacao"); 
        $this->object = new Busca_Proposta_Existente();
    }
    
    public function testAssertPreConditions()
    {
    	$this->assertTrue(class_exists("Busca_Proposta_Existente"));
    	$this->assertTrue(class_exists("Item_Proposta"));
    	$this->assertTrue(class_exists("Item_Proposta_Model"));
    	$this->assertTrue(class_exists("Concrete_Factory"));
    	$this->assertTrue(class_exists("Concrete_Importacao_Factory"));
    	$this->assertTrue(class_exists("Concrete_Exportacao_Factory"));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        
    }
            
    /**
     * @covers Busca_Proposta_Existente::verificarSeClienteJaPossuiPropostaValida
     * @todo   Implement testVerificarSeClienteJaPossuiPropostaValida().
     */
    /**
    public function testVerificarSeClienteJaPossuiPropostaValida()
    {
                
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        
    }
    **/
    /**
     * @covers Busca_Proposta_Existente::verificarSeClienteJaPossuiPropostaValidaERetornaId
     * @todo   Implement testVerificarSeClienteJaPossuiPropostaValidaERetornaId().
     */
    /**
    public function testVerificarSeClienteJaPossuiPropostaValidaERetornaId()
    {        
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    **/
    /**
    public function testbuscaPorItensDuplicadosDeUmaNovaProposta()
    {
        
    	$this->markTestIncomplete(
    			'This test has not been implemented yet.'
    	);
    	
        $proposta = new Proposta_Cotacao();
        
        $cliente = new Cliente();
        $cliente->setId(244);
        
        $proposta->adicionarNovoCliente($cliente);
        
        $factory = new Concrete_Factory();
        
        $item = new Item_Proposta();
        
        $proposta->adicionarNovoItem($item);
        
        $itens_duplicados = $this->object->buscaPorItensDuplicadosDeUmaNovaProposta($proposta);
        
    }    
    **/
}
