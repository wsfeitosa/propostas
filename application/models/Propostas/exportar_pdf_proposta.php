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
 * @package models/Propostas/exportar_pdf_proposta
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 26/07/2013
 * @version  versao 1.0
*/
class Exportar_Pdf_Proposta extends CI_Model {
	
	const TMP_DIR = "/var/www/html/allink/relatorios_temp/";
	
	protected $pdf = NULL, $proposta = NULL;
	protected $arquivo = FALSE;
	
	public function __construct(Proposta $proposta = NULL, $arquivo = FALSE)
	{
		parent::__construct();
		
		$this->load->model("Propostas/pdf");
		$this->load->model("Taxas/serializa_taxas");
		$this->load->model("Propostas/notas_propostas");
						
		if( isset($proposta) )
		{
			$this->proposta = $proposta;
		}
		
		$this->arquivo = $arquivo;	
				
	}
	
	public function setProposta( Proposta $proposta, $arquivo = FALSE )
	{
		$this->proposta = $proposta;
		$this->arquivo = $arquivo;
	}
	
	public function gerarPdf()
	{							
		$nome_arquivo = "proposta_".date('YmdHis').".pdf";
		
		$serializador = new Serializa_Taxas();
		
		/** Includes e defines */
		define('FPDF_FONTPATH',$_SERVER["DOCUMENT_ROOT"].'/Libs/pdf_table/font/');
		
		$pdf = new PDF("P","mm","A4");
						
		$pdf->AliasNbPages();
		$pdf->SetAutoPageBreak(true,20);
		$pdf->SetMargins(5,0,0.5);
		$pdf->AddPage();   
		
		//Imagem do inicio e titulo da cotação
		$pdf->setFont('arial','',8);
		$pdf->setXY(5,5);
		
		/** Data **/
		$pdf->Cell(30, 5, date('d/m/Y') );

		$pdf->SetFont('arial','B',12);
		$pdf->SetTextColor(44,56,132);
				
		$pdf->SetXY(5, $pdf->GetY() + 5);
		
		$pdf->Cell(120,10, "PROPOSTA COMERCIAL DE ".$this->proposta->getSentido()."ORTAÇÃO");
		
		/** Nome do cliente **/
		$pdf->SetFontSize(10);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetY($pdf->GetY() + 6);
		
		$cliente = $this->proposta->getClientes();
		
		$pdf->Cell(120,10, $cliente[0]->getRazao());
		
		/** Frase inicial **/
		$pdf->SetFont('arial','',8);		
		$pdf->SetY($pdf->GetY() + 10);
		
		$msg = "Agradecemos seu interesse em cotar com a Allink Transportes Internacionais Ltda. e apresentamos abaixo nossa proposta:";		
		$pdf->Cell(200,10,$msg);
						
		/** Começa a repetir os itens da proposta **/
		$itens_proposta = $this->proposta->getItens();
		
		$quantidade_de_itens_da_proposta = count($itens_proposta);
		
		$pdf->SetY($pdf->GetY() + 10);
		
		foreach( $itens_proposta as $item )
		{
			
			$quantidade_de_itens_da_proposta--;
			
			$pdf->setFont('arial','B',7);
			//Cabeçalho do item
			$pdf->SetFillColor(75,122,231);
			$pdf->SetTextColor(255,255,255);
			$pdf->Cell(200,5,$item->getNumero(),1,1,"C",1);
			$pdf->SetTextColor(0,0,0);

			/** Origem **/
			$pdf->setFont('arial','B',7);		
			$pdf->Cell(30,5,"Origem: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getTarifario()->getRota()->getPortoOrigem()->getNome(),0,0,"L");
			
			$pdf->SetX(110);
			/** Mercadoria **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"Mercadoria: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,urldecode($item->getMercadoria()),0,0,"L");
			
			$pdf->SetY($pdf->GetY() + 5);
			
			/** Porto Embarque **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"Porto de Embarque: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getTarifario()->getRota()->getPortoEmbarque()->getNome(),0,0,"L");
				
			$pdf->SetX(110);
			/** Qtd. Volumes **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"Qtd. Volumes: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getVolumes(),0,0,"L");
			
			$pdf->SetY($pdf->GetY() + 5);
			
			/** Porto de Desembarque **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"Porto de Desembarque: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getTarifario()->getRota()->getPortoDesembarque()->getNome(),0,0,"L");
				
			$pdf->SetX(110);
			/** Mercadoria **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"Peso Bruto (Kg):",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,sprintf("%01.3f", $item->getPeso()),0,0,"L");
				
			$pdf->SetY($pdf->GetY() + 5);
			
			/** Destino **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"Destino: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getTarifario()->getRota()->getPortoFinal()->getNome(),0,0,"L");
			
			$pdf->SetX(110);
			/** Cubagem (Cbm): **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"Cubagem (Cbm):",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,sprintf("%01.3f", $item->getCubagem()),0,0,"L");
			
			$pdf->SetY($pdf->GetY() + 5);
			
			/** Modalidade **/
			$pp = ""; $cc = "";
			
			if( $item->getPp() == true )
			{
				$pp = "PREPAID";
			}	
			
			if( $item->getCc() == true )
			{
				$cc = "COLLECT";
			}	
			
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"Modalidade: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$pp." - ".$cc,0,0,"L");
			
			$pdf->SetX(110);
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

			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"CBM = Peso: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$cubagem_ratio . ' = ' . $peso_ratio,0,0,"L");
			
			$altura_inicial_celula_breakdown = $pdf->GetY() + 5;

			$pdf->SetY($altura_inicial_celula_breakdown);
			
			/** Transit Time (BreakDown) **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"Transit Time: ",0,0,"L");
			$pdf->SetFont('arial','',5);			
			$altura_celula_transit = $pdf->GetY();			
			$pdf->MultiCell(70,5,trim($item->getTarifario()->getBreakDown()));

			$altura_apos_transit_time = $pdf->GetY();

			$pdf->SetXY(110,$altura_inicial_celula_breakdown);
			/** Validade **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"Validade: ",0,0,"L");
			$pdf->SetFont('arial','',7);
			$pdf->Cell(30,5,$item->getValidade()->format('d/m/Y'),0,0,"L");

			$pdf->SetY($altura_apos_transit_time + 5);
			
			/** Recupera às taxas no formato correto **/
			$taxas_formatadas = Array();
			$taxas_formatadas = $serializador->serializaTaxasProposta($item);
			
			$altura_do_label_de_frete = $pdf->getY();
			
			/** Frete maritimo e adicionais **/			
			$pdf->setFont('arial','B',7);
			$pdf->Cell(30,5,"FRETE MARÍTIMO E ADICIONAIS",0,1,"L");
									
			$pdf->SetFont('arial','',7);
									
			$taxas_sobre_frete = explode("---", $taxas_formatadas['label_taxas_adicionais']);
			
			foreach( $taxas_sobre_frete as $taxa_sobre_frete )
			{				
				$pdf->Cell(30,5,$taxa_sobre_frete,0,1,"L");				
			}	
									
			$pdf->SetXY(110,$altura_do_label_de_frete - 1);
			
			$altura_apos_frete = $pdf->GetY();

			/** Taxas locais **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"TAXAS LOCAIS",0,1,"L");
			
			$taxas_locais = explode("---", $taxas_formatadas['label_taxas_locais']);

			$pdf->SetFont('arial','',7);
			
			foreach( $taxas_locais as $taxa_local )
			{
				$pdf->SetX(110);
				$pdf->Cell(30,5,$taxa_local,0,1,"L");
			}
			
			$pdf->SetFont('arial','',7);
			
			$altura_apos_taxas_locais = $pdf->GetY();

			$altura_apos_todas_as_taxas = max($altura_apos_frete,$altura_apos_taxas_locais);

			//$pdf->SetY($pdf->GetY() + 10);

			$pdf->SetY($altura_apos_todas_as_taxas + 35);
			
			/** Observação **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"OBSERVAÇÃO:",0,1,"L");
			
			$pdf->SetFont('arial','',8);
			$pdf->MultiCell(200, 5, urldecode($item->getObservacaoCliente()));
			
			$pdf->SetY($pdf->GetY() + 10);
			
			/** Importante **/
			$pdf->setFont('arial','B',7);
			$pdf->Cell(22,5,"IMPORTANTE:",0,1,"L");
				
			$pdf->SetFont('arial','',8);
			$pdf->MultiCell(200, 5, $this->notas_propostas->selecionarNotas($this->proposta));

			/** Link para às observações do tarifário **/
			$link = 'http://'.$_SERVER['SERVER_ADDR']."/Clientes/tarifario/aditional_information.php?key=".$item->getTarifario()->getId();
			
			$pdf->Write(5,"SEE ADDITIONAL INFORMATION",$pdf->Link($pdf->GetX(), $pdf->GetY(), 45, 5, $link));

			/** Schesule **/	
			if( $this->proposta->getSentido() == "EXP" && $item->getTarifario()->getRota()->getPortoOrigem()->getId() == 3 )
			{			
				if( ! isset($_SESSION['matriz']) )
				{
					show_error("A sessão do usuário não está definida");
				}
				
				$pdf->SetY($pdf->GetY() + 10);
										
				/** Seleciona às próximas viagens **/
				$sql = "SELECT
							vgnavios.viagem, vgnavios.deadlineour, vgnavios.eta, navios.navio
						FROM
							NVOCC_STS_EXP.vgnavios
							INNER JOIN GERAIS.navios ON navios.id_navio = vgnavios.id_navio
						WHERE
							SUBSTRING(vgnavios.deadlineour,1,10) > '".date('Y-m-d')."' AND
							vgnavios.id_via = '".$item->getTarifario()->getRota()->getPortoDesembarque()->getId()."'			
						ORDER BY
							vgnavios.id_viagem ASC
						LIMIT
							4";
				
				$rs = $this->db->query($sql);
				
				$result_set = $rs->result();
				
				/** Imprime o cabeçalho **/
				if( $rs->num_rows() > 0 )
				{	
					$pdf->SetFont('arial','B',7);
					$pdf->Cell(60,5,"PRÓXIMAS SAÍDAS:",0,1,"L");
					
					$pdf->SetFont('arial','B',10);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetFillColor(179,179,179);
						
					$pdf->Cell(60,5,"NAVIOS",0,0,"C",true);
					$pdf->Cell(40,5,"ETA",0,0,"C",true);
					$pdf->Cell(40,5,"DEADLINE",0,1,"C",true);
				}
				$cont = 1;
				
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('arial','',10);
				
				foreach( $result_set as $viagem )
				{
					if( $cont % 2 == 0 )
					{
						$pdf->SetFillColor(204,204,204);
					}
					else
					{
						$pdf->SetFillColor(230,230,230);
					}
				
					$eta = new DateTime($viagem->eta);
					$deadline = new DateTime($viagem->deadlineour);
						
					$pdf->Cell(60,5,$viagem->navio." - ".$viagem->viagem,0,0,"L",true);
					$pdf->Cell(40,5,$eta->format("d/m/Y"),0,0,"C",true);
					$pdf->Cell(40,5,$deadline->format("d/m/Y"),0,1,"C",true);
						
					$cont++;
				}
				
			}
				
			$pdf->SetY($pdf->GetY() + 5);
			
			if( $quantidade_de_itens_da_proposta > 0 )
			{		
				$pdf->AddPage();				
			}	
									
			$pdf->SetY($pdf->GetY() + 10);
			
			
		}	
		
		//250 -> Fim da Página
		/**
		if( $pdf->GetY() > 200 )
		{
			$pdf->AddPage();
			$pdf->SetY($pdf->GetY() + 10);
		}
		**/

		if( $pdf->GetY() > 230 )
		{
			$pdf->AddPage();
			$pdf->SetY($pdf->GetY() + 10);
		}
		
		/** Assinatura **/
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		
		$usuario = new Usuario();
		$usuario->setId((int)$_SESSION['matriz'][7]);
		
		$usuario_model = new Usuario_Model();
		$usuario_model->findById($usuario);
		$assinatura = $usuario_model->gerarAssinatura($usuario);
		
		$pdf->SetFont('arial','B',12);
		$pdf->SetTextColor(44,56,132);

		$pdf->Cell(100,5,$usuario->getnome(),0,1,"L");
		
		$pdf->SetFontSize(10);		
		$pdf->Cell(100,5,$usuario->getCargo()." - ALLINK ".$usuario->getFilial()->getNomeFilial(),0,1,"L");

		$pdf->SetFont('arial','B',10);
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(100,5,"Allink Transportes Internacionais",0,1,"L");
		
		$pdf->SetY($pdf->GetY() + 5);
						
		$pdf->SetFont('arial','',8);
		$pdf->SetTextColor(44,56,132);
		$pdf->Cell(100,5,"Direct: +55 ".$usuario->getDdd()." ".$usuario->getFone(),0,1,"L");
		
		$ramal = $usuario->getRamal();
		
		if( ! empty( $ramal ) )
		{		
			$pdf->Cell(100,5,"Phone: +55 ".$usuario->getDdd()." ".$usuario->getRamal(),0,1,"L");
		}
		
		$fax = $usuario->getFax();
		
		if( ! empty( $fax ) )
		{
			$pdf->Cell(100,5,"Fax: +55 ".$usuario->getDdd()." ".$usuario->getFax(),0,1,"L");
		}		
		
		$pdf->Cell(100,5,$usuario->getEmail()->getEmail(),0,1,"L");
		
		$pdf->SetFont('arial','B',8);
		$pdf->Cell(100,5,"WWW.ALLINK.COM.BR",0,1,"L");
		
		/** Filiais **/
		$imagens = Array(
							1 => 'itj', 2 => 'poa', 3 => 'cwb', 4 => 'spo',
							5 => 'rjo', 6 => '', 7 => 'ssz', 8 => '',
							9 => '', 10 => '', 11 => '', 12 => '',
							13 => '', 14 => '', 
				   );
		
		//$pdf->Image("http://www.allink.com.br/marketing/imagem_1".$imagens[$usuario->getFilial()->getId()].".jpg",$pdf->GetX(),$pdf->GetY(),151,34,'JPG');
		
		$pdf->allow_charset_conversion = TRUE;

		if( $this->arquivo == TRUE )
		{
			$pdf->Output( self::TMP_DIR . $nome_arquivo, 'F' );
			return self::TMP_DIR . $nome_arquivo;
		}
		else
		{		
			$pdf->Output();
		}	
	}
			
}//END CLASS