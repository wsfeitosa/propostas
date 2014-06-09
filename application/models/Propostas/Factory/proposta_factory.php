<?php
/**
 * Implementa o padr�o factory
 *
 * Implementa o padr�o factory para cria��o dos objetos que v�o representar os tipos de propostas  
 * 
 * @package Propostas/Factory
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 03/05/2013
 * @name Factory
 * @version 1.0
 */

class Proposta_Factory {
    
	public function __construct(){
		
	}
	
    /**
     * Factory
     * 
     * Fabrica os objetos que ser�o usados para manipular os tipos de propostas no sistema
     * 
     * @name factory
     * @access public
     * @param mixed
     * @return object $className
     */
    public static function factory($type)
    {   
        if (include_once dirname(dirname(__FILE__)) . '/' . $type . '.php') {
            $classname = ucwords($type);            
            return new $classname();
        } else {
            throw new Exception ("Classe {$type} n�o encontrada");
        }
    }
}