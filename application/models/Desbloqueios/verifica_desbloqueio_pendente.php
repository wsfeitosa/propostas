<?php
/**
 * Verifica Desbloqueio Pendente
 *
 * Verifica se existem desbloqueios pendentes para uma proposta.
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 31/07/2013
 * @version  versao 1.0
*/
class Verifica_Desbloqueio_Pendente extends CI_Model {
		 	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * ExisteDesbloqueioPendente
	 *
	 * Verifica se existe algum desbloqueio pendente para uma proposta
	 *
	 * @name VerificarDesbloqueioPendente
	 * @access public
	 * @param int $id_proposta
	 * @return bool
	 */ 	
	public function ExisteDesbloqueioPendente($id_proposta = NULL)
	{
		if( is_null($id_proposta) || ! is_numeric($id_proposta) )
		{
			throw new InvalidArgumentException("Id da proposta inválido para buscar pelos desbloqueios!");
		}	
		
		$item_pendente = FALSE;
		
		/** Procura por algum desbloqueio de validade **/
		$this->db->
				select("id_item_proposta")->
				from("CLIENTES.itens_proposta")->
				where("id_proposta",$id_proposta)->
				where("id_status_item",2);
		
		$rs = $this->db->get();
		
		$itens_pendentes_encontrados = $rs->num_rows();
		
		if( $itens_pendentes_encontrados > 0 )
		{
			$item_pendente = TRUE;
		}	
		
		return $item_pendente;
		
	}

	public function existeDesbloqueioTaxaPendente( $id_item = NULL, $id_taxa = NULL )
	{

		if( is_null($id_item) || is_null($id_taxa) )
		{
			throw new InvalidArgumentException("Dados informados insulficientes para verificar os desbloqueios pendentes");
		}	

		$this->db->
				select()->
				from("CLIENTES.desbloqueios_taxas")->
				where("id_taxa_item", $id_item)->
				where("id_taxa", $id_taxa)->
				where("status","P")->
				where("modulo","proposta");

		$rs = $this->db->get();
		
		$linhas_encontradas = $rs->num_rows();

		if( $linhas_encontradas > 0 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}		

	}

	public function existeDesbloqueioValidadePendente($id_item = NULL)
	{

		if( is_null($id_item) )
		{
			throw new InvalidArgumentException("Dados informados insulficientes para verificar os desbloqueios de validades pendentes");
		}	

		$this->db->
				select()->
				from("CLIENTES.desbloqueios_validades")->
				where("id_item", $id_item)->				
				where("status","P")->
				where("modulo","proposta");

		$rs = $this->db->get();
		
		$linhas_encontradas = $rs->num_rows();

		if( $linhas_encontradas > 0 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}		

	}
	
}//END CLASS