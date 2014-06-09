<?php
/**
 * Sentido Factory
 *
 * Inteface que define como serão criados todos os objetos dependentes ou
 * relacionados, por exemplo uma classe tarifario_importacao só deveria ser usada com 
 * um porto_model_importação
 *
 * @package Tarifario/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 22/03/2013
 * @name Sentido_Factory
 * @version 1.0
 */
interface Sentido_Factory{
	
	public function getTarifarioDAO();
	
	public function getTarifarioModel();
	
	public function getPortoModel();
	
}