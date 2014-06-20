<?php
include_once "/var/www/html/allink/Clientes/propostas/application/models/Taxas_Locais_Acordadas/Memento/memento.php";

/**
 * Description of care_taker
 *
 * @author wsfall
 */
class Care_Taker extends CI_Model {
    
    private $memento = NULL;

	public function SaveState(Memento $memento)
	{		
		$data = Array(
						'numero_acordo' => $memento->getNumeroAcordo(),
						'dados_log' => $memento->getState(),
						'data_log' => date('Y-m-d H:i:s'),
						'id_usuario' => $_SESSION['matriz'][7],
		);
		
		$this->db->insert('CLIENTES.log_acordo_taxas_locais', $data);		
	}
	
	public function LoadState($id)
	{
		$rs = $this->db->get_where('CLIENTES.log_acordo_taxas_locais', array('id' => $id));
		
		$row = $rs->row();
		
		$memento = new Memento();
		
		$memento->setNumeroAcordo($row->numero_acordo);
		
		$memento->setState($row->dados_log);
		
		$this->memento = $memento;
		
		return $memento;
	}
    
}
