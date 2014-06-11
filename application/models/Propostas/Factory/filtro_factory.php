<?php
/**
 * Description of filtro_factory
 *
 * @author wsfall
 */
class Filtro_Factory {
    
    public function __construct(){
		
	}
	
    /**
     * Factory
     * 
     * Fabrica os objetos que serão usados para manipular os tipos de filtros.
     * 
     * @name factory
     * @access public
     * @param mixed
     * @return object $className
     */
    public static function factory($filter)
    {   
        if (@include_once dirname(dirname(__FILE__)) . '/Buscas/Filtros/filtro_' . $filter . '.php') {
            $classname = ucwords("Filtro_".$filter);            
            return new $classname();
        } else {
            throw new Exception ("Classe {$type} não encontrada");
        }
    }
}
