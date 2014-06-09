<?php
include_once APPPATH . "/models/Desbloqueios/notas.php";

class Notas_Model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function listAll( $sentido )
	{		
		
		if( $sentido != "IMP" && $sentido != "EXP" )
		{
			show_error("Sentido não determinado para buscar as notas dos clientes");
		}
		
		$notas = Array();
		
		$rs = $this->db->get("CLIENTES.notas_" . strtolower($sentido) . "ortacao");
		
		$notas_encontradas = $rs->result();
		
		foreach( $notas_encontradas as $nota_encontrada )
		{
			$nota = new Nota();
			
			if( $sentido == "IMP" )
			{
				$nota->setId($nota_encontrada->id_nota_importacao);
			}
			else
			{
				$nota->setId($nota_encontrada->id_nota_exportacao);
			}		
						
			$nota->setNota($nota_encontrada->nota);
			$nota->setValorMinimo($nota_encontrada->valor_minimo);
			$nota->setValorMaximo($nota_encontrada->valor_maximo);
			
			$notas[] = $nota;			
		}	
		
		return $notas;
		
	}
	
}
