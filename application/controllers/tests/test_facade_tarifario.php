<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test_facade_tarifario
 *
 * @author wsfall
 */
class Test_Facade_Tarifario extends CI_Controller {
    
    private $_inputs = Array();
	    
    public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
        $this->output->enable_profiler(TRUE);
	
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
    
    protected function assign($key = NULL, $value){
        
        if(is_null($key) )
        {
            $this->_inputs[] = $value;
        }
        else
        {
            $this->_inputs[$key] = $value;
        }    
		
	}

	protected function clean_provider(){
		$this->_inputs = Array();
	}

	private function provide($key = NULL){
        
        if(is_null($key) )
        {
            return $this->_inputs;
        }
        else
        {
            return $this->_inputs[$key];
        }    
		
	}
    
    public function testListarTarifariosShouldPass()
    {
        
        /** simula os parametros recebidos pelo controller **/
        $this->assign("clientes","1077:224:1077:2055:2634:3640:8988:");
        $this->assign("origem","BRSSZ");
        $this->assign("embarque","BRSSZ");
        $this->assign("desembarque","NULL");
        $this->assign("destino","DEHAM");
        $this->assign("imo","N");//FIXME verificar se a rota aceita ou não a modalidade imo antes de buscar a rota
        $this->assign("modalidade","PP:CC");//FIXME verificar se a rota aceita ou não a modalidade CC antes de buscar a rota
        $this->assign("modulo","proposta");
        $this->assign("sentido","EXP");
        
        $dados_enviados = new ArrayObject($this->provide());
        
        $facade = new Tarifario_Facade();
        
        $tarifarios = $facade->ListarTarifarios($dados_enviados);
                        
        $this->unit->run(is_array($tarifarios), TRUE, "Verifica se o retornado é um array");
        
        /** Conta a quantidade de itens retornados **/
        $quantidade_de_itens_encontrados = count($tarifarios);
        
        if( $quantidade_de_itens_encontrados > 0 )
        {
            $this->unit->run(get_class($tarifarios[0]),"Tarifario_Exportacao","Verifica se os objetos encontrados são do tipo correto");
        }       
        
    }    
    
    public function testBuscarTarifarioPorId()
    {
        
        $id_tarifario = 7407;
        $sentido = "Tarifario_Importacao";
        $clientes = "1077:";
        
        $facade = new Tarifario_Facade();
        
        $tarifario = $facade->BuscarTarifarioPeloId($id_tarifario, $sentido, $clientes);
        
        $this->unit->run($tarifario instanceof Tarifario, TRUE, "Busca Tarifario Por Id - Facade", "Verifica se o objeto retornado foi um objeto do tipo tarifario");
        
        $retornou_taxas = FALSE;
        
        if( count($tarifario->getTaxa()) > 0 )
        {
            $retornou_taxas = TRUE;
        }    
        
        $this->unit->run($retornou_taxas,TRUE,"Busca Tarifario Por Id - Facade","Verifica se o tarifário retornou com às taxas");
        
        
        
        
    }        
    
}//END TEST CLASS