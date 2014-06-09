<?php
include_once "formato.php";
/**
 * Class Formato HTML
 *
 * Aplica transforma um relatório qualquer do sistema (Objeto Relatorio)
 * no formato HTML
 *
 * @package Formatos
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 17/01/2013
 * @name Formato_HTML
 * @version 1.0
 */
class Formato_HTML implements Formato{
	
	const path = "/var/www/html/allink/relatorios_temp";

	/**
	  * Aplicar Formato
	  * 
	  * Aplica o formato pdf a objeto do tipo relatório
	  * 
	  * @name Aplicar Formato
	  * @access public
	  * @param $layout Layout
	  * @param $relatorio Relatorio
	  * @return caminho_relatorio string
	  */
	public function aplicarFormato( Layout $layout, Relatorio $relatorio ){
		
		return $layout->aplicarLayout($relatorio);
				
	}
		
}//END CLASS