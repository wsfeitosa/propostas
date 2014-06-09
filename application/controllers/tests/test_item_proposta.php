<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  Testes
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 14/01/2013
* @version  1.0
* Classes de testes dos itens da proposta
*/
 

class Test_Item_Proposta extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->use_strict(TRUE);
		$this->unit->active(TRUE);
	
		/** Carrega a library de debug **/
		$this->output->enable_profiler(TRUE);
	
		/** O models a serem testados **/
		$this->load->model("Email/email");
		$this->load->model("Email/envio");
		$this->load->model("Propostas/proposta_cotacao","proposta");
			
		include_once APPPATH."/models/Email/email.php";
		include_once APPPATH."/models/Propostas/item_proposta.php";
        include_once APPPATH."/models/Propostas/item_proposta_model.php";
		include_once APPPATH."/models/Propostas/status_item.php";
		include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
	
	}

	public function index()
	{
		try{
	
			foreach (get_class_methods($this) as $method)
			{
				if( strpos($method, "test") !== FALSE )
				{
					$this->$method();
				}
	
			}
	
		} catch (Exception $e) {
			show_error($e->getMessage());
		}
		echo $this->unit->report();
	
	}
	
	public function test_dataInicio()
	{
		$item = new Item_Proposta(new Tarifario_Exportacao());
				
		$this->unit->run($item->setInicio(date("Y-m-d")),TRUE,"Testa a atribuição de uma data da inicio");
		
		$this->unit->run($item->getInicio(),date("Y-m-d"),"Testa o retorno do valor da data inicial");
	}
	
	public function test_validade()
	{
		$item = new Item_Proposta(new Tarifario_Exportacao());
		
		$this->unit->run($item->setValidade(date("Y-m-d")),TRUE,"Testa a atribuição de uma data de validade");
		
		$this->unit->run($item->getValidade(),date("Y-m-d"),"Testa o retorno do valor da data de validade");
	}
	
	public function test_status()
	{
		$status = new Status_Item();
		
		$item = new Item_Proposta(new Tarifario_Exportacao());
		
		$this->unit->run($item->setStatus($status), TRUE, "Testa a atribuição de um novo status");
				
		$this->unit->run(get_class($item->getStatus()), "Status_Item", "Testa se o retorno é um objeto do tipo StatusItem");
	}
    /**
    public function testGerarNumeracaoDeveriaPassar()
    {
        
        $proposta = new Proposta_Cotacao();
        
        $proposta->setNumero("PP0313SP0004200");
        
        $tarifario = new Tarifario_Exportacao();
        
        $item = new Item_Proposta($tarifario);
        
        $item_model = new Item_Proposta_Model();
        
        $numero_gerado = $item_model->verificaUltimoNumeroDeItemGerado($proposta);
        
        
        $this->unit->run($numero_gerado, 'is_string', "Testa o tipo de número gerado para o item", "O numero gerado foi: ".$numero_gerado);
        
        
        $this->unit->run(strlen($numero_gerado), 15, "Testa o comprimento do número gerado", "O numero gerado foi: ".$numero_gerado);
            
    }
    **/ 
    
    public function testGerarNumeracaoDoItem()
    {
        
        $proposta = new Proposta_Cotacao();
        
        $proposta->setNumero("PP0313SP0004200");
        
        $tarifario = new Tarifario_Exportacao();
        
        $item = new Item_Proposta($tarifario);
        
        $item_model = new Item_Proposta_Model();
        
        //$numero_gerado = $item_model->geraNovoNumeroDeItemDeProposta($proposta);
        
        //$this->unit->run($numero_gerado, 'is_string', "Testa o tipo de número gerado para o item", "O numero gerado foi: ".$numero_gerado);        
        
        //$this->unit->run(strlen($numero_gerado), 15, "Testa o comprimento do número gerado", "O numero gerado foi: ".$numero_gerado);
        
    }        
		
}//END CLASS
