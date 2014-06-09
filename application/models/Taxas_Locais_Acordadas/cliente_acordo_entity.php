<?php
/**
 * Cliente_Acordo_Entity
 *
 * Classe que representa a entidade cliente_acordo no sistema 
 *
 * @package models/Taxas_locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/Entity.php";

class Cliente_Acordo_Entity implements Entity {
	
	protected $id, $id_acordo, $id_cliente;
	
	public function __construct()
	{
		
	}
	
	public function setId( $id )
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setIdAcordo( $id_acordo )
	{
		$this->id_acordo = $id_acordo;
		return $this;
	}
	
	public function getIdAcordo()
	{
		return $this->id_acordo;
	}
	
	public function setIdCliente( $id_cliente )
	{
		$this->id_cliente = $id_cliente;
		return $this;
	}
	
	public function getIdCliente()
	{
		return $this->id_cliente;
	}
	
}//END CLASS