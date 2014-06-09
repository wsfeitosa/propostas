<?php

class Test_Model_Proposta_Cotacao extends CI_Controller {
    
    public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Propostas/proposta_cotacao", "proposta", TRUE);
        $this->load->model("Propostas/proposta_model");
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
        
    public function testConsultaTarifarioPorIdDeveriaPassar() 
    {
        /** Id existente de uma proposta **/
        $id_proposta = 32;
        
        $proposta = new Proposta_Cotacao();
        
        $proposta->setId($id_proposta);
        
        $proposta_model = new Proposta_Model();
        
        $retorno_funcao = $proposta_model->buscarPropostaPorId($proposta);
        
        $this->unit->run(get_class($retorno_funcao), get_class($proposta) , "Testa o retorno da função", "Deveria ser um objeto do mesmo tipo que foi passado como parametro: " . get_class($proposta));
        
        /** Testa se o sentido (IMP exp da proposta foi definido) **/
        $this->unit->run(strlen($retorno_funcao->getSentido()), 3 , "Testa o sentido do objeto de retorno", $retorno_funcao->getSentido());
        
        /** Testa se o numero da proposta foi definido **/
        $this->unit->run(strlen($retorno_funcao->getNumero()), 15 , "Testa se o número da proposta foi definido", strlen($retorno_funcao->getNumero()));
        
        
        
    }//END TEST
    
}//END CLASS


