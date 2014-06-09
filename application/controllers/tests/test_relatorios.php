<?php
class Test_Relatorios extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
		
		/** Carrega o Model a ser testado **/
		$this->load->model("Propostas/proposta_tarifario", "proposta", TRUE);
				
		$this->load->model("Formatos/formato_excel");
		$this->load->model("Formatos/formato_pdf");
		$this->load->model("Formatos/formato_html");
		$this->load->model("Formatos/formato_csv");
		$this->load->model("Relatorios/Layouts/layout_relatorio_desbloqueio","layout");
		$this->load->model("Relatorios/relatorio_desbloqueios","relatorio");
		$this->load->model("Relatorios/relatorio_adapter","adapter");
		
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
	
	public function testGerar()
	{
		
		$relatorio = new Relatorio_Desbloqueios();
		
		$formato_excel = new Formato_Excel();
		
		$formato_pdf = new Formato_PDF();
		
		$formato_html = new Formato_HTML();
		
		$formato_csv = new Formato_CSV();
		
		$layout = new Layout_Relatorio_Desbloqueio();
		
		$adapter = new Relatorio_Adapter();
		
		$this->unit->run($relatorio->adicionarNovoParametro(Array("nome"=>"joão")),'is_integer',"Atribui um novo parametro ao relatorio");

		$this->unit->run($relatorio->obterParametros(),'is_array',"obtem os parametros atribuidos a classe");
		
		$adapter->gerarRelatorio($relatorio, $formato_csv, $layout);

		$adapter->exportar();
		
	}
	
}//END CLASS
