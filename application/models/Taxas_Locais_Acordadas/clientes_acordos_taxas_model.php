<?php
/**
 * Clientes_Acordos_Taxas_model
 *
 * Aplica às operações de banco de dados a os clientes dos acordos de taxas 
 *
 * @package models/Taxas_locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Clientes_Acordos_Taxas_Model extends CI_Model implements Database_Operations{
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function save( Entity $acordo_cliente )
	{
		
		$dados_para_salvar = Array(
									"id_cliente" => $acordo_cliente->getIdCliente(),
									"id_acordos_taxas_locais" => $acordo_cliente->getIdAcordo()
		);
		
		return $this->db->insert("CLIENTES.clientes_x_acordos_taxas_locais_globais",$dados_para_salvar);
		
	}
	/**
	 * findById
	 *
	 * Encontra os clientes utilizados em um acordo baseado no id do acordo
	 *
	 * @name findById
	 * @access public
	 * @param Entity $acordo
	 * @return ArrayObject $clientes_encontrados
	 */	
	public function findById( Entity $acordo )
	{
		
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			throw new InvalidArgumentException("Id do acordo não definido préviamente para realizar a consulta!");
		}	
		
		$rs = $this->db->get_where("CLIENTES.clientes_x_acordos_taxas_locais_globais","id_acordos_taxas_locais = {$acordo->getId()}");
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhum Cliente encontrado para o acordo: ".$acordo->getId());
		}	
		
		$clientes_acordos_encontrados = new ArrayObject(Array());
		
		$this->load->model("Taxas_Locais_Acordadas/cliente_acordo_entity");
		
		foreach( $rs->result() as $cliente_x_acordo )
		{
			$cliente_acordo_entity = new Cliente_Acordo_Entity();
			$cliente_acordo_entity->setId((int)$cliente_x_acordo->id);
			$cliente_acordo_entity->setIdCliente((int)$cliente_x_acordo->id_cliente);
			$cliente_acordo_entity->setIdAcordo((int)$cliente_x_acordo->id_acordos_taxas_locais);
			
			$clientes_acordos_encontrados->append($cliente_acordo_entity);
		}	
		
		return $clientes_acordos_encontrados; 
		
	}
	
	public function update( Entity $bean ){}
	public function delete( Entity $bean ){}
	
}//END CLASS