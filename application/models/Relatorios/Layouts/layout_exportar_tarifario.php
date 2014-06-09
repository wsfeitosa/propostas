<?php
/**
 * Layout_Exportar_Tarifario
 *
 * Layout de exportação do tarifário para o usuário alterar os valores dos frete e importar novamente no sistema 
 *
 * @package models/Relatorios/Layouts
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink tdansporte Internacionais LTDA. - 28/06/2013
 * @version  versao 1.0
*/
include APPPATH."/models/Relatorios/Layouts/layout.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Libs/remove_acentos.php";

class Layout_Exportar_Tarifario implements Layout {
	
	public function __construct() 
	{
		
	}
	/**
	 * aplicarLayout
	 *
	 * Cria o layout da planilha que será exportada para os usuários alterarem os valors dos fretes
	 * e importarem de volta no sistema
	 *
	 * @name aplicarLayout
	 * @access public
	 * @param Relatorio $relatorio
	 * @return stding $layout
	 */	
	public function aplicarLayout( Relatorio $relatorio )
	{
		
		$relatorio->gerar();
		
		$proposta = $relatorio->obterParametros();
						
		if( ! $proposta[0] instanceof Proposta_Tarifario )
		{
			throw new Exception("Este tipo de proposta não pode ser exportado pelo sistema! ".get_class($proposta[0]));
		}	
		
		$layout ="<table border='1'>
					<tr>
						<b>
						<td>ID TAXA</td>
						<td>ID ITEM</td>
						<td>SENTIDO</td>
						<td>NUMERO PROPOSTA</td>
						<td>CLIENTE(S)</td>
						<td>ORIGEM</td>
						<td>UNCODE ORIGEM</td>
						<td>EMBARQUE</td>
						<td>UNCODE EMBARQUE</td>
						<td>DESEMBARQUE</td>
						<td>UNCODE DESEMBARQUE</td>
						<td>DESTINO</td>
						<td>UNCODE DESTINO</td>	
						<td>VIA ADICIONAL</td>
						<td>UNCODE VIA ADICIONAL</td>
						<td>TAXA</td>
						<td>VALOR</td>
						<td>VALOR MINIMO</td>
						<td>VALOR MAXIMO</td>
						<td>UNIDADE</td>
						<td>MOEDA</td>
						<td>INICIO</td>
						<td>VALIDADE</td>
						</b>
					</tr>";
		
		$cont = 1;			

		foreach($proposta[0]->getItens() as $item)
		{									
			$clientes = (string)"";
			$valor = (float)0.00;
			$valor_minimo = (float)0.00;
			$valor_maximo = (float)0.00;
			$unidade = (string)"";
			$moeda = (string)"";
									
			/** Obtem e formata os clientes da proposta **/
			foreach( $proposta[0]->getClientes() as $cliente )
			{
				$clientes .= $cliente->getCnpj() . "-" . $cliente->getRazao()." | ";
			}
			/**
			if( $cont != 1 )
			{
				$clientes = "";
			}	
			**/
			/** Obtem à taxa de frete **/
			foreach( $item->getTarifario()->getTaxa() as $taxa )
			{	
				/**			
				if( $taxa instanceof Taxa_Adicional )
				{
					$valor = sprintf("%01.2f",$taxa->getValor());
					$valor_minimo = sprintf("%01.2f",$taxa->getValorMinimo());
					$valor_maximo = sprintf("%01.2f",$taxa->getValorMaximo());
					$unidade = $taxa->getUnidade()->getUnidade();					
					$moeda = $taxa->getMoeda()->getSigla();
					$nome_taxa = $taxa->getNome();
				}
				else
				{
					continue;
				}		
				**/

				$valor = sprintf("%01.2f",$taxa->getValor());
				$valor_minimo = sprintf("%01.2f",$taxa->getValorMinimo());
				$valor_maximo = sprintf("%01.2f",$taxa->getValorMaximo());
				$unidade = $taxa->getUnidade()->getUnidade();					
				$moeda = $taxa->getMoeda()->getSigla();
				$nome_taxa = $taxa->getNome();
				
				if( $unidade == "M³" )
				{
					$unidade = "M3";
				}	

				if( $moeda == "R$" )
				{
					$moeda = "BRL";
				}	

				$layout .= "<tr>
								<td>{$taxa->getId()}</td>
								<td>{$item->getId()}</td>
								<td>{$proposta[0]->getSentido()}</td>
								<td>{$item->getNumero()}</td>
								<td>".remove_acentos($clientes)."</td>
								<td>".remove_acentos($item->getTarifario()->getRota()->getPortoOrigem()->getNome())."</td>
								<td>{$item->getTarifario()->getRota()->getPortoOrigem()->getUnCode()}</td>
								<td>".remove_acentos($item->getTarifario()->getRota()->getPortoEmbarque()->getNome())."</td>
								<td>{$item->getTarifario()->getRota()->getPortoEmbarque()->getUnCode()}</td>
								<td>".remove_acentos($item->getTarifario()->getRota()->getPortoDesembarque()->getNome())."</td>
								<td>{$item->getTarifario()->getRota()->getPortoDesembarque()->getUnCode()}</td>
								<td>".remove_acentos($item->getTarifario()->getRota()->getPortoFinal()->getNome())."</td>
								<td>{$item->getTarifario()->getRota()->getPortoFinal()->getUnCode()}</td>
								<td>".remove_acentos($item->getTarifario()->getRota()->getPortoViaAdicional()->getNome())."</td>
								<td>{$item->getTarifario()->getRota()->getPortoViaAdicional()->getUnCode()}</td>							
								<td>".remove_acentos($nome_taxa)."</td>
								<td>{$valor}</td>
								<td>{$valor_minimo}</td>
								<td>{$valor_maximo}</td>
								<td>{$unidade}</td>
								<td>{$moeda}</td>
								<td>{$item->getInicio()->format('d/m/Y')}</td>
								<td>{$item->getValidade()->format('d/m/Y')}</td>
							</tr>";
			}

			$cont++;

		}	
		
		return $layout;
		
	}
	
}//END CLASS