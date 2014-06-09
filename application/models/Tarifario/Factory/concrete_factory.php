<?php
/**
 * Concrete_Factory
 *
 * Classe que cria de fato às familias de objeto relacionados a os
 * tarifarios e portos de importação e exportação
 *
 * @package Tarifario/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/03/2013
 * @name Concrete_Factory
 * @version 1.0
 */
class Concrete_Factory {
	
	public function CreatePortoModel( Sentido_Factory $factory )
	{
		return $factory->getPortoModel();
	} 
	
	public function CreateTarifarioObject( Sentido_Factory $factory )
	{
		return $factory->getTarifarioDAO();
	}
	
	public function CreateTarifarioModel( Sentido_Factory $factory )
	{
		return $factory->getTarifarioModel();
	}
	
}//END CLASS