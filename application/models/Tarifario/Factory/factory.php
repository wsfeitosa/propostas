<?php
/**
 * Implementa o padrão factory
 *
 * Implementa o padrão factory para criação dos objetos que vão realizar 
 * criar às familias de objetos do tarifario
 * 
 * @package Tarifario/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 21/03/2013
 * @name Factory
 * @version 1.0
 */

class Factory {
    
	public function __construct(){
		
	}
	
    /**
     * Factory
     * 
     * Fabrica os objetos que serão usados para realizar às buscas pelas propostas
     * 
     * @name factory
     * @access public
     * @param mixed
     * @return object $className
     */
    public static function factory($type)
    {
        if (include_once dirname(__FILE__) . '/concrete_' . strtolower($type) . 'ortacao_factory.php') {
            $classname = "Concrete_" . ucwords($type)  . 'ortacao_Factory';
            return new $classname;
        } else {
            throw new Exception ('Classe não encontrada');
        }
    }
}
