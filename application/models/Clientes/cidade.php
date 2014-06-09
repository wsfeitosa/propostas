<?php
class Cidade extends CI_Model {
	
	private $id = NULL;
	private $nome = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setId( $id = NULL )
	{
		
		if( empty($id) || ! is_integer($id) )
		{
			return FALSE;
		}	
		
		$this->id = $id;
		
		return TRUE;
		
	}
	
	public function getId()
	{
		return (int)$this->id;
	}
	
	public function setNome( $nome = NULL )
	{
		
		if( empty($nome) )
		{
			return FALSE;
		}	
		
		$this->nome = $nome;
		
		return TRUE;
		
	}
	
	public function getNome()
	{
		return $this->nome;
	}
	
	public function findById()
	{
		
		if( empty($this->id) )
		{
			throw new Exception("O id da cidade precisa ser informado antes da consulta!");
		}	
		
		$this->db->
				select("cidade")->
				from("CLIENTES.cidade")->
				where("id_cidade", $this->id);

		$query = $this->db->get();
		
		if( $query->num_rows() < 1 )
		{
			return FALSE;
		}	
		
		$rs = $query->row();
		
		$this->nome = $rs->cidade;
		
		return TRUE;
		
	}
	
}//END CLASS