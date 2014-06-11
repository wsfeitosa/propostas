<?php
/**
 * Description of filtro_data_inicial
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Data_Inicial_Inicio extends CI_Model implements Filtro {
    
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
        $data_inicial = new DateTime($this->filtro);
        
        return $this->db->where("itens_proposta.data_inicial >=",$data_inicial->format('Y-m-d'));
    }

    
}
