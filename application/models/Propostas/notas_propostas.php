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
				$nota = "- Oferta válida para carga geral, não perigosa, não perecível;
- A carga deve estar apropriadamente embalada para transporte marítimo;
- Cargas perigosas, mais pesadas do que volumosas, não empilháveis e com dimensões que excedam 5,9m (comprimento) X 2,0m (largura) X 2,0m
- (altura), estão sujeitas a aprovação e cobrança de taxas adicionais. As cotações devem ser feitas caso a caso;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por força maior, podem ocorrer sem aviso prévio;
- O tempo de trânsito (on board) indicado na proposta é estimado e pode sofrer alterações a qualquer momento;
- Todos os embarques estão sujeitos a cobrança de taxas locais na origem e no destino.
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
				$nota = "- Oferta NAC (Named Account) válida apenas para os embarques do exportador mencionado na proposta;
- Oferta válida para carga geral, não perigosa, não perecível;
- A carga deve estar apropriadamente embalada para transporte marítimo;
- Cargas perigosas, mais pesadas do que volumosas, não empilháveis e com dimensões que excedam 5,9m (comprimento) X 2,0m (largura) X 2,0m
- (altura), estão sujeitas a aprovação e cobrança de taxas adicionais. As cotações devem ser feitas caso a caso;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por força maior, podem ocorrer sem aviso prévio;
- O tempo de trânsito (on board) indicado na proposta é estimado e pode sofrer alterações a qualquer momento;
- Todos os embarques estão sujeitos a cobrança de taxas locais na origem e no destino.
				";
			break;
			
			case "Proposta_Spot";
				$nota = "- Oferta válida especificamente para a rota e mercadoria descritas nesta proposta;
- A carga deve estar apropriadamente embalada para transporte marítimo;
- Ajustes em valores, escalas, datas e navios praticados pelos armadores ou ocasionados por força maior, podem ocorrer sem aviso prévio;
- O tempo de trânsito (on board) indicado na proposta é estimado e pode sofrer alterações a qualquer momento;
- Todos os embarques estão sujeitos a cobrança de taxas locais na origem e no destino.
				";
			break;
			
			default:
				$nota = "";
			
		}
		
		return $nota;
		
	}

}