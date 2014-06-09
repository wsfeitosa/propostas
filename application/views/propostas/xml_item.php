<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = "<?xml version='1.0'?>";
$xml .= "<id_item>" . $id_item_sessao . "</id_item>";
echo $xml;
