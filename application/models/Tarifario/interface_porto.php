<?php
/**
 * Interface_Porto
 *
 * Interface que define os métodos que todas às classes de model 
 * relacionadas a portos devem adotar para garantir a interoperabilidade
 * entre elas.
 *
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/03/2013
 * @name Interface_Porto
 * @version 1.0
 */
interface Interface_Porto{
	
	public function findById( Porto $porto, $hub = FALSE );
	
	public function findByName( $name = NULL, $hub = FALSE );
	
	public function findByUnCode( Porto $porto, $hub = FALSE );
	
		
}
