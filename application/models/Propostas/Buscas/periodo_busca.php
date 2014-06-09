<?php
/**
 * Realiza a busca pelo periodo
 *
 * Realiza a busca pelo nome do cliente e implementa a interface de buscas
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Periodo_Busca
 * @version 1.0
 */

class Peiodo_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function buscar($periodo, $sentido)
    {
        echo get_class($this);
    }        
    
}


