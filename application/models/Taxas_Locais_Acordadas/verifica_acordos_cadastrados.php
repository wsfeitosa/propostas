<?php
/**
 * Verifica_Acordos_Cadastrados
 *
 * Verifica se já existem acordos cadastrados para um determinado cliente 
 *
 * @package verifica_acordos_cadastrados.php
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 27/05/2013
 * @version  versao 1.0
*/
class Verifica_Acordos_Cadastrados extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
	}
	
	/**
	 * BuscarAcordosCadastrados
	 *
	 * Procura por acordos ja cadastrados para um determinado cliente
	 *
	 * @name BuscarAcordosCadastrados
	 * @access public
	 * @param Cliente $cliente
	 * @param string $sentido
	 * @param Porto $porto
	 * @param DateTime $validade 
	 * @return Acordo_Entity $acordo
	 */ 	
	public function BuscarAcordosCadastrados( Cliente $cliente, $sentido, Porto $porto, DateTime $validade )
	{
		
		$this->db->
				select("acordos_taxas_locais_globais.id")->
				from("CLIENTES.acordos_taxas_locais_globais")->
				join("CLIENTES.clientes_x_acordos_taxas_locais_globais", "acordos_taxas_locais_globais.id = clientes_x_acordos_taxas_locais_globais.id_acordos_taxas_locais")->
				join("CLIENTES.portos_x_acordos_taxas_globais", "acordos_taxas_locais_globais.id = portos_x_acordos_taxas_globais.id_acordo")->
				where("validade <=", $validade->format('Y-m-d'))->
				where("id_cliente",$cliente->getId())->
				where("id_porto",$porto->getId());
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		
		$acordo_model = new Acordos_Taxas_Facade();
		
		$acordo = $acordo_model->recuperarAcordoTaxasLocais($rs->row()->id);
		
		return $acordo;
		
	}
	
}//END CLASS