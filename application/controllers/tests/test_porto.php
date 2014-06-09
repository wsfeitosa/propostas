<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Testes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @version  1.0
 * Classes de testes dos portos.
 */
class Test_Porto extends CI_Controller{
	
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
		$this->unit->run($this->porto->setId(2),TRUE,"Atribui um id para o porto");
		$this->unit->run($this->porto->getId(),2,"Obtem o id do porto");
		$this->unit->run($this->porto->setId("TESTE"),FALSE,"Testa a atribuição em caso de falha");
		$this->unit->run($this->porto->setId(0),FALSE,"Testa a atribuição em caso de falha");
	}
	
	public function test_nome()
	{
		$this->unit->run($this->porto->setNome("santos"),TRUE,"Atribui um nome para o porto");
		$this->unit->run($this->porto->setNome(),FALSE,"Testa a atribuição em caso de falha");
		$this->unit->run($this->porto->getNome(),"SANTOS","Obtem o nome do porto");
	}
	
	public function test_uncode()
	{		
		$this->unit->run($this->porto->setUnCode(),FALSE,"Testa a atribuição em caso de falha");
		$this->unit->run($this->porto->setUnCode("brssz"),TRUE,"Atribui um uncode para o porto");
		$this->unit->run($this->porto->getUnCode(),"BRSSZ","Obtem o nome do porto");				
	}
	
}//END CLASS