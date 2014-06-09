<?php
/**
 * Interface_Tarifario_Model
 *
 * Define uma interface comum para todas às classes de model do tipo Tarifario
 *
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 26/03/2013
 * @name Interface_Tarifario_Model
 * @version 1.0
 */
interface Interface_Tarifario_Model {
	
	public function obterTarifarios(Rota $rota, DateTime $inicio, DateTime $validade);
	
	public function findById( Tarifario $tarifario , $classificacao_cliente = "A", DateTime $inicio, DateTime $validade );
	
}