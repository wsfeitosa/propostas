<?php
class Serializa_Taxa extends CI_Model {
			
	protected $taxa = NULL;
	const SEPARADOR = ";";
	
	public function __construct( $string_serializada = NULL ) 
	{
		parent::__construct();
		
		if( ! is_null($string_serializada) )
		{
			$this->taxa = $string_serializada;
		}	
		
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/unidade");
		
	}
		
	public function ConverterStringParaTaxa( $string_serializada = NULL )
	{
		
		if( ! is_null($string_serializada) )
		{
			$this->taxa = $string_serializada;
		}
		
		// Verifica se algum parametro foi informado
		if( is_null($this->taxa) )
		{
			throw new Exception("Nenhuma Taxa informada para realizar a conversão!");
		}
	
		$string_separada = explode(self::SEPARADOR, $this->taxa);
						
		$taxa_adicional = new Taxa_Adicional();
		$taxa_adicional->setId((int)$string_separada[0]);
		
		$unidade = new Unidade();
		$unidade->setId((int)$string_separada[1]);
		$taxa_adicional->setUnidade($unidade);
		
		$moeda = new Moeda();
		$moeda->setId((int)$string_separada[2]);
		$taxa_adicional->setMoeda($moeda);

		$taxa_adicional->setValor(floatval($string_separada[4]));
		$taxa_adicional->setValorMinimo(floatval($string_separada[5]));
		$taxa_adicional->setValorMaximo(floatval($string_separada[6]));
		
		$taxa_adicional->setPPCC($string_separada[3]);
		
		return $taxa_adicional;
		
	}
	
	public function ConverterTaxaParaString( Taxa $taxa )
	{
		
		$string_serializada = "";
		
		$string_serializada = $taxa->getNome() . " | " . $taxa->getMoeda()->getSigla() . " " .
							  number_format($taxa->getValor(),2,".",",") . " " . $taxa->getUnidade()->getUnidade() . 
							  " | MIN. " . number_format($taxa->getValorMinimo(),2,".",",") . " | MAX. " . 
							  number_format($taxa->getValorMaximo(),2,".",",") . " " . $taxa->getPPCC();
		
		return $string_serializada;
		
	}

	public function ConverterTaxaParaComboValue( Taxa $taxa )
	{
		$valueDoCombo = "";
		
		$valueDoCombo .= $taxa->getId() . ";" . $taxa->getUnidade()->getId() . ";" . 
						 $taxa->getMoeda()->getId() . ";" . $taxa->getPPCC() . ";" .
						 $taxa->getValor() . ";" . $taxa->getValorMinimo() . ";" .
						 $taxa->getValorMaximo();
		
		return $valueDoCombo;		
	}
	
}