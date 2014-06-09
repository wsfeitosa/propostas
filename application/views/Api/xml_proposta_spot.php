<?php
include_once "/var/www/html/allink/Libs/remove_acentos.php";
ob_end_clean();
Header("Content-type: application/xml; charset=UTF-8");
$dom = new DOMDocument();

$dom->formatOutput = TRUE;

$t = $dom->createElement("tarifario");

$tarifario = $item->getTarifario();

$t->appendChild($dom->createElement("id_proposta",$item->getId()));

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
$id_porto_origem = $dom->createElement("id",$tarifario->getRota()->getPortoOrigem()->getId());
$nome_porto_origem = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoOrigem()->getNome()));
$uncode_origem = $dom->createElement("uncode",$tarifario->getRota()->getPortoOrigem()->getUnCode());

$porto_origem->appendChild($id_porto_origem);
$porto_origem->appendChild($nome_porto_origem);
$porto_origem->appendChild($uncode_origem);

$rota->appendChild($porto_origem);

/** Porto de embarque **/
$porto_embarque = $dom->createElement("embarque");
$id_porto_embarque = $dom->createElement("id",$tarifario->getRota()->getPortoembarque()->getId());
$nome_porto_embarque = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoEmbarque()->getNome()));
$uncode_embarque = $dom->createElement("uncode",$tarifario->getRota()->getPortoEmbarque()->getUnCode());

$porto_embarque->appendChild($id_porto_embarque);
$porto_embarque->appendChild($nome_porto_embarque);
$porto_embarque->appendChild($uncode_embarque);

$rota->appendChild($porto_embarque);

/** Porto de desembarque **/
$porto_desembarque = $dom->createElement("desembarque");
$id_porto_desembarque = $dom->createElement("id",$tarifario->getRota()->getPortoDesembarque()->getId());
$nome_porto_desembarque = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoDesembarque()->getNome()));
$uncode_desembarque = $dom->createElement("uncode",$tarifario->getRota()->getPortoDesembarque()->getUnCode());

$porto_desembarque->appendChild($id_porto_desembarque);
$porto_desembarque->appendChild($nome_porto_desembarque);
$porto_desembarque->appendChild($uncode_desembarque);

$rota->appendChild($porto_desembarque);

/** Porto de destino **/
$porto_destino = $dom->createElement("destino");
$id_porto_destino = $dom->createElement("id",$tarifario->getRota()->getPortoFinal()->getId());
$nome_porto_destino = $dom->createElement("nome",utf8_encode($tarifario->getRota()->getPortoFinal()->getNome()));
$uncode_destino = $dom->createElement("uncode",$tarifario->getRota()->getPortoFinal()->getUnCode());

$porto_destino->appendChild($id_porto_destino);
$porto_destino->appendChild($nome_porto_destino);
$porto_destino->appendChild($uncode_destino);

$rota->appendChild($porto_destino);

$t->appendChild($rota);

$observacao = $dom->createElement("observacao",utf8_decode(remove_acentos($tarifario->getObservacao())));

$t->appendChild($observacao);

/** Taxas adicionais **/
$taxas_adicionais = $dom->createElement("taxas_adicionais");
$taxas_locais = $dom->createElement("taxas_locais");

foreach($tarifario->getTaxa() as $taxa):

/** Remove ?s taxas zeradas **/
if( $taxa->getValor() < 1 )
{
	continue;
}

if( $taxa instanceof Taxa_Adicional )
{		
	$taxa_adicional = $dom->createElement("taxa");
	$taxa_adicional->appendChild($dom->createElement("id_taxa",$taxa->getId()));
	$taxa_adicional->appendChild($dom->createElement("nome",utf8_encode(remove_acentos($taxa->getNome()))));
	$taxa_adicional->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_adicional->appendChild($dom->createElement("unidade",$taxa->getUnidade()->getUnidade()));
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
	$taxa_local->appendChild($dom->createElement("nome",utf8_encode(remove_acentos($taxa->getNome()))));
	$taxa_local->appendChild($dom->createElement("id_unidade",$taxa->getUnidade()->getId()));
	$taxa_local->appendChild($dom->createElement("unidade",$taxa->getUnidade()->getUnidade()));
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

/** Adiciona ?s informa??es dos agentes **/
$agente = $dom->createElement("agente");
$agente->appendChild($dom->createElement("id_agente",$tarifario->getAgente()->getId()));
$agente->appendChild($dom->createElement("razao",utf8_encode(remove_acentos($tarifario->getAgente()->getRazao()))));

$t->appendChild($agente);

$sub_agente = $dom->createElement("sub_agente");
$sub_agente->appendChild($dom->createElement("id_agente",$tarifario->getSubAgente()->getId()));
$sub_agente->appendChild($dom->createElement("razao",utf8_encode(remove_acentos($tarifario->getSubAgente()->getRazao()))));

$t->appendChild($sub_agente);

/** Adiciona o transit time onboard **/
$transit_time = $dom->createElement("transit_time_onboard",$tarifario->getTransitTime());

$t->appendChild($transit_time);

/** Adiciona o breakdown do tarifario **/
$breakdown = $dom->createElement("breakdown",$tarifario->getBreakDown());

$t->appendChild($breakdown);

/** Adiciona o frete de compra **/
$frete_compra = $dom->createElement("frete_compra");
$frete_compra->appendChild($dom->createElement("valor",number_format($tarifario->getFreteCompra(),2,".",",")));
$frete_compra->appendChild($dom->createElement("valor_minimo",number_format($tarifario->getFreteCompraMinimo(),2,".",",")));

$t->appendChild($frete_compra);

/** autonomia de frete **/
$autonomia = $dom->createElement("autonomia_frete",number_format($tarifario->getAutonomiaFrete(),2,".",","));

$t->appendChild($autonomia);

/** Peso, cubagem e volumes **/
$peso = $dom->createElement("peso",number_format($item->getPeso(),3,".",","));
$t->appendChild($peso);

$cubagem = $dom->createElement("cubagem",number_format($item->getCubagem(),3,".",","));
$t->appendChild($cubagem);

$volumes = $dom->createElement("volumes",$item->getVolumes());
$t->appendChild($volumes);

/** Mercadoria da proposta **/
$mercadoria = $dom->createElement("mercadoria",remove_acentos(urldecode($item->getMercadoria())));
$t->appendChild($mercadoria);

/** observa??es da proposta **/
$observacao_cliente = $dom->createElement("observacao_cliente",remove_acentos(utf8_encode(urldecode($item->getObservacaoCliente()))));
$t->appendChild($observacao_cliente);

$observacao_interna = $dom->createElement("observacao_interna", remove_acentos(utf8_encode(urldecode($item->getObservacaoInterna()))));
$t->appendChild($observacao_interna);

$nome_nac = $dom->createElement("nome_nac", remove_acentos(urldecode($item->nome_nac)));
$t->appendChild($nome_nac);

/** Cliente da proposta **/
$cliente_proposta = $dom->createElement("cliente");
$cliente_proposta->appendChild($dom->createElement("razao",remove_acentos($cliente->getRazao())));
$cliente_proposta->appendChild($dom->createElement("id_cliente",$cliente->getId()));
$cliente_proposta->appendChild($dom->createElement("classificacao",$cliente->getClassificacao()));
$t->appendChild($cliente_proposta);

$aceita_modalidade_pp = $dom->createElement("aceita_modalidade_pp",$item->getPp());
$t->appendChild($aceita_modalidade_pp);

$aceita_modalidade_cc = $dom->createElement("aceita_modalidade_cc",$item->getCc());
$t->appendChild($aceita_modalidade_cc);

$dom->appendChild($t);

echo @$dom->saveXML();