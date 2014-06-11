<?php
/**
 * Description of filtro_id_cliente
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Id_Cliente extends CI_Model implements Filtro {
    
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
        return $this->db->where("clientes_x_propostas.id_cliente",  $this->filtro);
    }
    
}
