<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Classe que representa a entidade Pricing no sistema
 *
 * Esta classe reprenta o tipo de usuario Pricing no sistema
 * e herda seus métodos e propriedades da classe Usuario e também 
 * implementa a interface Gestor
 *
 * @package Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Pricing
 * @version 1.0
*/
include_once "usuario.php";
include_once "gestor.php";

class Pricing extends Usuario implements Gestor{
	
	/**
	  * Autorizar Desbloqueio
	  * 
	  * Autoriza ou nega as solicitações de desbloqueio dos usuários
	  * 
	  * @name autorizarDesbloqueio
	  * @access public
	  * @param string, Solicitacao
	  * @return boolean
	  */
	public function autorizarDesbloqueio( $decisao, Solicitacao $solicitacao )
	{
		if( ! is_bool($decisao) )
		{
			return FALSE;
		}	
		
		if( ! $decisao )
		{
			return FALSE;
		}	
		
		return TRUE;
		
	}
}