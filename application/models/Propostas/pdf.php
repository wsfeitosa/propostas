<?php
/**
 * @author  Wellington Feitosa - 06/08/2010 11:26
 * @version 1.0
 * Descri��o: Cabe�ario para o pdf do cota��o
 */ 
require_once($_SERVER["DOCUMENT_ROOT"].'/Libs/pdf_table/lib/pdftable.inc.php');

class PDF extends PDFTable
{
	public function Header()
	{
		if($_SESSION["matriz"][7]=="361" || $_SESSION["matriz"][7]=="229")
		{
			$logo = "allink_service.jpg"; 	
		}
		else
		{
			$logo = "logo_allink_proposta.jpg";
		}

        /** 
          * Antes a havia a Allink Service o logo do pdf era diferente,
          * do padr�o dependendo do usu�rio, como a gora todos os usu�rios 
          * s�o Allink a regra n�o vale mais
          **/		
        $logo = "logo_allink_proposta.jpg";

		$this->Image($_SERVER['DOCUMENT_ROOT']."/Imagens/".$logo,125,5,80,22,'JPG');
		$this->setY(20);
	}
	
	public function Footer()
	{
		//Vai para 1.5 cm da borda inferior
		$this->SetXY(170,278);
		//Imprime a imagem do rodap�
		$this->Image($_SERVER['DOCUMENT_ROOT']."/Imagens/rodape_atual.jpg",15,278,180,18,'JPG'); 
		//Imprime o n�mero da p�gina centralizado
		$this->setXY(200,290);
		$this->SetFont('arial','',7);
		$this->SetTextColor(0,0,0);
		$this->Cell(0,10,'Pag. '.$this->PageNo().' de {nb}',0,0,'C');
	}
} 
 
?>
