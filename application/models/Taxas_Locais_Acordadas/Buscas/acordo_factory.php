<?php
/**
 * Implementa o padr�o factory
 *
 * Implementa o padr�o factory para cria��o dos objetos que v�o 
 * realizar �s buscas pelos acordos de taxas locais
 * 
 * @package models/Taxas_Locais_Acordadas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 24/05/2013
 * @name Acordo_Factory
 * @version 1.0
 */

class Acordo_Factory {
    
	public function __construct(){
		
	}
	
    /**
     * Factory
     * 
     * Fabrica os objetos que ser�o usados para realizar �s buscas pelos acordos
     * 
     * @name acordo_factory
     * @access public
     * @param mixed
     * @return object $className
     */
    public static function acordo_factory($type)
    {
        if (include_once dirname(__FILE__) . "/" . strtolower($type) . '_search_acordo.php') {
            $classname = ucwords($type."_search_acordo");
            return new $classname;
        } else {
            throw new Exception ('Classe n�o encontrada');
        }
    }
}