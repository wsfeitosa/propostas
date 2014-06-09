<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
* @package  Taxas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 04/02/2013
* @version  1.0
* Classe que contém às regras de negócio das taxas locais do sistema
*/
include_once APPPATH."models/Taxas/taxa_local.php";
include_once APPPATH."models/Taxas/moeda.php";
include_once APPPATH."models/Taxas/unidade.php";

class Taxa_Local_Model extends CI_Model{

	public function __construct()
	{
		parent::__construct();		
	}
	
	/**
	  * ObterTaxasLocais
	  * 
	  * Busca às taxas locais padrão do porto
	  *   
	  * @name ObterTaxasLocais
	  * @access public
	  * @param string sentido
	  * @param string modalidade
	  * @param string tipo_cliente
	  * @param string porto
	  * @return array
	  */
	public function ObterTaxasLocais( $sentido = NULL, $modalidade = "LCL", $tipo_cliente = "A", $id_porto = NULL )
	{
		
		$sql = "SELECT
					taxas.id_taxa, taxas.local, taxas_adicionais.taxa_adicional as taxa,
					taxas.id_txadicional, taxas.valor, minimo, maximo,
					moedas.sigla as sigla_moeda,
					moedas.id_moeda,
					unidades.id_unidade, unidades.unidade, apelido,
					descricao, taxas.bloqueio_alteracao, taxas.bloqueio_remocao
				FROM
					FINANCEIRO.taxas
					inner join FINANCEIRO.taxas_adicionais on taxas_adicionais.id_txadicional = taxas.id_txadicional
					left join FINANCEIRO.unidades on taxas.unidade=unidades.id_unidade
					left join FINANCEIRO.moedas on taxas.tpmoeda=moedas.id_moeda
				WHERE
					impexp = '".$sentido."' AND
					id_porto = '".$id_porto."' AND
					taxas.tipo_embarque_taxa = '".$modalidade."' AND				
					(taxas.df = 'A' or taxas.df = '".$tipo_cliente."') AND
					local = 'SIM' AND
					taxas.status = 'S' AND			
					cancelado <> 'S'
				ORDER BY
					taxa";
		
		$rs = $this->db->query($sql);
		
		if( $rs->num_rows() < 1 )
		{
			log_message('error',"Não foi possivel encontrar às taxas locais do porto selecionado");			
			//throw new Exception("Não foi possivel encontrar às taxas locais do porto selecionado");
		}	

		$taxas_encontradas = Array();
		
		$result = $rs->result();
		
		/** cria objetos do tipo Taxa_Local com às taxas locais **/
		foreach( $result as $taxas )
		{
			
			$taxa_local = new Taxa_Local();
			
			$taxa_local->setId((int)$taxas->id_txadicional);
			$taxa_local->setNome($taxas->taxa);
			$taxa_local->setValor((float)$taxas->valor);
			$taxa_local->setValorMinimo((float)$taxas->minimo);
			$taxa_local->setValorMaximo((float)$taxas->maximo);
			
			/** Cria a unidade e a moeda **/
			$unidade = new Unidade();

			$unidade->setId((int)$taxas->id_unidade);
			$unidade->setUnidade($taxas->unidade);
			
			$taxa_local->setUnidade($unidade);
			
			$moeda = new Moeda();
			
			$moeda->setId((int)$taxas->id_moeda);
			$moeda->setSigla($taxas->sigla_moeda);
			
			$taxa_local->setMoeda($moeda);
			
			$taxas_encontradas[] = $taxa_local;
			
		}	
		
		return $taxas_encontradas;
				
	}//END FUNCTION
	
}//END CLASS