<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  Clientes
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 28/01/2013
* @version  1.0
* Controller de clientes
*/
include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Clientes extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->helper(Array('html','form','url'));
		$this->output->enable_profiler(FALSE);
	}
		
	public function index()
	{
		echo "There is no action here!";
	}
	
	public function find($name, $js_file)
	{
		
		$cliente = new Cliente_Model();
		
		try {
			
			$clientes = $cliente->findByName(utf8_decode(urldecode($name)));
			
		} catch (RuntimeException $rt) {
			
		} catch (Exception $e) {

			log_message('error', $e->getMessage());
			show_error($e->getMessage());
			
		}
		
		$header['form_title'] = 'Scoa - Clientes';
		$header['form_name'] = 'SELECIONAR CLIENTE';
		$header['css'] = '';
		$header['js'] = load_js(array("clientes/".$js_file));
		
		$data["clientes"] = $clientes;
		
		$imagens = '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/adicionar.jpg', 'id' => 'adicionar' , 'border' => 0)).'</a>';
			
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("clientes/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
		
}//END CLASS