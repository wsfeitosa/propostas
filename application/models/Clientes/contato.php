<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Cliente Model
 *
 * Classe que contém as regras de negócio da entidade cliente
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Cliente_Model
 * @version 1.0
 */
class Contato extends CI_Model{
	/**
	 * Armazena o id do contato 
	 * @var interger
	 */
	private $id = NULL;
	/**
	 * Armazena o nome do contato
	 * @var string
	 */
	private $nome = NULL;
	/**
	 * Armazena o email do contato
	 * @var Email
	 */
	private $email = NULL;
	
	public function __construct()
	{
		parent::__construct();	
	}
	
	/**
	  * Set Id
	  * 
	  * Atribui um id ao contato
	  * 
	  * @name setId
	  * @access public
	  * @param int
	  * @return boolean
	  */
	public function setId( $id = NULL )
	{
		
		if( empty($id) ) 
		{
			return FALSE;
		}	
		
		$this->id = (int)$id;
		
		return TRUE;
		
	}
	
	/**
	  * Get Id 
	  * 
	  * Obtem o id do contato
	  * 
	  * @name getId
	  * @access public
	  * @param 
	  * @return integer
	  */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	  * Set Nome
	  * 
	  * Atribui um nome ao contato
	  * 
	  * @name setNome
	  * @access public
	  * @param string
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
	  * Obtem o nome do contato
	  * 
	  * @name getNome
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getNome()
	{
		return $this->nome;
	}
	
	/**
	 * Set Email
	 *
	 * Atribui um email para o contato
	 *
	 * @name setEmail
	 * @access public
	 * @param Email
	 * @return boolean
	 */
	public function setEmail( Email $email )
	{
		$this->email = $email;
		return TRUE;
	}
	
	/**
	 * Get Email
	 *
	 * Obtem o email do contato
	 *
	 * @name getEmail
	 * @access public
	 * @param
	 * @return Email
	 */
	public function getEmail()
	{
		return $this->email;
	}
		
}//END CLASS