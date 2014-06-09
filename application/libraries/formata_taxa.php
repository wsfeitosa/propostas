<?php
/**
 * Formata_Taxa
 *
 * Formata taxas para exibição na tela 
 *
 * @package libraries
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 23/05/2013
 * @version  versao 1.0
*/
class Formata_Taxa {
	
	protected $taxa = NULL;
	
	public function __construct() 
	{
		
	}
	
	/**
	 * formatarLabelTaxa
	 *
	 * formata a taxa para exibição na tela (label)
	 *
	 * @name formatarLabelTaxa
	 * @access public	 
	 * @return string $taxa_formatada
	 * @throws InvalidArgumentException
	 */ 	
	public function formatarLabelTaxa( Taxa $taxa )
	{
		
		$this->taxa = $taxa;
		
		if( is_null($this->taxa) )
		{
			throw new UnexpectedValueException("A taxa informada não tem os valores necessários para a formatação");
		}
		
		$taxa_formatada = $this->taxa->getNome()." | " . $this->taxa->getMoeda()->getSigla() . " ". number_format($this->taxa->getValor(),2) . " " .
                          $this->taxa->getUnidade()->getUnidade() . " | " . number_format($this->taxa->getValorMinimo(),2) . " | " . 
                          number_format($this->taxa->getValorMaximo(),2);
		
		return $taxa_formatada;
		
	}
	
	/**
	 * formatarValueTaxa
	 *
	 * Formata uma taxas em um formato de value padrão para ser utilizado nos combos do sistema
	 *
	 * @name formatarValueTaxa
	 * @access public
	 * @param $taxa Taxa
	 * @return string $taxa_formatada
	 * @throws InvalidArgumentException
	 */ 	
	public function formatarValueTaxa( Taxa $taxa, $separador = ";" ) 
	{
		
		$this->taxa = $taxa;
		
		if( is_null($this->taxa) )
		{
			throw new UnexpectedValueException("A taxa informada não tem os valores necessários para a formatação");
		}
		
		$taxa_formatada = $taxa->getId() . $separador . $taxa->getNome() . $separador . 
						  number_format($taxa->getValor(),2) . $separador . number_format($taxa->getValorMinimo(),2) . $separador . 
						  number_format($taxa->getValorMaximo(),2) . $separador . $taxa->getMoeda()->getId() . $separador .
						  $taxa->getMoeda()->getSigla() . $separador . $taxa->getUnidade()->getId() . $separador .
						  $taxa->getUnidade()->getUnidade();		
		
		return $taxa_formatada;
		
	}
	
}//END CLASS