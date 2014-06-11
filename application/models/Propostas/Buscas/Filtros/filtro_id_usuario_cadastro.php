<?php
/**
 * Description of filtro_id_usuario_cadastro
 *
 * @author wsfall
 */
include_once dirname(dirname(__FILE__))."/filtro.php";

class Filtro_Id_Usuario_Cadastro extends CI_Model implements Filtro {
    
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
        return $this->db->where("propostas.id_usuario_inclusao",$this->filtro);
    }
    
}
