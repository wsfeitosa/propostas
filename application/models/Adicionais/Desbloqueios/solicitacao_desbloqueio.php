<?php
class Solicitacao_Desbloqueio extends CI_Model {
	
	protected $id = null;
	protected $acordo = null;
	protected $solicitante = null;
	protected $aprovador = null;
	protected $data_solicitacao = null;
	protected $data_desbloqueio = null;
	protected $alterar_retroativos = null;
	
	public function __construct() 
	{
		parent::__construct();
		$this->output->enable_profiler(false);
	}
	
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setAcordo( Acordo_Adicionais $acordo )
	{
		$this->acordo = $acordo;
		return $this;
	}
	
	public function getAcordo()
	{
		return $this->acordo;
	}
			
	public function setSolicitante(Usuario $solicitante)
	{
		$this->solicitante = $solicitante;
		return $this;
	}
	
	public function getSolicitante()
	{
		return $this->solicitante;
	}
	
	public function setAprovador(Usuario $aprovador)
	{
		$this->aprovador = $aprovador;
		return $this;
	}
	
	public function getAprovador()
	{
		return $this->aprovador;
	}
	
	public function setDataSolicitacao(DateTime $data_solicitacao)
	{
		$this->data_solicitacao = $data_solicitacao;
		return $this;
	}
	
	public function getDataSolicitacao()
	{
		return $this->data_solicitacao;
	}
	
	public function setDataDesbloqueio(DateTime $data_desbloqueio)
	{
		$this->data_desbloqueio = $data_desbloqueio;
		return $this;
	}
	
	public function getDataDesbloqueio()
	{
		return $this->data_desbloqueio;
	}
	
	public function setAlterarRetroativos($alterar_retroativos)
	{
		$this->alterar_retroativos = $alterar_retroativos;
	}
	
	public function getAlterarRetroativos()
	{
		return $this->alterar_retroativos;
	}
	
	public function obterGestoresDoSolicitante(Usuario $usuario)
	{		
		include_once '/var/www/html/allink/Administracao/gerenciador_permissoes_desbloqueio_propostas/files/acessos_desbloqueio.php';
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
						
		/**
		 * Seleciona o id do porto pelo id da filial, pelo relacionamento
		 * que existe na usuarios portos.		 	
		 */
		$rowSet = $this->db->get_where("USUARIOS.portos",array("sigla_processo"=>$usuario->getFilial()->getSiglaFilial()),1);

		$idPortoUsuario = $rowSet->row()->id_porto;
				
		$arrayGestores = $acessos_filiais[$idPortoUsuario];
		$gestoresEncontrados = Array();
		
		if(count($arrayGestores))
		{
			foreach( $arrayGestores as $gestor )
			{
				$usuario = new Usuario();
				$usuario->setId((int)$gestor);
				$this->usuario_model->findById($usuario);
				array_push($gestoresEncontrados, $usuario);	
			}	
		}	
		//FIXME remover esta linha em produção
		$gestoresEncontrados = Array();
		
		return $gestoresEncontrados;		
	}
	
	public function salvarSolicitacao()
	{		
		
		/** 
		 * Verifica se já existe alguma solicitação pendente deste mesmo acordo antes
		 * de salva-lo, se existir ela será cancelada.
		 */
		$this->db->
				select("*")->
				from("CLIENTES.desbloqueios_adicionais")->
				where("id_acordo",$this->acordo->getId())->
				where("status",strtoupper("pendente"));
		
		$rowSetAprovacoesPendentes = $this->db->get();
		
		if( $rowSetAprovacoesPendentes->num_rows() > 0 )
		{
			foreach( $rowSetAprovacoesPendentes->result() as $solicitacaoPendente )
			{
				$this->db->where("id",$solicitacaoPendente->id);
				$this->db->update("CLIENTES.desbloqueios_adicionais",Array("status"=>strtoupper("cancelado")));
			}	
		}	
		
		$this->acordo->data_inicio = $this->acordo->getInicio()->format('Y-m-d');
		$this->acordo->data_final = $this->acordo->getValidade()->format('Y-m-d');
		
		$solicitacao = Array(
							 "id_acordo" => $this->acordo->getId(),
							 "solicitado" => $this->acordo->serializar(),
							 "id_usuario_solicitacao" => $this->solicitante->getId(),
							 "data_solicitacao" => $this->data_solicitacao->format('Y-m-d H:i:s'),
							 "status" => strtoupper("pendente"),
							 "alterar_retroativos" => $this->acordo->alterar_retroativos 
		);
		
		$this->db->insert("CLIENTES.desbloqueios_adicionais",$solicitacao);
		$this->id = $this->db->insert_id();
						
		return $this;					
	}
	
	public function salvarAprovacao()
	{
		$solicitacao = Array(
							"id_acordo" => $this->acordo->getId(),
							"aprovado" => $this->acordo->serializar(),
							"id_usuario_aprovacao" => $this->aprovador->getId(),
							"data_aprovacao" => $this->data_desbloqueio->format('Y-m-d H:i:s'),
							"alterar_retroativos" => $this->alterar_retroativos
		);
		
		$this->db->where("id",$this->id);
		$this->db->update("CLIENTES.desbloqueios_adicionais",$solicitacao);

		return $this;
	}
	
	public function enviarSolicitacao()
	{
		$this->salvarSolicitacao();	
		
		// Envia a mensagem de solicitação
		$this->load->model("Adicionais/Desbloqueios/email_model");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Cliente/cliente_model");
		$this->load->model("Email/envio");
		$this->load->model("Email/email");
		
		$mensagem =	$this->email_model->formatarMensagemDeSolicitacao($this);
				
		$this->usuario_model->findById($this->solicitante);
		
		$this->envio->adicionarNovoEmail($this->solicitante->getEmail());
		
		foreach( $this->getAcordo()->getClientes() as $cliente )
		{
			$this->cliente_model->findById($cliente);
				
			$this->usuario_model->findById($cliente->getVendedorExportacao());
			$this->usuario_model->findById($cliente->getCustomerExportacao());
				
			$this->envio->adicionarNovoEmail($cliente->getVendedorExportacao()->getEmail());
			$this->envio->adicionarNovoEmail($cliente->getCustomerExportacao()->getEmail());
			
			/** 
			 * Verifica se o cliente do acordo é um cliente wwa, 
			 * se for, então copia a samira e o lemos 
			 **/
			$this->db->
					select("grupo_chave")->
					from("CLIENTES.grupo_comercial")->
					where("idgrupo_comercial",$cliente->getGrupoComercial());
			
			$rowSetWWA = $this->db->get();
			
			if( $rowSetWWA->num_rows() > 0 )
			{
				$clienteWWA = $rowSetWWA->row();
				
				/**
				 * Se o grupo comercial que o cliente faz parte é um grupo WWA,
				 * então o cliente é considerado wwa e a Samira eo Lemos serão copiados.
				 */  
				if( $clienteWWA->grupo_chave == "S" )
				{
					$wwa1 = new Email();
					$wwa2 = new Email();
					
					$wwa1->setEmail("samira.castro@allink.com.br");
					$wwa2->setEmail("cs07.spo@allink.com.br");
					
					$this->envio->adicionarNovoEmail($wwa1);
					$this->envio->adicionarNovoEmail($wwa2);
				}	
			}
			
		}
		
		/**
		 * Inclui os gestores do usuário que está solicitando o desbloqueio,
		 * que são os mesmos que estão no gerenciador de permissões do ppa
		 */
		$gestores = $this->obterGestoresDoSolicitante($this->solicitante);
			
		foreach( $gestores as $gestor )
		{
			$this->envio->adicionarNovoEmail($gestor->getEmail());
		}	
		
		$clienteAssunto = $this->acordo->getClientes();
		
		$assuntoEmail = "Solicitação de desbloqueio de acordo de adicionais: " . $this->acordo->getNumeroAcordo() . " - " . $clienteAssunto[0]->getRazao();
		
		//FIXME Descomentar esta linha para enviar às mensagens
		//$enviado = $this->envio->enviarMensagem($mensagem, $assuntoEmail);
		
		$enviado = true;
		
		if( ! $enviado )
		{
			throw new Exception("A solicitação de desbloqueio foi salva, porém os emails não puderam ser enviados");
		}
	}
	
	public function enviarAprovacao($exclusao = FALSE)
	{
		//Salva a aprovação e atualiza os dados do desbloqueio
		$this->load->model("Adicionais/Desbloqueios/email_model");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Cliente/cliente_model");
		$this->load->model("Email/envio");
		$this->load->model("Email/email");
		
		$aprovador = new Usuario();
		$aprovador->setId((int)$_SESSION['matriz'][7]);
		$this->usuario_model->findById($aprovador);
		
		$this->aprovador = $aprovador;
						
		$dataAprovacao = new DateTime();
		
		$this->data_desbloqueio = $dadosAprovacao;
		
		$dadosAprovacao['id_usuario_aprovacao'] = $aprovador->getId();
		$dadosAprovacao['data_aprovacao'] = $dataAprovacao->format('Y-m-d H:i:s');
		$dadosAprovacao['aprovado'] = $this->acordo->serializar();
				
        if( $exclusao == FALSE )
        {
            $acao = "";
            $dadosAprovacao['status'] = strtoupper("aprovado");
        }
        else
        {
            $acao = "(Exclusão) ";
            $dadosAprovacao['status'] = strtoupper("cancelado");
        }  
        
		$this->db->where("id",$this->id);
		$this->db->update("CLIENTES.desbloqueios_adicionais",$dadosAprovacao);
		
		$mensagem =	$this->email_model->formatarMensagemDeAprovacao($this, $exclusao);
		
		//Adiciona o email da pessoa que solicitou o desbloqueio
		$this->usuario_model->findById($this->solicitante);
		
		$this->envio->adicionarNovoEmail($this->solicitante->getEmail());
						
		//Adiciona o email do aprovador
		$this->envio->adicionarNovoEmail($this->aprovador->getEmail());
		
		foreach( $this->getAcordo()->getClientes() as $cliente )
		{
			$this->cliente_model->findById($cliente);
		
			$this->usuario_model->findById($cliente->getVendedorExportacao());
			$this->usuario_model->findById($cliente->getCustomerExportacao());
		
			$this->envio->adicionarNovoEmail($cliente->getVendedorExportacao()->getEmail());
			$this->envio->adicionarNovoEmail($cliente->getCustomerExportacao()->getEmail());
				
			/**
			 * Verifica se o cliente do acordo é um cliente wwa,
			 * se for, então copia a samira e o lemos
			**/
			$this->db->
			select("grupo_chave")->
			from("CLIENTES.grupo_comercial")->
			where("idgrupo_comercial",$cliente->getGrupoComercial());
				
			$rowSetWWA = $this->db->get();
				
			if( $rowSetWWA->num_rows() > 0 )
			{
				$clienteWWA = $rowSetWWA->row();
												
				/**
				 * Se o grupo comercial que o cliente faz parte é um grupo WWA,
				 * então o cliente é considerado wwa e a Samira eo Lemos serão copiados.
				*/
				if( $clienteWWA->grupo_chave == "S" )
				{
					$wwa1 = new Email();
					$wwa2 = new Email();
						
					$wwa1->setEmail("samira.castro@allink.com.br");
					$wwa2->setEmail("cs07.spo@allink.com.br");
						
					$this->envio->adicionarNovoEmail($wwa1);
					$this->envio->adicionarNovoEmail($wwa2);
				}
			}
				
		}
		
		$clienteAssunto = $this->acordo->getClientes();
		                
		$assuntoEmail = $acao."Resposta da solicitação de desbloqueio de acordo de adicionais: " . $this->acordo->getNumeroAcordo() . " - " . $clienteAssunto[0]->getRazao();
		
		//FIXME Descomentar esta linha para enviar às mensagens
		//$enviado = $this->envio->enviarMensagem($mensagem, $assuntoEmail);
		
		$enviado = true;
		
		if( ! $enviado )
		{
			throw new Exception("A solicitação de desbloqueio foi salva, porém os emails não puderam ser enviados");
		}
		
	}
	
}
