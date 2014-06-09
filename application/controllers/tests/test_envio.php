<?php
class Test_Envio extends CI_Controller{

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
		
		include_once APPPATH."/models/Email/email.php";
		
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
	
	public function test_adionarEmail()
	{
		
		$this->email->setEmail("wellington.feitosa@allink.com.br");
		
		$this->unit->run($this->envio->adicionarNovoEmail($this->email),intval("is_integer"),"Testa a adição de um novo email para envio");	
		
	}
	
	public function test_removerEmail()
	{
		
		$index = $this->envio->adicionarNovoEmail($this->email);
		
		$quantidadeEmails = $this->envio->obterQuantidadeEmails();
						
		$this->envio->removerEmail($index);
				
		$this->unit->run($this->envio->obterQuantidadeEmails(), ($quantidadeEmails - 1), "Testa de remoção de emails da classe de envio");
		
	}
	
	public function test_envioMensagem()
	{
		
		$corpo_mensagem = "<html>
								<head></head>
								<body>
									Se vc recebeu essa Mensagem é por que IU!
								</body>
						   </html>";
				
		$this->envio->adicionarNovoEmail(new Email("kleber.miliani@allink.com.br"));
		
		$this->envio->adicionarNovoEmail(new Email("fabio.shimada@allink.com.br"));
		
		$this->envio->adicionarNovoEmail(new Email("rafael.silverio@allink.com.br"));
						
		$this->unit->run($this->envio->enviarMensagem($corpo_mensagem, "Proposta Scoa ".date("d/m/Y H:i:s")),TRUE,"Testa o envio das mensagems do módulo");
	}
	
}//END CLASS
