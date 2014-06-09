<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Exportar_Pdf_Proposta
 *
 * Gera o pdf da proposta informada 
 *
 * @package models/Propostas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 26/07/2013
 * @version  versao 1.0
*/
class Notas_Propostas extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function selecionarNotas( Proposta $proposta )
	{
		$tipo_proposta = get_class($proposta);
		
		$nota = "";
				
		switch ($tipo_proposta)
		{
			case "Proposta_Cotacao";
				$nota = "- Oferta v�lida para carga geral, n�o perigosa, n�o perec�vel;
- A carga deve estar apropriadamente embalada para transporte mar�timo;
- Cargas perigosas, mais pesadas do que volumosas, n�o empilh�veis e com dimens�es que excedam 5,9m (comprimento) X 2,0m (largura) X 2,0m
- (altura), est�o sujeitas a aprova��o e cobran�a de taxas adicionais. As cota��es devem ser feitas caso a caso;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por for�a maior, podem ocorrer sem aviso pr�vio;
- O tempo de tr�nsito (on board) indicado na proposta � estimado e pode sofrer altera��es a qualquer momento;
- Todos os embarques est�o sujeitos a cobran�a de taxas locais na origem e no destino.
				";
			break;
			
			case "Proposta_Tarifario";
				$nota = "- Oferta valida para carga geral, nao perigosa, nao perecivel,
- A carga deve estar apropriadamente embalada para transporte maritimo,
- Cargas perigosas, mais pesadas do que volumosas, nao empilhaveis e com dimensoes que excedam 5,9m (comprimento) X 2,0m (largura) X 2,0m
- (altura), estao sujeitas a aprovacao e cobranca de taxas adicionais. As cotacoes devem ser feitas caso a caso,
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por forca maior, podem ocorrer sem aviso previo,
- O tempo de transito (on board) indicado na proposta e estimado e pode sofrer alteracoes a qualquer momento,
- Todos os embarques estao sujeitos a cobranca de taxas locais na origem e no destino.
				";
			break;
			
			case "Proposta_NAC";
				$nota = "- Oferta NAC (Named Account) v�lida apenas para os embarques do exportador mencionado na proposta;
- Oferta v�lida para carga geral, n�o perigosa, n�o perec�vel;
- A carga deve estar apropriadamente embalada para transporte mar�timo;
- Cargas perigosas, mais pesadas do que volumosas, n�o empilh�veis e com dimens�es que excedam 5,9m (comprimento) X 2,0m (largura) X 2,0m
- (altura), est�o sujeitas a aprova��o e cobran�a de taxas adicionais. As cota��es devem ser feitas caso a caso;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por for�a maior, podem ocorrer sem aviso pr�vio;
- O tempo de tr�nsito (on board) indicado na proposta � estimado e pode sofrer altera��es a qualquer momento;
- Todos os embarques est�o sujeitos a cobran�a de taxas locais na origem e no destino.
				";
			break;
			
			case "Proposta_Spot";
				$nota = "- Oferta v�lida especificamente para a rota e mercadoria descritas nesta proposta;
- A carga deve estar apropriadamente embalada para transporte mar�timo;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por for�a maior, podem ocorrer sem aviso pr�vio;
- O tempo de tr�nsito (on board) indicado na proposta � estimado e pode sofrer altera��es a qualquer momento;
- Todos os embarques est�o sujeitos a cobran�a de taxas locais na origem e no destino.
				";
			break;
			
			default:
				$nota = "";
			
		}
		
		return $nota;
		
	}

}