<?php
/**
 * Portos
 *
 * Controller com as ações permitidas a os portos
 *
 * @package controllers/portos
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 27/03/2013
 * @name Portos
 * @version 1.0
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Portos extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("form","html","url"));
		$this->load->model("Tarifario/Factory/concrete_factory");
		$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
		$this->load->model("Tarifario/Factory/concrete_importacao_factory");
	}
	
	public function find_origin( $name = NULL, $sentido = NULL )
	{
						
		if( empty($name) || empty($sentido) )
		{
			log_message("O porto de origem não pode ser invalido");
			show_error("O porto de origem não pode ser invalido");
		}
		
		try{
			
			$class = "Concrete_" . ucfirst($sentido) . "_Factory";
			
			$concrete_factory = new $class();
									
			$factory = new Concrete_Factory();
						
			$model = $factory->CreatePortoModel($concrete_factory);
				
			$portos = $model->findByName(urldecode($name),"origem");
				
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
		
	}//END FUNCTION
	
	public function find_loading( $name = NULL, $sentido )
	{
	
		if( empty($name) || empty($sentido) )
		{
			log_message("O porto de embarque não pode ser invalido");
			show_error("O porto de embarque não pode ser invalido");
		}
	
		try{
						
			$class = "Concrete_" . ucfirst($sentido) . "_Factory";
			
			$concrete_factory = new $class();
						
			$factory = new Concrete_Factory();
			
			$model = $factory->CreatePortoModel($concrete_factory);
				
			$portos = $model->findByName(urldecode($name),"embarque");
	
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
	
	public function find_discharge( $name = NULL, $sentido = NULL )
	{
		
		if( empty($name) || empty($sentido) )
		{
			log_message("O porto de desembarque não pode ser invalido");
			show_error("O porto de desembarque não pode ser invalido");
		}
	
		try{
	
			$class = "Concrete_" . ucfirst($sentido) . "_Factory";
			
			$concrete_factory = new $class();
						
			$factory = new Concrete_Factory();
			
			$model = $factory->CreatePortoModel($concrete_factory);
	
			$portos = $model->findByName(urldecode($name), "desembarque");
	
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
	
	public function find_destination( $name = NULL, $sentido = NULL, $tela = NULL )
	{
	
		if( empty($name) || empty($sentido) )
		{
			log_message("O porto de destino não pode ser invalido");
			show_error("O porto de destino não pode ser invalido");
		}
	
		try{
	
			$class = "Concrete_" . ucfirst($sentido) . "_Factory";
			
			$concrete_factory = new $class();
						
			$factory = new Concrete_Factory();
			
			$model = $factory->CreatePortoModel($concrete_factory);
	
			$portos = $model->findByName(urldecode($name), "destino");
	
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
		
		$data["tela"] = $tela;
		
		$footer['footer'] = "";
	
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("portos/find",$data);
		$this->load->view("Padrao/footer_view",$footer);
	
	}
	
}//END CLASS
