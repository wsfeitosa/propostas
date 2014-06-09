<?php
/**
 * Sobrescreve_Tarifario
 *
 * Descrição Longa da classe 
 *
 * @package sobrescreve_tarifario.php
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 18/07/2013
 * @version  versao 1.0
*/
class Sobrescreve_Tarifario extends CI_Model {
	
	protected $id_cliente = NULL, $tarifario = NULL;
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function __set($propriedade,$value)
	{
		$this->$propriedade = $value;
		return $this;
	}
	
	public function __get($propriedade)
	{
		return $this->$propriedade;
	}
	
	/**
	 * buscaPropostasValidas
	 *
	 * procura por propostas validadas para o cliente na rota do tarifário
	 *
	 * @name buscaPropostasValidas
	 * @access public
	 * @param int $id_cliente
	 * @param Tarifario $tarifario
	 * @return bool
	 */ 	
	public function buscaPropostasValidas($id_cliente = NULL, Tarifario $tarifario) 
	{
		
		if( is_null($id_cliente) || ! is_integer($id_cliente) )
		{
			throw new InvalidArgumentException("O cliente informado para a pesquisa é invalido!");
		}	
		
			
	}
	
}//END CLASS