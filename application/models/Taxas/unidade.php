<?php
class Unidade {
	
	/**
	 * Armazena o id da unidade
	 * @var int
	 */
	private $id = NULL;
	/**
	 * Armazena o nome da unidade
	 * @var 
	 */
	private $unidade = NULL;
	
	public function __construct()
	{
					
	}
	
	public function setId( $id = NULL )
	{
		
		if( empty($id) /**|| ! is_integer($id)**/ )
		{
			log_message("error",'Id de unidade invalido');
			throw new Exception("Id de unidade invalido! ".$id);
		}	
		
		$this->id = $id;
						
		return TRUE;	
			
	}
	
	public function getId()
	{
		return (int)$this->id;
	}
	
	public function setUnidade( $unidade = NULL )
	{
		
		if( empty($unidade) )
		{
			log_message("error",'unidade invalida');
			throw new Exception("unidade invalida!");
		}
		
		$this->unidade = $unidade;
		
		return TRUE;
		
	}
	
	public function getUnidade()
	{
		return $this->unidade;		
	}
			
}//END CLASS