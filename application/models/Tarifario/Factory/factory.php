<?php
/**
 * Implementa o padr�o factory
 *
 * Implementa o padr�o factory para cria��o dos objetos que v�o realizar 
 * criar �s familias de objetos do tarifario
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
     * Fabrica os objetos que ser�o usados para realizar �s buscas pelas propostas
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
            throw new Exception ('Classe n�o encontrada');
        }
    }
}
