<?php
class Adicionais_Facade extends CI_Model{
	
	public function __construct() 
	{
		parent::__construct();
		$this->output->enable_profiler(false);
	}
	
	public function salvarAcordo()
	{
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Adicionais/clientes_acordo_adicionais_model");
		$this->load->model("Adicionais/taxas_acordo_adicionais_model");
		$this->load->model("Clientes/cliente");
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/unidade");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Adicionais/serializa_taxa");
							
		/** Cria o objeto do acordo **/
		$acordo = new Acordo_Adicionais();
		$conversorDeTaxas = new Serializa_Taxa();
		
		$id_acordo = $this->input->post('id_acordo');	
		
		if( ! empty($id_acordo) )
		{
			$acordo->setId((int)$this->input->post('id_acordo'));					
		}	
		
		/** Testa se o post tem mesmo um array e se o sentido preenchido **/
		if( ! is_array($this->input->post('clientes_selecionados')) || count($this->input->post('clientes_selecionados')) < 1 )
		{
			throw new Exception("Houve um problema ao recuperar os clientes selecionados no formulï¿½rio!");
		}	
		
		if( ! is_array($this->input->post('taxas_selecionadas')) || count($this->input->post('taxas_selecionadas')) < 1 )
		{
			throw new Exception("Houve um problema ao recuperar às taxas selecionadas no formulário!");
		}
		
		/** Cria os objetos clientes **/
		foreach( $this->input->post('clientes_selecionados') as $cliente_selecionado )
		{
			$cliente = new Cliente();
			$cliente->setId((int)$cliente_selecionado);
			$acordo->setCliente($cliente);
		}
				
		/** Cria às taxas e insere no acordo **/
		foreach( $this->input->post('taxas_selecionadas') as $taxa_adicional_serializada )
		{
			$taxa = $conversorDeTaxas->ConverterStringParaTaxa($taxa_adicional_serializada);
			$acordo->setTaxas($taxa);			
		}	
		
		$acordo->setSentido($this->input->post('sentido'));
		$acordo->setObservacao($this->input->post('observacao_interna'));
		
		//Datas de inicio e validade
		$dataInicioAcordo = new DateTime($this->input->post('inicio'));
		$dataValidadeAcordo = new DateTime($this->input->post('validade'));

		$acordo->setInicio($dataInicioAcordo);
		$acordo->setValidade($dataValidadeAcordo);

		$acordo_model = new Acordo_Adicionais_Model();
		
		/** 
		 * Inclui a opção do usuário de influenciar ou não os acordos retroativamente
		 */
		$acordo->alterar_retroativos = $this->input->post('alterar_retroativos');
		
		$acordo_model->salvarAcordo($acordo);

		//Remove clientes duplicados se houver
		$acordo->removerClientesDuplicados();
		
		$clientes_acordo_model = new Clientes_Acordo_Adicionais_model();
		$clientes_acordo_model->salvarClientesDoAcordoDeAdicionais($acordo);
		
		if( $acordo->contarTaxas() > 0 )
		{		
			//Salva às taxas do acordo
			$taxas_acordo_model = new Taxas_Acordo_Adicionais_Model();
			$taxas_acordo_model->salvarTaxasDoAcordoDeAdicionais($acordo);		
		}
		
		return $acordo;
	}
	
	public function consultarAcordo( $id_acordo = null )
	{
		if( empty($id_acordo) )
		{
			show("Nenhum acordo foi informado para realizar a busca!");
		}
		
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Adicionais/acordo_adicionais_model");
		
		$acordo_adicionais = new Acordo_Adicionais();
				
		$acordo_adicionais->setId((int)$id_acordo);
		
		$acordo_model = new Acordo_Adicionais_Model();
		
		$acordo_model->consultarAcordoAdicionaisPorId($acordo_adicionais);		
		
        $acordo_model->localizaUltimoDesbloqueio($acordo_adicionais);
        
		return $acordo_adicionais;
	}
	
	public function listarSolicitacoesDeDesbloqueio()
	{		
		$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
		$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio_model");
		$this->load->model("Clientes/cliente_model");		
		
		$solicitacoesPendentes = $this->solicitacao_desbloqueio_model->obterSolicitacoesPendentes();
		
		foreach( $solicitacoesPendentes as $solicitacao )
		{
			foreach( $solicitacao->getAcordo()->getClientes() as $cliente )
			{
				$this->cliente_model->findById($cliente);
			}	
		}	
		
		return $solicitacoesPendentes;
	}
	
	public function responderSolicitacao( Array $response )
	{
		$this->load->model("Adicionais/Desbloqueios/agrupa_valores");
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Adicionais/serializa_taxa");
		$this->load->model("Adicionais/clientes_acordo_adicionais_model");
		$this->load->model("Adicionais/taxas_acordo_adicionais_model");
		$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
		$this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio_model");
		$this->load->model("Clientes/cliente");
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/unidade");
		$this->load->model("Usuarios/usuario");
		
		$valores = $this->agrupa_valores->converterParaValores($response);

		$acordo = new Acordo_Adicionais();
		
		$acordo->setId((int)$response['id_acordo']);
		
		$acordo->alterar_retroativos = $this->input->post('alterar_retroativos-'.$response['id_acordo']);
		
		/**
		 * Busca os dados da solicitação de desbloqueio
		 */
		$solicitacao = new Solicitacao_Desbloqueio();
		$solicitacao->setId((int)$response['id_solicitacao']);
		
		$this->solicitacao_desbloqueio_model->buscaSolicitacaoPorId($solicitacao);
		
		/**
		 * Se a validade foi modificada, então altera a validade e a data de inicio
		 */
		
		//Seleciona os dados da proposta que está salva para verificar se alguma das datas foi alterada
		$this->db->
				select("inicio, validade")->
				from("CLIENTES.acordo_adicionais")->
				where("id",$response['id_acordo']);
		
		$rowSet = $this->db->get();

		$rowAcordoSalvo = $rowSet->row();
		
		$inicio = new DateTime($rowAcordoSalvo->inicio);
		$validade = new DateTime($rowAcordoSalvo->validade);
		
		$inicioAprovado = new DateTime($response['inicio-'.$response['id_acordo']]);
		$validadeAprovada = new DateTime($response['validade-'.$response['id_acordo']]);
		
		$validadeFoiAlterada = false;
		
		if( $inicio->format('Y-m-d') != $inicioAprovado->format('Y-m-d') )
		{
			$validadeFoiAlterada = true;
		}	
		
		if( $validade->format('Y-m-d') != $validadeAprovada->format('Y-m-d') )
		{
			$validadeFoiAlterada = true;
		}	
		
		if( $validadeFoiAlterada === true ) 
		{
			//Atualiza a validade no acordo
			$dados['inicio'] = $inicioAprovado->format('Y-m-d');
			$dados['validade'] = $validadeAprovada->format('Y-m-d');
			
			$this->db->where("id",$response['id_acordo']);
			$this->db->update("CLIENTES.acordo_adicionais",$dados);
		}	
		
		//Atualiza os valores das taxas no acordo
		foreach( $valores as $id_taxa => $taxa )
		{
			/**
			 * FIXME às vezes a data de validade pode vir no array das taxas e isso causa um erro
			 * no momento de salvar ou atualizar às taxas, para contornar o problema eu verifico se todas
			 * às chaves do array vieram preenchidas, pois às chaves do array são os ids das taxas.
			 */
			if( empty($id_taxa) OR ! is_numeric($id_taxa) )
			{
				continue;	
			}	
			
			$dadosDaTaxaParaSalvar = array(																		
										'id_unidade' => $taxa['unidade'],
										'id_moeda' => $taxa['moeda'],
										'valor' => str_replace(",", ".", $taxa['valor']),
										'valor_minimo' => str_replace(",", ".", $taxa['valor_minimo']),
										'valor_maximo' => str_replace(",", ".", $taxa['valor_maximo']),																														
			);
			
			/**
			 * Verifica se a taxa já existe no acordo, se sim atualiza, se não inclui a taxa
			 */
			(bool)$novaTaxa;
			
			$this->db->
					select("*")->
					from("CLIENTES.taxas_acordo_adicionais")->
					where("id_acordo_adicional",$acordo->getId())->
					where("id_taxa",$id_taxa);
			
			$rowSet = $this->db->get();
						
			if( $rowSet->num_rows() > 0 )
			{
				$novaTaxa = false;
				$rowTaxaAcordo = $rowSet->row();
				
				$dadosDaTaxaParaSalvar['id_usuario_alteracao'] = $solicitacao->getSolicitante()->getId();
				$dadosDaTaxaParaSalvar['data_alteracao'] = $solicitacao->getDataSolicitacao()->format('Y-m-d H:i:s');
				
				$this->db->where("id",$rowTaxaAcordo->id);
				$this->db->update("CLIENTES.taxas_acordo_adicionais",$dadosDaTaxaParaSalvar);
			}
			else 
			{
				$novaTaxa = true;
				
				$dadosDaTaxaParaSalvar['id_acordo_adicional'] = $acordo->getId();
				$dadosDaTaxaParaSalvar['id_taxa'] = $id_taxa;
				$dadosDaTaxaParaSalvar['id_usuario_alteracao'] = $solicitacao->getSolicitante()->getId();
				$dadosDaTaxaParaSalvar['data_alteracao'] = $solicitacao->getDataSolicitacao()->format('Y-m-d H:i:s');
				$dadosDaTaxaParaSalvar['id_usuario_cadastro'] = $solicitacao->getSolicitante()->getId();
				$dadosDaTaxaParaSalvar['data_cadastro'] = $solicitacao->getDataSolicitacao()->format('Y-m-d H:i:s');
				
				$this->db->insert("CLIENTES.taxas_acordo_adicionais",$dadosDaTaxaParaSalvar);								
			}		
						
		}	

		//Muda o status de aprovação pendente do acordo
		$this->db->where("id",$response['id_acordo']);
		$this->db->update("CLIENTES.acordo_adicionais",array("aprovacao_pendente" => "N"));
		
		//Consulta os dados da nova proposta para atualizar no desbloqueio
		
		//FIXME limpa às taxas do acordo antes de fazer a consulta para que elas não sejam duplicadas
		$solicitacao->getAcordo()->limparTaxas();
		
		$this->acordo_adicionais_model->consultarAcordoAdicionaisPorId($solicitacao->getAcordo());
		
		$solicitacao->getAcordo()->removerClientesDuplicados();
		
		/**
		 * Antes de enviar o email de aprovação o sistema verifica se o usuário
		 * optou por alterar retroativamente às propostas do cliente. 
		 * Apenas propostas Cotação e Tarifário serão influênciadas.
		 */		
		$solicitacao->enviarAprovacao();
		
	}
    
    public function excluirSolicitacaoDeDesbloqueio(Acordo_Adicionais $acordo)
    {
        /**
         * Busca os dados da solicitação de desbloqueio
         */
        $this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
        $this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio_model");
        $this->load->model("Adicionais/acordo_adicionais_model");
        
        $this->db->
                   select("id")->
                   from("CLIENTES.desbloqueios_adicionais")->
                   where("status",  strtoupper("pendente"))->
                   where("id_acordo",$acordo->getId());
        
        $rowSet = $this->db->get();
        $id_solicitacao = $rowSet->row()->id;
        
        $solicitacaoDesbloqueio = new Solicitacao_Desbloqueio();
        $solicitacaoDesbloqueio->setId((int)$id_solicitacao);
        
        $solicitacao_model = new Solicitacao_Desbloqueio_Model();
        $solicitacao_model->buscaSolicitacaoPorId($solicitacaoDesbloqueio);
        
        /**
         * Se o acordo for novo então cancela o acordo, caso contrário apenas muda o status.
         */
        $acordo_model = new Acordo_Adicionais_Model();
        $acordo_model->consultarAcordoAdicionaisPorId($acordo);
        
        $dadosDoAcordoParaAtualizacao = array("aprovacao_pendente" => strtoupper("N"));
        
        if( ! $acordo->getUsuarioAlteracao() instanceof Usuario )
        {
            $dadosDoAcordoParaAtualizacao["ativo"] = "N";
        }    
        
		$this->db->where("id",$acordo->getId());
		$this->db->update("CLIENTES.acordo_adicionais",$dadosDoAcordoParaAtualizacao);
        
        /**
         * Envia uma mensagem avisando ao usuário que fez a solicitação
         * que a solicitação dele foi excluida. 
         */
        
        //FIXME se a data do que foi serializada na solicitação de desbloqueio
        // Estiver invalida (pois o PHP não serializa objetod DateTime ???),
        // Então substitui com a data original do acordo        
        $solicitacaoDesbloqueio->getAcordo()->setInicio($acordo->getInicio());       
        $solicitacaoDesbloqueio->getAcordo()->setValidade($acordo->getValidade());        
        
        $solicitacaoDesbloqueio->enviarAprovacao(TRUE);        
    }   
    
    public function revalidarAcordo(Acordo_Adicionais $acordo, $meses = NULL)
    {
        $this->load->model("Adicionais/Desbloqueios/solicitacao_desbloqueio");
		                        
        switch($meses)
        {
            case 0:
                $this->db->where("id",$acordo->getId());
                $this->db->update("CLIENTES.acordo_adicionais", Array("avisar_vencimento"=>"N"));
            break;
        
            case 12:
                $fimDoAno = new DateTime(date('Y')."-12-31");
                $acordo->setValidade($fimDoAno);
            break;
        
            default :
                $acordo->getValidade()->modify("+{$meses} Months");
        }
        
        if( $meses !== 0 )
        {
            $this->load->model("Usuarios/usuario");
            
            $solicitante = new Usuario();
			$solicitante->setId((int)$_SESSION['matriz'][7]);
            
            /** Verifica se o item da proposta está dentro da validade **/            
            $solicitacao = new Solicitacao_Desbloqueio();
            
            $solicitacao->setDataSolicitacao(new DateTime());
            $solicitacao->setAcordo($acordo);
            $solicitacao->setSolicitante($solicitante);  
            $solicitacao->setAlterarRetroativos("N");
                        
            $solicitacao->enviarSolicitacao();
        }    
               
    }
	
    public function cancelarAcordo($numero_acordo = NULL)
    {
        if(is_null($numero_acordo) )
        {
            throw new InvalidArgumentException("O número do acordo informado não é válido!");
        }   
        
        /**
         * Tenta encontra o acordo com o número informado
         */
        $this->load->model("Adicionais/busca_acordos_adicionais");
        $this->load->model("Adicionais/acordo_adicionais_model");
        $this->load->model("Usuarios/usuario_model");
        $this->load->model("Usuarios/usuario");
        $this->load->model("Email/envio");
        $this->load->model("Email/email");
        
        $buscador = new Busca_Acordos_Adicionais();
        
        $acordo = $buscador->buscarAcordoDeAdicionaisPorNumero($numero_acordo);
        
        /**
         * Se o acordo foi encontrado então cancela o acordo
         */
        $acordo_model = new Acordo_Adicionais_Model();
        
        $acordo_model->consultarAcordoAdicionaisPorId($acordo);
        
        $acordo_model->cancelaAcordo($acordo);
        
        /**
         * Aqui envia a mensagem de cancelamento aos usuários interassados no acordo
         * que acabou de ser cancelado (customers e vendedores)
         */
        $usuario_model = new Usuario_Model(); 
        
        $envio = new Envio();
        
        $usuario_model->findById($acordo->getUsuarioCadastro());
                
        $envio->adicionarNovoEmail($acordo->getUsuarioCadastro()->getEmail());
        
        /** Copia tambe? o usuário que está cencelando o acordo **/
        $usuarioCancelamento = new Usuario();
        $usuarioCancelamento->setId((int)$_SESSION['matriz'][7]);
        $usuario_model->findById($usuarioCancelamento);
        
        $envio->adicionarNovoEmail($usuarioCancelamento->getEmail());
        
        $stringClientes = "";
        
        foreach( $acordo->getClientes() as $cliente )
        {
            $usuario_model->findById($cliente->getVendedorExportacao());
            $usuario_model->findById($cliente->getCustomerExportacao());
            
            $envio->adicionarNovoEmail($cliente->getVendedorExportacao()->getEmail());
            $envio->adicionarNovoEmail($cliente->getCustomerExportacao()->getEmail());
            
            $stringClientes .= $cliente->getCnpj() . " -> " . $cliente->getRazao()."<br />";
        }    
        
        $mensagem ='<div class="container_elemento">				            
			            <label class="conteudo">
			            	O acordo de adicionais '.$acordo->getNumeroAcordo().' do(s) cliente(s),<p />'.
                            $stringClientes    
                            .'<p />foi cancelado em '.date('d/m/Y H:i:s').' por '.$usuarioCancelamento->getnome().'.
			            </label>
			        </div>';
		$corpo_email .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							    <title>Scoa</title>
							    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
							    <meta name="description" content="Scoa Sistema de controle Allink" />
							    <meta name="author" content="Allink Transportes Internacionais Ltda" />
							    <meta name="robots" content="noindex,nofollow" />
							    <meta name="robots" content="noarchive" />
							    '.$envio->incluir_estilo_email().'
							</head>
							<body>';		
		$corpo_email .= "<div class='principal'>";
		$corpo_email .= "<p class='titulo'>CANCELAMENTO DE ACORDO DE ADICIONAIS</p>";
		$corpo_email .= $mensagem;
		$corpo_email .="</div>";
		$corpo_email .="<div class='botoes'>Email enviado em: ".date('d/m/Y H:i:s')."</div>";
		$corpo_email .= "</body></html>";
        
        $cliente_assunto = $acordo->getClientes();
        
        $assunto = "Cancelamento de acordo de adicionais ".$acordo->getNumeroAcordo()." -> " . $cliente_assunto[0]->getRazao();
        
        //return $envio->enviarMensagem($corpo_email, $assunto);
        return;       
    }    
    
}
