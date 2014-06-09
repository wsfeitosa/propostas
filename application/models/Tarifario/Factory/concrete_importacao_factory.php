<?php
/**
 * Concrete_Importacao_Factory
 *
 * Implementa a inerface de Sentido_Factory criando às familias de objetos
 * pertencentes a importação
 *
 * @package Tarifario/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/03/2013
 * @name Concrete_Importacao_Factory
 * @version 1.0
 */
include_once APPPATH."/models/Tarifario/Factory/sentido_factory.php";

class Concrete_Importacao_Factory implements Sentido_Factory {
	
	/**
	  * getPortoModel
	  * 
	  * Obtem a classe de model utilizada para os portos da importação
	  * 
	  * @name getPortoModel
	  * @access public	   
	  * @return object Porto_Importacao_Model
	  */
	public function getPortoModel()
	{		
		include_once APPPATH."/models/Tarifario/porto_importacao_model.php";
		return new Porto_Importacao_Model();		
	}		
	
	/**
	 * getTarifarioDAO
	 *
	 * Obtem a classe de model utilizada para o tarifario da importação
	 *
	 * @name getTarifarioDAO
	 * @access public
	 * @return object Tarifario_Importacao
	 */
	public function getTarifarioDAO()
	{
		include_once APPPATH."/models/Tarifario/tarifario_importacao.php";
		return new Tarifario_Importacao();
	}
	
	/**
	 * getTarifarioModel()
	 *
	 * Obtem a classe de model utilizada para o tarifario da importação
	 *
	 * @name getPortoModel
	 * @access public
	 * @return object Tarifario_Importacao_Model
	 */
	public function getTarifarioModel()
	{			
		include_once APPPATH . "/models/Tarifario/tarifario_importacao_model.php";
		return new Tarifario_Importacao_Model();			
	}
	
}//END CLASS