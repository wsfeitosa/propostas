<?php
class Clientes_Acordo_Adicionais_model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();	
		$this->output->enable_profiler(false);	
	}
	
	public function salvarClientesDoAcordoDeAdicionais( Acordo_Adicionais $acordo )
	{		
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			log_message('O id do acordo não foi definido para salvar os clientes do acordo');
			show_error("Impossivel salvar os clientes do acordo");
		}
						
		// Limpa todos os clientes do acordo que estão registrados antes de salvar os novos	
		$this->db->delete("CLIENTES.clientes_x_acordo_adicionais",array("id_acordo_adicionais" => $acordo->getId()));
		
		foreach( $acordo->getClientes() as $clienteAcordo )
		{
			$dadosDoClienteParaSalvar = array(
												'id_cliente' => $clienteAcordo->getId(), 
												'id_acordo_adicionais' => $acordo->getId()									
			);

			$this->db->insert("CLIENTES.clientes_x_acordo_adicionais",$dadosDoClienteParaSalvar);
			
		}	
		
	}
	
	public function buscarAcordosPorIdDoCliente( Cliente $cliente )
	{
		
		$id_cliente = $cliente->getId();
		
		if( empty($id_cliente) )
		{
			log_message('error','Nenhum Cliente informado para realizar a busca');
			throw new InvalidArgumentException("Nenhum Cliente informado para realizar a busca");
		}	
						
		$this->db->
				select("clientes_x_acordo_adicionais.*, acordo_adicionais.*")->
				from("CLIENTES.clientes_x_acordo_adicionais")->
				join("CLIENTES.acordo_adicionais","acordo_adicionais.id = clientes_x_acordo_adicionais.id_acordo_adicionais")->
				where("id_cliente",$cliente->getId())->
				where("inicio <=",date('Y-m-d'))->
				where("validade >=",date('Y-m-d'))->
                where("ativo","S");
		
		$rowSetAcordos = $this->db->get();
						
		if( $rowSetAcordos->num_rows() < 1 )
		{
			return new ArrayObject();
		}	
		
		$this->load->model("Adicionais/acordo_adicionais");
		
		$acordosEncontrados = new ArrayObject(array());
		
		foreach( $rowSetAcordos->result() as $acordos )
		{							
			$acordo = new Acordo_Adicionais();
			$acordo->setId((int)$acordos->id_acordo_adicionais);
			$acordosEncontrados->append($acordo);				
		}	
		
		return $acordosEncontrados;
		
	}
	
	public function buscaClientesDoAcordoDeAdicionais( Acordo_Adicionais $acordo )
	{
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
	
		$id_acordo = $acordo->getId();
	
		if(empty($id_acordo))
		{
			throw new Exception("O id do acordo não foi informado para realizar a busca!");
		}
	
		$this->db->
		select("*")->
		from("CLIENTES.clientes_x_acordo_adicionais")->
		where("id_acordo_adicionais",$acordo->getId());
        
		$rowSetClientes = $this->db->get();
        
		if( $rowSetClientes->num_rows() < 1 )
		{
			throw new RuntimeException("Os clientes do acordo não puderam ser encontrados!");
		}
	
		$cliente_model = new Cliente_Model();
	
		foreach( $rowSetClientes->result() as $clienteAcordo )
		{
			$cliente = new Cliente();
			$cliente->setId((int)$clienteAcordo->id_cliente);
			$cliente_model->findById($cliente);
			$acordo->setCliente($cliente);
		}
	
	}
        
}