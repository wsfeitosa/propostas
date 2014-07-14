<?php
/**
 * Descrição Curta
 *
 * Descrição Longa da classe 
 *
 * @package api_facade.php
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 17/07/2013
 * @version  versao 1.0
*/
class Api_Facade extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();		
	}
	
	/**
	 * ListarTarifarios
	 *
	 * Busca às tarifas disponiveis para o cliente informado, na rota informada
	 *
	 * @name ListarTarifarios
	 * @access public
	 * @param ArrayObject $parametros 
	 * @return int
	 */ 	
	public function ListarTarifarios(ArrayObject $parametros) 
	{

		$this->load->model("Tarifario/Factory/factory");
		$this->load->model("Tarifario/Factory/concrete_factory");
		$this->load->model("Tarifario/porto");
		$this->load->model("Tarifario/rota");
		
		/** Cria os factories que vão gerenciar às classes **/
		$factory = Factory::factory($parametros->offsetGet("sentido"));
		
		$concrete_factory = new Concrete_Factory();
		
		$porto_model = $concrete_factory->CreatePortoModel($factory);
						
		/** Cria os objetos dos portos **/				
		$origem = new Porto();
		$embarque = new Porto();
		$desembarque = new Porto();
		$destino = new Porto();		
		
		$origem->setUnCode($parametros->offsetGet("origem"));
		$embarque->setUnCode($parametros->offsetGet("embarque"));
		$desembarque->setUnCode($parametros->offsetGet("desembarque"));
		$destino->setUnCode($parametros->offsetGet("destino"));
						
		$porto_model->findByUnCode($origem,"origem");
		$porto_model->findByUnCode($embarque,"embarque");
		$porto_model->findByUnCode($desembarque,"desembarque");
		$porto_model->findByUnCode($destino,"destino");
		
		$rota = new Rota();
		$rota->setPortoOrigem($origem);
		$rota->setPortoEmbarque($embarque);
		$rota->setPortoDesembarque($desembarque);
		$rota->setPortoFinal($destino);
		
		/** Encontra às rotas registradas no tarifario, se existirem **/
		$tarifario_model = $concrete_factory->CreateTarifarioModel($factory);
		
		$tarifarios_encontrados = $tarifario_model->obterTarifarios($rota, new DateTime(), new DateTime());

		/** Verifica se existe alguma proposta valida para o cliente selecionado para alguma das rotas encontradas **/
		$this->load->model("Propostas/Buscas/busca_proposta_existente");
		$this->load->model("Clientes/cliente");
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Taxas/item_proposta_taxa_model");
				
		$finder = new Busca_Proposta_Existente();
		
		$item_taxa_model = new Item_Proposta_Taxa_Model();
		
		$cliente = new Cliente();
		$cliente->setId((int)$parametros->offsetGet("id_cliente"));
				
		foreach( $tarifarios_encontrados as $k=>$tarifario )
		{
			
			/** 
			 * Verifica se a carga foi informada como imo e se a rota aceita carga imo,
			 * caso não aceite, então não lista a rota
			 */
			if( $parametros->offsetGet('imo') == true && $tarifario->getAceitaImo() == "N" )
			{
				unset($tarifarios_encontrados[$k]);
				continue;
			}	
			
			/**
			 * Verifica se a modalidade CC foi informada e se a rota aceita esta modalidade,
			 * caso não aceite, então não lista a rota
			 */
			if( $parametros->offsetGet('ppcc') == "CC" && $tarifario->getAceitaFreteCc() == "N" )
			{
				unset($tarifarios_encontrados[$k]);
				continue;
			}	

			$id_item_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $tarifario, new DateTime(), new DateTime());
			
			if( $id_item_proposta !== FALSE )
			{
				/** Se um item de proposta válido foi encontrado então sobrescreve os valores do tarifário padrão **/
				$item_proposta = new Item_Proposta($tarifario);
				$item_proposta->setId((int)$id_item_proposta);				
				
				$item_taxa_model->buscaTaxasDoItemDaProposta($item_proposta);

				$this->db->select("id_proposta, numero_proposta")->from("CLIENTES.itens_proposta")->where("id_item_proposta",$id_item_proposta);
				
				$rsItem = $this->db->get();
				
				$this->db->select("numero_proposta")->from("CLIENTES.propostas")->where("id_proposta",$rsItem->row()->id_proposta);
				
				$rsNac = $this->db->get();

				if( ! empty($rsNac->row()->numero_proposta) )
				{				
					$item_proposta->getTarifario()->numero_proposta = $rsItem->row()->numero_proposta;								
				}	

				$existe_frete = FALSE;
				
				/** 
				  * Aqui verifico se o frete foi definido pois assim tenho como saber
				  * se é uma proposta de taxas locais
				  */
				foreach ($item_proposta->getTarifario()->getTaxa() as $taxa_tarifario) 
				{
					if( $taxa_tarifario->getId() == 10 )
					{
						$existe_frete = TRUE;
					}					
				}

				// Se o frete não foi definido, então é uma proposta de taxas locais 
				// então busca o frete e adicionais padrão do tarifário				
				if( ! $existe_frete )
				{
					$this->load->model("Taxas/taxa_tarifario_model");

					$taxa_tarifario_model = new Taxa_Tarifario_Model();

					$taxa_tarifario_model->obterTaxasRotaTarifario($item_proposta->getTarifario(),new DateTime(),new DateTime());					
				}

				$tarifarios_encontrados[$k] = $item_proposta->getTarifario();				
				unset($item_proposta);								
			}

			/** Verifica se existe algum nac na rota sendo pesquisada **/
			
			$this->db->
				select("itens_proposta.id_item_proposta, itens_proposta.data_inicial, itens_proposta.validade")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId())->
				where("itens_proposta.validade >=",date('Y-m-d'))->
				where("propostas.tipo_proposta =","proposta_nac");

			$resultNac = $this->db->get();

			$quantidadeDeNacsEncontrados = $resultNac->num_rows();

			if( $quantidadeDeNacsEncontrados > 0 )
			{
				
				$nacsEncontrados = $resultNac->result();

				foreach ($nacsEncontrados as $nac)
				{
					
					/** Verifica se o nac está dentro da validade **/
					if( $nac->validade < date('Y-m-d') )
					{
						continue;
					}	

					/** Se um item de proposta válido foi encontrado então sobrescreve os valores do tarifário padrão **/
					$tarifario_nac = clone($tarifario);
					
					$item_nac = new Item_Proposta($tarifario_nac);
					$item_nac->setId((int)$nac->id_item_proposta);				
					
					$item_taxa_model->buscaTaxasDoItemDaProposta($item_nac);
									
					$this->db->select("id_proposta, numero_proposta")->from("CLIENTES.itens_proposta")->where("id_item_proposta",$nac->id_item_proposta);
					
					$rsItem = $this->db->get();
					
					$this->db->select("numero_proposta,tipo_proposta,nome_nac")->from("CLIENTES.propostas")->where("id_proposta",$rsItem->row()->id_proposta);
					
					$rsNac = $this->db->get();
					
					if( ! empty($rsNac->row()->nome_nac) )
					{					
						$item_nac->getTarifario()->nome_nac = $rsNac->row()->nome_nac;
						$item_nac->getTarifario()->numero_proposta = $rsItem->row()->numero_proposta;	

						$tarifarios_encontrados[] = $item_nac->getTarifario();					
					}	

					unset($item_nac);		
				}

			}			
						
		}	
			
		$this->load->view("Api/listar_tarifarios",Array("tarifarios" => $tarifarios_encontrados));
		//TODO Fazer o tratamento de erros		
	}
	
	/**
	 * BuscarTarifarioPorId
	 *
	 * Encontra um tarifário baseado no id do tarifário
	 *
	 * @name BuscarTarifarioPorId
	 * @access public
	 * @param int $id
	 * @return int
	 */ 	
	public function BuscarTarifarioPorId($id_tarifario, $id_cliente, $imo = "N", $ppcc = NULL)
	{
		
		/** Busca o cliente para definir a classificação **/
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Clientes/define_classificacao");
		$this->load->model("Taxas/remove_taxa_imo");
		$this->load->model("Taxas/remove_taxas_exportacao");
		
		$cliente = new Cliente();
		$cliente->setId((int)$id_cliente);
		
		$cliente_model = new Cliente_Model();
		$cliente_model->findById($cliente);		
		
		/** Busca os tarifarios **/
		$rs = $this->db->get_where('FINANCEIRO.tarifarios_pricing',Array('id_tarifario_pricing' => $id_tarifario)); 
				
		if( $rs->num_rows() < 1 )
		{
			throw new Exception("Impossivel encontrar o tarifario informado!");
		}	
		
		$row = $rs->row();
		
		/** Cria os factories que vão gerenciar às classes **/
		$this->load->model("Tarifario/Factory/factory");
		$this->load->model("Tarifario/Factory/concrete_factory");
				
		$factory = Factory::factory($row->modulo);
		
		$concrete_factory = new Concrete_Factory();
		
		$tarifario = $concrete_factory->CreateTarifarioObject($factory);
		$tarifario_model = $concrete_factory->CreateTarifarioModel($factory);
		
		$tarifario->setId((int)$row->id_tarifario_pricing);
		
		$classificacao = new Define_Classificacao();
						
		$tarifario_model->findById($tarifario,$classificacao->ObterClassificacao($cliente),new DateTime(),new DateTime());
				
		/** Verifica se existem acordos de taxas locais para o(s) cliente(s) **/
		$this->load->model("Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente");
		
		$buscador_acordo_taxas_locais = new Busca_Acordo_Taxas_Locais_Cliente();
		 
		if( $row->modulo == "EXP" )
		{
			$porto_acordo_taxas = $tarifario->getRota()->getPortoOrigem();			
		}
		else
		{
			$porto_acordo_taxas = $tarifario->getRota()->getPortoFinal();			
		}
		
		$acordo_taxas = $buscador_acordo_taxas_locais->buscarAcordoTaxasCliente($row->modulo, $cliente, $porto_acordo_taxas, new DateTime(),new DateTime());
		
		if( $acordo_taxas instanceof Acordo_Taxas_Entity )
		{
			/** Compara às taxas que são o padrão do porto com às taxas do acordo **/
			include_once APPPATH . "models/Taxas/compara_taxas.php";
			 
			$comparador = new Compara_Taxas($tarifario->getTaxa(), $acordo_taxas->getTaxas());
			 
			$taxas_locais_acordadas = $comparador->comparar_taxas();
			 
			/** Limpa às taxas que estão no tarifário para incluir às acordadas **/
			$tarifario->limparTaxasTarifario();
			 
			foreach( $taxas_locais_acordadas as $taxa_acordada )
			{
				if( $taxa_acordada->getValor() < 1 )
				{
					continue;
				}	
				$tarifario->adicionarNovaTaxa($taxa_acordada);
			}
		}
		
        /**
         * Verifica se existem acordos de adicionais validos para 
         * algum dos clientes nessa rota
         */
        $this->load->model("Adicionais/clientes_acordo_adicionais_model");
        
        $buscador_acordo_adicionais = new Clientes_Acordo_Adicionais_model();
        
        $acordos_adicional = $buscador_acordo_adicionais->buscarAcordosPorIdDoCliente($cliente);
                        
        if( $acordos_adicional->count() > 0 && $tarifario->getSentido() == 'EXP' )
        {
            $this->load->model("Adicionais/acordo_adicionais_model");
            
            $acordo_model = new Acordo_Adicionais_Model();
            
            $iterador_adicionais = $acordos_adicional->getIterator();
            
            while( $iterador_adicionais->valid() )
            {
                $acordo = $iterador_adicionais->current();
                
                $acordo_model->consultarAcordoAdicionaisPorId($acordo);
                              
                /** Compara às taxas que são o padrão do porto com às taxas do acordo **/
                include_once APPPATH . "models/Taxas/compara_taxas.php";
			 
                $comparador = new Compara_Taxas($tarifario->getTaxa(), $acordo->getTaxas());
			 
                $taxas_adicionais_acordadas = $comparador->comparar_taxas();
                                                
                /** Limpa às taxas que estão no tarifário para incluir às acordadas **/
                $tarifario->limparTaxasTarifario();

                foreach( $taxas_adicionais_acordadas as $taxa_acordada )
                {
                    if( $taxa_acordada->getValor() < 1 )
                    {
                        continue;
                    }	
                    $tarifario->adicionarNovaTaxa($taxa_acordada);
                                        
                }
                
                /** 
                 * Cria uma variavel em tempo de excução na classe Tarifario e 
                 * informa quais são às taxas que estão negociadas para esse tarifário
                 */
                foreach($acordo->getTaxas() as $taxaAcordoAdicionais)
                {
                    $tarifario->adicional_negociado = $tarifario->adicional_negociado . "\n" . $taxaAcordoAdicionais->getNome();
                }                     
                
                $iterador_adicionais->next();    
            }    
        }    
                      
		/** Verifica se existe alguma proposta valida para o cliente informado **/
		$this->load->model("Propostas/Buscas/busca_proposta_existente");
		$this->load->model("Cliente/cliente");
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Taxas/item_proposta_taxa_model");
		
		$finder = new Busca_Proposta_Existente();
		
		$item_taxa_model = new Item_Proposta_Taxa_Model();
		
		$id_item_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $tarifario, new DateTime(),new DateTime());
			
		if( $id_item_proposta !== FALSE )
		{
            /** 
             * Se for uma rota com uma proposta então, retira a mensagem do aacordo de incentivos (Adicionais sobre o frete)            
             */
            $tarifario->adicional_negociado = NULL;
            
			/** Se um item de proposta válido foi encontrado então sobrescreve os valores do tarifário padrão **/
			$item_proposta = new Item_Proposta($tarifario);
			$item_proposta->setId((int)$id_item_proposta);
		
			$item_taxa_model->buscaTaxasDoItemDaProposta($item_proposta,TRUE);
			
			$existe_frete = FALSE;
				
			/** 
			  * Aqui verifico se o frete foi definido pois assim tenho como saber
			  * se é uma proposta de taxas locais
			  */
			foreach ($item_proposta->getTarifario()->getTaxa() as $taxa_tarifario) 
			{
				if( $taxa_tarifario->getId() == 10 )
				{
					$existe_frete = TRUE;
				}					
			}

			// Se o frete não foi definido, então é uma proposta de taxas locais 
			// então busca o frete e adicionais padrão do tarifário				
			if( ! $existe_frete )
			{
				$this->load->model("Taxas/taxa_tarifario_model");

				$taxa_tarifario_model = new Taxa_Tarifario_Model();

				$taxa_tarifario_model->obterTaxasRotaTarifario($item_proposta->getTarifario(),new DateTime(),new DateTime());				
			}

			$tarifarios_encontrados[$k] = $item_proposta->getTarifario();
		
			unset($item_proposta);
		}

		/** 
         * Se a carga for não for imo então remove a taxa de imo.         
         */
		$tarifario->solicitacao_imo = $imo;		
        $remove_imo = new Remove_Taxa_Imo(); 
        $remove_imo->removerTaxa($tarifario);   
		
        $tarifario->solicitacao_ppcc = $ppcc;
        $remove_taxas_exp = new Remove_Taxas_Exportacao();
        $remove_taxas_exp->removerTaxa($tarifario);
        
		return $tarifario;		
				
	}
	
	public function BuscarPropostaSpot( $numero )
	{
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Propostas/item_proposta_model");
		
		/** Propocura a proposta spot pelo numero **/
		$this->db->
					select("itens_proposta.id_item_proposta, propostas.sentido, propostas.nome_nac")->
					from("CLIENTES.itens_proposta")->
					join("CLIENTES.propostas", "itens_proposta.id_proposta = propostas.id_proposta")->
					where("itens_proposta.numero_proposta",$numero)->
					where("propostas.tipo_proposta !=", "proposta_cotacao")->
					where("propostas.tipo_proposta !=", "proposta_tarifario")->
					where("propostas.tipo_proposta !=", "proposta_especial")->
					where("itens_proposta.validade >=",date('Y-m-d'))->
					where("itens_proposta.utilizada", "N");
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			throw new Exception("Nenhuma proposta encontrada com a númeração informada");
		}	

		$row = $rs->row();
		
		$item = new Item_Proposta();
		
		$item->setId((int)$row->id_item_proposta);
		
		$model = new Item_Proposta_Model();

		$item = $model->buscarItemPorIdDoItem($item, $row->sentido);
		
		$item->nome_nac = $row->nome_nac;

		return $item;
	}
	
	/**
	 * ListarPropostasSpotAtivasPorCliente
	 *
	 * lista às propostas spot ativas que um cliente possui
	 *
	 * @name ListarPropostasSpotAtivasPorCliente
	 * @access public
	 * @param int $id_cliente
	 * @return int
	 */ 	
	public function ListarPropostasSpotAtivasPorCliente($id_cliente) 
	{
		
		/** Propocura a proposta spot pelo numero **/
		$this->db->
				select("itens_proposta.numero_proposta, propostas.sentido, propostas.tipo_proposta, propostas.id_proposta")->
				from("CLIENTES.itens_proposta")->
				join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
				join("CLIENTES.clientes_x_propostas", "clientes_x_propostas.id_proposta = propostas.id_proposta")->
				where("clientes_x_propostas.id_cliente", $id_cliente)->
				where("propostas.tipo_proposta", "proposta_spot")->
				where("itens_proposta.validade >=",date('Y-m-d'))->
				where("itens_proposta.utilizada", "N");
		 
		$rs = $this->db->get();
				
		if( $rs->num_rows() < 1 )
		{
			throw new Exception("Nenhuma proposta spot encontrada para o cliente informado");
		}
		
		$spots_encontradas = Array();
		
		foreach( $rs->result() as $spot )
		{
			array_push($spots_encontradas, $spot->numero_proposta);
		}	
		
		return $spots_encontradas;
		
	}
	
	/**
	 * BuscaTaxasLocais
	 *
	 * Busca às taxas locais e seus respectivos acordos
	 *
	 * @name BuscaTaxasLocais
	 * @access public
	 * @param int $id_cliente
	 * @param string $sentido
	 * @param string $modalidade
	 * @param int $id_origem,
	 * @param int $id_destino, 
	 * @return Array $taxas_locais
	 */ 	
	public function BuscaTaxasLocais($id_cliente, $sentido, $modalidade, $id_origem, $id_destino)
	{
		$this->load->model("Taxas/taxa_local_model","taxa_model");
		$this->load->model("Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente");		
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Clientes/define_classificacao");
		$this->load->model("Tarifario/porto");
		$this->load->model("Taxas/compara_taxas");
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Propostas/item_proposta_model");
		$this->load->model("Tarifario/Factory/concrete_factory");
    	$this->load->model("Tarifario/Factory/concrete_importacao_factory");
    	$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
    	$this->load->model("Tarifario/Factory/factory");
		
		$cliente = new Cliente();
		$cliente->setId((int)$id_cliente);
		
		$cliente_model = new Cliente_Model();
		$cliente_model->findById($cliente);		
				
		/** Seleciona a modalidade do cliente **/
		$this->db->select("id_classificacao")->from("CLIENTES.clientes")->where("id_cliente",$id_cliente);
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			show_error("Impossivel encontrar o cliente informado");
		}	
		
		$id_classificacao = $rs->row()->id_classificacao;
		
		$classificacao = "F";
		
		if( $id_classificacao == "7" )
		{
			$classificacao = "D";
		}	
		
		$id_porto_brasil = 0;
		$id_porto_estrangeiro = 0;
		$porto_brasil = new Porto();
		$porto_estrangeiro = new Porto();
		
		if( $sentido == "IMP" )
		{			
			$id_porto_brasil = $id_destino;
			$id_porto_estrangeiro = $id_origem;			
		}
		else
		{
			$id_porto_brasil = $id_origem;
			$id_porto_estrangeiro = $id_destino;			
		}		
		
		$porto_brasil->setId((int)$id_porto_brasil);
		$porto_estrangeiro->setId((int)$id_porto_estrangeiro);
		
		/** Busca às taxas normais **/
		try{		
			$taxas_locais = $this->taxa_model->ObterTaxasLocais( $sentido, $modalidade, $classificacao, $id_porto_brasil );
		} catch (Exception $e) {
			return Array();
		}
		
		if( $modalidade == "FCL" )
		{
			return $taxas_locais;
		}	
		
		/** Verifica se existe algum acordo de taxas locais para este porto **/				
		$buscador_acordo_taxas_locais = new Busca_Acordo_Taxas_Locais_Cliente();
				
		$acordo_taxas = $buscador_acordo_taxas_locais->buscarAcordoTaxasCliente($sentido, $cliente, $porto_brasil, new DateTime(),new DateTime());
			
		/** compara às taxas e faz o replace de valores **/		
		if( $acordo_taxas instanceof Acordo_Taxas_Entity )
		{
			$comparadorDeTaxas = new Compara_Taxas($taxas_locais, $acordo_taxas->getTaxas());
			
			$taxas_locais = $comparadorDeTaxas->comparar_taxas();						
		}	

		/** Verifica se o cliente tem alguma proposta **/
		$this->db->
				select("propostas.id_proposta")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.propostas", "clientes_x_propostas.id_proposta = propostas.id_proposta")->
				where("id_cliente",$id_cliente)->
				where("propostas.tipo_proposta !=","proposta_spot")->
				where("propostas.tipo_proposta !=","proposta_nac")->
				where("sentido",$sentido);
		
		$rs = $this->db->get();
		
		$linhas_encontradas = $rs->num_rows();
				
		/** Provisóriamente ficará sempre como 0 **/
		/**
		if( $_SESSION['matriz'][4] == "CPD" )
		{
			$linhas_encontradas = $rs->num_rows();
		}
		else
		{
			$linhas_encontradas = 0;				
		}	
		**/

		if( $linhas_encontradas > 0 )
		{						
			$item_model = new Item_Proposta_Model();
			
			$result = $rs->result();
			
			/** Verifica se alguma das propostas encontradas é para rota sendo pesquisada **/
			foreach( $result as $proposta )
			{
				
				/** Obtem os itens da proposta **/
				$this->db->
						select("id_item_proposta, id_tarifario_pricing")->
						from("CLIENTES.itens_proposta")->
						where("id_proposta",$proposta->id_proposta)->
						where("data_inicial <=", date('Y-m-d'))->
						where("validade >=", date('Y-m-d'));
				
				$rsItens = $this->db->get();
				
				if( $rsItens->num_rows < 1 )
				{
					continue;
				}	 
				
				/** percorre os itens da proposta encontrada **/
				$resultItens = $rsItens->result();
				
				foreach( $resultItens as $item )
				{
					/** Cria os objetos de tarifario necessários baseado no tipode proposta **/
			    	$factory = new Concrete_Factory();
			    	 
			    	$concrete_factory = Factory::factory($sentido);
			    	
			    	$tarifario = $factory->CreateTarifarioObject($concrete_factory);
			    	
			    	try {
						$item_encontrado = new Item_Proposta($tarifario);
						$item_encontrado->setId((int)$item->id_item_proposta);
						$item_encontrado = $item_model->buscarItemPorIdDoItem($item_encontrado, $sentido);				    			
			    	} catch (RunTimeException $e) {
			    		continue;	
			    	}	
			    						
					/** Compara a origem da rota com a origem informada no tarifário **/
					$id_porto_estrangeiro_comparacao = 0;
					$id_porto_brasil_comparacao = 0;
					
					if( $sentido == "IMP" )
					{						
						$id_porto_estrangeiro_comparacao = $item_encontrado->getTarifario()->getRota()->getPortoOrigem()->getId();							
						$id_porto_brasil_comparacao = $item_encontrado->getTarifario()->getRota()->getPortoFinal()->getId();
					}
					else
					{
						$id_porto_estrangeiro_comparacao = $item_encontrado->getTarifario()->getRota()->getPortoFinal()->getId();
						$id_porto_brasil_comparacao = $item_encontrado->getTarifario()->getRota()->getPortoOrigem()->getId();
					} 
					
					/** Compara origem e destino da proposta com a informada **/
					if( ($id_porto_estrangeiro == $id_porto_estrangeiro_comparacao) && ($id_porto_brasil == $id_porto_brasil_comparacao) )
					{						
						$taxas_proposta = $item_encontrado->getTarifario()->getTaxa();

						$comparaTaxasProposta = new Compara_Taxas($taxas_locais, $taxas_proposta);
						
						$taxas_locais = $comparaTaxasProposta->comparar_taxas();						
					}	
					
				}	
				
			}	
			
		}	
		
        /**
         * Remove a taxa de frete do array com às taxas que vão para o processo house
         */
        unset($taxas_locais[10]);        
        
		return $taxas_locais;
		
	}
	
	/**
	 * BuscarTarifarioNacPorId
	 *
	 * Encontra um tarifário NAC baseado no id do tarifário
	 *
	 * @name BuscarTarifarioPorId
	 * @access public
	 * @param int $id
	 * @return int
	 */
	public function BuscarTarifarioNacPorId($id_tarifario, $id_cliente, $imo = "N", $ppcc = NULL)
	{
	
		/** Busca o cliente para definir a classificação **/
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Clientes/define_classificacao");
		$this->load->model("Taxas/remove_taxa_imo");
		$this->load->model("Taxas/remove_taxas_exportacao");
	
		$cliente = new Cliente();
		$cliente->setId((int)$id_cliente);
	
		$cliente_model = new Cliente_Model();
		$cliente_model->findById($cliente);
	
		/** Busca os tarifarios **/
		$rs = $this->db->get_where('FINANCEIRO.tarifarios_pricing',Array('id_tarifario_pricing' => $id_tarifario));
	
		if( $rs->num_rows() < 1 )
		{
			throw new Exception("Impossivel encontrar o tarifario informado!");
		}
	
		$row = $rs->row();
	
		/** Cria os factories que vão gerenciar às classes **/
		$this->load->model("Tarifario/Factory/factory");
		$this->load->model("Tarifario/Factory/concrete_factory");
	
		$factory = Factory::factory($row->modulo);
	
		$concrete_factory = new Concrete_Factory();
	
		$tarifario = $concrete_factory->CreateTarifarioObject($factory);
		$tarifario_model = $concrete_factory->CreateTarifarioModel($factory);
	
		$tarifario->setId((int)$row->id_tarifario_pricing);
	
		$classificacao = new Define_Classificacao();
	
		$tarifario_model->findById($tarifario,$classificacao->ObterClassificacao($cliente),new DateTime(),new DateTime());
	
		/** Verifica se existem acordos de taxas locais para o(s) cliente(s) **/
		$this->load->model("Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente");
	
		$buscador_acordo_taxas_locais = new Busca_Acordo_Taxas_Locais_Cliente();
			
		if( $row->modulo == "EXP" )
		{
			$porto_acordo_taxas = $tarifario->getRota()->getPortoOrigem();
		}
		else
		{
			$porto_acordo_taxas = $tarifario->getRota()->getPortoFinal();
		}
	
		$acordo_taxas = $buscador_acordo_taxas_locais->buscarAcordoTaxasCliente($row->modulo, $cliente, $porto_acordo_taxas, new DateTime(),new DateTime());
	
		if( $acordo_taxas instanceof Acordo_Taxas_Entity )
		{
			/** Compara às taxas que são o padrão do porto com às taxas do acordo **/
			include_once APPPATH . "models/Taxas/compara_taxas.php";
	
			$comparador = new Compara_Taxas($tarifario->getTaxa(), $acordo_taxas->getTaxas());
	
			$taxas_locais_acordadas = $comparador->comparar_taxas();
	
			/** Limpa às taxas que estão no tarifário para incluir às acordadas **/
			$tarifario->limparTaxasTarifario();
	
			foreach( $taxas_locais_acordadas as $taxa_acordada )
			{
				if( $taxa_acordada->getValor() < 1 )
				{
					continue;
				}
				$tarifario->adicionarNovaTaxa($taxa_acordada);
			}
		}
	
		/** Verifica se existe alguma proposta valida para o cliente informado **/
		$this->load->model("Propostas/Buscas/busca_proposta_existente");
		$this->load->model("Cliente/cliente");
		$this->load->model("Propostas/item_proposta");
		$this->load->model("Taxas/item_proposta_taxa_model");
	
		$finder = new Busca_Proposta_Existente();
	
		$item_taxa_model = new Item_Proposta_Taxa_Model();
			
		$id_item_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaNacERetornaId($cliente, $tarifario, new DateTime(),new DateTime());
				
		if( $id_item_proposta !== FALSE )
		{
			/** Se um item de proposta válido foi encontrado então sobrescreve os valores do tarifário padrão **/
			$item_proposta = new Item_Proposta($tarifario);
			$item_proposta->setId((int)$id_item_proposta);
	
			$item_taxa_model->buscaTaxasDoItemDaProposta($item_proposta,TRUE);
			
			/** inclui o id do item da proposta para enviar ao xml gerado **/
			$tarifario->id_item = $id_item_proposta;
				
			$existe_frete = FALSE;
	
			/**
			 * Aqui verifico se o frete foi definido pois assim tenho como saber
			 * se é uma proposta de taxas locais
			 */
			foreach ($item_proposta->getTarifario()->getTaxa() as $taxa_tarifario)
			{
				if( $taxa_tarifario->getId() == 10 )
				{
					$existe_frete = TRUE;
				}
			}
	
			// Se o frete não foi definido, então é uma proposta de taxas locais
			// então busca o frete e adicionais padrão do tarifário
			if( ! $existe_frete )
			{
				$this->load->model("Taxas/taxa_tarifario_model");
	
				$taxa_tarifario_model = new Taxa_Tarifario_Model();
	
				$taxa_tarifario_model->obterTaxasRotaTarifario($item_proposta->getTarifario(),new DateTime(),new DateTime());
			}
	
			$tarifarios_encontrados[$k] = $item_proposta->getTarifario();
	
			unset($item_proposta);
		}
	
		/**
		 * Se a carga for não for imo então remove a taxa de imo.
		 */
		$tarifario->solicitacao_imo = $imo;
		$remove_imo = new Remove_Taxa_Imo();
		$remove_imo->removerTaxa($tarifario);
	
		$tarifario->solicitacao_ppcc = $ppcc;
		$remove_taxas_exp = new Remove_Taxas_Exportacao();
		$remove_taxas_exp->removerTaxa($tarifario);

		return $tarifario;
	
	}
	
}//END CLASS