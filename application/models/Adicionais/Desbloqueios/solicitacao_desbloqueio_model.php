<?php
class Solicitacao_Desbloqueio_Model extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
		$this->output->enable_profiler(false);
		$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");	
	}
	
	public function obterSolicitacoesPendentes()
	{
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Adicionais/acordo_adicionais_model");
		
		$solicitacoesPendentes = Array();
				
		$this->db->
				select("*")->
				from("CLIENTES.desbloqueios_adicionais")->
				where("status",strtoupper("pendente"));
		
		$rowSetPendentes = $this->db->get();
		
		if( $rowSetPendentes->num_rows() > 0 )
		{
			foreach( $rowSetPendentes->result() as $solicitacao )
			{
				$acordo = $this->acordo_adicionais->deserializar($solicitacao->solicitado);

				$this->acordo_adicionais_model->completarDadosDoAcordo($acordo);
				
				$solicitacaoDesbloqueio = new Solicitacao_Desbloqueio();
				
				$solicitacaoDesbloqueio->setAcordo($acordo);
				
				/**
				 * Obtem o usuário que solicitou o desbloqueio				 
				 */
				$solicitacaoDesbloqueio->setId((int)$solicitacao->id);
				
				$solicitante = new Usuario();
				$solicitante->setId((int)$solicitacao->id_usuario_solicitacao);
				$this->usuario_model->findById($solicitante);

				$solicitacaoDesbloqueio->setSolicitante($solicitante);
				
				$dataSolicitacao = new DateTime($solicitacao->data_solicitacao);
				$solicitacaoDesbloqueio->setDataSolicitacao($dataSolicitacao);
				
                /**
                 * Verifica se o aprovador é da mesma filial do solicitante,
                 * pelas regras o aprovador só aprova as solicitações dos
                 * usuários da mesma filial.
                 */   
                if( ($solicitante->getFilial()->getSiglaFilial() == $_SESSION['matriz'][1]) || $_SESSION['matriz'][4] == "CPD" )
                {
                    array_push($solicitacoesPendentes, $solicitacaoDesbloqueio);
                }    
			}	
		}

		return $solicitacoesPendentes;		
	}
	
	public function buscaSolicitacaoPorId(Solicitacao_Desbloqueio $solicitacao)
	{
		$idSolicitacao = $solicitacao->getId();		
		
		if( empty($idSolicitacao) )
		{
			throw new InvalidArgumentException("Nenhuma chave foi definida para realizar a busca pela solicitação de desbloqueio!");
		}	
		
		$this->db->
				select("*")->
				from("CLIENTES.desbloqueios_adicionais")->
				where("id",$solicitacao->getId());
		
		$rowSolicitacao = $this->db->get();
		
		if( $rowSolicitacao->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhuma Solicitação Encontrada para este acordo!");
		}	
		
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Adicionais/serializa_taxa");
		
		$row = $rowSolicitacao->row();
		
		$solicitante = new Usuario();
		$solicitante->setId((int)$row->id_usuario_solicitacao);
		$this->usuario_model->findById($solicitante);
		
		$solicitacao->setSolicitante($solicitante);
		
		$dataSolicitacao = new DateTime($row->data_solicitacao);
		
		$solicitacao->setDataSolicitacao($dataSolicitacao);
		
		$acordo_adicionais = new Acordo_Adicionais();
		
		$acordo = $acordo_adicionais->deserializar($row->solicitado);
		
		$solicitacao->setAcordo($acordo);
						
	}
			
}
