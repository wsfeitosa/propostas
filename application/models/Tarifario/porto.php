<?php
/**
 * Classe que representa a entidade Porto no sistema
 *
 * Esta classe representa a entidade porto no sistema e é utilizada 
 * em todos os locais onde um porto é requerido, em especial nas classes
 * Rota e Tarifario.
 * 
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @name Tarifario
 * @version 1.0
 */
class Porto {
	
	protected $id;
	protected $nome;
	protected $uncode;
	protected $pais;
	
	public function __construct(){
		
	}
	
	/**
	  * Set ID
	  * 
	  * Atribui um id ao porto
	  * 
	  * @name setId
	  * @access public
	  * @param int
	  * @return boolean
	  */
	public function setId( $id = NULL )
	{
		
		if( empty($id) || ! is_integer($id) )
		{
			return FALSE;
		}	
		
		$this->id = $id;
		
		return TRUE;
		
	}
	
	/**
	  * Get Id
	  *
	  * Obtem o id do porto
	  *
	  * @name getId
	  * @access public
	  * @param 
	  * @return int
	  */
	public function getId()
	{
		return (int)$this->id;
	}
	
	/**
	  * Set Nome
	  * 
	  * Atribui um nome ao porto
	  * 
	  * @name setNome 
	  * @access public
	  * @param String
	  * @return boolean
	  */
	public function setNome( $nome = NULL )
	{
		
		if( empty($nome) )
		{
			return FALSE;
		}	
		
		$this->nome = $nome;
		
		return TRUE;
		
	}
	
	/**
	  * Get Nome
	  * 
	  * Obtem o nome do porto
	  * 
	  * @name getNome
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getNome()
	{
		return strtoupper($this->nome);
	}
	
	/**
	 * Set UnCode
	 *
	 * Atribui um uncode ao porto
	 *
	 * @name setUnCode
	 * @access public
	 * @param String
	 * @return boolean
	 */
	public function setUnCode( $uncode = NULL )
	{
	
		if( empty($uncode) )
		{
			return FALSE;
		}
	
		$this->uncode = $uncode;
	
		return TRUE;
	
	}
	
	/**
	 * Get UnCode
	 *
	 * Obtem o uncode do porto
	 *
	 * @name getUnCode
	 * @access public
	 * @param
	 * @return string
	 */
	public function getUnCode()
	{		
		return strtoupper($this->uncode);
	}
	
	/**
	 * Set Pais
	 *
	 * Atribui um Pais ao porto
	 *
	 * @name setPais
	 * @access public
	 * @param String
	 * @return boolean
	 */
	public function setPais( $pais = NULL )
	{
	
		if( empty($pais) )
		{
			return FALSE;
		}
	
		$this->pais = $pais;
	
		return TRUE;
	
	}
	
	/**
	 * Get Pais
	 *
	 * Obtem o pais do porto
	 *
	 * @name getPais
	 * @access public
	 * @param
	 * @return string
	 */
	public function getPais()
	{
		return strtoupper($this->pais);
	}
	
}//END CLASS