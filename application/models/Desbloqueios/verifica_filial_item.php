<?php
/**
 * Verifica Filial Item
 *
 * Verifica a filial de um item de uma proposta ou de um acordo de taxas locais 
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 11/06/2013
 * @version  versao 1.0
*/
class Verifica_Filial_Item extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * buscarDadosDoItemPeloId
	 * 
	 * @name buscarDadosDoItemPeloId
	 * @access public
	 * @param int $id_item
	 * @return Array
	 */ 	
	public function buscarDadosDoItemPeloId($id_item = NULL, $modulo = NULL) 
	{
		
		if( is_null($id_item) || is_null($modulo) )
		{
			show_error("Impossivel recuperar o item do desbloqueio");
		}	
		
		switch( $modulo )
		{
			case "proposta":	
				
				$sql = "SELECT
							tarifarios_pricing.*, itens_proposta.*
						FROM
							CLIENTES.itens_proposta
							INNER JOIN FINANCEIRO.tarifarios_pricing 
							ON tarifarios_pricing.id_tarifario_pricing = itens_proposta.id_tarifario_pricing
						WHERE
							itens_proposta.id_item_proposta = ".$id_item;
				
				$rs = $this->db->query($sql);		

				if($rs->num_rows() < 1)
				{
					show_error("Não foi possivel recuperar a proposta");
				}	
				
				return $rs->row();
			break;

			case "taxa_local":
				$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
				$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
				
				$acordo_taxas_facade = new Acordos_Taxas_Facade();
				
				$acordo_entity = $acordo_taxas_facade->recuperarAcordoTaxasLocais((int)$id_item);
				
				return $acordo_entity;
			break;

			default:
				show_error("Módulo inválido para recuperar o item");
		}
		
	}
	
}//END CLASS