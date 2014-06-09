<?php
class Test_Propostas extends CI_Controller {
	
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
		
		include_once APPPATH."/models/Propostas/item_proposta.php";
		include_once APPPATH."/models/Propostas/status_item.php";
		include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
		
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
	
	public function test_adicionarItem()
	{			
		$this->unit->run($this->proposta->adicionarNovoItem(new Item_Proposta(new Tarifario_Exportacao())),'is_integer','Teste para adicionar novo Item');
	}
	
	public function test_editarItem()
	{
		
		/** Novo item para a proposta **/
		$item = new Item_Proposta(new Tarifario_Exportacao());
		
		/** Adiciona o item a proposta **/
		$this->proposta->adicionarNovoItem($item);
										
		$this->unit->run($this->proposta->editarItem(0),'is_object','Teste para editar um item da proposta');	
	}
	
	public function test_removerItem()
	{
		
		/** Quantidade de itens antes da exclusão **/
		$index = $this->proposta->adicionarNovoItem(new Item_Proposta(new Tarifario_Exportacao()));
		
		$quantidadeItens = $this->proposta->obterQuantidadeItens();

		$this->proposta->removerItem($index);
		
		$this->unit->run($this->proposta->obterQuantidadeItens(), ( $quantidadeItens - 1 ), 'Testa a exclusão de itens da proposta');
		
	}
	
	public function test_adicionarCliente()
	{
		$this->unit->run($this->proposta->adicionarNovoCliente(new Cliente),'is_integer','Teste para adicionar um novo cliente');
	}

	public function test_removerCliente()
	{	
		/** Quantidade de cliente antes da exclusão **/
		$index = $this->proposta->adicionarNovoCliente(new Cliente);
		
		$quantidadeClientes = $this->proposta->obterQuantidadeClientes();		
		
		$this->proposta->removerCliente($index);
		
		$this->unit->run($this->proposta->obterQuantidadeClientes(), ($quantidadeClientes - 1),'Teste de exclusão um cliente');
		
	}	
	
	public function test_adicionarEmail()
	{
		$this->unit->run($this->proposta->adicionarNovoEmail($this->email),'is_integer','Teste para adicionar um novo email a proposta');
	}
	
	public function test_removerEmail()
	{
		/** Quantidade de emails antes da exclusão **/
		$index = $this->proposta->adicionarNovoEmail($this->email);
		
		$quantidadeEmails = $this->proposta->obterQuantidadeEmails();
		
		$this->proposta->removerEmail($index);
				
		$this->unit->run($this->proposta->obterQuantidadeEmails(), ($quantidadeEmails - 1), 'Testes exclusão de um email da proposta');
		
	}
	
	public function test_setSentido()
	{
		$this->unit->run($this->proposta->setSentido('IMP'),'is_bool','Atribui um sentido (imp ou exp) a proposta');
	}
	
	public function test_getSentido()
	{
		$this->unit->run($this->proposta->getSentido(), 'is_string', 'Testa se o sentido da classe proposta é do tipo correto');
	}
	
}//END CLASS