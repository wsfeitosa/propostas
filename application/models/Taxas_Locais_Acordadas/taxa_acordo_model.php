<?php
/**
 * Taxa_Acordo_Model
 *
 * Aplica às operações de banco de dados às taxas dos acordos 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";
include_once APPPATH."/models/Taxas/taxa_model.php";

class Taxa_Acordo_Model extends CI_Model implements Database_Operations {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * save
	 *
	 * Salva uma taxa local de um acordo de taxas locais globais
	 *
	 * @name save
	 * @access public
	 * @param $taxa Entity
	 * @return boolean
	 */ 	
	public function save(Entity $taxa)
	{
		
		$dados_para_salvar = Array(
									"id_taxa_adicional" => $taxa->getId(),
									"id_unidade" => $taxa->getUnidade()->getId(),
									"id_moeda" => $taxa->getMoeda()->getId(),
									"valor" => $taxa->getValor(),
									"valor_minimo" => $taxa->getValorMinimo(),
									"valor_maximo" => $taxa->getValorMaximo(),
									"id_acordos_taxas_locais" => $taxa->getIdItem()
		);
		
		return $this->db->insert("CLIENTES.taxas_x_acordos_taxas_locais_globais",$dados_para_salvar);
						
	}
	/**
	 * findById
	 *
	 * Busca às taxas do acordo, pelo id do acordo
	 *
	 * @name findById
	 * @access public
	 * @param Entity $acordo
	 * @return int
	 */	
	public function findById( Entity $acordo )
	{
						
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			throw new InvalidArgumentException("Id do acordo não definido préviamente para realizar a consulta!");
		}
		
		$rs = $this->db->get_where("CLIENTES.taxas_x_acordos_taxas_locais_globais","id_acordos_taxas_locais = {$acordo->getId()}");
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhuma Taxa encontrada para o acordo: ".$acordo->getId());
		}
		
		/** Importa os models de taxas **/
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/taxa_local");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		
		$taxas_encontradas = new ArrayObject(Array());
		
		foreach ( $rs->result() as $taxas )
		{
			
			$taxa = new Taxa_Local();
			$taxa->setId((int)$taxas->id_taxa_adicional);
			
			$taxa->setValor((float)$taxas->valor);
			$taxa->setValorMinimo((float)$taxas->valor_minimo);
			$taxa->setValorMaximo((float)$taxas->valor_maximo);
			
			$taxa_model = new Taxa_Model();
			$taxa_model->obterNomeTaxaAdicional($taxa);
			
			$moeda = new Moeda();
			
			$moeda_model = new Moeda_Model();
			
			$moeda->setId((int)$taxas->id_moeda);
			
			$moeda_model->findById($moeda);
			
			$taxa->setMoeda($moeda);
			
			$unidade = new Unidade();
			
			$unidade_model = new Unidade_Model();
			
			$unidade->setId((int)$taxas->id_unidade);
			
			$unidade_model->findById($unidade);
			
			$taxa->setUnidade($unidade);
			
			$taxas_encontradas->append($taxa);
		}	
		
		return $taxas_encontradas;
		
	}
	
	public function update( Entity $taxa ){}
	public function delete( Entity $taxa ){}
	
}