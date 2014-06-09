<?php
/**
 * Autorizacao_Taxa
 *
 * Implementa a interface Autorizacao, autoriza às solicitações de desbloqueio
 * das taxas  
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 05/06/2013
 * @version  versao 1.0
*/
include_once APPPATH . "models/autorizacao.php";

class Autorizacao_Taxa extends CI_Model implements Autorizacao {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function verificar_autorizacao_desbloqueio(Usuario $usuario)
	{
		
	}
	
	public function autorizar_desbloqueio(Desbloqueio_Entity $entity)
	{
		
	}
	
}//END CLASS