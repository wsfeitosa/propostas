<?php
class Moeda extends CI_Model{
	
	/**
	 * Armazena o id da moeda
	 * @var int
	 */
	private $id = NULL;
	/**
	 * Armazena a sigla da moeda
	 * @var string
	 */
	private $moeda = NULL;
	/**
	 * Armazena a sigla da moeda
	 * @var string
	 */
	private $sigla = null;
	
	public function __construct( $id = NULL )
	{
		parent::__construct();	
		
		if( ! empty($id) )
		{
			$this->setId($id);
		}
		
	}
	
	public function setId( $id = NULL )
	{
	
		if( empty($id) || ! is_integer($id) )
		{
			log_message("error",'Id de moeda invalido');
			throw new Exception("Id de moeda invalido!");
		}
	
		$this->id = $id;
	
		return TRUE;
			
	}
	
	public function getId()
	{
		return (int)$this->id;
	}
	
	public function setMoeda( $moeda = NULL )
	{
		
		if( empty($moeda) )
		{
			log_message("error",'moeda invalida');
			throw new Exception("moeda invalida");
		}
		
		$this->moeda = $moeda;
		
		return TRUE;
		
	}

	public function getMoeda()
	{
		return $this->moeda;
	}
			
	public function getSigla()
	{
		return $this->sigla;
	}
	
	public function setSigla($sigla)
	{
		$this->sigla = $sigla;
	}
	
}