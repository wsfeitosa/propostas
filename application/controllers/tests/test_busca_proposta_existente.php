<?php
class Test_Busca_Proposta_Existente extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Propostas/Buscas/busca_proposta_existente");	
		$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
		$this->load->model("Tarifario/Factory/concrete_importacao_factory");
		$this->load->model("Tarifario/Factory/concrete_factory");
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
	
	public function testVerificarPropostaExistenteParaClienteNaRotaEscolhidaShouldPass()
	{
		
		$finder = new Busca_Proposta_Existente();

		$maker = new Concrete_Importacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$tarifario = $factory->CreateTarifarioObject($maker);
		
		$this->unit->run(get_class($finder),'Busca_Proposta_Existente',"Verifica se o objeto finder criado é do tipo correto");
		
		$this->unit->run($tarifario instanceof Tarifario,TRUE,"Verifica se o objeto criado (Tarifario) é do tipo correto",get_class($tarifario));
		
		$cliente = new Cliente();
		
		$cliente->setId((int)8988);
						
		$tarifario->setId((int)7407);
		
		/*
		 * @bool
		 */
		$cliente_possui_proposta = $finder->verificarSeClienteJaPossuiPropostaValida( $cliente, $tarifario );
		
		$this->unit->run($cliente_possui_proposta,TRUE,"Verifica se o cliente já possui proposta valida cadastrada na rota selecionada");
		
		$cliente->setId((int)4788);
		
		$tarifario->setId((int)149);
		
		/*
		 * @bool
		*/
		$cliente_possui_proposta = $finder->verificarSeClienteJaPossuiPropostaValida( $cliente, $tarifario );		
		
		$this->unit->run($cliente_possui_proposta,FALSE,"Verifica se o cliente já possui proposta valida cadastrada na rota selecionada","Deve retornar um false, pois não existe esse registro");
		
	}
    
    public function testRetornaPropostaExistenteIdShouldPass()
    {
        
        $finder = new Busca_Proposta_Existente();

		$maker = new Concrete_Importacao_Factory();
		
		$factory = new Concrete_Factory();
		
		$tarifario = $factory->CreateTarifarioObject($maker);
		
		$this->unit->run(get_class($finder),'Busca_Proposta_Existente',"Verifica se o objeto finder criado é do tipo correto");
		
		$this->unit->run($tarifario instanceof Tarifario,TRUE,"Verifica se o objeto criado (Tarifario) é do tipo correto",get_class($tarifario));
		
		$cliente = new Cliente();
		
		$cliente->setId((int)8988);
						
		$tarifario->setId((int)7407);
        
        $id_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $tarifario);
               
        $this->unit->run($id_proposta,27,"Verifica se existe uma proposta valida e retorna o ID","Id Proposta: ".$id_proposta);
        
        /** Este Teste verifica se no caso de não existir uma proposta existente, deveria retorna um false **/
        $cliente->setId((int)123);
						
		$tarifario->setId((int)456);
        
        $id_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $tarifario);
        
        $this->unit->run($id_proposta,FALSE,"Verifica se existe uma proposta valida e retorna o ID","Deveria retornar um FALSE, pois estou testando em caso de falha");
                
    }        
	
}//END CLASS