<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Classe que representa a entidade Usuario
 *
 * Esta classe reprenta os usuários do sistema que são utilizadas pelas
 * demais classes que representam usuários, gestor, customer, vendedor, etc...
 *
 * @package Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Usuario
 * @version 1.0
 */
class Usuario {
	
	protected $id = NULL;
	protected $nome = NULL;
	protected $email = NULL;
	protected $cargo = NULL;
	protected $filial = NULL;
	protected $ddd = NULL;
	protected $fone = NULL;
	protected $ramal = NULL;
	protected $fax = NULL;
	protected $porto_base = NULL;
	
	public function __construct()
	{
		
	}
	
	/**
	  * Set Id
	  * 
	  * atribui um id ao usuario
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
	  * obten o id do usuário
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
	  * Atribui um nome ao usuário
	  * 
	  * @name setNome
	  * @access public 
	  * @param $nome
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
	  * Obtem o nome do usuário
	  * 
	  * @name getNome
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getnome()
	{
		return (string)$this->nome;
	}
	
	/**
	  * Set Email
	  * 
	  * Atribui um email ao usuario
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
	  * Obtem o email do usuário
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
	
	/**
	  * Set Cargo
	  * 
	  * Atribui um cargo ao usuário
	  *   
	  * @name setCargo
	  * @access public
	  * @param String
	  * @return boolean
	  */
	public function setCargo( $cargo  = NULL )
	{
		
		if( empty($cargo) )
		{
			return FALSE;
		}	
		
		$this->cargo = $cargo;
		
		return TRUE;
		
	}
	
	/**
	  * Get cargo
	  * 
	  * Obtem o cargo do usuário
	  * 
	  * @name getCargo
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getCargo()
	{
		return $this->cargo;
	}
	
	/**
	  * Set Filial
	  * 
	  * Atribui uma filial ao usuario
	  * 
	  * @name getFilial
	  * @access public
	  * @param Filial
	  * @return boolean
	  */
	public function setFilial( Filial $filial )
	{
		
		$this->filial = $filial;
		
		return TRUE;
		
	}
	
	/**
	  * Get Filial
	  * 
	  * Obtem a filial do usuário
	  * 
	  * @name getFilial
	  * @access public
	  * @param 
	  * @return Filial
	  */
	public function getFilial()
	{
		return $this->filial;
	}
	
	/** 
	  * Solicitar Desbloqueio
	  * 
	  * Faz a solicitação de desbloqueio de taxa ou validade de 
	  * uma proposta, todos os usuários do sistema podem fazer uma
	  * solicitação, e por esse motivo todos hedam desta classe
	  * 
	  * @name solictarDesbloqueio
	  * @access public
	  * @param Desbloqueio
	  * @return boolean
	  */
	public function solicitarDesbloqueio( Solicitacao $solicitacao )
	{
		
		$solicitacao->solicitarDesbloqueio();
		
		return TRUE;
	}
	
	public function getDdd()
	{
		return (int)$this->ddd;
	}
		
	public function setDdd($ddd)
	{
		$this->ddd = $ddd;
		return $this;
	}
		
	
	public function getFone()
	{
		return $this->fone;
	}
		
	public function setFone($fone)
	{
		$this->fone = $fone;
		return $this;
	}
		
	
	public function getRamal()
	{
		return $this->ramal;
	}
		
	public function setRamal($ramal)
	{
		$this->ramal = $ramal;
		return $this;
	}

	public function getFax()
	{
		return $this->fax;
	}
		
	public function setFax($fax)
	{
		$this->fax = $fax;
		return $this;
	}
	
	public function setPortoBase( $porto_base )
	{
		$this->porto_base = $porto_base;
		return $this;
	}

	public function getPortoBase()
	{
		return $this->porto_base;
	}
	
}//END CLASS
