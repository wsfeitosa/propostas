<?php
/**
 * Description of filtro_id_vendedor_imp
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Id_Vendedor_Imp extends CI_Model implements Filtro {
    
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
        return $this->db->join("CLIENTES.clientes", "clientes.id_cliente = clientes_x_propostas.id_cliente")->
               where("customer",$this->filtro);
    }
    
}

