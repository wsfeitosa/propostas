<?php
/**
 * Description of filtro_filial
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Filial extends CI_Model implements Filtro {
    
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
        return $this->db->where("SUBSTRING(CLIENTES.propostas.numero_proposta,7,2)",$this->filtro);
    }

    
}
