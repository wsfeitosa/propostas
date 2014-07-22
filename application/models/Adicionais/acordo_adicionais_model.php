<?php
class Acordo_Adicionais_Model extends CI_Model{
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('session');
		$this->output->enable_profiler(false);
	}
	
	public function salvarAcordo( Acordo_Adicionais $acordo )
	{	
				
		$this->load->model("Adicionais/validador");
		
		// Verifica se já existe um acordo válido para o mesmo cliente com a mesma taxa no mesmo período
		$validador = new Validador();
						
		$erros = $validador->validarAcordo($acordo);		
										
		$novoRegistro = false;

		//Verifica se foi uma alteração ou uma inclusão
		if( is_null($acordo->getId()) || $acordo->getId() == 0 )
		{
			$novoRegistro = true;
		}	
		
		$dadosDoAcordoParaSalvar = Array(										 
										"sentido" => $acordo->getSentido(),										
										"inicio" => $acordo->getInicio()->format('Y-m-d'),
										"validade" => $acordo->getValidade()->format('Y-m-d'),
										"observacao" => strtoupper($acordo->getObservacao()),										
										"ativo" => "S",
		);
				
		if($novoRegistro === true)
		{									
			$dadosDoAcordoParaSalvar["aprovacao_pendente"] = "S";
			$dadosDoAcordoParaSalvar["numero_acordo"] = $this->gerarNovoNumeroAcordo();
			$dadosDoAcordoParaSalvar['data_cadastro'] = date('Y-m-d H:i:s');
			$dadosDoAcordoParaSalvar['id_usuario_cadastro'] = $_SESSION['matriz'][7];
			
			$this->db->insert("CLIENTES.acordo_adicionais",$dadosDoAcordoParaSalvar);	
			$acordo->setId((int) $this->db->insert_id());
			$acordo->setNumeroAcordo($dadosDoAcordoParaSalvar['numero_acordo']);
			
			//Envia a solicitação de debloqueio para o gestor sempre que é um acordo novo
			$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
			$this->load->model("Usuarios/usuario");
			$solicitacao = new Solicitacao_Desbloqueio();
			$solicitacao->setAcordo($acordo);
				
			$solicitante = new Usuario();
			$solicitante->setId((int)$_SESSION['matriz'][7]);
				
			$solicitacao->setSolicitante($solicitante);
			$solicitacao->setDataSolicitacao(new DateTime());
				
			$solicitacao->enviarSolicitacao();
		}
		else
		{			
			$this->load->model("Adicionais/Desbloqueios/comparacao_acordos");	
			$this->load->model("Adicionais/acordo_adicionais");		
			
			$comparadorDeAcordos = new Comparacao_Acordos();
			
			//Busca os dados do acordo que já está salvo para fazer a comparação com o que foi alterado
			$acordoJaSalvo = new Acordo_Adicionais();
			$acordoJaSalvo->setId((int)$acordo->getId());
			$this->consultarAcordoAdicionaisPorId($acordoJaSalvo);
			
			// Se houve modificação então envia para o desbloqueio
			if( $comparadorDeAcordos->verificaSeDoisAcordosSaoIguais($acordoJaSalvo, $acordo) === true )
			{				
				$dadosDoAcordoParaSalvar["aprovacao_pendente"] = "S";
				
				$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
				$this->load->model("Usuarios/usuario");
				
				$acordo->setNumeroAcordo($acordoJaSalvo->getNumeroAcordo());
				
				$solicitacao = new Solicitacao_Desbloqueio();
				$solicitacao->setAcordo($acordo);
				
				$solicitante = new Usuario();
				$solicitante->setId((int)$_SESSION['matriz'][7]);
				
				$solicitacao->setSolicitante($solicitante);
				$solicitacao->setDataSolicitacao(new DateTime());
				
				$solicitacao->enviarSolicitacao();

				unset($dadosDoAcordoParaSalvar['inicio']);
				unset($dadosDoAcordoParaSalvar['validade']);
				$acordo->limparTaxas();										
			}
						
			$dadosDoAcordoParaSalvar['id_usuario_alteracao'] = $_SESSION['matriz'][7];
			$dadosDoAcordoParaSalvar['data_alteracao'] = date('Y-m-d H:i:s');
			
			$this->db->where("acordo_adicionais.id",$acordo->getId());
			$this->db->update("CLIENTES.acordo_adicionais",$dadosDoAcordoParaSalvar);
		}
				
	}
	
	public function consultarAcordoAdicionaisPorId( Acordo_Adicionais $acordo )
	{
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Adicionais/taxas_acordo_adicionais_model");
		$this->load->model("Adicionais/clientes_acordo_adicionais_model");
		
		$id_acordo = $acordo->getId();
		
		if(empty($id_acordo))
		{
			throw new Exception("O id do acordo não foi informado para realizar a busca!");
		}	
			
		$this->db->
				select("*")->
				from("CLIENTES.acordo_adicionais")->
				where("id",$acordo->getId());
		
		$rowSetAcordo = $this->db->get();
		
		if( $rowSetAcordo->num_rows() < 1 )
		{
			throw new RuntimeException("O acordo não pode ser encontrado!");
		}	
		
		$row = $rowSetAcordo->row();
		
		$acordo->setNumeroAcordo($row->numero_acordo);
		$acordo->setDataCadastro(new DateTime($row->data_cadastro));	
		$acordo->setDataAlteracao(new DateTime($row->data_alteracao));	
		$acordo->setInicio(new DateTime($row->inicio));
		$acordo->setValidade(new DateTime($row->validade));
		$acordo->setObservacao($row->observacao);
		$acordo->setSentido($row->sentido);		
		$acordo->setAtivo($row->ativo);
		$acordo->setAprovacaoPendente($row->aprovacao_pendente);
		
		//Busca os usuário de cadastro e alteração se houver
		$usuario_model = new Usuario_Model();
		
		$usuario_cadastro = new Usuario();
		$usuario_cadastro->setId((int)$row->id_usuario_cadastro);		
		$usuario_model->findById($usuario_cadastro);
		
		$acordo->setUsuarioCadastro($usuario_cadastro);
		
		if( ! empty($row->id_usuario_alteracao) )
		{
			$usuario_alteracao = new Usuario();
			$usuario_alteracao->setId((int)$row->id_usuario_alteracao);
			$usuario_model->findById($usuario_alteracao);
			
			$acordo->setUsuarioAlteracao($usuario_alteracao);
		}

		//Busca os clientes do acordo
		$cliente_acordo_model = new Clientes_Acordo_Adicionais_model();
		$cliente_acordo_model->buscaClientesDoAcordoDeAdicionais($acordo);
		
		//Busca às taxas do acordo
		$taxas_acordo_model = new Taxas_Acordo_Adicionais_Model();
		$taxas_acordo_model->buscaTaxasDoAcordoDeAdicionais($acordo);
										
	}

	public function completarDadosDoAcordo( Acordo_Adicionais $acordo )
	{
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Clentes/cliente_model");
		
		if( $acordo->getUsuarioCadastro() instanceof Usuario )
		{		
			$this->usuario_model->findById($acordo->getUsuarioCadastro());
		}
		
		if( $acordo->getUsuarioAlteracao() instanceof Usuario )
		{
			$this->usuario_model->findById($acordo->getUsuarioAlteracao());
		}	
					
		foreach( $acordo->getClientes() as $cliente )
		{
			$this->cliente_model->findById($cliente);
		}
	
		foreach( $acordo->getTaxas() as $taxa )
		{
			$this->unidade_model->findById($taxa->getUnidade());
			$this->moeda_model->findById($taxa->getMoeda());
			$this->taxa_model->obterNomeTaxaAdicional($taxa);
		}
	}
	
	protected function gerarNovoNumeroAcordo()
	{

		$prefixo = "ADF";
		$mesAno = date('my');	
		$filial = $_SESSION['matriz'][1];	
		$sequencial = 000000;

		/** Seleciona o último acordo cadastrado **/
		$this->db->
				select("numero_acordo")->
				from("CLIENTES.acordo_adicionais")->
				order_by("id","DESC")->
				limit(1);

		$rowSetUltimoAcordo = $this->db->get();
		
		if( $rowSetUltimoAcordo->num_rows() < 1 )
		{
			$sequencial = sprintf("%06d",($sequencial + "1"));					
		}
		else
		{				
			$anoDoUltimoAcordo = substr($rowSetUltimoAcordo->row()->numero_acordo, 5, 2);
			
			if( date('y') != $anoDoUltimoAcordo )
			{
				$sequencial = sprintf("%06d",($sequencial + "1"));								
			}	
			else
			{
				$sequencial = substr($rowSetUltimoAcordo->row()->numero_acordo, 9);
				$sequencial = sprintf("%06d",($sequencial + "1"));				
			}			
		}
			
		$numeroAcordo = $prefixo . $mesAno . $filial . $sequencial;

		return $numeroAcordo;		

	}
    
    public function verificarStatusDoAcordo( Acordo_Adicionais $acordo )
    {
        
    }    
    
    public function cancelaAcordo(Acordo_Adicionais $acordo)
    {
        $this->db->where("id",$acordo->getId());
        return $this->db->update("CLIENTES.acordo_adicionais", array("ativo" => "N"));
    } 
    
    public function localizaUltimoDesbloqueio( Acordo_Adicionais $acordo )
    {
        $this->db->
                select("*")->
                from("CLIENTES.desbloqueios_adicionais")->
                where("id_acordo",$acordo->getId())->
                where("status !=",strtoupper("pendente"))->
                order_by("id","desc");
        
        $rowSet = $this->db->get();
        
        if( $rowSet->num_rows() > 0 )
        {
            $row = $rowSet->row();               
            
            $this->load->model("Usuarios/usuario");
            $this->load->model("Usuarios/usuario_model");
            
            $usuario_desbloqueio = new Usuario();
            $usuario_model = new Usuario_Model();
            
            $usuario_desbloqueio->setId((int)$row->id_usuario_aprovacao);
            $usuario_model->findById($usuario_desbloqueio);
            $data_desbloqueio = new DateTime($row->data_desbloqueio);
            
            $acordo->setUsuarioDesbloqueio($usuario_desbloqueio);
            $acordo->setDataDesbloqueio($data_desbloqueio);
        }    
        
    }   
		
}