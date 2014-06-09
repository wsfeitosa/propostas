<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);

$xml->startDocument('1.0','ISO-8859-1');

$xml->startElement("resultado_busca");

	$xml->startElement("acordos_duplicados");	
		$xml->text($duplicacao);
	$xml->endElement();
	
	if( $duplicacao )
	{
		$iterator = $acordos_duplicados->getIterator();
		
		while($iterator->valid())
		{
			$xml->startElement("numero_acordo");
				$xml->text($iterator->current()->getNumero());
			$xml->endElement();
			$iterator->next();
		}		
	}	
			
$xml->endElement();

echo $xml->outputMemory(true);