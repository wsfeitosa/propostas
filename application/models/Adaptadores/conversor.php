<?php
/**
 * Conversor
 *
 * Esta classe define todos os métodos que os adaptadores concretos terão de implementar
 *
 * @package Adaptadores
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 07/03/2013
 * @name Conversor
 * @version 1.0
 */
abstract class Conversor {
	
	public function __construct()
	{
		
	}
			
	abstract function converter(Array $array_de_objetos, Array $parametros);
			
}//END CLASS