<?php
/**
 * Classe Portos Taxas
 *
 * Classe que trata das regras de negócio dos portos do sistema para o cadastro de 
 * acordos de taxas locais 
 *
 * @package Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 13/05/2013
 * @version  versao 1.0
*/
class Portos_Taxas extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function obterPortosDasTaxasLocais()
	{
		
		$this->db->
				select("id_porto, porto")->
				from("USUARIOS.portos")->
				where("ativo","S");
		
		$rs = $this->db->get();
		
		$array_portos = Array("0" => "TODOS");
		
		$result = $rs->result();
		
		foreach( $result as $porto )
		{
			$array_portos[$porto->id_porto] = $porto->porto;
		}	
		
		return $array_portos;
						
	}
	
}//END FUNCTION