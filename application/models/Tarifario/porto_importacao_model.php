<?php
/**
 * Class Porto Model
 *
 * Classe com as regras de negócio do objeto porto no sistema
 *
 * @package Tarifario
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Porto_Model
 * @version 1.0
 */
include_once APPPATH."/models/Tarifario/porto.php";
include_once APPPATH."/models/Tarifario/interface_porto.php";

class Porto_Importacao_Model extends CI_Model implements Interface_Porto {
			
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Find By Id
	 *
	 * Busca os dados de um porto na importação baseado no id
	 *
	 * @name findById
	 * @access public
	 * @param Porto
	 * @return void
	 */
	public function findById( Porto $porto, $hub = FALSE )
	{
		
		if( ! $hub )
		{
			throw new InvalidArgumentException("Nenhum porto informado para realizar a consulta");
		}	
		
		if( strtoupper($hub) == "DESTINO" )
		{
			$this->db->
			select("id_porto,porto, un_code")->
			from("USUARIOS.portos")->
			where("id_porto",$porto->getId())->
			where("portos.ativo","S");
			
			$rs = $this->db->get();
			
			if( $rs->num_rows() < 1 )
			{				
				print"<pre>";print_r($this->db);exit;
				log_message('error','Não foi possivel encontrar o porto de destino com o id informado: '. pr($porto));
				throw new Exception("Não foi possivel encontrar o porto de destino com o id informado: ". pr($porto));
			}
			
			$row = $rs->row();
			
			$porto->setNome($row->porto);
			$porto->setPais("BRASIL");
			$porto->setUnCode($row->un_code);
			
		}
		else
		{
			$this->db->
			select("porto.id_porto, porto.pais as un_pais, porto.sigla, porto.porto, paises.pais")->
			from("GERAIS.porto")->
			join("GERAIS.paises","porto.id_pais = paises.id_pais","left")->
			where("id_porto",$porto->getId())->
			//where("porto.ativo","S")->
			order_by("porto","asc");
			
			$rs = $this->db->get();
			
			if( $rs->num_rows() < 1 )
			{				
				log_message('error','Não foi possivel encontrar o porto com o id informado: ' . pr($porto));
				throw new Exception("Não foi possivel encontrar o porto com o id informado: ". pr($this->db));
			}
			
			$row = $rs->row();
			
			$porto->setNome($row->porto);
			$porto->setPais($row->pais);
			$porto->setUnCode($row->un_pais.$row->sigla);
			
		}	
							
	}//END FUNCTION
	
	/**
	  * Find By Name
	  * 
	  * Busca o porto de origem de uma rota de importação
	  * 
	  * @name findOrigem
	  * @access public
	  * @param string
	  * @return Porto
	  */
	public function findByName( $name = NULL, $hub = FALSE )
	{
		
		if( empty($name) || ! $hub )
		{
			log_message('error',"Nome do porto invalido informado para a pesquisa");
			throw new Exception("Nome do porto invalido informado para a pesquisa");
		}	
						
		$this->db->
			select("porto.id_porto, porto.pais as un_pais, porto.sigla, porto.porto, paises.pais")->
			from("GERAIS.porto")->
			join("GERAIS.paises","porto.id_pais = paises.id_pais","left")->
			like("porto",$name)->
			where("porto.ativo","S")->			
			order_by("porto","asc");		
		
		if( strtoupper($hub) == "DESEMBARQUE" || strtoupper($hub) == "DESTINO" )
		{
			$this->db->where("porto.pais =","BR");
		}
		else
		{
			$this->db->where("porto.pais !=","BR");
		}
		
		$rs = $this->db->get();
		
		/** Cria objetos do tipo Porto **/
		$portos = Array();
		
		if( $rs->num_rows() < 1 )
		{
			return $portos;
		}	
						
		foreach( $rs->result() as $row )
		{

			$porto = new Porto();
			
			$porto->setId((int)$row->id_porto);
			$porto->setNome($row->porto);
			$porto->setUnCode($row->un_pais.$row->sigla);
			$porto->setPais($row->pais);
			
			$portos[] = $porto;
			
		}	
		
		return $portos;
		
	}//END FUCNTION
	
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
		
		/** Verifica se o tamanho do uncode está correto **/
		if( ( strlen($un_pais) != 2 || strlen($un_porto) != 3 ) && $uncode != 'NULL' ) 
		{
			show_error("UN Code invalido para realizar a busca pelo porto. " . pr($porto) );exit;
		}	
		
		if( empty($uncode) )
		{
			log_message('error',"O uncode do porto não foi informado");
			throw new Exception("O uncode do porto não foi informado");
		}	
		
		$this->db->
			select("porto.id_porto, porto.pais as un_pais, porto.sigla, porto.porto, paises.pais")->
			from("GERAIS.porto")->
			join("GERAIS.paises","porto.id_pais = paises.id_pais","left")->
			where("porto.pais",$un_pais)->
			where("porto.sigla",$un_porto)->
			where("porto.ativo","S")->			
			order_by("porto","asc");	
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}

		$row = $rs->row();
		
		$porto->setId((int)$row->id_porto);
		$porto->setNome($row->porto);
		$porto->setUnCode($row->un_pais.$row->sigla);
		$porto->setPais($row->pais);
				
		return TRUE;
		
	}//END FUNCTION
	
	/**
	 * Change Id Port Delivery
	 *
	 * Muda a propriedade id de um objeto do tipo porto,
	 * pois na importação o porto de destino vem da usuários portos
	 * e não da gerais porto como no resto dos casos
	 *
	 * @name changeIdPortDelivery
	 * @access public
	 * @param Porto
	 * @return void
	 */
	public function changeIdPortDelivery( Porto $porto )
	{
				
		if( ! $porto->getUnCode() )
		{
			log_message('error','O uncode do porto não foi definido!');
			throw new Exception('O uncode do porto não foi definido!');
		}
	
		$this->db->
		select("id_porto")->
		from("USUARIOS.portos")->
		where("un_code",$porto->getUnCode())->
		where("portos.ativo","S");
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			log_message('error','Impossivel converter o porto!');
			throw new Exception('Impossivel converter o porto!');
		}	
		
		$row = $rs->row();
				
		$porto->setId((int)$row->id_porto);
		
		
	}//END FUNCTION
			
}//END CLASS