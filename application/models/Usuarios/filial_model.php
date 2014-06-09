<?php
/**
 * Filial_Model
 *
 * Aplica às regras de negócio a o objeto filial no sistema 
 *
 * @package models/Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 27/05/2013
 * @version  versao 1.0
*/
class Filial_Model extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * findById
	 *
	 * Busca a filial pelo id da filial
	 *
	 * @name findById
	 * @access public
	 * @param Filial $filial
	 * @return void
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */ 	
	public function findById(Filial $filial) 
	{
		
		$id_filial = $filial->getId();
		
		if( empty($id_filial) )
		{
			throw new InvalidArgumentException("Id da filial não definido para realizar a consulta!");
		}	
		
		$rs = $this->db->get_where("USUARIOS.filiais","id_filial = {$filial->getId()}");
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhuma Filial Encontrada!");
		}	
		
		$row = $rs->row();
		
		$filial->setNomeFilial($row->nomefilial);
		$filial->setSiglaFilial($row->filial);
		
	}	
	
}//END CLASS