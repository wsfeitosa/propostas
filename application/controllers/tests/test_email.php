<?php
class Test_email extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
		
		/** O model a ser testado **/
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
	
	public function test_setEmail()
	{
		$this->unit->run($this->email->setEmail("wsfeitosa@gmail.com"),"is_null","Testa a Atribuição de uma email para a classe");
	}
	
	public function test_getEmail()
	{		
		$this->unit->run($this->email->getEmail(), "is_string", "Testa o retorno da função getEmail");
	}
	
	public function test_emailValido()
	{
		
		/** Testa a função que valida os emails **/
		
		/** Email valido **/
		$this->email->setEmail("wellington.feitosa@allink.com.br");
		
		$this->unit->run($this->email->emailValido(),TRUE,'Testa a função que valida os emails', 'Teste com um email valido: wellington.feitosa@allink.com.br');
		
		/** Email Invalido **/
		$this->email->setEmail("wellington.feitosa-allink.com.br");
		
		$this->unit->run($this->email->emailValido(),FALSE,'Testa a função que valida os emails', 'Teste com um email invalido: wellington.feitosa-allink.com.br');

		$this->email->setEmail("k@.com.br");
		
		$this->unit->run($this->email->emailValido(),FALSE,'Testa a função que valida os emails', 'Teste com um email invalido: k@.com.br');
		
	}
	
}//END CLASS