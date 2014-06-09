<?php
interface SearchDriver{
	/**
	 * @name search
	 * @param $dado_pesquisa
	 * @return ArrayObject $acordos_encontrados
	 */
	public function search( $dado_pesquisa = NULL, $vencidos = "N" );
}