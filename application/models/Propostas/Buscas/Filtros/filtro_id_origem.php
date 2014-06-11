<?php
/**
 * Description of filtro_id_origem
 *
 * @author wsfall
 */
class Filtro_Id_Origem extends CI_Model implements Filtro {
    
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
        return $this->db->where("tarifarios_pricing.id_place_receipt", $this->filtro);
    }
    
}
