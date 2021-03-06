<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Classe que representa a entidade Gestor Filial no sistema
 *
 * Esta classe reprenta o tipo de usuario gestor de filial no sistema
 * e herda seus m�todos e propriedades da classe Usuario e tamb�m 
 * implementa a interface Gestor
 *
 * @package Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Gestor_Filial
 * @version 1.0
*/
include_once "usuario.php";
include_once "gestor.php";

class Gestor_Filial extends Usuario implements Gestor{
	
	/**
	  * Autorizar Desbloqueio
	  * 
	  * Autoriza ou nega as solicita��es de desbloqueio dos usu�rios
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