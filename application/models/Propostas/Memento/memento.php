<?php
class Memento extends CI_Model{
	
	private $state = NULL;
	private $numero_proposta = NULL;

	public function __construct($state = NULL, $numero = NULL)
	{		
		if( ! is_null($state) )
		{
			$this->state = $state;
		}			
		
		if( ! is_null($numero) )
		{
			$this->numero_proposta = $numero;
		}		
	}
	
	public function SetState( $state )
	{		
		$this->state = $state;
	}
	
	public function GetState()
	{
		return $this->state;
	}
	
	public function SetNumeroProposta($numero)
	{
		$this->numero_proposta = $numero;
	}
	
	public function GetNumeroProposta()
	{
		return $this->numero_proposta;
	}
	
}
