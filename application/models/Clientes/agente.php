<?php
/**
 * Agente
 *
 * Classe que manipula a entidade agente no módulo de propostas 
 *
 * @package models/Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 03/07/2013
 * @version  versao 1.0
*/
class Agente extends CI_Model {
	
	protected $id_agente = NULL, $razao = NULL;
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function setId( $id )
	{
		$this->id_agente = $id;		
		return $this;
	}
	
	public function getId()
	{
		return $this->id_agente;
	}
	
	public function setRazao( $razao )
	{
		$this->razao = $razao;
		return $this;
	}
	
	public function getRazao()
	{
		return $this->razao;
	}
	
	/**
	 * findById
	 *
	 * busca o agente pelo id
	 *
	 * @name findById
	 * @access public
	 * @param $id_agente
	 * @return Agente $this
	 */ 	
	public function findById()
	{
		
		if( is_null($this->id_agente) )
		{
			throw new InvalidArgumentException("O id do agente não foi informado para realizar a pesquisa!");
		}	
		
		$this->db->
				select("id_agente, apelido")->
				from("CLIENTES.agentes")->
				where("id_agente", $this->id_agente);

		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 || $rs === FALSE )
		{
			throw new RuntimeException("Impossivel encontrar o agente informado!");
		}	
		
		$agente = $rs->row();
		
		$this->id_agente = $agente->id_agente;
		$this->razao = $agente->apelido;
		
		return $this;
		
	}
	
}//END CLASS