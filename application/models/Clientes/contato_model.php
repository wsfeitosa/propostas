<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Contato Model
 *
 * Classe que contém as regras de negócio da entidade contato
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Contato_Model
 * @version 1.0
 */

class Contato_Model extends CI_Model{
		
	public function __construct()
	{
		parent::__construct();
	}
			
	public function findByIdCliente( $id_cliente = NULL )
	{
		
		if( empty($id_cliente) )
		{
			log_message("Id do cliente invalido para buscar o contato!");
			throw new RuntimeException("Id do cliente invalido para buscar o contato!");
		}	
		
		include_once "contato.php";
		include_once APPPATH."/models/Email/email.php";
		
		$this->db->
				select("id_contato, contato, email")->
				from("CLIENTES.contato")->
				where("id_cliente", $id_cliente);
		
		$rs = $this->db->get();
				
		$contatos = Array();
		
		foreach( $rs->result() as $row )
		{
			$contato = new Contato();
			
			$contato->setId($row->id_contato);
			$contato->setNome($row->contato);
			
			/** Cria um novo objeto do tipo email **/
			$email = new Email();
			$email->setEmail($row->email);
			
			$contato->setEmail($email);
			
			$contatos[] = $contato;
			
		}	
		
		return $contatos;
		
	}
    
    /**
     * findByIdContato
     * 
     * Faz a busca pelos dados do contato através do ID do contato
     * 
     * @name findByIdContato
     * @access public
     * @param Contato $contato
     * @return Contato
     */
    public function findById(Contato $contato)
    {
        
        $id = $contato->getId();
        
        if( empty($id) )
        {
            log_message("Contato inválido para realizar a busca!");
            show_error("Contato inválido para realizar a busca!");
        }    
              
        include_once APPPATH."/models/Email/email.php";
        
        $this->db->
				select("id_contato, contato, email")->
				from("CLIENTES.contato")->
				where("id_contato", $contato->getId());
        
        $rs = $this->db->get();
                        		
		foreach( $rs->result() as $row )
		{			
			$contato->setId($row->id_contato);
			$contato->setNome($row->contato);
			
			/** Cria um novo objeto do tipo email **/
			$email = new Email();
			$email->setEmail($row->email);
			
			$contato->setEmail($email);					
		}	
		
		return $contato;
        
    }        
       
}//END CLASS