<?php
/**
 * Implementa o padrão factory
 *
 * Implementa o padrão factory para criação dos objetos que vão representar 
 * às propostas no sistema 
 * 
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 21/03/2013
 * @name Proposta_Factory
 * @version 1.0
 */

class Proposta_Factory {
    
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
        if (include_once dirname(__FILE__).'/' . $type . '.php') {
            $classname = ucwords($type);
            return new $classname;
        } else {
            throw new Exception ('Classe não encontrada');
        }
    }
}

