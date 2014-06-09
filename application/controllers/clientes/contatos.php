<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Contatos
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @version  1.0
 * Controller de contatos
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Contatos extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Clientes/contato");
		$this->load->model("Clientes/contato_model");
		$this->load->model("Clientes/cliente");
		$this->load->helper(Array("form","html","url"));
	}
	
	public function find( $clientes = NULL )
	{
		
		if( empty($clientes) )
		{
			log_message("Clientes Invalidos para a pesquisa dos contatos");
			show_error("Clientes Invalidos para a pesquisa dos contatos");
		}	
		
		/** Explode os ids dos clientes que estão separados pelo caractere : **/
		$ids_clientes = explode(":", $clientes);
		
		array_pop($ids_clientes);
		
		$contatos = Array();
		
		$contato_model = new Contato_Model();
		
		try{
		
			foreach( $ids_clientes as $id_cliente )
			{
				
				$array_contatos = $contato_model->findByIdCliente($id_cliente);
				
				foreach( $array_contatos as $contato )
				{
					$contatos[] = $contato;
				}	
				
			}
				
		} catch (RuntimeException $rt) {
			show_error($rt->getMessage());
		} catch (Exception $e) {
			show_error($e->getMessage());
		}		
		
		$header['form_title'] = 'Scoa - Contatos';
		$header['form_name'] = 'SELECIONAR CONTATOS';
		$header['css'] = '';
		$header['js'] = load_js(array('clientes/find_contatos.js'));
		
		$data["contatos"] = $contatos;
		
		$imagens = '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/adicionar.jpg', 'id' => 'adicionar' , 'border' => 0)).'</a>';
			
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("clientes/find_contatos",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}//END FUNCTION
	
}//END CLASS