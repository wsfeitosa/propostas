<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);

$xml->startDocument('1.0','ISO-8859-1');

if( $taxas_locais->offsetExists("error") )
{
	$xml->startElement("taxas_locais");
		$xml->startElement("error");
			$xml->text($taxas_locais->offsetGet('error'));
		$xml->endElement();	
	$xml->endElement();
	echo $xml->outputMemory(true);

	exit;
}

$xml->startElement("taxas_locais");

$iterator = $taxas_locais->getIterator();
	
while( $iterator->valid() )
{
	$xml->startElement("taxa_local");
	
		$xml->startElement("id_taxa_adicional");
			$xml->text($iterator->current()->getId());
		$xml->endElement();
		
		$xml->startElement("taxa");
			$xml->text(utf8_encode($iterator->current()->getNome()));
		$xml->endElement();
		
		$xml->startElement("valor");
			$xml->text( sprintf( "%02.2f",$iterator->current()->getValor() ) );
		$xml->endElement();
		
		$xml->startElement("valor_minimo");
			$xml->text( sprintf( "%02.2f",$iterator->current()->getValorMinimo() ) );
		$xml->endElement();
		
		$xml->startElement("valor_maximo");
			$xml->text( sprintf( "%02.2f", $iterator->current()->getValorMaximo() ) );
		$xml->endElement();
		
		$xml->startElement("moeda");
			$xml->text($iterator->current()->getMoeda()->getSigla());
		$xml->endElement();
		
		$xml->startElement("id_moeda");
			$xml->text($iterator->current()->getMoeda()->getId());
		$xml->endElement();
		
		$xml->startElement("unidade");
			$xml->text($iterator->current()->getUnidade()->getUnidade());
		$xml->endElement();
		
		$xml->startElement("id_unidade");
			$xml->text($iterator->current()->getUnidade()->getId());
		$xml->endElement();
		
	$xml->endElement();
	
	$iterator->next();
}		
	
$xml->endElement();
echo $xml->outputMemory(true);
