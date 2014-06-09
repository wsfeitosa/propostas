<?php
ini_set("memory_limit","512M");
/**
 * Porto_Exportacao_Model
 *
 * Classe que contém às regras de negócio dos portos de exportação do 
 * módulo de propostas, implementa a Interface_Porto para que tenha 
 * compatibilidade com a de importação
 *
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 25/03/2013
 * @name Porto_Exportacao_model
 * @version 1.0
 */
include_once APPPATH."/models/Tarifario/interface_porto.php";

class Porto_Exportacao_Model extends CI_Model implements Interface_Porto {
		
	public function __construct(){
		parent::__construct();		
	}
	
	/**
	 * Find By Id
	 *
	 * Busca os dados de um porto na exportação baseado no id
	 *
	 * @name findById
	 * @access public
	 * @param Porto
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function findById( Porto $porto, $hub = FALSE )
	{
		
		if( ! $hub )
		{
			throw new InvalidArgumentException("Porto informado inválido para realizar a pesquisa!");
		}
	
		switch ( strtolower($hub) )
		{
			case "origem":
				$this->db->
						select("id_porto,porto,un_code")->						
						from("USUARIOS.portos")->
						where("portos.id_porto",$porto->getId());	
						
				$porto->setPais("BRASIL");						
			break;

			case "embarque":
				$this->db->
						select("id_porto,porto,un_code")->
						from("USUARIOS.portos")->
						where("portos.id_porto",$porto->getId());
				
				$porto->setPais("BRASIL");		
			break;
			
			case "desembarque":
				$this->db->
						select("id_via as id_porto, vias.via as porto, vias.un_code_via as un_code, paises.pais")->
						from("GERAIS.vias")->
						join("GERAIS.paises","paises.id_pais = vias.id_pais")->
						where("vias.id_via",$porto->getId());	
			break;
			
			case "destino":
				$this->db->
						select("id_destino as id_porto, destino as porto, destinos.un_code_destino as un_code, paises.pais")->
						from("GERAIS.destinos")->
						join("GERAIS.paises","destinos.id_pais = paises.id_pais")->
						where("destinos.id_destino", $porto->getId());	
			break;
			
			default:
				show_error("Impossível encontrar o porto especificado: {$porto}");
			
		}		

		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhum porto de {$hub} encontrado com o parametro: {$porto->getId()}");
		}	
		
		$porto_encontrado = $rs->row();
		
		$porto->setNome($porto_encontrado->porto);
		$porto->setUnCode($porto_encontrado->un_code);
		
		if( $porto->getPais() == NULL )
		{
			$porto->setPais($porto_encontrado->pais);
		}
	
	}
	
	/**
	 * Find By Name
	 *
	 * Busca o porto de origem de uma rota de Exportação
	 *
	 * @name findByName
	 * @access public
	 * @param string
	 * @return Porto
	 */
	public function findByName( $name = NULL, $hub = NULL )
	{
		
		$this->load->model("Tarifario/porto");
		
		if( is_null($name) || is_null($hub) )
		{
			throw new InvalidArgumentException("Paramatros informados para realizar a busca pelo porto inválidos!");
		}	
		
		switch (strtoupper($hub))
		{
			case "ORIGEM":				
				$sql = "SELECT
							id_porto, porto, un_code, 'BRASIL' as pais
						FROM
							USUARIOS.portos
						WHERE
							portos.porto LIKE '%".$name."%'";
				
				$rs = $this->db->query($sql);										
			break;

			case "EMBARQUE":
				$sql = "SELECT
							id_porto, porto, un_code, 'BRASIL' as pais
						FROM
							USUARIOS.portos
						WHERE
							portos.porto LIKE '%".$name."%'";
				
				$rs = $this->db->query($sql);
			break;
			
			case "DESEMBARQUE":
				$sql = "SELECT
							vias.id_via as id_porto, vias.via as porto, vias.un_code_via as un_code, paises.pais
						FROM
							GERAIS.vias
							INNER JOIN GERAIS.paises ON paises.id_pais = vias.id_pais
						WHERE
							vias.via LIKE '%".$name."%'";
				$rs = $this->db->query($sql);
			break;
			
			case "DESTINO":
				$sql = "SELECT
							destinos.id_destino as id_porto, destinos.destino as porto, destinos.un_code_destino as un_code, paises.pais
						FROM
							GERAIS.destinos
							INNER JOIN GERAIS.paises ON paises.id_pais = destinos.id_pais
						WHERE
							destinos.destino LIKE '%".$name."%' AND
							destinos.cancelado = 'N'";

				$rs = $this->db->query($sql);
			break;
			
			default:
				show_error("Impossível encontrar o porto especificado: {$name}");
		}
		
		$portos_encontrados = Array();
						
		if( $rs->num_rows() < 1 )
		{
			return $portos_encontrados;
		}

		foreach( $rs->result() as $porto_encontrado )
		{
			
			$porto = new Porto();
			
			$porto->setId((int)$porto_encontrado->id_porto);	
			$porto->setNome($porto_encontrado->porto);
			$porto->setPais($porto_encontrado->pais);
			$porto->setUnCode($porto_encontrado->un_code);
			
			$portos_encontrados[] = $porto;
			
		}	
		
		return $portos_encontrados;
		
	}
	
	/**
	 * Find By UNCODE
	 *
	 * Faz a busca por porto pelo uncode
	 *
	 * @name findByUncode
	 * @access public
	 * @param Porto
	 * @return boolean
	 */
	public function findByUnCode( Porto $porto, $hub = FALSE )
	{
		
		/** Verifica se o uncode do porto foi preenchido **/
		$uncode = $porto->getUnCode();
		
		$un_pais = substr($uncode, 0,2);
		
		$un_porto = substr($uncode, 2);
		
		if( empty($uncode) )
		{
			log_message('error',"O uncode do porto não foi informado");
			throw new Exception("O uncode do porto não foi informado");
		}
		
		switch (strtoupper($hub))
		{
			case "ORIGEM":
				$sql = "SELECT
							id_porto, porto, un_code, 'BRASIL' as pais
						FROM
							USUARIOS.portos
						WHERE
							portos.un_code LIKE '%".$porto->getUnCode()."%'";
		
				$rs = $this->db->query($sql);
			break;
		
			case "EMBARQUE":
				$sql = "SELECT
							id_porto, porto, un_code, 'BRASIL' as pais
						FROM
							USUARIOS.portos
						WHERE
							portos.un_code LIKE '%".$porto->getUnCode()."%'";
		
				$rs = $this->db->query($sql);
			break;
					
			case "DESEMBARQUE":
				$sql = "SELECT
							vias.id_via as id_porto, vias.via as porto, vias.un_code_via as un_code, paises.pais
						FROM
							GERAIS.vias
							INNER JOIN GERAIS.paises ON paises.id_pais = vias.id_pais
						WHERE
							vias.un_code_via LIKE '%".$porto->getUnCode()."%'";
				$rs = $this->db->query($sql);
				break;
					
			case "DESTINO":
				$sql = "SELECT
							destinos.id_destino as id_porto, destinos.destino as porto, destinos.un_code_destino as un_code, paises.pais
						FROM
							GERAIS.destinos
							INNER JOIN GERAIS.paises ON paises.id_pais = destinos.id_pais
						WHERE
							destinos.un_code_destino LIKE '%".$porto->getUnCode()."%'AND
							destinos.cancelado = 'N'";
		
				$rs = $this->db->query($sql);
			break;
					
			default:
				show_error("Impossível encontrar o porto especificado: {$porto->getUnCode()}");
		}
						
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}
		
		$row = $rs->row();
		
		$porto->setId((int)$row->id_porto);
		$porto->setNome($row->porto);
		$porto->setUnCode($row->un_code);
		$porto->setPais($row->pais);
		
	}
	
}//END CLASS