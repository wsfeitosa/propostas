<?php
include_once '/var/www/html/allink/Libs/remove_acentos.php';
Header("Content-type: application/xml; charset=ISO-8859-1");
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0','ISO-8859-1');

$xml->startElement("tarifas");

	foreach($tarifarios as $tarifa)
	{
						
		$xml->startElement("servico");

			$xml->startElement("id_tarifario");
				$xml->text($tarifa->getId());
			$xml->endElement();
		
			$xml->startElement("origem");
				$xml->text(remove_acentos($tarifa->getRota()->getPortoOrigem()->getNome()));
			$xml->endElement();
			
			$xml->startElement("embarque");
				$xml->text(remove_acentos($tarifa->getRota()->getPortoEmbarque()->getNome()));
			$xml->endElement();
			
			$xml->startElement("desembarque");
				$xml->text(remove_acentos($tarifa->getRota()->getPortoDesembarque()->getNome()));
			$xml->endElement();
			
			$xml->startElement("destino");
				$xml->text(remove_acentos($tarifa->getRota()->getPortoFinal()->getNome()));
			$xml->endElement();
			
			$xml->startElement("id_agente");
				$xml->text($tarifa->getAgente()->getId());
			$xml->endElement();
			
			$xml->startElement("agente");
				$xml->text(remove_acentos($tarifa->getAgente()->getRazao()));
			$xml->endElement();
			
			$xml->startElement("id_sub_agente");
				$xml->text($tarifa->getSubAgente()->getId());
			$xml->endElement();
				
			$xml->startElement("sub_agente");
				$xml->text(remove_acentos($tarifa->getSubAgente()->getRazao()));
			$xml->endElement();
			
			$xml->startElement("rota_principal");
				$xml->text(remove_acentos($tarifa->getRotaPrincipal()));
			$xml->endElement();

			if(isset($tarifa->numero_proposta))
			{
				$xml->startElement("numero_proposta");
					$xml->text(remove_acentos($tarifa->numero_proposta));
				$xml->endElement();
			}	

			if(isset($tarifa->nome_nac))
			{
				$xml->startElement("nome_nac");
					$xml->text(remove_acentos($tarifa->nome_nac));
				$xml->endElement();				
			}
			
			foreach( $tarifa->getTaxa() as $taxa )
			{
				if( $taxa->getId() == 10 )
				{
					$xml->startElement("frete");
						
						$xml->startElement("valor");
							$xml->text(number_format($taxa->getValor(),2,".",","));
						$xml->endElement();
						
						$xml->startElement("valor_minimo");
							$xml->text(number_format($taxa->getValorMinimo(),2,".",","));
						$xml->endElement();
						
						$xml->startElement("valor_maximo");
							$xml->text(number_format($taxa->getValorMaximo(),2,".",","));
						$xml->endElement();
						
						$xml->startElement("moeda");
							$xml->text($taxa->getMoeda()->getSigla());
						$xml->endElement();
						
						$xml->startElement("unidade");
							$xml->text($taxa->getUnidade()->getUnidade());
						$xml->endElement();
					
					$xml->endElement();
				}	
			}	
		
		$xml->endElement();
	}	

$xml->endElement();

echo $xml->outputMemory(true);