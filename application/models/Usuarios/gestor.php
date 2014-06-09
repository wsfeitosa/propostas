<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Inteface que representa a entidade Gestor
 *
 * Esta interface implementa os métodos necessários para que os 
 * gestores possam desbloquear propostas
 *
 * @package Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Gestor
 * @version 1.0
 */
interface Gestor {
	public function autorizarDesbloqueio( $decisao, Solicitacao $solicitacao );
}