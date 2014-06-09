<?php
class Test_Adaptadores extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
	
		/** Carrega o Model a ser testado **/
		$this->load->model("Adaptadores/adaptador");	
		$this->load->model("Adaptadores/array_conversor");
		$this->load->model("Clientes/cidade");
					
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
	
	public function testArrayAdapter()
	{
		
		/** Cria um array com objetos do tipo cidade **/
		$array_cidades = Array();
		
		$cidade = new Cidade();
		
		$cidade->setId((int) 1);
		$cidade->setNome("São Paulo");
		
		$array_cidades[] = clone $cidade;
		
		$cidade->setId((int) 2);
		$cidade->setNome("Rio de Janeiro");
		
		$array_cidades[] = clone $cidade;
		
		$cidade->setId((int) 3);
		$cidade->setNome("Curitiba");
		
		$array_cidades[] = clone $cidade;
		
		$adaptador = new Adaptador();
		
		$conversor = new Array_Conversor();
		
		$cidades_adaptadas = $conversor->converter($array_cidades, Array("value" => "id", "label" => "nome"));
		
		//$cidades_adaptadas = $adaptador->adaptar($conversor);
		
		pr($cidades_adaptadas);
		
		$this->unit->run($cidades_adaptadas, 'is_array', "Testa o retorno do conversor de objetos em Array");
		
		$this->unit->run(count($cidades_adaptadas), 'is_int', "Testa se é maior que 0");
		
	}//END FUNCTION
	
}//END CLASS