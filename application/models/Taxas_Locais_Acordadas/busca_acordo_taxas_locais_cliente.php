<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Busca_Acordo_Taxas_Locais_Cliente
 *
 * Busca os acordos de taxas locais que o cliente possa ter cadastrado no sistema
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/05/2013
 * @version  versao 1.0
*/
class Busca_Acordo_Taxas_Locais_Cliente extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
	}

	/**
	 * buscarAcordoTaxasCliente
	 *
	 * Esta função busca por acordos cadastrados para um determinado cliente
	 *
	 * @name buscarAcordoTaxasCliente
	 * @access public
	 * @param string $sentido
	 * @param Cliente $cliente
	 * @param Porto $porto
	 * @param DateTime $data_inicial
	 * @param DateTime $validade 
	 * @return Acordo_Taxas_Entity $acordo
	 * @throws RuntimeException
	 */ 	
	public function buscarAcordoTaxasCliente( $sentido, Cliente $cliente, Porto $porto, DateTime $data_inicial, DateTime $validade )
	{
		
		/** Verifica se existe acordo para o cliente informado **/
		$rs = $this->db->get_where("CLIENTES.clientes_x_acordos_taxas_locais_globais","id_cliente = {$cliente->getId()}");
		
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	

		$acordos_encontrados_porto = Array();
		/** Se algum acordo para o cliente foi encontrado, então pesquisa pelo porto **/
		$rowSet = $rs->result(); 
		
		foreach( $rowSet as $acordos_cliente_result )
		{
			
			$this->db->
						select("portos_x_acordos_taxas_globais.*")->
						from("CLIENTES.portos_x_acordos_taxas_globais")->
						where("id_porto", $porto->getId())->
						where("id_acordo",$acordos_cliente_result->id_acordos_taxas_locais)->
						group_by("id_acordo");
			
			$result_porto = $this->db->get();
			
			if( $result_porto->num_rows() < 1 )
			{
				continue;
			}	
									
			foreach( $result_porto->result() as $result )
			{
				array_push($acordos_encontrados_porto, $result->id_acordo);
			}	
			
		}	

		$acordos_encontrados_porto = array_unique($acordos_encontrados_porto);
		
		if( count($acordos_encontrados_porto) < 1 )
		{
			return FALSE;
		}	
		
		/** Se foi encontrado um acordo para o cliente no porto informado, então pesquisa pelo período **/
		foreach($acordos_encontrados_porto as $acordo_porto)
		{
			
			$this->db->
					select("id")->
					from("CLIENTES.acordos_taxas_locais_globais")->
					where("data_inicial <=", $data_inicial->format('Y-m-d'))->
					where("validade >=", $validade->format('Y-m-d'))->
					where("id",$acordo_porto)->
					where("sentido",$sentido)->
					where("registro_ativo", "S");
			
			$result_acordo_data = $this->db->get();
			
			if( $result_acordo_data->num_rows() < 1 )
			{
				continue;
			}
			
			$facade = new Acordos_Taxas_Facade();
			
			$acordo_encontrado = $facade->recuperarAcordoTaxasLocais($result_acordo_data->row()->id);
			
			return $acordo_encontrado;
						
		}	
		
		return FALSE;
		
	}
	
}