<?php
/**
 * Classe que representa a entidade Frete - Taxa
 *
 * Esta é uma classe que representa as taxas do tipo frete do sistema. 
 *
 * @package Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @name Frete
 * @version 1.0
 * @abstract
 */
include_once 'taxa.php';
define("IDTAXA", 10);

class Frete extends Taxa{
	
	/**
	  * Set Id
	  * 
	  * Sobrescreve a função que atribui o id para taxa
	  * utilizada nas demais classes de taxas, uma vez que o id
	  * do frete será sempre o mesmo (10) em todos os casos, 
	  * para isso foi definida uma constante com este valor 
	  * 
	  * @name setId
	  * @access private
	  * @param 
	  * @return Exception
	  */
	public function setId()
	{
		log_message('error','Não é possivel atribuir um id para a taxa de frete');
		throw New Exception('Não é possivel atribuir um id para a taxa de frete');
	}
	
	/**
	 * getId
	 *
	 * @name getId
	 * @access public
	 * @param
	 * @return int
	 */
	public function getId()
	{
		return IDTAXA;
	}
}