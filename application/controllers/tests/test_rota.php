<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Testes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @version  1.0
 * Classe de testes da Rota.
 */
class Test_Rota extends CI_Controller{
	
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
		include_once APPPATH."/models/Tarifario/rota.php";
		include_once APPPATH."/models/Tarifario/porto.php";
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
	
	public function test_portoOrigem()
	{
		
		$origem = new Porto();
		
		$rota = new Rota();
		
		$this->unit->run($rota->setPortoOrigem($origem),TRUE,"Atribui o porto de origem");		
		$this->unit->run(get_class($rota->getPortoOrigem()),get_class($origem),"Obtem o porto de origem");
		
	}
	
	public function test_portoEmbarque()
	{
		
		$embarque = new Porto();
		
		$rota = new Rota();
		
		$this->unit->run($rota->setPortoEmbarque($embarque),TRUE,"Atribui o porto de embarque");
		$this->unit->run(get_class($rota->getPortoEmbarque()),get_class($embarque),"Obtem o porto de embarque");
		
	}
	
	public function test_portoDesembarque()
	{
	
		$desembarque = new Porto();
	
		$rota = new Rota();
	
		$this->unit->run($rota->setPortoDesembarque($desembarque),TRUE,"Atribui o porto de desembarque");
		$this->unit->run(get_class($rota->getPortoDesembarque()),get_class($desembarque),"Obtem o porto de desembarque");
	
	}
	
	public function test_portoFinal()
	{
	
		$final = new Porto();
	
		$rota = new Rota();
	
		$this->unit->run($rota->setPortoFinal($final),TRUE,"Atribui o porto final");
		$this->unit->run(get_class($rota->getPortoFinal()),get_class($final),"Obtem o porto final");
	
	}
	
}//END CLASS