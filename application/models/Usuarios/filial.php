<?php
class Filial{
	
	private $id = NULL;
	private $nome_filial = NULL;
	private $sigla_filial = NULL;
	
	public function __construct(){
		
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
	
	public function setNomeFilial( $nome_filial = NULL )
	{
	
		if( empty($nome_filial) )
		{
			return FALSE;
		}
	
		$this->nome_filial = $nome_filial;
	
		return TRUE;
	
	}
	
	public function getNomeFilial()
	{
		return $this->nome_filial;
	}
	
	public function setSiglaFilial( $sigla_filial = NULL )
	{
	
		if( empty($sigla_filial) )
		{
			return FALSE;
		}
	
		$this->sigla_filial = $sigla_filial;
	
		return TRUE;
	
	}
	
	public function getSiglaFilial()
	{
		return $this->sigla_filial;
	}
	
}//END CLASS