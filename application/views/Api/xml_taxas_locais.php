<?php
include_once "/var/www/html/allink/Libs/remove_acentos.php";

Header("Content-type: application/xml; charset=ISO-8859-1");

$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0','ISO-8859-1');

$xml->startElement("taxas_locais");

foreach($taxas_locais as $taxa):
	
	if( $taxa->getValor() < 1 )
	{
		continue;
	}	

	$xml->startElement("taxa_local");
		
		$xml->startElement("id_taxa");
			$xml->text($taxa->getId());
		$xml->endElement();
		
		$xml->startElement("nome");
			$xml->text(remove_acentos($taxa->getNome()));
		$xml->endElement();

		$xml->startElement("id_unidade");
			$xml->text($taxa->getUnidade()->getId());
		$xml->endElement();
		
		$xml->startElement("unidade");
			$xml->text(remove_acentos($taxa->getUnidade()->getUnidade()));
		$xml->endElement();
		
		$xml->startElement("id_moeda");
			$xml->text($taxa->getMoeda()->getId());
		$xml->endElement();
		
		$xml->startElement("moeda");
			$xml->text(remove_acentos($taxa->getMoeda()->getSigla()));
		$xml->endElement();
		
		$xml->startElement("valor");
			$xml->text(number_format($taxa->getValor(),2,".",","));
		$xml->endElement();
		
		$xml->startElement("valor_minimo");
			$xml->text(number_format($taxa->getValorMinimo(),2,".",","));
		$xml->endElement();
		
		$xml->startElement("valor_maximo");
			$xml->text(number_format($taxa->getValorMaximo(),2,".",","));
		$xml->endElement();
				
	$xml->endElement();

endforeach;

$xml->endElement();
echo $xml->outputMemory(true);
