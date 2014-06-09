<?php
/**
 * Vencimento_Search_Acordo
 *
 * Faz a busca pelos acordos que contem o vencimento especificado 
 *
 * @package models/Taxas_Locais_Acordadas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 27/05/2013
 * @version  versao 1.0
*/
include dirname(dirname(__FILE__)) . "/Interfaces/search_driver.php";

class Vencimento_Search_Acordo extends CI_Model implements SearchDriver {
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
	}
	
	/**
	 * search
	 *
	 * Pesquisa os acordos baseado no vencimento do acordo
	 *
	 * @name search
	 * @access public
	 * @param string $dado_pesquisa
	 * @throws InvalidArgumentException
	 * @return Array
	 */
	public function search( $dado_pesquisa = NULL, $vencidos = "N" )
	{
						
		$acordos_encontrados = Array();

		$validade = new DateTime($dado_pesquisa);
		
		/** Busca os acordos com a data de validade especificada **/
		$this->db->
				select("id")->
				from("CLIENTES.acordos_taxas_locais_globais")->
				where("registro_ativo","S")->
				where("validade",$validade->format('Y-m-d'));
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return $acordos_encontrados;
		}	
		
		$acordos_facade = new Acordos_Taxas_Facade();
		
		foreach( $rs->result() as $resultSet )
		{
			$acordo = $acordos_facade->recuperarAcordoTaxasLocais($resultSet->id);
			
			if( ! array_key_exists($resultSet->id, $acordos_encontrados) )
			{
				$acordos_encontrados[$resultSet->id] = $acordo;
			}
		}	
		
		return $acordos_encontrados;
		
	}
	
}//END CLASS