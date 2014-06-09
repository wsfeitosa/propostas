<?php
class Test_Cliente extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Propostas/proposta_tarifario", "proposta", TRUE);
	
		$this->load->model("Email/email");
		$this->load->model("Clientes/cliente");
			
	}
	
	/** Função que vai executar todos os testes **/
	public function index()
	{
		try {
				
			/** Testes à serem rodados **/
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
		
		$cliente = New Cliente();
		
		$this->unit->run($cliente->setId(12),TRUE,"Atribui um id para o cliente");
		$this->unit->run($cliente->getId(),12,"Obtem o id do cliente");
		$this->unit->run($cliente->setId("TECO"),FALSE,"Testa a atribuição de um id invalido");
		
	}
	
	public function testRazao()
	{
		
		$cliente = new Cliente();
		
		$this->unit->run($cliente->setRazao("TECO"),TRUE,"Atribui uma razão para o cliente");
		$this->unit->run($cliente->getRazao(),"TECO","Obtem a razão do cliente");
		$this->unit->run($cliente->setRazao(12),TRUE,"Testa a atribuição de uma razão invalida");
		$this->unit->run($cliente->getRazao(),"12","Testa a obtenção da razão quando um número foi informado");
				
	}
	
	public function test_cnpj()
	{
		
		$cliente = New Cliente();
		
		$this->unit->run($cliente->setCNPJ("334.264.168-16"),TRUE,"Atribui um novo CNPJ");
		
		$this->unit->run($cliente->getCNPJ(),'33426416816',"Obtem o cnpj do cliente");
		
	}
	
	public function test_email()
	{
		
		$cliente = new Cliente();
		
		$email = new Email("wsfeitosa@gmail.com");
		
		$this->unit->run($cliente->setEmail($email),TRUE,"Atribuição de um email");
		$this->unit->run(get_class($cliente->getEmail()),get_class($email),"testa a obtenção de um email");
	}
	
}//END CLASS