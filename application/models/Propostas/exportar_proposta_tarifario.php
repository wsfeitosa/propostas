<?php
set_time_limit(0);
ini_set("memory_limit", "2048M");
/**
 * Exportar_Proposta_Tarifario
 *
 * Exporta uma proposta tarifário em formato adequeado para ser enviado ao cliente
 *
 * @package models/Propostas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 03/07/2013
 * @version  versao 1.0
*/
include_once $_SERVER['DOCUMENT_ROOT'] . "/Libs/php_excel/Classes/PHPExcel.php";
include_once APPPATH."/models/Taxas/taxa_local_model.php";
include_once APPPATH."/models/Clientes/cliente.php";
include_once APPPATH."/models/Clientes/cliente_model.php";
include_once APPPATH."/models/Clientes/define_classificacao.php";
include_once APPPATH."/models/Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente.php";
include_once APPPATH."/models/Tarifario/porto.php";

class Exportar_Proposta_Tarifario extends CI_Model {
	
	const TMP_DIR = "/var/www/html/allink/relatorios_temp/";
	
	protected $proposta = NULL;
	protected $arquivo = FALSE;
	protected $excel = NULL;
	protected $frequencias = Array(
									 1 => "SEMANAL", 2 => "QUINZENAL", 3 => "DEZENAL", 4 => "MENSAL",
									 5 => "2 / SEMANA", 6 => "3 / SEMANA", 7 => "4 / SEMANA", 8 => "5 / SEMANA", 
			  );

	protected $meses = Array(
								1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4=> "Abril",
								5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto",
								9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro",
			  );
	
	const FONT = "Calibri";
	const SIZE = 8;	
	
	public function __construct(Proposta_Tarifario $proposta = NULL,$arquivo = FALSE) 
	{		
		parent::__construct();
		
		if(isset($proposta))
		{	
			$this->proposta = $proposta;
		}	
		
		$this->excel = new PHPExcel();
		
		$this->arquivo = $arquivo;

		$this->load->model("Propostas/notas_propostas");
	}
	
	public function setPropostaTarifario(Proposta_Tarifario $proposta, $arquivo = FALSE )
	{
		$this->proposta = $proposta;
		$this->arquivo = $arquivo;
		return $this;
	}
	
	public function exportar()
	{		
		include_once APPPATH."/models/Taxas_Locais_Acordadas/conversor_taxas.php";
		
		$conversor = new Conversor_Taxas();
		
		$this->excel->getDefaultStyle()->getFont()->setName(Exportar_Proposta_Tarifario::FONT);
		$this->excel->getDefaultStyle()->getFont()->setSize(Exportar_Proposta_Tarifario::SIZE);
				
		$this->excel->setActiveSheetIndex(0);
		
		$planilha_atual = $this->excel->getActiveSheet();
										
		$planilha_atual->setShowGridlines(false); // Linhas de Grade
		
		$planilha_atual->setTitle("Freight");
						
		/** Imagem do Logo da Allink **/
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('/var/www/html/allink/Imagens/allink.jpg');
		$objDrawing->setHeight(50);
		$objDrawing->setCoordinates('A1');
		
		$objDrawing->setWorksheet($planilha_atual);	
		
		if( $this->proposta->getSentido() == "EXP" )
		{
			$sentido = "EXPORTAÇÃO";
			$country_label = "DESTINATION";
		}
		else
		{
			$sentido = "IMPORTAÇÃO";
			$country_label = "ORIGIN";
		}		
						
		$planilha_atual->setCellValue("B1",("TARIFARIO LCL ".utf8_encode($sentido)." - ".strtoupper($this->meses[date("n")]). " ".date('Y')));

		$planilha_atual->getStyle("B1")->getFont()->setBold(true)->setSize(12);
										
		$planilha_atual->getStyle("B2")->getFont()->setBold(true)->setSize(10);
						
		$planilha_atual->setCellValue("G3","OFR")->getStyle("D3")->getFont()->setBold(true);
		$planilha_atual->setCellValue("H3","OFR")->getStyle("E3")->getFont()->setBold(true);
		$planilha_atual->setCellValue("J3","%")->getStyle("D3")->getFont()->setBold(true);
		$planilha_atual->setCellValue("K3","WM")->getStyle("E3")->getFont()->setBold(true);
		
		$planilha_atual->getStyle('A3:V3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		/** linhas de Cabeçalho **/					
		$planilha_atual->setCellValue("A4", "ORIGIN")->getStyle("A4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("B4", "LOADING PORT")->getStyle("B4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("C4", "DISCHARGE PORT")->getStyle("C4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("D4", "DESTINATION")->getStyle("D4")->applyFromArray($this->getExcelStyles('dark_blue'));	
		$planilha_atual->setCellValue("E4", "ADITTIONAL PORT")->getStyle("E4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("F4", $country_label." COUNTRY")->getStyle("F4")->applyFromArray($this->getExcelStyles('dark_blue'));		
		$planilha_atual->setCellValue("G4", "CURRENCY")->getStyle("G4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("H4", "WM")->getStyle("H4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("I4", "MIN")->getStyle("I4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("J4", "BUNKER")->getStyle("J4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("K4", "EFAF")->getStyle("K4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("L4", "ADITIONAL OF FREIGHT")->getStyle("L4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("M4", "SEE SPECIFIC CHARGES")->getStyle("M4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("N4", "TT 1")->getStyle("N4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("O4", "FREQ. 1")->getStyle("O4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("P4", "TT 2")->getStyle("P4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("Q4", "FREQ. 2")->getStyle("Q4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("R4", "AGENT")->getStyle("R4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("S4", "REMARKS")->getStyle("S4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("T4", "CBM = Peso")->getStyle("T4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("U4", "INICIO")->getStyle("U4")->applyFromArray($this->getExcelStyles('dark_blue'));
		$planilha_atual->setCellValue("V4", "VALIDADE")->getStyle("V4")->applyFromArray($this->getExcelStyles('dark_blue'));
		
		$itens_proposta = $this->proposta->getItens();
		
		/** Retorna às taxas do tarifário **/
		include_once APPPATH."/models/Taxas/compara_taxas.php";
		include_once APPPATH."/models/Taxas/serializa_taxas.php";
		
		foreach( $itens_proposta as $k=>$item )
		{
			
			$v = $k+5;
			
			$via = NULL;

			/** Seleciona a via **/
			if( $this->proposta->getSentido() == "IMP" )
			{							
				$pais = $item->getTarifario()->getRota()->getPortoOrigem()->getPais();

				$this->db->
						select("transit_receipt_x_loading, transit_loading_x_via,
							    id_frequencia_receipt_x_loading, id_frequencia_loading_x_via")->
						from("FINANCEIRO.tarifarios_pricing")->
						where("tarifarios_pricing.id_tarifario_pricing",$item->getTarifario()->getId());

				$rs_transit_time = $this->db->get();
				
				$linhas_transit_time = $rs_transit_time->num_rows();
				
				if( $linhas_transit_time < 1 )
				{
					show_error("Impossivel recuperar o transit time do tarifário");
				}	
				
				$transit_time = $rs_transit_time->row();
				
				$tt1 = $transit_time->transit_receipt_x_loading;
				$tt2 = $transit_time->transit_loading_x_via;
				$frequencia1 = $this->frequencias[$transit_time->id_frequencia_receipt_x_loading];
				$frequencia2 = $this->frequencias[$transit_time->id_frequencia_loading_x_via];		
			}
			else
			{
				$pais = $item->getTarifario()->getRota()->getPortoFinal()->getPais();

				/** Obtem o transit time e a frequencia **/			
				$this->db->
						select("transit_loading_x_via, transit_via_x_place_delivery,
							    id_frequencia_loading_x_via, id_frequencia_via_x_place_delivery")->
						from("FINANCEIRO.tarifarios_pricing")->
						where("tarifarios_pricing.id_tarifario_pricing",$item->getTarifario()->getId());
				
				$rs_transit_time = $this->db->get();
				
				$linhas_transit_time = $rs_transit_time->num_rows();
				
				if( $linhas_transit_time < 1 )
				{
					show_error("Impossivel recuperar o transit time do tarifário");
				}	
				
				$transit_time = $rs_transit_time->row();
				
				$tt1 = $transit_time->transit_loading_x_via;
				$tt2 = $transit_time->transit_via_x_place_delivery;
				$frequencia1 = $this->frequencias[$transit_time->id_frequencia_loading_x_via];
				$frequencia2 = $this->frequencias[$transit_time->id_frequencia_via_x_place_delivery];
			}		
															
			$serializador = new Serializa_Taxas();
		
			$comparador = new Compara_Taxas();
		
			$taxas_separadas = $comparador->separaTaxasPorTipo($item->getTarifario()->getTaxa());
			
			$frete = (float)0.00;
			$frete_min = (float)0.00;
			$moeda_frete = (string)"";
			$bunker = (float)0.00;
			$efaf = (float)0.00;
			$adicionais_frete = Array();
			
			foreach( $taxas_separadas['taxas_adicionais'] as $taxa_adicional )
			{
												
				if( $taxa_adicional->getId() == 10 )
				{
					$frete = $taxa_adicional->getValor();
					$frete_min = $taxa_adicional->getValorMinimo();
					$moeda_frete = $taxa_adicional->getMoeda()->getSigla();					
				}

				if( $taxa_adicional->getId() == 13 )
				{
					$bunker = $taxa_adicional->getValor() . " " . $taxa_adicional->getUnidade()->getUnidade();
				}	
				
				if( $taxa_adicional->getId() == 1060 )
				{
					$efaf = $taxa_adicional->getValor() . " " . $taxa_adicional->getUnidade()->getUnidade();
				}

				if( $taxa_adicional->getId() != 10 && $taxa_adicional->getId() != 13 && $taxa_adicional->getId() != 1060 )
				{					
					$adicionais_frete[] = $taxa_adicional; 
				}	
				
			}	
			
			$taxas_serializadas = $serializador->SerializaTaxasParaSessao($adicionais_frete);
			
			$taxas_serializadas = str_replace("---", "\r\n", $taxas_serializadas['label_taxas']);

			$agente = "";
			
			/** Verifica se o agente foi preenchido no tarifário **/
			if( is_object($item->getTarifario()->getSubAgente()) )
			{
				$agente = utf8_encode($item->getTarifario()->getSubAgente()->getRazao());
			}	
			
			/** Razão entre peso e cubagem registrada no tarifário **/
			$peso_ratio = (float)0.000;
			$cubagem_ratio = (float)0.000;

			$this->db->
					select("peso_ratio, cubagem_ratio")->
					from("FINANCEIRO.tarifarios_pricing")->
					where("id_tarifario_pricing",$item->getTarifario()->getId());

			$rs_ratio = $this->db->get();

			$row_ratio = $rs_ratio->row();

			$peso_ratio = $row_ratio->peso_ratio;

			$cubagem_ratio = $row_ratio->cubagem_ratio;	

			$planilha_atual->setCellValue("A".$v, $item->getTarifario()->getRota()->getPortoOrigem()->getNome())->getStyle("A".$v)->applyFromArray($this->getExcelStyles('light_blue'));
			$planilha_atual->setCellValue("B".$v, $item->getTarifario()->getRota()->getPortoEmbarque()->getNome())->getStyle("B".$v)->applyFromArray($this->getExcelStyles('light_blue'));
			$planilha_atual->setCellValue("C".$v, $item->getTarifario()->getRota()->getPortoDesembarque()->getNome())->getStyle("C".$v)->applyFromArray($this->getExcelStyles('light_blue'));			
			$planilha_atual->setCellValue("D".$v, $item->getTarifario()->getRota()->getPortoFinal()->getNome())->getStyle("D".$v)->applyFromArray($this->getExcelStyles('light_blue'));			
			$planilha_atual->setCellValue("E".$v, $item->getTarifario()->getRota()->getPortoViaAdicional()->getNome())->getStyle("E".$v)->applyFromArray($this->getExcelStyles('light_blue'));
			$planilha_atual->setCellValue("F".$v, utf8_encode($pais))->getStyle("F".$v)->applyFromArray($this->getExcelStyles('light_blue'));			
			$planilha_atual->getCell("G".$v)->setValueExplicit($moeda_frete, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("G".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("H".$v)->setValueExplicit(sprintf("%01.2f", $frete), PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("H".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("I".$v)->setValueExplicit(sprintf("%01.2f", $frete_min), PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("I".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("J".$v)->setValueExplicit(sprintf("%01.2f", $bunker), PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("J".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("K".$v)->setValueExplicit(sprintf("%01.2f", $efaf), PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("K".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("L".$v, $taxas_serializadas)->getStyle("L".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("M".$v, "SEE SPECIFIC CHARGES")->getStyle("M".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("N".$v)->setValueExplicit($tt1, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("N".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("O".$v, $frequencia1)->getStyle("O".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->getCell("P".$v)->setValueExplicit($tt2, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("P".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("Q".$v, $frequencia2)->getStyle("Q".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("R".$v, $agente)->getStyle("R".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("S".$v, "SEE ADDITIONAL INFORMATION")->getStyle("S".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("T".$v, $cubagem_ratio . ' = ' . $peso_ratio)->getStyle("T".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("U".$v, $item->getInicio()->format('d/m/Y'))->getStyle("U".$v)->applyFromArray($this->getExcelStyles('white'));
			$planilha_atual->setCellValue("V".$v, $item->getValidade()->format('d/m/Y'))->getStyle("V".$v)->applyFromArray($this->getExcelStyles('white'));
			
			$planilha_atual->getCell("S".$v)->getHyperlink('"SEE ADDITIONAL INFORMATION"')->setUrl('http://'.$_SERVER['SERVER_ADDR']."/Clientes/tarifario/aditional_information.php?key=".$item->getTarifario()->getId());
			$planilha_atual->getCell("M".$v)->getHyperlink('"SEE SPECIFIC CHARGES"')->setUrl('http://'.$_SERVER['SERVER_ADDR']."/Clientes/tarifario/specific_charges.php?key=".$item->getId());

		}	
		
		$planilha_atual->getDefaultColumnDimension()->setWidth(20);		
		
		$planilha_atual->getColumnDimension("E")->setWidth(35);
		$planilha_atual->getColumnDimension("Q")->setWidth(35);
		$planilha_atual->getColumnDimension("K")->setWidth(40);
		$planilha_atual->getColumnDimension("L")->setWidth(40);
		$planilha_atual->getColumnDimension("R")->setWidth(40);
		
		$planilha_atual->getRowDimension(1)->setRowHeight(30);
				
		//$planilha_atual->getProtection()->setSheet(true);
		
		/** Muda de sheet na planilha para criar às taxas locais do cliente **/
		$this->excel->createSheet(1);
		
		$this->excel->setActiveSheetIndex(1);
		
		$planilha_taxas_locais = $this->excel->getActiveSheet();
		
		$planilha_taxas_locais->setShowGridlines(false); // Linhas de Grade
		
		$planilha_taxas_locais->setTitle("Local Charges");
		
		$planilha_taxas_locais->setCellValue("B1", "ATUALIZADO em ". date('d') . " de " . $this->meses[date('n')] . " de " . date('Y'))->getStyle()->applyFromArray(Array('font' => array('bold' => true)));
		
		$planilha_taxas_locais->mergeCells("B2:O2")->getStyle("B2:O2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$planilha_taxas_locais->setCellValue("B2","LCL LOCAL CHARGES FOR SHIPMENTS")->getStyle("B2:O2")->applyFromArray($this->getExcelStyles('green'));
						
		$planilha_taxas_locais->getStyle('B2:O2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$planilha_taxas_locais->mergeCells("B3:E3")->getStyle("B3:E3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$planilha_taxas_locais->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$planilha_taxas_locais->setCellValue("B3", "PORT")->getStyle('E3:O3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$planilha_taxas_locais->mergeCells("F3:O3")->getStyle("F3:O3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$planilha_taxas_locais->setCellValue("F3", "CHARGES")->getStyle('F3:O3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$planilha_taxas_locais->getStyle("B3:O3")->applyFromArray(Array('font' => array('bold' => true)));
		
		$planilha_taxas_locais->getStyle("E3:O3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		/** Escreve às taxas locais **/		
		$taxas_locais_cliente_forwarder = $this->getLocalCharges();
		
		$cont = 4;
				
		foreach( $taxas_locais_cliente_forwarder as $nome_porto=>$taxas_locais )
		{

			if( ! is_array($taxas_locais) || count($taxas_locais) < 1 )
			{
				continue;
			}	
			
			$quantidade_taxas_porto = 0;
			$linhas_mescladas = 0;
			$altura_celula_nome_porto = 0;
			
			$quantidade_taxas_porto = count($taxas_locais);
				
			$linhas_mescladas = (int)($cont + $quantidade_taxas_porto) + 1;
			
			$altura_celula_nome_porto = $quantidade_taxas_porto * 20;
			
			$planilha_taxas_locais->getRowDimension($cont)->setRowHeight($altura_celula_nome_porto);
						
			$planilha_taxas_locais->mergeCells("B".$cont.":"."E".$linhas_mescladas)->getStyle("B".$cont.":"."E".$linhas_mescladas)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$planilha_taxas_locais->setCellValue("B".$cont, $nome_porto)->getStyle("B".$cont)->applyFromArray(Array('font' => array('bold' => true)));
			
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
															
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			
			/** Coloca um alinha em branco antes das taxas locais para separa-las **/
			$planilha_taxas_locais->getRowDimension($cont)->setRowHeight((int)20);
			$planilha_taxas_locais->mergeCells("F".$cont.":O".$cont)->getStyle("F".$cont.":F".$cont)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);;
			$planilha_taxas_locais->getStyle("F".$cont.":O".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							
			$cont++;
			
			/** Taxas **/								
			foreach( $taxas_locais as $taxa_local )
			{				
				$taxa_serializada = $conversor->serializaTaxa($taxa_local);
				
				$planilha_taxas_locais->getRowDimension($cont)->setRowHeight(20);
				
				$planilha_taxas_locais->mergeCells("F".$cont.":O".$cont)->getStyle("F".$cont.":O".$cont)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
				$planilha_taxas_locais->getStyle("F".$cont.":O".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$planilha_taxas_locais->setCellValue("F".$cont, utf8_encode($taxa_serializada['label']))->getStyle("F".$cont)->applyFromArray(Array('font' => array('bold' => true)));
				
				/** Bordas da primeira coluna **/
				$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								
				$cont++;							
			}		
			
			/** Coloca um alinha em branco depois das taxas locais para separa-las **/
			$planilha_taxas_locais->getRowDimension($cont)->setRowHeight((int)20);
			$planilha_taxas_locais->mergeCells("F".$cont.":O".$cont)->getStyle("F".$cont.":F".$cont)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);;
			$planilha_taxas_locais->getStyle("F".$cont.":O".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("F".$cont.":O".$cont)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			/** Bordas da primeira coluna **/
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$planilha_taxas_locais->getStyle("B".$cont.":E".$cont)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						
			$cont++;						
		}	
		
		/** Planilha com às informações importantes **/				
		$this->excel->createSheet(2);

		$this->excel->setActiveSheetIndex(2);
		
		$planilha_informacoes_importantes = $this->excel->getActiveSheet();
										
		$planilha_informacoes_importantes->setShowGridlines(false); // Linhas de Grade
		
		$planilha_informacoes_importantes->setTitle("Important Information");

		include_once '/var/www/html/allink/Libs/remove_acentos.php';
		
		$planilha_informacoes_importantes->getDefaultRowDimension()->setRowHeight(120);

		$planilha_informacoes_importantes->mergeCells("A1:W1");

		$planilha_informacoes_importantes->
										getCell("A1")->
										setValueExplicit(trim(utf8_encode($this->notas_propostas->selecionarNotas($this->proposta))), PHPExcel_Cell_DataType::TYPE_STRING)->
										getStyle("A1")->
										applyFromArray($this->getExcelStyles('white'));

		/** Bloqueia a planilha **/		
		$planilha_taxas_locais->getDefaultRowDimension()->setRowHeight(100);		
		
		$planilha_taxas_locais->getRowDimension(1)->setRowHeight(13);
		
		$planilha_taxas_locais->getRowDimension(2)->setRowHeight(13);
		
		$planilha_taxas_locais->getRowDimension(3)->setRowHeight(13);
		
		//$planilha_taxas_locais->getProtection()->setSheet(true);
				
		$this->excel->setActiveSheetIndex(0);

		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '512MB');
        if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings))
            die('PHPExcel caching error');

		if( $this->arquivo === FALSE )
		{			
			//header('Content-Type: application/vnd.ms-excel');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="proposta_tarifario'.time().'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');									
		}
		else
		{
			$nome_arquivo = "excel_proposta_tarifario_".date('YmdHis').".xls";
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save( self::TMP_DIR . $nome_arquivo );
			return self::TMP_DIR . $nome_arquivo;
		}			
	}
	
	/**
	 * getLocalCharges
	 *
	 * Obtem às taxas locais do cliente
	 *
	 * @name getLocalCharges
	 * @access public
	 * @param $id_cliente
	 * @return int
	 */ 	
	public function getLocalCharges()
	{
						
		/** Seleciona os portos **/
		$this->db->
				select("id_porto, un_code, porto")->
				from("USUARIOS.portos");

		$rs = $this->db->get();
		
		$taxa_model = new Taxa_Local_Model();
						
		$cliente = new Cliente();
		$cliente_model = new Cliente_Model();
		
		$id_cliente = $this->proposta->getClientes();
		
		$cliente->setId((int)$id_cliente[0]->getId());
		
		$cliente_model->findById($cliente);
						
		$busca_acordos = new Busca_Acordo_Taxas_Locais_Cliente();
		
		$taxas_locais_encontradas = Array();

		$objeto_porto = new Porto();
		
		$classificacao = new Define_Classificacao();
		
		foreach( $rs->result() as $porto )
		{
			try{
				$taxas_locais = $taxa_model->ObterTaxasLocais($this->proposta->getSentido(), "LCL", $classificacao->ObterClassificacao($cliente), $porto->id_porto);
				$taxas_locais_encontradas[$porto->porto] = $taxas_locais;

				/** Verifica se existe um acordo de taxas locais para este cliente **/
				$objeto_porto->setId((int)$porto->id_porto);
				$objeto_porto->setUnCode($porto->un_code);
				
				//FIXME verificar como vai ficar a data de validade das taxas locais
				$acordo = $busca_acordos->buscarAcordoTaxasCliente($this->proposta->getSentido(), $cliente, $objeto_porto, new DateTime(), new DateTime());
				
				if( $acordo instanceof Acordo_Taxas_Entity )
				{					
					$taxas_locais_encontradas[$porto->porto] = $acordo->getTaxas();					
				}	
												
			} catch (Exception $e) {
				$taxas_locais_encontradas[$porto->porto] = Array();
			}
			
		}	
			
		return $taxas_locais_encontradas;
	}
	
	/**
	 * getExcelStyles
	 *
	 * Obtem stylos personalizados para aplicar na planiha do tariário
	 *
	 * @name getExcelStyles
	 * @access public
	 * @param string $style_name
	 * @return Array $style
	 */ 	
	protected function getExcelStyles($style_name = NULL) 
	{
		
		if( is_null($style_name) )
		{
			throw new InvalidArgumentException("O nome do estilo informado não corresponde a nenhum estilo existente!");
		}

		$styles = Array();
		
		$styles['green'] = Array(
								'font' => array(
										"size"=>"8",
										'bold'=>true,
										'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_BLACK ),
										'align' => "center"
								),
								'fill' => array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'startcolor' => array(
												'rgb' => '98FB98'
										)
								)
		);
		
		$styles['red'] = Array(
									'font' => array(
													"size"=>"12",	
													'bold'=>true,
													'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_WHITE ),
									),
									'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'startcolor' => array(
																	'rgb' => 'FF0000'
											)
									)
						  );
		
		$styles['dark_blue'] = Array(
										'font' => array(
												"size"=>"10",
												'bold'=>true,
												'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_WHITE ),
										),
										'fill' => array(
												'type' => PHPExcel_Style_Fill::FILL_SOLID,
												'startcolor' => array(
														'rgb' => '1f497d'
												)
										)
								
							   );
		
		$styles['light_blue'] = Array(
										'font' => array(
												"size"=>"8",
												'bold'=>true,
												'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_BLACK ),
										),
										'fill' => array(
												'type' => PHPExcel_Style_Fill::FILL_SOLID,
												'startcolor' => array(
														'rgb' => 'b9cde5'
												)
										)
								
							   );
		
		$styles['white'] = Array(
										'font' => array(
												"size" => "8",
												'bold' => false,
												'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_BLACK ),
										),
										'fill' => array(
												'type' => PHPExcel_Style_Fill::FILL_SOLID,
												'startcolor' => array(
														'rgb' => 'FFFFFF'
												)
										)
								
								);
		
		$styles['yellow'] = Array(
									'font' => array(
											"size" => "8",
											'bold' => true,
											'color' => array( 'argb' => PHPExcel_Style_Color::COLOR_BLACK ),
									),
									'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'startcolor' => array(
													'rgb' => 'FFFF99'
											)
									)
							
							);
		
		return $styles[$style_name];
		
	}
					
}