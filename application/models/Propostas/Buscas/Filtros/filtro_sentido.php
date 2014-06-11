<?php
/**
 * Description of filtro_sentido
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Sentido extends CI_Model implements Filtro {
    
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
               $this->db->where("tarifarios_pricing.modulo", $this->filtro); 
        return $this->db->where("sentido",$this->filtro);
    }
        
}
