<?php
/**
 * Conversor_Taxas
 *
 * Serializa e descerializa às taxas para que sejam efetuadas às operções de 
 * transição entre os eventos de interface do usuário e às operações de regras de
 * negócio. 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
class Conversor_Taxas extends CI_Model {
	
	const caracter_separador = ";";
	
	public function __construct() {
		parent::__construct();
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/moeda_model");	
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/taxa_model");	
	}
	
	/**
	 * deserializaTaxa
	 *
	 * Converte os dados post do formulário em objetos do tipo taxas_adicionais
	 *
	 * @name deserializaTaxa
	 * @access public
	 * @param string $taxa_serializada
	 * @return Taxa_Adicional $taxa
	 */ 	
	public function deserializaTaxa($taxa_serializada) 
	{
		
		$dados_taxa = explode(self::caracter_separador, $taxa_serializada);	
		
		$taxa = new Taxa_Adicional();
		
		$taxa->setId((int)$dados_taxa[0]);
		
		$taxa_model = new Taxa_Model();
		
		$taxa_model->obterNomeTaxaAdicional($taxa);
		
		$taxa->setValor((float) $dados_taxa[2]);
		$taxa->setValorMinimo((float)$dados_taxa[3]);
		$taxa->setValorMaximo((float)$dados_taxa[4]);
		
		$moeda = new Moeda();
		
		$moeda->setId((int)$dados_taxa[5]);
		$moeda->setSigla($dados_taxa[6]);
		
		$unidade = new Unidade();
		
		$unidade->setId((int)$dados_taxa[7]);
		$unidade->setUnidade($dados_taxa[8]);
		
		$taxa->setMoeda($moeda);
		
		$taxa->setUnidade($unidade);
		
		return $taxa;
				
	}
	
	/**
	 * serializaTaxa
	 *
	 * Serializa um objeto do tipo taxa com o caractere desejado
	 *
	 * @name serializaTaxa
	 * @access public
	 * @param Taxa $taxa
	 * @param string $separador
	 * @return string $taxa_serializada
	 */ 	
	public function serializaTaxa(Taxa $taxa, $separador = ";")
	{
		
		$taxa_serializada['value'] =   
									$taxa->getId() . $separador . $taxa->getNome() . $separador .
									$taxa->getValor() . $separador . $taxa->getValorMinimo() . $separador .
									$taxa->getValorMaximo() . $separador . $taxa->getMoeda()->getId() . $separador .
									$taxa->getMoeda()->getSigla() . $separador . $taxa->getUnidade()->getId() . $separador .
									$taxa->getUnidade()->getUnidade();
		
		$taxa_serializada['label'] = 
									$taxa->getNome() . " | " . $taxa->getMoeda()->getSigla() . " " .
									sprintf("%01.2f", $taxa->getValor() ) . " " . $taxa->getUnidade()->getUnidade() . " | MIN." .
									sprintf("%01.2f", $taxa->getValorMinimo() ) . " | MAX. " . sprintf("%01.2f", $taxa->getValorMaximo() );
		
		return $taxa_serializada;
		
	}
	
}//END CLASS