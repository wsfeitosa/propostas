<?php
if( ! isset($_SESSION['matriz']) )
{    
    session_start();
}

ini_set("memory_limit", "512M");  
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 19/02/2013
* @version  1.0
* Classe que controla às regras de negócio das propostas cotação no sistema
*/
class Proposta_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();       
    }
        
    public function salvarProposta()
    {
    	$this->load->model("Email/email");
    	$this->load->model("Email/email_model");
    	$this->load->model("Clientes/cliente");
    	$this->load->model("Clientes/cliente_model");
    	$this->load->model("Clientes/contato");
    	$this->load->model("Clientes/contato_model");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	$this->load->model("Tarifario/Factory/concrete_factory");
    	$this->load->model("Tarifario/Factory/concrete_importacao_factory");
    	$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
    	$this->load->model('Propostas/item_proposta');
    	$this->load->model('Propostas/item_proposta_model');
    	$this->load->model("Taxas/serializa_taxas");    	
    	$this->load->model("Propostas/Memento/care_taker");
    	    	
    	try{
            $proposta = Proposta_Factory::factory($this->input->post('tipo_proposta'));
    	}catch (Exception $e) {
    		log_message('error',$e->getMessage());
    		show_error($e->getTraceAsString());
    	}
    	
        $proposta->setNumero($this->gerarNumero($this->input->post('tipo_proposta')));
        
        $proposta->setSentido($this->input->post('sentido'));
        
        $proposta->setTipoProposta($this->input->post('tipo_proposta'));

        /** Se for um nac, então salva o cliente do nac **/
        if( $proposta instanceof Proposta_NAC )
        {	
        	$proposta->setNomeNac($this->input->post('nome_nac'));
        }
                
        $cliente_model = new Cliente_Model();
                
        /** cria os objetos do tipo cliente **/
        foreach ( $this->input->post("clientes_selecionados") as $cliente_selecionado )
        {
            $cliente = new Cliente();
            
            $cliente->setId((int)$cliente_selecionado);
            
            /** Adiciona o cliente a proposta **/
            $proposta->adicionarNovoCliente($cliente);
        }    
        
        /** Cria os objetos do tipo contato **/
        $contatos_da_proposta = Array();
        $contato_model = new Contato_Model();
        
        if( is_array($this->input->post('contatos_para_selecionados')) )
        {    
        
            foreach ($this->input->post('contatos_para_selecionados') as $contato_selecionado) 
            {
                /**
                $contato = new Contato();

                $contato->setId((int)$contato_selecionado);

                //O obtem o email do contato
                $contato_model->findById($contato);
                **/
                $email_para = new Email();
                $email_para->setEmail($contato_selecionado);
                $email_para->setTipo("P");

                $proposta->adicionarNovoEmail($email_para);

            }
        
        }
        
        /** Salva os emails avulsos inseridos na proposta **/
        if( is_array($this->input->post('contatos_cc_selecionados')) )
        {    
            foreach ($this->input->post('contatos_cc_selecionados') as $email_cc)
            {
                $email = new Email();
                $email->setEmail($email_cc);
                $email->setTipo("C");
                $proposta->adicionarNovoEmail($email);
            }
        }
        
        /** Inclui os itens na proposta, recuperados da sessão **/
        if( count($_SESSION['itens_proposta']) < 1 )
        {
            log_message('error','Não foi possível recuperar os itens da sessão');
            show_error('Não foi possível salvar os itens da proposta!');
        }
        
        $factory = new Concrete_Factory();
		
        $concrete_factory = new Concrete_Exportacao_Factory();
        
        if( $proposta->getSentido() == "IMP" )
        {
        	$concrete_factory = new Concrete_Importacao_Factory();
        }	
        
        $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
        
        $serializador_de_taxas = new Serializa_Taxas();
        
        if( ! isset($_SESSION['itens_proposta']) || count($_SESSION['itens_proposta']) < 1 )
        {
            $msg = "Impossível recuperar os itens da sessão no momento de salvar. Proposta{$proposta->getNumero()}";
            log_message('error',$msg);
            show_error($msg);exit;
        }    
        
        foreach( $_SESSION['itens_proposta'] as $item )
        {
            
            $tarifario = $factory->CreateTarifarioObject($concrete_factory);
            $tarifario->setId((int)$item['id_tarifario']);

            /** Carrega às informações do tarifário selecionado na proposta **/
            $tarifario_model->findById($tarifario,"A",new DateTime($item['inicio']),new DateTime($item['validade']));
            
            /** 
             * Remove às taxas padrões trazidas pelo model do tarifário,
             * para preencher com os dados da taxas informadas na proposta  
             */
            $tarifario->limparTaxasTarifario();
            
            /** converte a string da sessão que contém às taxas em objetos **/
            if( ! empty($item['taxas_locais']) || $item['taxas_locais'] != 'NULL' )
            {
                $taxas_locais = $serializador_de_taxas->deserializaTaxasProposta($item['taxas_locais'], "Taxa_Local");

                /** Atribui às taxas a cada um dos respctivos itens **/
                foreach( $taxas_locais as $taxa_local )
                {
                    $tarifario->adicionarNovaTaxa($taxa_local);
                }
            } 
            
            if( ! empty($item['frete_adicionais']) || $item['frete_adicionais'] != 'NULL' )
            {
                $frete_adicionais = $serializador_de_taxas->deserializaTaxasProposta($item['frete_adicionais'], "Taxa_Adicional");
                
                foreach ($frete_adicionais as $taxa_adicional) 
                {
                    $tarifario->adicionarNovaTaxa($taxa_adicional);
                }
            }    
           
            $data_inicio_acordo = new DateTime($item['inicio']);
            $validade_acordo = new DateTime($item['validade']);
            
            $item_proposta = new Item_Proposta($tarifario);
                        
            $item_proposta->setInicio($data_inicio_acordo->format('Y-m-d'));
            $item_proposta->setValidade($validade_acordo->format('Y-m-d'));
            $item_proposta->setCc((bool)$item['cc']);
            $item_proposta->setPp((bool)$item['pp']);
            $item_proposta->setImo($item['imo']);
            $item_proposta->setCubagem($item['cubagem']);
            $item_proposta->setPeso($item['peso']);
            $item_proposta->setVolumes($item['volumes']);
            $item_proposta->setMercadoria($item['mercadoria']);
            $item_proposta->setObservacaoCliente($item['observacao_cliente']);
            $item_proposta->setObservacaoInterna($item['observacao_interna']);
                  
            $proposta->adicionarNovoItem($item_proposta);                       
        }  
                        
        /** Salva a proposta **/
        $this->salvar($proposta);
        
        /** Salva os clientes da proposta **/
        $cliente_model->salvarClienteProposta($proposta);
        
        /** Salva os itens da proposta **/
        $item_proposta_model = new Item_Proposta_Model();
        $item_proposta_model->salvarItens($proposta);
        
        /** Salva os emails da proposta **/
        $email_model = new Email_Model();        
        $email_model->salvarEmail($proposta);

        /** Salva o log contendo o status da proposta (memento) **/
        $memento = $proposta->CreateMemento();
        $this->care_taker->SaveState($memento);
        
        /** Destroi os itens que estão salvos na sessão do PHP **/
        unset($_SESSION['itens_proposta']);
        
        return $proposta;
        
    }//END FUNCTION        
    
    /**
     * salvar
     * 
     * Salva os dados referentes a proposta na tabela CLIENTES.propostas 
     * 
     * @name salvar
     * @access private
     * @param $proposta Proposta
     * @return boolean
     */
    protected function salvar( Proposta $proposta )
    {
        
    	if( ! is_null($proposta->getNomeNac()) )
    	{
    		$nome_nac = $proposta->getNomeNac();
    	}	
    	else
    	{
    		$nome_nac = NULL;
    	}	
    	
        $dados_para_salvar = Array(
                                    'numero_proposta' => $proposta->getNumero(),
                                    'sentido' => $proposta->getSentido(),
                                    'enviada' => FALSE,
                                    'id_usuario_inclusao' => $_SESSION['matriz'][7],
                                    'tipo_proposta' => $proposta->getTipoProposta(),
                                    'data_inclusao' => date('Y-m-d H:i:s'),
        							'nome_nac' => $nome_nac,
        );
        
       $rs = $this->db->insert("CLIENTES.propostas",$dados_para_salvar);
       
       $proposta->setId((int)$this->db->insert_id());
       
       return $rs;
       
    }        
    
    /**
     * gerarNumero
     * 
     * Gera o numero da proposta
     * 
     * @name gerarNumero
     * @access private     
     * @return string
     */
    public function gerarNumero( $tipo_proposta )
    {
        
    	if( ! isset($_SESSION['matriz']) )
    	{
    		show_error("Impossível gerar a numeração, a sessão do usuário não está definida!");
    	}	
    	
        $this->db->
        		select("propostas.numero_proposta")->
                from("CLIENTES.propostas")->
                order_by("propostas.id_proposta","desc")->
                limit(1);
        
        $rs = $this->db->get();
        
        $sigla = $this->retornaSiglaDaNumeracaoDaProposta($tipo_proposta);
        
        if( $rs->num_rows() < 1 )
        {            
            $referencia = $sigla.date('my').$_SESSION["matriz"][1]."000000000";
        }
        else
       { 	
       		$referencia = $rs->row()->numero_proposta;
       	}
       	
        //ANO DA ULTIMA COTACAO
        $ano = substr($referencia,4,2);
        //ANO ATUAL
        $ano_atual = date("y");
        
        if($ano == $ano_atual)
        {
            $numero_base = substr($referencia,8,5);
        }
        else
       {
            $numero_base = "00000";
        }               
                        
        $numero_referencia = $sigla.date("my").$_SESSION["matriz"][1].sprintf("%05d",($numero_base + "1"))."0000";      
        
        return $numero_referencia;
        
    }//END FUNCTION      
    
    /**
     * buscarPropostaPorId
     * 
     * busca uma proposta cadastrada pelo id da proposta
     * 
     * @name buscarPropostaPorId
     * @access public
     * @param Proposta $proposta
     * @return Proposta
     */
    
    public function buscarPropostaPorId(Proposta $proposta, $initial = 0, $limit = 0)
    {   	
    	    	
    	include_once APPPATH."models/Email/email_model.php";
    	include_once APPPATH."models/Clientes/cliente_model.php";
    	include_once APPPATH."models/Propostas/item_proposta_model.php";
    	include_once APPPATH."models/Usuarios/usuario_model.php";
    	include_once APPPATH."models/Usuarios/usuario.php";
    	
        /** verifica se o id da proposta foi informado **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error',"Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
            show_error("Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
        }    
        
        $this->db->
                select("id_proposta, sentido, numero_proposta, tipo_proposta, nome_nac, 
                		id_usuario_inclusao, id_usuario_alteracao, data_inclusao, data_alteracao")->
                from("CLIENTES.propostas")->
                where("propostas.id_proposta", $id_proposta);
        
        $rs = $this->db->get();        
        
        if( $rs->num_rows() < 1 )
        {
            log_message('error',"A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
            throw new UnexpectedValueException("A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
        }    
        
        $proposta_encontrada = $rs->row();
        
        $proposta->setId( (int) $proposta_encontrada->id_proposta );
        $proposta->setSentido( (string) $proposta_encontrada->sentido );
        $proposta->setNumero( (string) $proposta_encontrada->numero_proposta );
        $proposta->setTipoProposta( (string) $proposta_encontrada->tipo_proposta );
        $proposta->setNomeNac( (string) $proposta_encontrada->nome_nac);
        
        /** Recupera os itens das propostas **/
        $item_model = new Item_Proposta_Model();
        
        $item_model->buscarItensPorIdDaProposta($proposta,$initial,$limit);
                
        /** Busca os clientes **/
        $cliente_model = new Cliente_Model();
        
        $cliente_model->findByIdDaProposta($proposta); 
               
        /** Busca os emails adicionados a proposta **/        
        $email_model = new Email_Model();
                
        $email_model->buscaEmailPeloIdDaProposta($proposta);
        
        /** Recupera os usuários de cadastro e da última alteração **/
        $usuario_inclusao = new Usuario();
        $usuario_inclusao->setId((int)$proposta_encontrada->id_usuario_inclusao);
        
        $usuario_model = new Usuario_Model();
        $usuario_model->findById($usuario_inclusao);      
                        
        $proposta->usuario_inclusao = $usuario_inclusao;
        
        $data_inclusao = new DateTime($proposta_encontrada->data_inclusao);
        
        $proposta->data_inclusao = $data_inclusao;
        
        if( ! empty($proposta_encontrada->id_usuario_alteracao) )
        {
        	$usuario_alteracao = new Usuario();
        	$usuario_alteracao->setId((int)$proposta_encontrada->id_usuario_alteracao);
        	$usuario_model->findById($usuario_alteracao);
        	
        	$data_alteracao = new DateTime($proposta_encontrada->data_alteracao);
        	
        	$proposta->usuario_alteracao = $usuario_alteracao;
        	$proposta->data_alteracao = $data_alteracao;        	
        }	
                
        return $proposta;
    }
     
    /**
      * preparaDadosDaPropostaParaView
      * 
      * Tranforma os dados da proposta para enviar a view
      * 
      * @name preparaDadosDaPropostaParaView
      * @access public
      * @param Proposta $proposta 
      * @return array $proposta_formatada
      */ 
    public function serializaDadosDaPropostaParaView(Proposta $proposta)
    {
        
        $dados_serializados = Array();
        
        $itensDaProposta = $proposta->getItens();
        
        foreach ($itensDaProposta as $item) 
        {
            
            $dados_serializados[$item->getId()] = $item->getTarifario()->getRota()->getPortoOrigem()->getNome() . " - " . 
                                                  $item->getTarifario()->getRota()->getPortoEmbarque()->getNome() . " - " .
                                                  $item->getTarifario()->getRota()->getPortoDesembarque()->getNome() . " - " .
                                                  $item->getTarifario()->getRota()->getPortoFinal()->getNome();
        }
    	
        return $dados_serializados;
        
    }//END FUNCTION
    
    /**
     * alterarProposta
     * 
     * Altera od dados de uma proposta já existente
     * 
     * @name alterarProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
     */
    public function alterarProposta() 
    {   
    	  	
    	$this->load->model("Clientes/cliente");
    	$this->load->model("Clientes/cliente_model");
    	$this->load->model("Clientes/contato");
    	$this->load->model("Clientes/contato_model");
    	$this->load->model("Email/email");
    	$this->load->model("Email/email_model");
    	$this->load->model("Taxas/serializa_taxas");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	$this->load->model("Tarifario/Factory/concrete_factory");
    	$this->load->model("Tarifario/Factory/concrete_importacao_factory");
    	$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
    	$this->load->model('Propostas/item_proposta');
    	$this->load->model('Propostas/item_proposta_model');
        //$this->load->model("Propostas/Memento/memento");
        $this->load->model("Propostas/Memento/care_taker");
    	
        /** Cria o objeto necessario para atender a requisicao **/       
        $proposta = Proposta_Factory::factory($this->input->post('tipo_proposta'));
               
        $proposta->setId($this->input->post('id_proposta'));    
        $proposta->setSentido($this->input->post('sentido'));
        $proposta->setNumero($this->buscarNumeroDaPropostaPeloId($proposta));        
        
        $dados_para_alteracao['enviada'] = FALSE; //FIXME aki tera de ser informado se a proposta ja foi ou não enviada
        $dados_para_alteracao['id_usuario_alteracao'] = $_SESSION['matriz'][7];
        $dados_para_alteracao['data_alteracao'] = date('Y-m-d H:i:s');
        
        if( $this->input->post('nome_nac') != NULL && $this->input->post('nome_nac') != "" )
        {        
        	$dados_para_alteracao['nome_nac'] = $this->input->post('nome_nac');
        }
        
        $this->db->where("propostas.id_proposta",$proposta->getId());
        $this->db->update("CLIENTES.propostas",$dados_para_alteracao);
                        
        $cliente_model = new Cliente_Model();
                
        /** cria os objetos do tipo cliente **/
        $clientesDoFormulario = $this->input->post("clientes_selecionados");
        
        foreach ( $clientesDoFormulario as $cliente_selecionado )
        {
            $cliente = new Cliente();
            
            $cliente->setId((int)$cliente_selecionado);
            
            /** Adiciona o cliente a proposta **/
            $proposta->adicionarNovoCliente($cliente);
        }    
        
        /** Cria os objetos do tipo contato **/
        $contatos_da_proposta = Array();
        $contato_model = new Contato_Model();
        
        if( is_array($this->input->post('contatos_para_selecionados')) )
        {    
        	
        	$contatosDoFormulario = $this->input->post('contatos_para_selecionados');
        	
            foreach ($contatosDoFormulario as $contato_selecionado) 
            {
                /**
                $contato = new Contato();

                $contato->setId((int)$contato_selecionado);

                // O obtem o email do contato
                $contato_model->findById($contato);

                $email_para = $contato->getEmail();
                **/
                $email_para = new Email();
                $email_para->setEmail($contato_selecionado);
                $email_para->setTipo("P");

                $proposta->adicionarNovoEmail($email_para);

            }
        
        }
        
        /** Salva os emails avulsos inseridos na proposta **/
        if( is_array($this->input->post('contatos_cc_selecionados')) )
        {    
            foreach ($this->input->post('contatos_cc_selecionados') as $email_cc)
            {
                $email = new Email();
                $email->setEmail($email_cc);
                $email->setTipo("C");
                $proposta->adicionarNovoEmail($email);
            }
        }
        
        /** Inclui os itens na proposta, recuperados da sessão **/        
        if( ! isset($_SESSION['itens_proposta']) )
        {
            log_message('error','Não foi possivel recuperar os itens da proposta para realizar a alteração! Proposta: '.$proposta->getId());
            show_error('Não foi possivel recuperar os itens da proposta para realizar a alteração!');
        }         
             
        if( $proposta->getSentido() == "IMP" )
        {           
            $factory = new Concrete_Importacao_Factory();            
        } 
        else
       {
        	$factory = new Concrete_Exportacao_Factory();
        } 	   

        $concrete_factory = new Concrete_Factory();
        
        $tarifario_model = $concrete_factory->CreateTarifarioModel($factory);
                        
        $serializador_de_taxas = new Serializa_Taxas();
        
        foreach( $_SESSION['itens_proposta'] as $item )
        {
            
            $tarifario = $concrete_factory->CreateTarifarioObject($factory);
            $tarifario->setId((int)$item['id_tarifario']);
                        
            /** Carrega às informações do tarifário selecionado na proposta **/
            $tarifario_model->findById($tarifario,"A",new DateTime($item['inicio']), new DateTime($item['validade']));
                        
            /** 
             * Remove às taxas padrões trazidas pelo model do tarifário,
             * para preencher com os dados da taxas informadas na proposta  
             */
            $tarifario->limparTaxasTarifario();
            
            /** converte a string da sessão que contém às taxas em objetos **/
            $taxas_locais = $serializador_de_taxas->deserializaTaxasProposta($item['taxas_locais'], "Taxa_Local");
            
            $frete_adicionais = $serializador_de_taxas->deserializaTaxasProposta($item['frete_adicionais'], "Taxa_Adicional");
            
            /** Atribui às taxas a cada um dos respctivos itens **/
            foreach( $taxas_locais as $taxa_local )
            {
                $tarifario->adicionarNovaTaxa($taxa_local);
            }
            
            foreach ($frete_adicionais as $taxa_adicional) 
            {
                $tarifario->adicionarNovaTaxa($taxa_adicional);
            }
            
            $data_inicio = new DateTime($item['inicio']);
            $data_validade = new DateTime($item['validade']);
            
            $item_proposta = new Item_Proposta($tarifario);
            $item_proposta->setInicio($data_inicio->format('Y-m-d'));
            $item_proposta->setValidade($data_validade->format('Y-m-d'));
            $item_proposta->setCc((bool)$item['cc']);
            $item_proposta->setPp((bool)$item['pp']);
            $item_proposta->setImo($item['imo']);
            $item_proposta->setCubagem($item['cubagem']);
            $item_proposta->setPeso($item['peso']);
            $item_proposta->setVolumes($item['volumes']);
            $item_proposta->setMercadoria($item['mercadoria']);
            $item_proposta->setObservacaoCliente($item['observacao_cliente']);
            $item_proposta->setObservacaoInterna($item['observacao_interna']);
            
            /** Adiciona o id do item da proposta se for um item já existente **/
            if( ! is_null($item['id_item']) )
            {
                 $item_proposta->setId($item['id_item']);
                 
                 /** Seleciona o numero da proposta **/
                 $rsNumero = $this->db->get_where("CLIENTES.itens_proposta",Array('id_item_proposta'=>$item['id_item']));
                 
                 $rowNumero = $rsNumero->row();
                 
                 $item_proposta->setNumero($rowNumero->numero_proposta);
            }    
            
            $proposta->adicionarNovoItem($item_proposta);
        }
                
        /** Exclui os clientes que estão relacionados na proposta antes de salva-la **/
        $cliente_model->excluiClientesPeloIdDaProposta($proposta);
        
        /** Salva os clientes da proposta **/
        $cliente_model->salvarClienteProposta($proposta);
        
        /** Salva os itens da proposta **/
        $item_proposta_model = new Item_Proposta_Model();
        $item_proposta_model->salvarItens($proposta);
                        
        /** Salva os emails da proposta **/
        $email_model = new Email_Model(); 
        
        /** Exclui os emails da proposta antes de adicionar os novos emails após a alteração **/
        $email_model->excluirEmailsDaPropostaPeloIdDaProposta($proposta);
        
        $email_model->salvarEmail($proposta);

        /** Salva o log contendo o status da proposta (memento) **/
        $memento = $proposta->CreateMemento();
        $this->care_taker->SaveState($memento);
        
        /** Destroi os itens que estão salvos na sessão do PHP **/
        unset($_SESSION['itens_proposta']);
        
        return $proposta;       
                      
    }//END FUNCTION
    
    /**
     * buscarNumeroDaPropostaPeloId
     * 
     * Retorna o numero da proposta fazendo a busca pelo Id
     * 
     * @name buscarNumeroDaPropostaPeloId
     * @access public
     * @param Proposta $proposta
     * @return string $numero
     */
    public function buscarNumeroDaPropostaPeloId(Proposta $proposta)
    {
        
         /** verifica se o id da propostan foi informado **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error',"Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
            show_error("Impossivel realizar a consulta, o id da proposta não foi definido corretamente!");
        }
        
        $this->db->
                select("propostas.numero_proposta")->
                from("CLIENTES.propostas")->
                where("propostas.id_proposta",$id_proposta);
        
        $rs = $this->db->get();
                        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Impossivel encontrar o número da proposta solicitada");
        }    
        
        $numero = $rs->row()->numero_proposta;
        
        return $numero;
        
    }//END FUNCTION
    
    /**
     * retornaSiglaDaNumeracaoDaProposta
     *
     * Retorna a sigla correta para ser utilizada na geração do número da proposta.
     *
     * @name retornaSiglaDaNumeracaoDaProposta
     * @access protected
     * @param string $tipo_de_proposta
     * @return string $sigla
     */
    protected function retornaSiglaDaNumeracaoDaProposta( $tipo_de_proposta )
    {
    	
    	switch( $tipo_de_proposta )
    	{
    		case "proposta_cotacao":
    			return "PC";
    		break;

    		case "proposta_tarifario":
    			return "PT";
    		break;
    		
    		case "proposta_especial":
    			return "PE";
    		break;
    		
    		case "proposta_spot":
    			return "PS";
    		break;
    		
    		case "proposta_nac":
    			return "NC";
    		break;
    		
    		default:
    			$error_message = "Impossível gerar o número da proposta, tipo de proposta desconhecido!";
    			log_message('erros',$error_message);
    			show_error($error_message);
    		
    	}
    	
    }

    public function excluir_proposta( $numero_proposta = NULL )
    {
        if( empty($numero_proposta) )
        {
            throw new Exception("Número da proposta informado inválido!", 1);                            
        }    

        /** Procura pela proposta informada **/
         $this->db->
                select("propostas.id_proposta, propostas.numero_proposta")->
                from("CLIENTES.propostas")->
                where("propostas.numero_proposta",$numero_proposta);
        
        $rs = $this->db->get();
                        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Impossivel encontrar o número da proposta solicitada");
        }    
        
        $id_proposta = $rs->row()->id_proposta;
        $numero_proposta = $rs->row()->numero_proposta;
        
        switch( substr($numero_proposta,0,2) )
        {
            case "PC":
                $type = "proposta_cotacao";
            break;

            case "PT":
                $type = "proposta_tarifario";
            break;
            
            case "PE":
                $type = "proposta_especial";
            break;
            
            case "PS":
                $type = "proposta_spot";
            break;
            
            case "NC":
                $type = "proposta_nac";
            break;
            
            default:
                $error_message = "tipo de proposta desconhecido!";
                log_message('erros',$error_message);
                throw new Exception($error_message);        
        }

        $this->load->model("Propostas/Factory/proposta_factory");
        $this->load->model("Propostas/item_proposta_model");
        $this->load->model('Clientes/cliente_model');
        $this->load->model('Email/email_model');
        $this->load->model("Propostas/Memento/care_taker");
        
        $proposta = Proposta_Factory::factory($type);
        $proposta->setId((int)$id_proposta);
        $proposta->setNumero($numero_proposta);

        $proposta = $this->buscarPropostaPorId($proposta);

        /** Salva o log contendo o status da proposta (memento) **/
        $memento = $proposta->CreateMemento();
        $this->care_taker->SaveState($memento);

        error_log("Proposta ".$numero_proposta." excluída por:".$_SESSION['matriz'][0]." em ".date('d/m/Y H:i:s')."\r\n",3,"/var/www/html/allink/logs/log_exclusao_proposta.log");

        $item_proposta_model = new Item_Proposta_Model();
        $cliente_model = new Cliente_Model();
        $email_model = new Email_Model();

        /** Exclui os clientes da proposta **/
        $cliente_model->excluiClientesPeloIdDaProposta($proposta);

        /** Exclui os contatos da proposta **/
        $email_model->excluirEmailsDaPropostaPeloIdDaProposta($proposta);

        $itens_proposta = $proposta->getItens();

        foreach( $itens_proposta as $item )
        {
            /** Exclui o item e às taxas do item da proposta **/
            $item_proposta_model->excluirItemDaPropostaPeloIdDoItem($item);

            /** Excluir os desbloqueios pendentes que houverem do item **/
            $this->db->where("id_taxa_item",$item->getId());
            $this->db->where("modulo","proposta");
            $this->db->where("status","P");
            $this->db->delete("CLIENTES.desbloqueios_taxas");

            $this->db->where("id_item",$item->getId());
            $this->db->where("modulo","proposta");
            $this->db->where("status","P");
            $this->db->delete("CLIENTES.desbloqueios_validades");
        }        

        /** Exclui a proposta **/
        $this->db->where("id_proposta = ",$proposta->getId());
        $this->db->delete('CLIENTES.propostas');
    }

    /**
      * carregarItensParaViewAjax
      * 
      * Carrega pequenas quantidades de dados dos itens de proposta para às views da proposta
      * 
      * @name carregarItensParaViewAjax
      * @access public
      * @param int $id_proposta
      * @param int $initial
      * @param int $limit 
      * @return mixed $itens_formatados
      */ 
    public function carregarItensParaViewAjax($id_proposta = NULL, $initial = 0, $limit = 5)
    {
        $this->load->model("Propostas/item_proposta_model");
        $this->load->model("Propostas/proposta_tarifario");
        
        $item_model = new Item_Proposta_Model();
        
        $itens_formatados = "";
        
        $this->db->
                select("id_proposta, sentido, numero_proposta, tipo_proposta, nome_nac, 
                        id_usuario_inclusao, id_usuario_alteracao, data_inclusao, data_alteracao")->
                from("CLIENTES.propostas")->
                where("propostas.id_proposta", $id_proposta);
        
        $rs = $this->db->get();        
        
        if( $rs->num_rows() < 1 )
        {
            log_message('error',"A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
            throw new UnexpectedValueException("A proposta com o Id : ".$proposta->getId()." não pode ser recuperada!");
        }    
        
        $proposta_encontrada = $rs->row();
        
        $proposta = new Proposta_Tarifario();

        $proposta->setId( (int) $proposta_encontrada->id_proposta );
        $proposta->setSentido( (string) $proposta_encontrada->sentido );
        $proposta->setNumero( (string) $proposta_encontrada->numero_proposta );
        $proposta->setTipoProposta( (string) $proposta_encontrada->tipo_proposta );
        $proposta->setNomeNac( (string) $proposta_encontrada->nome_nac);

        $item_model->buscarItensPorIdDaProposta($proposta, $initial, $limit);
        
        $itens = $proposta->getItens();
        
        foreach($itens as $item)
        {

            if($item->getInicio() instanceof DateTime)
            {    
                $data_inicio = $item->getInicio();
            }
            else
            {
                $data_inicio = new DateTime($item->getInicio());                
            }

            if($item->getValidade() instanceof DateTime)
            {
                $validade = $item->getValidade();
            }   
            else
            {
                $validade = new DateTime($item->getValidade());                
            }  

            if($item->getPp() == TRUE) { $pp = "SIM"; } else { $pp = "NÃO"; } 
            if($item->getCc() == TRUE) { $cc = "SIM";} else { $cc = "NÃO"; }
            if($item->getImo() == "S") { $imo = "SIM";} else { $imo = "NÃO"; }

            // Formata às taxas adicionais e frete
            $frete_adicionais = "";

            foreach($item->getTarifario()->getTaxa() as $taxa)
            {
                if( $taxa instanceof Taxa_Adicional )
                {                                             
                    if( $taxa->getBloqueada() == TRUE )
                    {
                        $style_frete = "style = 'color:red;'";                                                
                    } 
                    else
                    {
                        $style_frete = "";
                    }    

                    
                    $frete_adicionais .=  $taxa->getNome()." | " . $taxa->getMoeda()->getSigla() . " ". number_format($taxa->getValor(),2) . " " .
                                          $taxa->getUnidade()->getUnidade() . " | " . number_format($taxa->getValorMinimo(),2) . " | " . 
                                          number_format($taxa->getValorMaximo(),2) . " " . $taxa->getPPCC(). "<br />";                                            
                }   
            }

            // Formata às taxas locais
            $taxas_locais = "";

            foreach($item->getTarifario()->getTaxa() as $taxa)
            {
                if( $taxa instanceof Taxa_Local )
                {   
                    if( $taxa->getBloqueada() == TRUE )
                    {
                        $style = "style = 'color:red;'";
                    } 
                    else
                    {
                        $style = "";
                    }
                                                  
                    $taxas_locais .= ($taxa->getNome())." | " . $taxa->getMoeda()->getSigla() . " ". number_format($taxa->getValor(),2) . " " .
                                      $taxa->getUnidade()->getUnidade() . " | " . number_format($taxa->getValorMinimo(),2) . " | " . 
                                      number_format($taxa->getValorMaximo(),2) . " " . $taxa->getPPCC() . "<br />";
                         
                }   
            }

            $itens_formatados .='<h2 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" role="tab" aria-expanded="false" aria-selected="false" tabindex="-1">
                                    <span class="ui-icon ui-icon-triangle-1-e"></span>
                                    <a href="#"  tabindex="-1">
                                     '.$item->getNumero().' ->    
                                     '.$item->getTarifario()->getRota()->getPortoOrigem()->getNome() . " - ". 
                                     $item->getTarifario()->getRota()->getPortoEmbarque()->getNome() . " - " .
                                     $item->getTarifario()->getRota()->getPortoDesembarque()->getNome() . " - " .
                                     $item->getTarifario()->getRota()->getPortoFinal()->getNome() . '
                                     </a>
                                </h2>
                                <div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="height: 295.2px; display: none;" role="tabpanel">
                                    <p>
                                    <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
                                        <tr>
                                            <td colspan="4">Status do Item</td>                             
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="texto_pb">
                                                '.$item->getStatus()->getStatus().'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Inicio</td>
                                            <td colspan="2">Validade(Embarque)</td>
                                        </tr>
                                        <tr>                               
                                            <td colspan="2" class="texto_pb">
                                                '.$data_inicio->format('d/m/Y').'                                
                                            </td>
                                            <td colspan="2" class="texto_pb"  >                                
                                                '.$validade->format("d/m/Y").'                                
                                            </td>                                
                                        </tr>
                                        <tr>
                                            <td width="25%">Mercadoria</td>
                                            <td width="25%">PP</td>
                                            <td width="25%">CC</td>
                                            <td width="25%">Carga Imo</td>                                
                                        </tr>
                                        <tr>
                                            <td class="texto_pb">'.utf8_decode(urldecode($item->getMercadoria())).'</td>
                                            <td class="texto_pb">'.$pp.'</td>
                                            <td class="texto_pb">'.$cc.'</td>
                                        <td class="texto_pb">'.$imo.'</td>                                
                                        </tr>
                                        <tr>
                                            <td>Origem:</td>
                                            <td>Embarque:</td>
                                            <td>Desembarque:</td>
                                            <td>Destino:</td>
                                        </tr>
                                        <tr>
                                            <td class="texto_pb">'.$item->getTarifario()->getRota()->getPortoOrigem()->getNome().'</td>
                                            <td class="texto_pb">'.$item->getTarifario()->getRota()->getPortoEmbarque()->getNome().'</td>
                                            <td class="texto_pb">'.$item->getTarifario()->getRota()->getPortoDesembarque()->getNome().'</td>
                                            <td class="texto_pb">'.$item->getTarifario()->getRota()->getPortoFinal()->getNome().'</td>
                                        </tr>
                                        <tr>
                                            <td width="25%">Peso(Kg)</td>
                                            <td width="25%">Cubagem(M3)</td>
                                            <td width="25%">Volumes</td>
                                            <td width="25%">Carga Imo</td>                                
                                        </tr>
                                        <tr>
                                            <td class="texto_pb">'.$item->getPeso().'</td>
                                            <td class="texto_pb">'.$item->getCubagem().'</td>
                                            <td class="texto_pb">'.$item->getVolumes().'</td>
                                            <td class="texto_pb">&nbsp;</td>                                
                                        </tr>
                                        <tr>
                                            <td colspan="2" width="50%">Frete e Adicionais</td>
                                            <td colspan="2" width="50%">Taxas Locais</td>
                                        </tr>                            
                                        <tr>                                
                                            <td class="texto_pb" colspan="2">                                                
                                                '.$frete_adicionais.'                                                
                                            </td>
                                            <td class="texto_pb" colspan="2">
                                                '.$taxas_locais.'                                            
                                            </td>                                
                                        </tr>
                                        <tr>
                                            <td colspan="2" width="50%">Observações Internas</td>
                                            <td colspan="2" width="50%">Observações Cliente</td>
                                        </tr>
                                        <tr>
                                            <td class="texto_pb" colspan="2">'.nl2br(utf8_decode(urldecode($item->getObservacaoInterna()))).'</td>
                                            <td class="texto_pb" colspan="2">'.nl2br(utf8_decode(urldecode($item->getObservacaoCliente()))).'</td>
                                        </tr>
                                    </table>
                                    </p>
                                </div>';
        }   
        
        echo $itens_formatados;            

    }
    
}//END CLASS


