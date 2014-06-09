<?php
/**
 * Exportacao_Factory
 *
 * Implementa a interface de Sentido_Factory criando às familias de objetos
 * pertencentes a exportação
 *
 * @package Tarifario/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/03/2013
 * @name Exportacao_factory
 * @version 1.0
 */
include_once APPPATH."/models/Tarifario/Factory/sentido_factory.php";

class Concrete_Exportacao_Factory implements Sentido_Factory {
	
	/**
	 * getPortoModel
	 *
	 * Obtem a classe de model utilizada para os portos da exportação
	 *
	 * @name getPortoModel
	 * @access public
	 * @return object Porto_Exportacao_Model
	 */
	public function getPortoModel()
	{			
		include_once APPPATH."/models/Tarifario/porto_exportacao_model.php";
		return new Porto_Exportacao_Model();			
	}
	
	/**
	 * getTarifarioDAO
	 *
	 * Obtem a classe de model utilizada para o tarifario da exportação
	 *
	 * @name getTarifarioDAO
	 * @access public
	 * @return object Tarifario_Exportacao
	 */
	public function getTarifarioDAO()
	{
		include_once APPPATH."/models/Tarifario/tarifario_exportacao.php";
		return new Tarifario_Exportacao();
	}
	
	/**
	 * getTarifarioModel()
	 *
	 * Obtem a classe de model utilizada para o tarifario da Exportação
	 *
	 * @name getPortoModel
	 * @access public
	 * @return object Tarifario_Exportacao_Model
	 */
	public function getTarifarioModel()
	{			
		include_once APPPATH . "/models/Tarifario/tarifario_exportacao_model.php";
		return new Tarifario_Exportacao_Model();		
	}	
	
}//END CLASS