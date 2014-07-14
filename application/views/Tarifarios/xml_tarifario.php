<?php
include_once "/var/www/html/allink/Libs/remove_acentos.php";

Header("Content-type: application/xml; charset=ISO-8859-1");
$dom = new DOMDocument();

$dom->formatOutput = TRUE;

$t = $dom->createElement("tarifario");

/** Se for uma proposta spot, então cria tag com o id do item da proposta **/
if( ! empty($id_item) )
{
	$t->appendChild($dom->createElement("id_proposta",$id_item));
}
else
{
	$t->appendChild($dom->createElement("id_proposta",0));
}	

/** Id do tarifario **/
$id = $dom->createElement("id_tarifario",$tarifario->getId());
$t->appendChild($id);

/** Data de Inicio **/
$data_inicio = $dom->createElement("data_inicio",$tarifario->getInicio()->format('d/m/Y'));
$t->appendChild($data_inicio);

/** Validade do tarifario **/
$validade = $dom->createElement("validade",$tarifario->getValidade()->format('d/m/Y'));
$t->appendChild($validade);

/** Sentido (IMP OU EXP) **/
$sentido = $dom->createElement("sentido",$tarifario->getSentido());
$t->appendChild($sentido);

/** Rota do tarifario **/
$rota = $dom->createElement("rota");

/** Porto de origem **/
$porto_origem = $dom->createElement("origem");
$id_porto_origem = $dom->createElement("id",remove_acentos($tarifario->getRota()->getPortoOrigem()->getId()));
$nome_porto_origem = $dom->createElement("nome",remove_acentos($tarifario->getRota()->getPortoOrigem()->getNome()));
$uncode_origem = $dom->createElement("uncode",remove_acentos($tarifario->getRota()->getPortoOrigem()->getUnCode()));

$porto_origem->appendChild($id_porto_origem);
$porto_origem->appendChild($nome_porto_origem);
$porto_origem->appendChild($uncode_origem);

$rota->appendChild($porto_origem);

/** Porto de embarque **/
$porto_embarque = $dom->createElement("embarque");
$id_porto_embarque = $dom->createElement("id",$tarifario->getRota()->getPortoembarque()->getId());
$nome_porto_embarque = $dom->createElement("nome",remove_acentos($tarifario->getRota()->getPortoEmbarque()->getNome()));
$uncode_embarque = $dom->createElement("uncode",remove_acentos($tarifario->getRota()->getPortoEmbarque()->getUnCode()));

$porto_embarque->appendChild($id_porto_embarque);
$porto_embarque->appendChild($nome_porto_embarque);
$porto_embarque->appendChild($uncode_embarque);

$rota->appendChild($porto_embarque);

/** Porto de desembarque **/
$porto_desembarque = $dom->createElement("desembarque");
$id_porto_desembarque = $dom->createElement("id",$tarifario->getRota()->getPortoDesembarque()->getId());
$nome_porto_desembarque = $dom->createElement("nome",remove_acentos($tarifario->getRota()->getPortoDesembarque()->getNome()));
$uncode_desembarque = $dom->createElement("uncode",remove_acentos($tarifario->getRota()->getPortoDesembarque()->getUnCode()));

$porto_desembarque->appendChild($id_porto_desembarque);
$porto_desembarque->appendChild($nome_porto_desembarque);
$porto_desembarque->appendChild($uncode_desembarque);

$rota->appendChild($porto_desembarque);

/** Porto de destino **/
$porto_destino = $dom->createElement("destino");
$id_porto_destino = $dom->createElement("id",$tarifario->getRota()->getPortoFinal()->getId());
$nome_porto_destino = $dom->createElement("nome",remove_acentos($tarifario->getRota()->getPortoFinal()->getNome()));
$uncode_destino = $dom->createElement("uncode",$tarifario->getRota()->getPortoFinal()->getUnCode());

$porto_destino->appendChild($id_porto_destino);
$porto_destino->appendChild($nome_porto_destino);
$porto_destino->appendChild($uncode_destino);

$rota->appendChild($porto_destino);

$t->appendChild($rota);

$observacao = $dom->createElement("observacao",utf8_encode(remove_acentos($tarifario->getObservacao())));

$t->appendChild($observacao);

/** Taxas adicionais **/
$taxas_adicionais = $dom->createElement("taxas_adicionais");
$taxas_locais = $dom->createElement("taxas_locais");

foreach($tarifario->getTaxa() as $taxa):

if( $taxa instanceof Taxa_Adicional )
{
	$taxa_adicional = $dom->createElement("taxa");
	$taxa_adicional->appendChild($dom->createElement("id_taxa",$taxa->getId()));
	$taxa_adicional->appendChild($dom->createElement("nome",remove_acentos($taxa->getNome())));
	$taxa_adicional->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_adicional->appendChild($dom->createElement("unidade",remove_acentos($taxa->getUnidade()->getUnidade())));
	$taxa_adicional->appendChild($dom->createElement("id_moeda",$taxa->getMoeda()->getId()));
	$taxa_adicional->appendChild($dom->createElement("moeda",$taxa->getMoeda()->getSigla()));
	$taxa_adicional->appendChild($dom->createElement("valor",number_format($taxa->getValor(),2,".",",")));
	$taxa_adicional->appendChild($dom->createElement("valor_minimo",number_format($taxa->getValorMinimo(),2,".",",")));
	$taxa_adicional->appendChild($dom->createElement("valor_maximo",number_format($taxa->getValorMaximo(),2,".",",")));
	$taxa_adicional->appendChild($dom->createElement("ppcc",$taxa->getPPCC()));

	$taxas_adicionais->appendChild($taxa_adicional);
}
else
{
	$taxa_local = $dom->createElement("taxa");
	$taxa_local->appendChild($dom->createElement("id_taxa",$taxa->getId()));
	$taxa_local->appendChild($dom->createElement("nome",(remove_acentos($taxa->getNome()))));
	$taxa_local->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_local->appendChild($dom->createElement("unidade",remove_acentos($taxa->getUnidade()->getUnidade())));
	$taxa_local->appendChild($dom->createElement("id_moeda",$taxa->getMoeda()->getId()));
	$taxa_local->appendChild($dom->createElement("moeda",$taxa->getMoeda()->getSigla()));
	$taxa_local->appendChild($dom->createElement("valor",number_format($taxa->getValor(),2,".",",")));
	$taxa_local->appendChild($dom->createElement("valor_minimo",number_format($taxa->getValorMinimo(),2,".",",")));
	$taxa_local->appendChild($dom->createElement("valor_maximo",number_format($taxa->getValorMaximo(),2,".",",")));
	$taxa_local->appendChild($dom->createElement("ppcc",$taxa->getPPCC()));

	$taxas_locais->appendChild($taxa_local);
}

endforeach;

$t->appendChild($taxas_adicionais);

$t->appendChild($taxas_locais);

/** Adiciona às informações dos agentes **/
$agente = $dom->createElement("agente");
$agente->appendChild($dom->createElement("id_agente",$tarifario->getAgente()->getId()));
$agente->appendChild($dom->createElement("razao",utf8_encode(remove_acentos($tarifario->getAgente()->getRazao()))));

$t->appendChild($agente);

$sub_agente = $dom->createElement("sub_agente");
$sub_agente->appendChild($dom->createElement("id_agente",$tarifario->getSubAgente()->getId()));
$sub_agente->appendChild($dom->createElement("razao",utf8_encode(remove_acentos($tarifario->getSubAgente()->getRazao()))));

$t->appendChild($sub_agente);

/** Adiciona o transit time onboard **/
$transit_time = $dom->createElement("transit_time_onboard",utf8_encode($tarifario->getTransitTime()));

$t->appendChild($transit_time);

/** Adiciona o breakdown do tarifario **/
$breakdown = $dom->createElement("breakdown",utf8_encode($tarifario->getBreakDown()));

$t->appendChild($breakdown);

/** Adiciona o frete de compra **/
$frete_compra = $dom->createElement("frete_compra");
$frete_compra->appendChild($dom->createElement("valor",number_format($tarifario->getFreteCompra(),2,".",",")));
$frete_compra->appendChild($dom->createElement("valor_minimo",number_format($tarifario->getFreteCompraMinimo(),2,".",",")));

$t->appendChild($frete_compra);

/** autonomia de frete **/
$autonomia = $dom->createElement("autonomia_frete",number_format($tarifario->getAutonomiaFrete(),2,".",","));

$t->appendChild($autonomia);

/** Aviso dos adicionais de frete negociados **/
if( isset($tarifario->adicional_negociado) )
{
    $adicional_ngociado = $dom->createElement("adicional_negociado",$tarifario->adicional_negociado);
    
    $t->appendChild($adicional_ngociado);
}    

$dom->appendChild($t);

echo $dom->saveXML();