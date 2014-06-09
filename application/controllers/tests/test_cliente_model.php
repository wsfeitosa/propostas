<?php
class Test_Cliente_Model extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Clientes/cliente");	
		$this->load->model("Email/email");	
			
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
	
	public function testValidarModalidadeClientesPropostaShouldPass()
	{
		$ids_dos_clientes_selecionados = "224:1077:2055:2634:3640:8988:";
		
		$cliente_model = new Cliente_Model();
		
		$clientes_sao_do_mesmo_tipo = $cliente_model->verificarModalidadeDosClientes($ids_dos_clientes_selecionados);
		
		$this->unit->run($clientes_sao_do_mesmo_tipo,TRUE,"Verifica se todos os clientes informados tem a mesma modalidade (Direto ou Forwarder)");						
	}
	
	public function testValidarModalidadeClientesPropostaShouldFail()
	{
		$ids_dos_clientes_selecionados = "1964:8421:8426:14893:15971:17376:1964:8421:8426:14893:15971:17376:";
		
		$cliente_model = new Cliente_Model();
		
		$clientes_sao_do_mesmo_tipo = $cliente_model->verificarModalidadeDosClientes($ids_dos_clientes_selecionados);
		
		$this->unit->run($clientes_sao_do_mesmo_tipo,FALSE,"Verifica se todos os clientes informados tem a mesma modalidade (Direto ou Forwarder)");
	}	
	
}//END CLASS