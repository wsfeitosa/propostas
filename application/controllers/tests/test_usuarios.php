<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Testes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @version  1.0
 * Classes de testes dos usuários do sistema .
 */
class Test_Usuarios extends CI_Controller{
	
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
		$this->load->model("Usuarios/vendedor");
		$this->load->model("Usuarios/pricing");
		$this->load->model("Email/email");
	
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
		$this->unit->run($this->vendedor->setId(338),TRUE,"Atribui um id para o usuario");
		$this->unit->run($this->vendedor->getId(),338,"Obtem o id do usuario");
		$this->unit->run($this->vendedor->setId("TESTE"),FALSE,"Testa a atribuição de um id invalido");
	}
	
	public function test_nome()
	{
		
		$user = new Vendedor();
		
		$this->unit->run($user->setNome("PEPE"),TRUE,"Atribui um nome para o usuario");
		$this->unit->run($user->getNome(),"PEPE","Obtem o nome do usuario");
		$this->unit->run($user->setNome(),FALSE,"Testa caso o nome for atribuido vazio");
		
	}
	
	public function test_email()
	{
		
		$email = new Email("wellington.feitosa@allink.com.br");
		
		$user = new Pricing();
		
		$this->unit->run($user->setEmail($email),TRUE,"Atribui um email para o usuário");
		$this->unit->run(get_class($user->getEmail()),get_class($email),"Obtem o email do usuario");
		
	}
	
	public function test_cargo()
	{
		
		$user =  new Vendedor();
		
		$this->unit->run($user->setCargo("PEPE"),TRUE,"Atribui um cargo ao usuário");
		$this->unit->run($user->getCargo(),"PEPE","Obtem o cargo do usuario");
		$this->unit->run($user->setCargo(""),FALSE,"Testa caso o cargo esteja vazio");
		
	}
	
	public function test_filial()
	{
		$user = new Pricing();
		
		$filial = new Filial();
		
		$this->unit->run($user->setFilial($filial),TRUE,"Atribui uma filial ao usuario");
		$this->unit->run(get_class($user->getFilial()),get_class($filial),"Obtem a filial do usuario");
		
	}
	
	public function test_solicitacao()
	{
		$vendedor = new Vendedor();
		
		$solicitacao = new Solicitacao();
		
		$this->unit->run($vendedor->solicitarDesbloqueio($solicitacao),TRUE,"Envia um solicitacao de desbloqueio");
		
	}
	
	public function test_autorizacao()
	{
		$pricing = new Pricing();
		
		$solicitacao = new Solicitacao();
		
		$this->unit->run($pricing->autorizarDesbloqueio(FALSE, $solicitacao),FALSE,"Autoriza desbloqueio");
	}
	
}//END CLASS

class Filial{}
class Solicitacao{ public function solicitarDesbloqueio(){} }