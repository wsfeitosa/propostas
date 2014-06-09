<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0','ISO-8859-1');

$xml->startElement("item");
		
	$xml->startElement("id");
		$xml->text($id_item);
	$xml->endElement();
    
    $xml->startElement("id_tarifario");
        $xml->text($item["id_tarifario"]);
    $xml->endElement();

	$xml->startElement("mercadoria");
		$xml->text(urldecode($item['mercadoria']));
	$xml->endElement();
	
	$xml->startElement("pp");
		$xml->text($item['pp']);
	$xml->endElement();
	
	$xml->startElement("cc");
		$xml->text($item['cc']);
	$xml->endElement();
	
	$xml->startElement("validade");
		$xml->text($item['validade']);
	$xml->endElement();
	
	$xml->startElement("peso");
		$xml->text($item['peso']);
	$xml->endElement();
	
	$xml->startElement("cubagem");
		$xml->text($item['cubagem']);
	$xml->endElement();
	
	$xml->startElement("volumes");
		$xml->text($item['volumes']);
	$xml->endElement();
	
	$xml->startElement("origem");
		$xml->text(($item['origem']));
	$xml->endElement();
	
	$xml->startElement("embarque");
		$xml->text(($item['embarque']));
	$xml->endElement();
	
	$xml->startElement("desembarque");
		$xml->text(($item['desembarque']));
	$xml->endElement();
	
	$xml->startElement("destino");
		$xml->text(urldecode($item['destino']));
	$xml->endElement();
	
	$xml->startElement("un_origem");
		$xml->text($item['un_origem']);
	$xml->endElement();
	
	$xml->startElement("un_embarque");
		$xml->text($item['un_embarque']);
	$xml->endElement();
	
	$xml->startElement("un_desembarque");
		$xml->text($item['un_desembarque']);
	$xml->endElement();
	
	$xml->startElement("un_destino");
		$xml->text($item['un_destino']);
	$xml->endElement();
	
	$xml->startElement("frete_adicionais");
		$xml->text($item['frete_adicionais']);
	$xml->endElement();
	
	$xml->startElement("labels_frete_adicionais");
		$xml->text(($item['labels_frete_adicionais']));
	$xml->endElement();
	
	$xml->startElement("taxas_locais");
		$xml->text(($item['taxas_locais']));
	$xml->endElement();
	
	$xml->startElement("labels_taxas_locais");
		$xml->text(($item['labels_taxas_locais']));
	$xml->endElement();
	
	$xml->startElement("observacao_interna");
		$xml->text(urldecode($item['observacao_interna']));
	$xml->endElement();
	
	$xml->startElement("observacao_cliente");
		$xml->text(urldecode($item['observacao_cliente']));
	$xml->endElement();
	
	$xml->startElement("erro");
		$xml->text($erro);
	$xml->endElement();
	
$xml->endElement();

echo $xml->outputMemory(true);