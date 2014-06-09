<?php
/**
 * Cliente
 *
 * Faz a busca pelos acordos que contem o cliente especificado 
 *
 * @package models/Taxas_Locais_Acordadas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 24/05/2013
 * @version  versao 1.0
*/
include dirname(dirname(__FILE__)) . "/Interfaces/search_driver.php";

class Cliente_Search_Acordo extends CI_Model implements SearchDriver {
	
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

		/** Busca pelos clientes cujo o nome corresponde ao informado **/
		$clientes_encontrados_acordo = $this->buscarClientesPeloNome($dado_pesquisa);
		
		$acordos_encontrados = Array();
		
		$acordos_facade = new Acordos_Taxas_Facade();
		
		/** Verifica quais dos clientes encontrados possui acordo de taxas **/
		foreach( $clientes_encontrados_acordo as $cliente )
		{
			
			$this->db->
					select("acordos_taxas_locais_globais.id")->
					from("CLIENTES.clientes_x_acordos_taxas_locais_globais")->
					join("CLIENTES.acordos_taxas_locais_globais", "acordos_taxas_locais_globais.id = clientes_x_acordos_taxas_locais_globais.id_acordos_taxas_locais")->
					where("registro_ativo","S")->
					where("clientes_x_acordos_taxas_locais_globais.id_cliente",$cliente->getId());

			if( strtoupper($vencidos) == "N" )
			{
				$this->db->where("acordos_taxas_locais_globais.validade >=",date('Y-m-d'));
			}	

			$this->db->group_by("acordos_taxas_locais_globais.id");
			
			$rs = $this->db->get();
			
			if( $rs->num_rows() < 0 )
			{
				continue;
			}	
			
			foreach( $rs->result() as $resultSet )
			{
				$acordo = $acordos_facade->recuperarAcordoTaxasLocais($resultSet->id);
				
				if( ! array_key_exists($resultSet->id, $acordos_encontrados) )
				{
					$acordos_encontrados[$resultSet->id] = $acordo;
				}	
								
			}	
									
		}	
		
		return $acordos_encontrados;
		
	}
	
	/**
	 * buscarClientePeloNome
	 *
	 * Pesquisa os clientes pelo nome informado através do conjunto de classes de cliente
	 *
	 * @name buscarClientesPeloNome
	 * @access protected
	 * @param string $nome
	 * @throws InvalidArgumentException
	 * @return Array
	 */
	protected function buscarClientesPeloNome( $nome = "" )
	{
		
		if( empty($nome) )
		{
			throw new InvalidArgumentException("Nenhum cliente Informado para realiza a busca pelo acordo!");
		}	
				
		$this->load->model("Clientes/cliente_model");
						
		$cliente_model = new Cliente_Model();
		return $cliente_model->findByName($nome);		
				
	}
	
}//END CLASS