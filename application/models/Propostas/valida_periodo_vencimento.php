<?php
class Valida_Periodo_Vencimento extends CI_Model {
	
	public function __construct()
	{
		parent::__construct ();
	}
	
	public function retornaCorDeAcordoComVencimento( Proposta $proposta )
	{
		/** Obtem a validade de um item da proposta **/
		$this->db->
				select("validade")->
				from("CLIENTES.itens_proposta")->
				where("id_proposta",$proposta->getId())->
				limit("1");
		
		$rowItem = $this->db->get();

		$item = $rowItem->row();
		
		$validade = new DateTime($item->validade);
		
		$hoje = new DateTime();
		$hoje->modify('+1 week');
		
		if( ( $validade->format('Y-m-d') < $hoje->format('Y-m-d') ) && ( $validade->format('Y-m-d') > date('Y-m-d') ) )
		{
			return "style = 'background-color:#008B45;'";
		}

		$hoje = new DateTime();
		$hoje->modify('-1 week');
		
		if( ($validade->format('Y-m-d') < date('Y-m-d')) && ($validade->format('Y-m-d') >= $hoje->format('Y-m-d') ) )
		{
			return "style = 'background-color:#EEEE00;'";
		}	
		
		$hoje = new DateTime();
		$hoje->modify('-1 month');
		
		if( ($validade->format('Y-m-d') < date('Y-m-d')) && ($validade->format('Y-m-d') >= $hoje->format('Y-m-d') ) )
		{
			return "style = 'background-color:red;'";
		}

		$hoje = new DateTime();
		$hoje->modify('-1 month');
		
		if( ($validade->format('Y-m-d') < date('Y-m-d')) && ($validade->format('Y-m-d') < $hoje->format('Y-m-d') ) )
		{
			return "style = 'background-color:#D3D3D3;'";
		}
		
	}
	
}
