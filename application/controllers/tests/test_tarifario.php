<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Testes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @version  1.0
 * Classes de testes do tarifarios de importação e exportação.
 */

class Test_Tarifario extends CI_Controller{

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
		include_once APPPATH."/models/Email/email.php";
		include_once APPPATH."/models/Propostas/item_proposta.php";
		include_once APPPATH."/models/Propostas/status_item.php";
		include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
		include_once APPPATH."/models/Tarifario/rota.php";
		include_once APPPATH."/models/Taxas/taxa_adicional.php";
		include_once APPPATH."/models/Taxas/frete.php";
		
		$this->load->model("Tarifario/tarifario_model");
		$this->load->model("Tarifario/porto");
	
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
	
	public function test_id()
	{
		
		$tarifario = new Tarifario_Exportacao(new Rota);
		
		$this->unit->run($tarifario->setId(12),TRUE,"Teste de atruição de um ID");
		$this->unit->run($tarifario->getId(),12,"Teste de otenção do id do tarifario");
		$this->unit->run($tarifario->setId("TESTE"),FALSE,"Teste a falha na hora de atribuir um id ao tarifario");
		
	}
	
	public function test_inicio()
	{
		
		$tarifario = new Tarifario_Exportacao(new Rota);
		
		$this->unit->run($tarifario->setInicio(date('Y-m-d')),TRUE,"Atribui uma nova data de inicio ao tarifario");
		$this->unit->run($tarifario->getInicio(),date('Y-m-d'),"Obtem a data de inicio do tarifario");
		
	}
	
	public function test_validade()
	{
		
		$tarifario = new Tarifario_Exportacao(new Rota);
		
		$this->unit->run($tarifario->setValidade(New DateTime()),TRUE,"Atribui uma validade ao tarifario");
		$this->unit->run(get_class($tarifario->getValidade()),get_class(New DateTime()),"Obtem a data de Validade do tarifario");
		
	}
	
	public function test_rota()
	{
		
		$tarifario = new Tarifario_Exportacao(new Rota);
		
		$this->unit->run($tarifario->setRota(new Rota()), TRUE, "Atribui um valor a rota");
		$this->unit->run(get_class($tarifario->getRota()),get_class(new Rota()),"Obtem o valor da rota atual");
		
	}
	
	public function test_taxas()
	{
		
		$tarifario = new Tarifario_Exportacao(new Rota);
		
		$index = $tarifario->adicionarNovaTaxa(new Taxa_Adicional());
		
		$this->unit->run($index,0,"Testa a adição de uma nova taxa");
		$this->unit->run($tarifario->removerTaxa($index),TRUE,"Remove uma taxa do tarifario");
		$this->unit->run($tarifario->removerTaxa(1000),FALSE,"Testa a exclusão de uma taxa que não existe");
		$this->unit->run($tarifario->removerTaxa("TECO"),FALSE,"Testa caso seja informado um valor não inteiro");
		
	}
	
	public function test_puxar_tarifario()
	{
		$tarifario_model = new Tarifario_Model();
		pr($tarifario_model);
	}
	
}//END CLASS