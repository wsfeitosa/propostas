<?php

if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	

/**
 * Gera_Numero_Acordo
 *
 * Gera às númerações dos acordos globais de taxas locais 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 17/05/2013
 * @version  versao 1.0
*/
class Gera_Numero_Acordo extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * gerarNumeroAcordo
	 *
	 * Gerar uma nova numeração para um acordo de taxas locais
	 *
	 * @name gerarNumeroAcordo
	 * @access public	 
	 * @return string $numero
	 */ 	
	public function gerarNumeroAcordo() 
	{

		if( ! isset($_SESSION['matriz'][1]) )
		{
			$message = "Sessão não iniciada ou expirada, impossível gerar a numeração do acordo!";
			log_message('error',$message);
			show_error($message);
		}	
		
		$sigla_acordo = "TX";
		$mes_atual = date("m");
		$ano_atual = date("y");
		$sigla_filial = $_SESSION['matriz'][1];
		
		/** Tenta selecionar o último registro de acordo **/
		$ultimo_numero_cadastrado = $this->ObterUltimoNumeroDeAcordo();
		  
		if( ! $ultimo_numero_cadastrado )
		{
			$sequencial = "00000";
		}	
		else
		{
			
			$ano_ultima_proposta = substr($ultimo_numero_cadastrado, 4,2);
			
			if( $ano_ultima_proposta == $ano_atual )
			{
				$sequencial = substr($ultimo_numero_cadastrado, 8);
			}	
			else
			{
				$sequencial = "00000";
			}	
						
		}		
			
		/** Acrescenta um para gerar o próximo número **/
		$sequencia_proximo_numero = intval($sequencial) + 1; 
		
		$numero = $sigla_acordo . $mes_atual . $ano_atual . $sigla_filial . sprintf("%05d", $sequencia_proximo_numero);
		
		return $numero;
	}
	
	/**
	 * ObterUltimoNumeroDeAcordo
	 *
	 * Obtém o último número de acordo de taxas locais cadastrado no sistema
	 *
	 * @name ObterUltimoNumeroDeAcordo
	 * @access protected	 
	 * @return string $numero
	 */ 	
	protected function ObterUltimoNumeroDeAcordo() 
	{
		
		$this->db->
				select("numero")->
				from("CLIENTES.acordos_taxas_locais_globais")->
				order_by("acordos_taxas_locais_globais.id","desc")->
                limit(1);
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}

		$acordo = $rs->row();
		
		return $acordo->numero;
		
	}
	
}//END CLASS