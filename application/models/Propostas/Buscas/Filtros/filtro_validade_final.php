<?php
/**
 * Description of filtro_data_inicial
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Validade_Final extends CI_Model implements Filtro {
    
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
        $data_final = new DateTime($this->filtro);
        
        return $this->db->where("itens_proposta.validade <=",$data_final->format('Y-m-d'));
    }

    
}