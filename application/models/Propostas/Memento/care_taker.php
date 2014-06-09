<?php
include_once APPPATH."/models/Propostas/Memento/memento.php";

class Care_Taker extends CI_Model{
	
	private $memento = NULL;

	public function SaveState(Memento $memento)
	{		
		$data = Array(
						'numero_proposta' => $memento->GetNumeroProposta(),
						'dados_log' => $memento->GetState(),
						'data_log' => date('Y-m-d H:i:s'),
						'id_usuario' => $_SESSION['matriz'][7],
		);
		
		$this->db->insert('CLIENTES.log_propostas', $data);		
	}
	
	public function LoadState($id)
	{
		$rs = $this->db->get_where('CLIENTES.log_propostas', array('id_log' => $id));
		
		$row = $rs->row();
		
		$memento = new Memento();
		
		$memento->SetNumeroProposta($row->numero_proposta);
		
		$memento->SetState($row->dados_log);
		
		$this->memento = $memento;
		
		return $memento;
	}
	
}
