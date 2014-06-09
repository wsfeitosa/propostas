<?php
/**
 * Unidade_Model
 *
 * Classe que abriga às operações e regras de negócio da entidade moeda
 *
 * @package Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 01/02/2013
 * @name Unidade_Model
 * @version 1.0
 */
class Unidade_Model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();		
	}
	
	public function findById( Unidade $unidade )
	{
		
		$rs = $this->db->get_where("FINANCEIRO.unidades",Array("id_unidade" => $unidade->getId()));
		
		if( $rs->num_rows() < 1 )
		{
			log_message('error',"Impossivel encontrar a unidade solicitada!");
			//throw new Exception("Impossivel encontrar a unidade solicitada!".pr($this->db));
			return FALSE;
		}	
		
		$row = $rs->row();
		
		$unidade->setUnidade($row->unidade); 
		
	}//END FUNCTION
	
	/**
	 * retornaTodasAsUnidades
	 *
	 * Retorna todas às unidades de medida cadastradas no sistemas
	 *
	 * @name retornaTodasAsUnidades
	 * @access public	 
	 * @return Array $unidades
	 */ 	
	public function retornaTodasAsUnidades() 
	{
		
		$this->db->
				select("id_unidade, unidade")->
				from("FINANCEIRO.unidades");
				
		$this->db->cache_on();
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
    	{
    		$message = "Nenhuma Taxa Cadastrada No Sistema!";
    		log_message('error',$message);
    		show_error($message);
    	}	
    	
    	$unidades = Array();
    	
    	$result = $rs->result();
    	
    	foreach( $result as $unidade )
    	{
    		$unidades[$unidade->id_unidade] = $unidade->unidade;
    	}	
    	$this->db->cache_off();    	    	
    	return $unidades;
	}
	
}//END CLASS