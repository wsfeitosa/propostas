<?php
/**
 * Description of filtro_id_grupo_comercial
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Id_Grupo_Comercial extends CI_Model implements Filtro {
    
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
               where("id_grupo_comercial",$this->filtro);
    }

    
}
