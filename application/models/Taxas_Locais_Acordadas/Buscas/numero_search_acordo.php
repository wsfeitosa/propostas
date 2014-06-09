<?php
/**
 * Numero
 *
 * Busca os acordos pelo número de cadastro do acordo de taxas locais implementa a interface
 * search_driver 
 *
 * @package models/Taxas_Locais_Acordadas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 24/05/2013
 * @version  versao 1.0
*/
include dirname(dirname(__FILE__)) . "/Interfaces/search_driver.php";

class Numero_Search_Acordo extends CI_Model implements SearchDriver {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
	}
	
	/**
	 * search
	 *
	 * Pesquisa os acordos baseado no número do acordo
	 *
	 * @name search
	 * @access public
	 * @param string $dado_pesquisa
	 * @throws InvalidArgumentException
	 * @return Array
	 */ 	
	public function search( $dado_pesquisa = NULL, $vencidos = "N" ) 
	{
		
		if( is_null($dado_pesquisa) )
		{
			throw new InvalidArgumentException("Nenhum número informado para realizar a pesquisa pelos acordos de taxas!");
		}	
		
		$acordos_encontrados = Array();
		
		$this->db->
				select("id")->
				from("CLIENTES.acordos_taxas_locais_globais")->
				where("registro_ativo","S")->
				like("numero",$dado_pesquisa);

		if( strtoupper($vencidos) == "N" )
		{
			$this->db->where("acordos_taxas_locais_globais.validade >=",date('Y-m-d'));
		}		
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return $acordos_encontrados;
		}	
		
		$acordos_facade = new Acordos_Taxas_Facade();
		
		foreach( $rs->result() as $resultSet )
		{
			$acordo = $acordos_facade->recuperarAcordoTaxasLocais($resultSet->id);
			
			$acordos_encontrados[] = $acordo;
		}	
		
		return $acordos_encontrados;
	}
	
}//END CLASS