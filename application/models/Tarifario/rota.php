<?php
/**
 * Classe que representa a entidade Rota
 *
 * Esta classe reprenta às rotas do sistema que são utilizadas pela
 * classe tarifário, a classe rota é formada por objetos da classe porto
 *
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 14/01/2013
 * @name Rotas
 * @version 1.0
 */
class Rota {
		
	private $portoOrigem = NULL;
	private $portoEmbarque = NULL;
	private $portoDesembarque = NULL;
	private $portoFinal = NULL;
	private $portoViaAdicional = NULL;
	
	/**
	  * Set Porto Origem
	  * 
	  * Atribui valor ao porto de origem, a função recebe um parametro
	  * do tipo Porto da classe Porto
	  * 
	  * @name SetPortoOrigem
	  * @access public
	  * @param Porto
	  * @return boolean
	  */
	public function setPortoOrigem( $porto )
	{
		if( ! $porto instanceof Porto  )
		{
			log_message('error','Objeto de tipo invalido passado a classe Rota');
			throw new Exception('Não foi possivel criar o porto de origem!');
		}	
		
		$this->portoOrigem = $porto;
                
		return TRUE;
		
	}   
	
	/**
	 * Get Porto Origem
	 *
	 * Obtem o valor do porto de origem
	 *
	 * @name getPortoOrigem
	 * @access public
	 * @param 
	 * @return Object
	 */
	public function getPortoOrigem()
	{
		return $this->portoOrigem;
	}
	
	/**
	 * Set Porto Embarque
	 *
	 * Atribui valor ao porto de embarque, a função recebe um parametro
	 * do tipo Porto da classe Porto
	 *
	 * @name SetPortoEmbarque
	 * @access public
	 * @param Porto
	 * @return boolean
	 */
	public function setPortoEmbarque( $porto )
	{
		if( ! $porto instanceof Porto  )
		{
			log_message('error','Objeto de tipo invalido passado a classe Rota');
			throw new Exception('Não foi possivel criar o porto de embarque!');
		}
	
		$this->portoEmbarque = $porto;
	
		return TRUE;
	
	}
	
	/**
	 * Get Porto Embarque
	 *
	 * Obtem o valor do porto de embarque
	 *
	 * @name getPortoEmbarque
	 * @access public
	 * @param
	 * @return Object
	 */
	public function getPortoEmbarque()
	{
		return $this->portoEmbarque;
	}
	
	/**
	 * Set Porto Desembarque
	 *
	 * Atribui valor ao porto de desembarque, a função recebe um parametro
	 * do tipo Porto da classe Porto
	 *
	 * @name SetPortoDesembarque
	 * @access public
	 * @param Porto
	 * @return boolean
	 */
	public function setPortoDesembarque( $porto )
	{
		if( ! $porto instanceof Porto  )
		{
			log_message('error','Objeto de tipo invalido passado a classe Rota');
			throw new Exception('Não foi possivel criar o porto de desembarque!');
		}
	
		$this->portoDesembarque = $porto;
	
		return TRUE;
	
	}
	
	/**
	 * Get Porto Desembarque
	 *
	 * Obtem o valor do porto de desembarque
	 *
	 * @name getPortoDesembarque
	 * @access public
	 * @param
	 * @return Object
	 */
	public function getPortoDesembarque()
	{
		return $this->portoDesembarque;
	}
	
	/**
	 * Set Porto Final
	 *
	 * Atribui valor ao porto de final, a função recebe um parametro
	 * do tipo Porto da classe Porto
	 *
	 * @name SetPortoFinal
	 * @access public
	 * @param Porto
	 * @return boolean
	 */
	public function setPortoFinal( $porto )
	{
		if( ! $porto instanceof Porto  )
		{
			log_message('error','Objeto de tipo invalido passado a classe Rota');
			throw new Exception('Não foi possivel criar o porto final!');
		}
	
		$this->portoFinal = $porto;
	
		return TRUE;
	
	}
	
	/**
	 * Get Porto Final
	 *
	 * Obtem o valor do porto de final
	 *
	 * @name getPortoFinal
	 * @access public
	 * @param
	 * @return Object
	 */
	public function getPortoFinal()
	{
		return $this->portoFinal;
	}
	
	/**
	 * Set Porto Via Adicional
	 *
	 * Atribui valor ao porto da via adicional, a função recebe um parametro
	 * do tipo Porto da classe Porto
	 *
	 * @name SetPortoFinal
	 * @access public
	 * @param Porto
	 * @return boolean
	 */
	public function setPortoViaAdicional( $porto )
	{
		if( ! $porto instanceof Porto  )
		{
			log_message('error','Objeto de tipo invalido passado a classe Rota');
			throw new Exception('Não foi possivel criar o porto da via adicional!');
		}
	
		$this->portoViaAdicional = $porto;
	
		return TRUE;
	
	}
	
	/**
	 * Get Porto Via Adicional
	 *
	 * Obtem o valor do porto da via adicional
	 *
	 * @name getPortoFinal
	 * @access public
	 * @param
	 * @return Object
	 */
	public function getPortoViaAdicional()
	{
		return $this->portoViaAdicional;
	}
	
}//END CLASS