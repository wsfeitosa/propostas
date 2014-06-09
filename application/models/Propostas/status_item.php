<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Classe responsavel para manipulação dos status dos itens da proposta
 *
 * Esta classe é quem contem as regras para manipulação status dos itens das propostas
 * sempre que objeto do tipo proposta é criado, deve ser atribuido a ele um objeto desta
 * classe.
 *
 * @package Proposta
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 14/01/2013
 * @version  versao 1.0
*/

class Status_Item extends CI_Model {
	
	protected $id = NULL;
	protected $status = NULL;
	
	public function __construct()
	{
		
	}
	
	/**
	  * Atribui um valor ao atributo id 
	  * 
	  * permite atribuir um valor ao atributo id da classe 
	  * 
	  * @name setId
	  * @access public
	  * @param int
	  * @return boolean
	  */
	public function setId( $id = NULL )
	{
		if( is_null($id) )
		{
			error_log("Id invalido atribuido ao atributo id do status do item");
			throw new Exception("Id invalido atribuido ao atributo id do status do item");
		}
		
		$this->id = $id;
		
		return TRUE;
	}
	
	/**
	 * obtem um valor do atributo id
	 *
	 * permite obter um valor do atributo id da classe
	 *
	 * @name getId
	 * @access public
	 * @param 
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Atribui um valor ao atributo status
	 *
	 * permite atribuir um valor ao atributo status da classe
	 *
	 * @name setStatus
	 * @access public
	 * @param setStatus
	 * @return boolean
	 */
	public function setStatus( $status = NULL )
	{
		if( is_null($status) )
		{
			error_log("Status invalido atribuido ao atributo status do status do item");
			throw new Exception("Status invalido atribuido ao atributo status do status do item");
		}
		
		$this->status = $status;
		
		return TRUE;
	}
    
    public function getStatus() 
    {
        return $this->status;
    }

    public function findById($id = NULL)
    {    	    	    	
    	if( ! is_null($id) )
    	{
    		$this->id = $id;
    	}

    	$this->db->
    			select("id_status_item, status")->
    			from("CLIENTES.status_itens_propostas")->
    			where('id_status_item', $this->id);
    	
    	$rs = $this->db->get();
    	
    	$linhas = $rs->num_rows();
    	
    	if( $linhas < 1 )
    	{
    		show_error("Não foi possivel recuperar o status da proposta");
    	}	
    	
    	$row = $rs->row();
    	
    	$this->id = $row->id_status_item;
    	$this->status = $row->status;
    	
    	return $this;    	
    }
	
}//END CLASS