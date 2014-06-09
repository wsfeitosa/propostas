<?php
/**
 * Portos_Acordos_Entity
 *
 * Representa a entidade (tabela) de portos onde são salvos os portos 
 * relacionados a os acordos no sistema.  
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 21/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/Entity.php";

class Portos_Acordos_Entity implements Entity {
	
	protected $acordo; 
	protected $porto;
	
	public function __Construct() {
		
	}
	
	public function setPorto( Porto $porto )
	{
		$this->porto = $porto;
		return $this;
	}

	public function getPorto()
	{
		return $this->porto;
	}
	
	public function setAcordo( Acordo_Taxas_Entity $acordo )
	{
		$this->acordo = $acordo;
		return $this;
	}
	
	public function getAcordo()
	{
		return $this->acordo;
	}
	
}//END CLASS