<?php
include_once "/var/www/html/allink/Libs/remove_acentos.php";
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0','ISO-8859-1');
$xml->startElement("error");
	$xml->startElement("message");
		$xml->text(remove_acentos($error));
	$xml->endElement();
$xml->endElement();
echo $xml->outputMemory(true);	
	