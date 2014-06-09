<?php
/**
 * Class Portos Importacao
 *
 * Controller com as ações permitidas a os portos de importação
 *
 * @package portos
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Portos_Importacao
 * @version 1.0
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Portos_Importacao extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("form","html","url"));
		$this->load->model("Tarifario/porto_importacao_model","model");
	}
	
	public function find_origin( $name = NULL )
	{
		
		if( empty($name) )
		{
			log_message("O porto de origem não pode ser invalido");
			show_error("O porto de origem não pode ser invalido");
		}	
		
		try{
		
			$model = new Porto_Importacao_Model();
			
			$portos = $model->findByName(urldecode($name),"N");
			
		} catch (RuntimeException $rt) {
				
			log_message('error', $e->getMessage());
						
		} catch (Exception $e) {
		
			log_message('error', $e->getMessage());
			show_error($e->getMessage());
				
		}
		
		$header['form_title'] = 'Scoa - Portos';
		$header['form_name'] = 'SELECIONAR PORTO';
		$header['css'] = '';
		$header['js'] = load_js(array('portos/importacao/find_origin.js'));
		
		$data["portos"] = $portos;
				
		$footer['footer'] = "";
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("portos/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
						
	}
	
	public function find_loading( $name = NULL )
	{
		
		if( empty($name) )
		{
			log_message("O porto de embarque não pode ser invalido");
			show_error("O porto de embarque não pode ser invalido");
		}
		
		try{
		
			$model = new Porto_Importacao_Model();
				
			$portos = $model->findByName(urldecode($name),"N");
				
		} catch (RuntimeException $rt) {
		
			log_message('error', $e->getMessage());
		
		} catch (Exception $e) {
		
			log_message('error', $e->getMessage());
			show_error($e->getMessage());
		
		}
		
		$header['form_title'] = 'Scoa - Portos';
		$header['form_name'] = 'SELECIONAR PORTO';
		$header['css'] = '';
		$header['js'] = load_js(array('portos/importacao/find_loading.js'));
		
		$data["portos"] = $portos;
		
		$footer['footer'] = "";
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("portos/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function find_discharge( $name = NULL )
	{
	
		if( empty($name) )
		{
			log_message("O porto de desembarque não pode ser invalido");
			show_error("O porto de desembarque não pode ser invalido");
		}
	
		try{
	
			$model = new Porto_Importacao_Model();
	
			$portos = $model->findByName(urldecode($name));
	
		} catch (RuntimeException $rt) {
	
			log_message('error', $e->getMessage());
	
		} catch (Exception $e) {
	
			log_message('error', $e->getMessage());
			show_error($e->getMessage());
	
		}
	
		$header['form_title'] = 'Scoa - Portos';
		$header['form_name'] = 'SELECIONAR PORTO';
		$header['css'] = '';
		$header['js'] = load_js(array('portos/importacao/find_discharge.js'));
	
		$data["portos"] = $portos;
	
		$footer['footer'] = "";
	
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("portos/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
	
	}
	
	public function find_destination( $name = NULL )
	{
	
		if( empty($name) )
		{
			log_message("O porto de destino não pode ser invalido");
			show_error("O porto de destino não pode ser invalido");
		}
	
		try{
	
			$model = new Porto_Importacao_Model();
	
			$portos = $model->findByName(urldecode($name));
	
		} catch (RuntimeException $rt) {
	
			log_message('error', $e->getMessage());
	
		} catch (Exception $e) {
	
			log_message('error', $e->getMessage());
			show_error($e->getMessage());
	
		}
	
		$header['form_title'] = 'Scoa - Portos';
		$header['form_name'] = 'SELECIONAR PORTO';
		$header['css'] = '';
		$header['js'] = load_js(array('portos/importacao/find_destination.js'));
	
		$data["portos"] = $portos;
	
		$footer['footer'] = "";
	
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("portos/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
	
	}
	
}//END CLASS