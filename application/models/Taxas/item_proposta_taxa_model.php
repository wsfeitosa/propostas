<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 10/05/2013
 * @version  1.0
 * Classe que manipula às taxas de um item de proposta
 */
include_once APPPATH."models/Taxas/taxa_local.php";
include_once APPPATH."models/Taxas/taxa_adicional.php";
include_once APPPATH."models/Taxas/taxa_model.php";
include_once APPPATH."models/Taxas/moeda.php";
include_once APPPATH."models/Taxas/moeda_model.php";
include_once APPPATH."models/Taxas/unidade.php";
include_once APPPATH."models/Taxas/unidade_model.php";
include_once APPPATH."models/Desbloqueios/verifica_desbloqueio_pendente.php";

class Item_Proposta_Taxa_Model extends CI_Model{
	
	public function __construct(){
		parent::__construct();	
	}
	
	/**
	 * buscaTaxasDoItemDaProposta
	 *
	 * Busca às taxas cadastradas em um item de proposta no sistema
	 *
	 * @name buscaTaxasDoItemDaProposta
	 * @access public
	 * @param Item_Proposta $item
	 * @return void
	 */
	public function buscaTaxasDoItemDaProposta(Item_Proposta $item, $taxa_imo = FALSE) {
		
		/** Verifica se o id do item foi definido **/
		$id_item = $item->getId();
		
		if( is_null($id_item) || $id_item == "0" || $id_item == "" ) 
		{
			throw new InvalidArgumentException("Id do item da proposta não definido para realizar a busca pelas taxas");
		}	
		
		$this->db->
				select("taxa_portuaria, id_taxa_adicional, valor, valor_minimo, valor_maximo, id_moeda, id_unidade, ppcc")->
				from("CLIENTES.taxas_item_proposta")->
				where("taxas_item_proposta.id_item_proposta",$id_item);
		
		$rs = $this->db->get();
		
		$linhas = $rs->num_rows();
		
		if( $linhas > 1 )
		{

			$verifica_desbloqueio = new Verifica_Desbloqueio_Pendente();

			$taxa_model = new Taxa_Model();
			
			$moeda_model = new Moeda_Model();
			
			$unidade_model = new Unidade_Model();
							
			if( $item->getTarifario() instanceOf Tarifario )
			{
				//Limpa todas às taxas do tarifário menos o add Imo - correção de bug 006
				if( $taxa_imo === TRUE )
				{	
					foreach( $item->getTarifario()->getTaxa() as $taxa_padrao )
					{
						if( $taxa_padrao->getId() == "14" )
						{
							$addimo = $taxa_padrao;
						}	
					}
				}	
					
				$item->getTarifario()->limparTaxasTarifario();	
			}
			else
			{
				return;
			}				
			
			/** Cria às taxas para substituir na proposta **/
			$result = $rs->result();			
						
			foreach( $result as $taxa )
			{
				
				/** Cria um objeto do tipo taxa de acordo com o tipo de taxa Taxa_Local ou Taxa_Adicional **/
				if( $taxa->taxa_portuaria == "S" )
				{
					$taxa_proposta = new Taxa_Local();
				}
				else
				{
					$taxa_proposta = new Taxa_Adicional();
				}		
				
				/** Verifica se a taxa estava bloqueada **/
				$taxa_bloqueada = $verifica_desbloqueio->existeDesbloqueioTaxaPendente($id_item, $taxa->id_taxa_adicional);

				$taxa_proposta->setIdItem($id_item);
				$taxa_proposta->setId((int)$taxa->id_taxa_adicional);
				$taxa_proposta->setValor((float)$taxa->valor);
				$taxa_proposta->setValorMinimo((float)$taxa->valor_minimo);
				$taxa_proposta->setValorMaximo((float)$taxa->valor_maximo);
				$taxa_proposta->setBloqueada($taxa_bloqueada);
				$taxa_proposta->setPPCC($taxa->ppcc);
				
				/** Obtem o nome da taxa **/
				$taxa_model->obterNomeTaxaAdicional($taxa_proposta);
				
				/** Obtem a moeda **/
				$moeda = new Moeda();
				$moeda->setId((int)$taxa->id_moeda);
								
				$moeda_model->findById($moeda);
				
				$taxa_proposta->setMoeda($moeda);
				
				/** Obtém a unidade **/
				$unidade = new Unidade();
				$unidade->setId((int)$taxa->id_unidade);
								
				$unidade_model->findById($unidade);
				
				$taxa_proposta->setUnidade($unidade);
				
				$item->getTarifario()->adicionarNovaTaxa($taxa_proposta);
															
			}

			if( $taxa_imo === TRUE )
			{	
				/** Verifica se a taxa add imo já foi trazida pelo item de proposta encotrado **/
				$existe_addimo = FALSE;

				foreach( $item->getTarifario()->getTaxa() as $taxa_item )
				{
					if( $taxa_item->getId() == "14" )
					{
						$existe_addimo = TRUE;
					}	
				}

				/** 
				  * Se não existir taxa de add imo nas taxas do item da proposta e existir um valor padrão
				  * para o add imo, então adiciona a taxa de add imo padrão às taxas da proposta
				  */
				if( $existe_addimo === FALSE && $addimo instanceOf Taxa )
				{
					$item->getTarifario()->adicionarNovaTaxa($addimo);
				}
			}		

		}		
		
	}//END FUNCTION	 
	
}//END CLASS