<?php
include_once "formato.php";
include "/var/www/html/allink/Libs/MPDF/mpdf.php";
/**
 * Class Formato PDF
 *
 * Aplica transforma um relatório qualquer do sistema (Objeto Relatorio)
 * no formato pdf
 *
 * @package Formatos
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 17/01/2013
 * @name Formato_PDF
 * @version 1.0
 */
class Formato_PDF implements Formato{
	
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
		
		$layout_formatado = $layout->aplicarLayout($relatorio);
		
		$mpdf = new mPDF();
		$mpdf->allow_charset_conversion = TRUE;
		$mpdf->WriteHTML(utf8_encode($layout_formatado));
		$mpdf->Output( self::path . "/" . $relatorio->obterNome().".pdf",'F');
		
		return "/relatorios_temp/" . $relatorio->obterNome() . ".pdf";
		
	}
		
}//END CLASS