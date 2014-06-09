<?php
/**
 * Portos_Acordos_Model
 *
 * Aplica às operações de banco de dados na entidade Portos_Acordos_Entity 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 21/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Portos_Acordos_Model extends CI_Model implements Database_Operations {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function save( Entity $entity )
	{
		
		$dados_para_salvar = Array(
									"id_porto" => $entity->getPorto()->getId(),
									"id_acordo" => $entity->getAcordo()->getId(),
		);
		
		return $this->db->insert("CLIENTES.portos_x_acordos_taxas_globais", $dados_para_salvar);
				
	}
	
	public function findById( Entity $acordo )
	{
		
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			throw new InvalidArgumentException("Id do acordo não definido préviamente para realizar a consulta!");
		}
		
		$rs = $this->db->get_where("CLIENTES.portos_x_acordos_taxas_globais","id_acordo = {$acordo->getId()}");
		
		$portos_encontrados = new ArrayObject(Array());
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhum Porto encontrado para o acordo: ".$acordo->getId());
		}
		
		$this->load->model("Tarifario/porto");
		$this->load->model("Tarifario/Factory/concrete_factory");
		$this->load->model("Tarifario/Factory/factory");
				
		$factory = Factory::factory($acordo->getSentido());
		$concrete_factory = new Concrete_Factory();
		
		$result = $rs->result();
		
		foreach( $result as $porto_encontrado )
		{
			$porto_model = $concrete_factory->CreatePortoModel($factory);

			$porto = new Porto();			
			$porto->setId((int)$porto_encontrado->id_porto);

			switch($acordo->getSentido())
			{
				case "IMP":
					$hub = "destino";
				break;
				
				case "EXP":
					$hub = "origem";
				break;	

				default:
					throw new InvalidArgumentException("Um argumento diferente de IMP ou EXP foi informado!");
			}
			
			$porto_model->findById($porto,$hub);
			
			$portos_encontrados->append($porto);			
		}	
		
		return $portos_encontrados;
		
	}
	
	public function update( Entity $bean ){}
	public function delete( Entity $bean ){}
	
}//END CLASS