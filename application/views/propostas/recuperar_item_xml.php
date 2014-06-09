<?php
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = "<item>
			<id>".$id_item."</id>\n
			<id_tarifario>".$item["id_tarifario"]."</id_tarifario>\n
			<mercadoria>".urldecode($item['mercadoria'])."</mercadoria>\n
			<pp>".$item['pp']."</pp>\n
			<cc>".$item['cc']."</cc>\n			
			<imo>".$item['imo']."</imo>\n
			<inicio>".$item['inicio']."</inicio>\n		
			<validade>".$item['validade']."</validade>\n
			<peso>".$item['peso']."</peso>\n
			<cubagem>".$item['cubagem']."</cubagem>\n
			<volumes>".$item['volumes']."</volumes>\n
			<origem>".$item['origem']."</origem>\n
			<embarque>".$item['embarque']."</embarque>\n
			<desembarque>".$item['desembarque']."</desembarque>\n
			<destino>".$item['destino']."</destino>\n
			<un_origem>".$item['un_origem']."</un_origem>\n
			<un_embarque>".$item['un_embarque']."</un_embarque>\n
			<un_desembarque>".$item['un_desembarque']."</un_desembarque>\n
			<un_destino>".urldecode($item['un_destino'])."</un_destino>\n
			<frete_adicionais>".urldecode($item['frete_adicionais'])."</frete_adicionais>\n
			<labels_frete_adicionais>".urldecode($item['labels_frete_adicionais'])."</labels_frete_adicionais>\n
			<taxas_locais>".urldecode($item['taxas_locais'])."</taxas_locais>\n
			<labels_taxas_locais>".urldecode($item['labels_taxas_locais'])."</labels_taxas_locais>\n
			<observacao_interna>".urldecode($item['observacao_interna'])."</observacao_interna>\n
			<observacao_cliente>".urldecode($item['observacao_cliente'])."</observacao_cliente>\n
			<erro>".$erro."</erro>\n
		</item>";
echo $xml;