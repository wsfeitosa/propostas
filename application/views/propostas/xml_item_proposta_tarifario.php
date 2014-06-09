<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0','ISO-8859-1');

$xml->startElement("root");

	$xml->startElement("id_item");
		$xml->text($id_item_sessao);
	$xml->endElement();
	
	$xml->startElement("origem");
		$xml->text($rota->getPortoOrigem()->getNome());
	$xml->endElement();
	
	$xml->startElement("embarque");
		$xml->text($rota->getPortoEmbarque()->getNome());
	$xml->endElement();
	
	$xml->startElement("desembarque");
		$xml->text($rota->getPortoDesembarque()->getNome());
	$xml->endElement();
	
	$xml->startElement("destino");
		$xml->text($rota->getPortoFinal()->getNome());
	$xml->endElement();
	
$xml->endElement();	

echo $xml->outputMemory(true);