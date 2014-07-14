<?php
/**
 * Compara_Taxas
 *
 * Classe que compara taxas e retorna às taxas que devem prevalecer 
 *
 * @package models/Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/05/2013
 * @version  versao 1.0
*/
class Compara_Taxas {
	
	protected $taxas_originais = Array();
	protected $taxas_comparacao = Array();
	protected $resultado_comparacao = Array();
	
	public function __construct( Array $taxas_originais = Array(), Array $taxas_comparacao = Array() ) 
	{					
		$this->taxas_originais = $taxas_originais;
		$this->taxas_comparacao = $taxas_comparacao;		
	}
	
	/**
	 * comparar_taxas
	 *
	 * Compara dois arrays de taxas e faz o replace das taxas originais pelas taxas de comparação
	 * caso às taxas sejam de mesmo ID
	 *
	 * @name comparar_taxas
	 * @access public	 
	 * @return Array $resultado_comparacao
	 */ 	
	public function comparar_taxas() 
	{
		
		if( ! is_array($this->taxas_originais) || count($this->taxas_originais) < 1 )
		{
			throw new InvalidArgumentException("Array de Taxas Originais inválido para realizar a operação!");
		}	
		
		if( ! is_array($this->taxas_comparacao) || count($this->taxas_comparacao) < 1 )
		{
			throw new InvalidArgumentException("Array de Taxas Para Comparação inválido para realizar a operação!");
		}
		
		foreach( $this->taxas_originais as $taxa_original )
		{
			$this->resultado_comparacao[$taxa_original->getId()] = $taxa_original;			
		}	
		
		foreach( $this->taxas_comparacao as $taxa_comparacao )
		{
			if( array_key_exists($taxa_comparacao->getId(), $this->resultado_comparacao) )
			{
				$this->resultado_comparacao[$taxa_comparacao->getId()] = $taxa_comparacao;
			}
            /**
			else
			{
				$this->resultado_comparacao[$taxa_comparacao->getId()] = $taxa_comparacao;
			}
            **/ 		
		}	
		
		return $this->resultado_comparacao;
		
	}
	
	/**
	 * separaTaxasPorTipo
	 *
	 * Separa às taxas por tipo de taxa (Local ou Adicional)
	 *
	 * @name separaTaxasPorTipo
	 * @access public
	 * @param Array $taxas
	 * @return Array $taxas_separadas
	 */ 	
	public function separaTaxasPorTipo(Array $taxas) 
	{
		
		$taxas_separadas = Array('taxas_locais' => Array(), 'taxas_adicionais' => Array());
		
		foreach( $taxas as $taxa )
		{
			if( $taxa instanceof Taxa_Local )
			{
				$taxas_separadas['taxas_locais'][] = $taxa;
			}
			elseif( $taxa instanceof Taxa_Adicional )
			{
				$taxas_separadas['taxas_adicionais'][] = $taxa;
			}
			else
			{
				throw new RuntimeException("Taxa de tipo desconhecido, impossivel realizar a separação!");
			}		
		}		
		
		return $taxas_separadas;
		
	}
	
}//END CLASS