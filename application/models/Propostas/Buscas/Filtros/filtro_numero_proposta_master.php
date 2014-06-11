<?php
/**
 * Description of filtro_numero_proposta_master
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Numero_Proposta_Master extends CI_Model implements Filtro {
    
    protected $filtro = null;

    public function __construct() 
    {
        parent::__construct();
    }

    public function novo($filtro) 
    {
        $this->filtro = $filtro;
    }

    public function retornar() 
    {
        return $this->db->like("propostas.numero_proposta",$this->filtro);
    }
    
}
