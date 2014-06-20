<?php
if( ! isset($_SESSION) )
{    
    session_start();
} 

ini_set("memory_limit", "1024M");
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 27/02/2013
* @version  1.0
* Classe que manupula a sessão do PHP para a inclusão de itens das propostas a serem salvos
*/

class Item_Proposta_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();        
    }
      
    /**
     * salvarItens
     * 
     * Salva os itens de uma proposta
     * 
     * @name salvarItem
     * @access public
     * @param $proposta Proposta
     * @return boolean
     */
     public function salvarItens( Proposta $proposta )
     {
             	
     	 $this->load->model("Taxas/taxa_model");
     	 $this->load->model("Propostas/Buscas/busca_proposta_existente");
         $this->load->model("Propostas/item_proposta");
         $this->load->model("Propostas/status_item");
         $this->load->model("Usuarios/usuario");
         $this->load->model("Usuarios/filial");
         $this->load->model("Desbloqueios/solicitacao_taxa");
         $this->load->model("Desbloqueios/Solicitacao_Taxa_Entity");
         $this->load->model("Desbloqueios/solicitacao_periodo_entity");
     	 $this->load->model("Desbloqueios/solicitacao_desbloqueio_periodo_facade");
     	 
         $model = new Taxa_Model();
         
         $finder = new Busca_Proposta_Existente();
         
         $solicitacao = new Solicitacao_Taxa();
         
         /** 
           * Verifica se já existe outra proposta para este cliente nesta rota,
           * caso houver, cancela o item da proposta encontrada antes de salvar o novo
           * para que não existam dois ativos ao mesmo tempo 
           */
         $itens_para_excluir = $finder->buscaPorItensDuplicadosDeUmaNovaProposta($proposta);           
         
         if( $itens_para_excluir->count() > 0 )
         {
             $iterator = $itens_para_excluir->getIterator();

             while( $iterator->valid() )
             {
                 $item_exclusao = new Item_Proposta();

                 $item_exclusao->setId($iterator->current());

                 $this->excluirItemDaPropostaPeloIdDoItem($item_exclusao);

                 $iterator->next();
             }    

         }    
         
         $solicitacao_facade = new Solicitacao_Desbloqueio_Periodo_Facade();
         
         /** aplica a função a cada item da proposta **/
         $itens_recuperados = $proposta->getItens();         
         
         $itens_solicitacao = Array();

         foreach ($itens_recuperados as $item) 
         {                      
             /** Se o id da proposta estiver definido executa uma alteração, caso contrário executa a inclusão do item **/
             $id_item = $item->getId();
             
             /** Verifica se o item da proposta está dentro da validade **/             
             $solicitacao_entity = new Solicitacao_Periodo_Entity();

             $solicitacao_entity->setDataSolicitacao(new DateTime());
             $solicitacao_entity->setInicio($item->getInicio());
             $solicitacao_entity->setStatus("P");
             $solicitacao_entity->setUsuarioSolicitacao($_SESSION['matriz'][7]);
             $solicitacao_entity->setModulo("proposta");
             
             /** 
              * Se o id do item for vazio, então é uma nova proposta então valida o desbloqueio
              * pelo ultimo dia do mês, caso contrário é uma alteração e então é necessário buscar a data 
              * de validade atual do acordo para validar se a validade solicitada é maior que a validade atual.               
              */
             if( empty($id_item) )
             {
                 $solicitacao_entity->setValidade($item->getValidade());             
                 
                 $solicitacao_entity->setIdItem((int)$id_item);

                 $necessita_desbloqueio = $solicitacao_facade->verificaSeEstaDentroDaValidade($solicitacao_entity,$proposta->getSentido());
                 
                 if( $necessita_desbloqueio == TRUE )
                 {                    
                    $item->setValidade(date('Y-m-t'));
                    array_push($itens_solicitacao, $solicitacao_entity);
                 }
             }   
             else
           {
                 /** Busca pela validade atual do item **/
                 $this->db->
                        select("validade")->
                        from("CLIENTES.itens_proposta")->
                        where("id_item_proposta",$id_item);

                 $rsValidade = $this->db->get();
                 
                 if( ! $rsValidade )
                 {
                    show_error("Impossivel encontrar a validade anterior da proposta!");exit;
                 }   

                 $rowValidade = $rsValidade->row();

                 $validade_anterior = new DateTime($rowValidade->validade);

                 $validade_solicitada = new DateTime($item->getValidade());

                 if( $validade_solicitada->format('Y-m-d') > $validade_anterior->format('Y-m-d') )
                 {
                     $solicitacao_entity->setValidade($validade_solicitada->format('Y-m-d'));
                     /** Coloca a validade da proposta até o último dia do mês **/
                     $item->setValidade(date('Y-m-t'));
                     array_push($itens_solicitacao, $solicitacao_entity);
                 }   

             }             
             
             $peso = str_replace(",", ".", $item->getPeso());
             $cubagem = str_replace(",",".", $item->getCubagem());
             
             $dados_para_salvar = Array(
                                        "id_proposta" => $proposta->getId(),
                                        "id_tarifario_pricing" => $item->getTarifario()->getId(),                                        
                                        "mercadoria" => strtoupper($item->getMercadoria()),
                                        "pp" => $item->getPp(),
                                        "cc" => $item->getCc(),
                                        "imo" => $item->getImo(),
                                        "peso" => $peso,
                                        "cubagem" => $cubagem,
                                        "volumes" => $item->getVolumes(),
                                        "observacao_interna" => strtoupper($item->getObservacaoInterna()),
                                        "observacao_cliente" => strtoupper($item->getObservacaoCliente()),                                                                                            
                                        "data_inicial" => $item->getInicio(),
                                        "validade" => $item->getValidade()
             );

             if( empty($id_item) )
             {
                 $dados_para_salvar['id_status_item'] = '1'; 
                 
                 $dados_para_salvar["numero_proposta"] = $this->geraNovoNumeroDeItemDeProposta($proposta);
                 
                 $item->setNumero($dados_para_salvar["numero_proposta"]);
                 
                 $rs = $this->db->insert("CLIENTES.itens_proposta",$dados_para_salvar);
             
                 $id_do_item_salvo = $this->db->insert_id();
                 
                 $item->setId((int)$id_do_item_salvo);
                 
                 /** Seta o status do item **/
                 $status_item = new Status_Item();
                                  
                 $item->setStatus($status_item->findById((int)1));
                 
             }
             else
             {       		 
                 $this->db->where("itens_proposta.id_item_proposta",$item->getId());
                 
                 $rs = $this->db->update("CLIENTES.itens_proposta",$dados_para_salvar);
                 
                 $id_do_item_salvo = $item->getId();       
                 
                 /** Exclui às taxas antigas antes da salvar às taxas novas **/
                 $model->exluirTaxasPorItemDeProposta($item);
                 
                 /** Recupera o status atual do item e seta o status na memória **/
                 $rsStatus = $this->db->get_where("CLIENTES.itens_proposta",Array('id_item_proposta'=>$item->getId()));
                 
                 $rowStatus = $rsStatus->row();
                 
                 $status_item = new Status_Item();
                 
                 $item->setStatus($status_item->findById((int)$rowStatus->id_status_item));
                 
             }    
             
             $solicitacao_entity->setIdItem((int)$id_do_item_salvo);
                  
             /** Salva às taxas referentes à este item da proposta **/   
             $taxas_tarifario = $item->getTarifario()->obterTodasAsTaxas();
                                       
             foreach ($taxas_tarifario as $taxa) 
             {          	
             	 $model->salvarTaxa($taxa, $id_do_item_salvo);
             }
             
             /** 
              * Verifica se existem desbloqueios pendentes para este item,
              * se houver, então salva e envia os desbloqueios.
              */
             if( isset($_SESSION['Desbloqueios'][$id_do_item_salvo]) )
             {             	
             	foreach($_SESSION['Desbloqueios'][$id_do_item_salvo] as $entity)
             	{             		
             		$solicitacao->solicitar_desbloqueio(unserialize($entity));
             	}
                 
                $solicitacao->enviar_grupo_solicitacoes();            	
             }	
                          
         }

        if( count($itens_solicitacao) > 0 )
        {
           $solicitacao_facade->solicitaDesbloqueioPeriodoGrupo($itens_solicitacao);                 
        }
         
        unset($_SESSION['Desbloqueios']);
         
     }//END FUNCTION    
     
    /**
     * buscarItensPorIdDaProposta
     * 
     * Busca todos os itens de uma proposta pelo id da proposta
     * 
     * @name buscarItensPorIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return array
     */
     public function buscarItensPorIdDaProposta( Proposta $proposta, $initial = 0, $limit = 10 ) 
     {
              	      	 
     	 include_once APPPATH."models/Tarifario/Factory/concrete_factory.php";
     	 include_once APPPATH."models/Tarifario/Factory/concrete_importacao_factory.php";
     	 include_once APPPATH."models/Tarifario/Factory/concrete_exportacao_factory.php";
     	 include_once APPPATH."models/Tarifario/Factory/factory.php";
     	 include_once APPPATH."models/Propostas/item_proposta.php";
     	 include_once APPPATH."models/Propostas/status_item.php";
     	 include_once APPPATH."models/Taxas/item_proposta_taxa_model.php";
     	 
         /** Verifica se o id da proposta já foi definido **/
         $id_proposta = $proposta->getId();
         
         if( empty($id_proposta) )
         {
             throw new InvalidArgumentException("O id da proposta ainda não foi definido, não é possivel realizar a busca pelos itens da proposta!");
         }    
        
         $this->db->
                 select("id_item_proposta, id_tarifario_pricing, numero_proposta, 
                 		 mercadoria, pp, cc, imo, peso, cubagem, volumes, observacao_cliente,
                 		 observacao_interna, id_status_item, data_inicial, validade")->
                 from("CLIENTES.itens_proposta")->
                 where("itens_proposta.id_proposta",$proposta->getId())->
                 order_by("numero_proposta","asc");

         if( $limit > 0 )
         {
             $this->db->limit($limit, $initial);
         }         
                 
         $rs = $this->db->get();
         
         $itens_encontrados = Array();
         
         $linhas = $rs->num_rows();
         
         if( $linhas < 1 )
         {
             return $itens_encontrados;
         }    
         
         /** Cria os objetos de tarifario necessários baseado no tipo de proposta **/
         $factory = new Concrete_Factory();         
         
         $concrete_factory = Factory::factory($proposta->getSentido());
         
         $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
         
         $item_proposta_taxa_model = new Item_Proposta_Taxa_Model();
         
         $result_itens_encontrados = $rs->result();
                  
         foreach ( $result_itens_encontrados as $item_encontrado ) 
         {
             
             $tarifario = $factory->CreateTarifarioObject($concrete_factory);             
             
             $tarifario->setId((int) $item_encontrado->id_tarifario_pricing);
             
             $data_inicial = new DateTime($item_encontrado->data_inicial);
             $validade = new DateTime($item_encontrado->validade);
                          
             $tarifario = $tarifario_model->findById($tarifario,"A",$data_inicial, $validade );                                       
             
             if( ! $tarifario instanceof Tarifario )
             {
                continue;
             }   

             $item = new Item_Proposta($tarifario);
                                      
             $item->setId((int) $item_encontrado->id_item_proposta);
             $item->setNumero($item_encontrado->numero_proposta);
             $item->setMercadoria($item_encontrado->mercadoria);             
             $item->setPp($item_encontrado->pp);             
             $item->setCc($item_encontrado->cc);
             $item->setImo($item_encontrado->imo);
             $item->setPeso($item_encontrado->peso);
             $item->setCubagem($item_encontrado->cubagem);
             $item->setVolumes($item_encontrado->volumes);
             $item->setInicio($data_inicial);
             $item->setValidade($validade);
             $item->setObservacaoCliente($item_encontrado->observacao_cliente);
             $item->setObservacaoInterna($item_encontrado->observacao_interna);
                         
             /** Define o status do item **/
             $status = new Status_Item();
             $status->findById((int)$item_encontrado->id_status_item);             
             $item->setStatus($status);      
             
             $item_proposta_taxa_model->buscaTaxasDoItemDaProposta($item);
                                       
             $proposta->adicionarNovoItem($item);
                                            
         }
        
     }//END FUNCTION
     
    /**
     * verificaUltimoNumeroDeItemGerado
     * 
     * Verifica qual foi o último número de item gerado para um número de proposta
     * 
     * @name verificaUltimoNumeroDeItemGerado
     * @access protected
     * @param Proposta $proposta
     * @return string $numero
     */
     protected function verificaUltimoNumeroDeItemGerado(Proposta $proposta) 
     {
         
         $numero_proposta = $proposta->getNumero();
         
         if( empty($numero_proposta) )
         {
             throw new InvalidArgumentException("Id da proposta não definido, impossivel gerar a numeração do item");
         }    
         
         $this->db->select("itens_proposta.numero_proposta")->
                    from("CLIENTES.itens_proposta")->
                    like("numero_proposta",  substr($proposta->getNumero(), 0, -4));
         
         $rs = $this->db->get();
                           
         $maior_sequencial_encontrado = '0000';
         
         $result = $rs->result();
         
         foreach( $result as $numero_encotrado )
         {             
             $sequencial_encontrado = substr($numero_encotrado->numero_proposta, 13);
             
             if( intval($sequencial_encontrado) > intval($maior_sequencial_encontrado) )
             {           
                 $maior_sequencial_encontrado = $sequencial_encontrado;
             }    
         }    
         
         return $maior_sequencial_encontrado;
         
     }//END FUNCTION
         
    /**
     * geraNovoNumeroDeItemDeProposta
     * 
     * Gera um novo número para umitem de proposta baseado na numeração da proposta
     * 
     * @name geraNovoNumeroDeItemDeProposta
     * @access protected
     * @param Proposta $proposta
     * @return string $numero
     */     
    protected function geraNovoNumeroDeItemDeProposta(Proposta $proposta) 
    {
        
        $numero_proposta = $proposta->getNumero();
         
        if( empty($numero_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta não definido, impossivel gerar a numeração do item");
        }
        
        $ultimo_sequencial_gerado = $this->verificaUltimoNumeroDeItemGerado($proposta);
        
        $sequencial_do_novo_numero = intval($ultimo_sequencial_gerado) + 1;
        
        $sequencial_do_novo_numero = sprintf("%04d", $sequencial_do_novo_numero);
        
        $numero_base = substr($proposta->getNumero(), 0, -4);
        
        $novo_numero_gerado = $numero_base . $sequencial_do_novo_numero;
        
        return $novo_numero_gerado;
        
    }//END FUNCTION
    
    /**
     * incluirItemDaPropostaNaSessao
     *  
     * inclui um item da proposta na sessão do PHP
     * 
     * @name incluirItemDaPropostaNaSessao
     * @access public
     * @param Item_Propostas
     * @return boolean
     */
    public function incluirItemDaPropostaNaSessao(Item_Proposta $item) 
    {
        
        $this->load->model("Adaptadores/sessao");
        $this->load->model("Taxas/serializa_taxas");
        
        /** Serializa às taxas da proposta para inclusão na sessão **/
        $serializador_de_taxas = new Serializa_Taxas();
        
        $taxas_serializadas = $serializador_de_taxas->serializaTaxasProposta($item);
        
        $sessao = new Sessao();
        
        $sessao
        ->setIdItem($item->getId())        
        ->setCc($item->getCc())
        ->setPp($item->getPp())
        ->setImo($item->getImo())
        ->setPeso($item->getPeso())
        ->setCubagem($item->getCubagem())
        ->setVolumes((int)$item->getVolumes())
        ->setOrigem($item->getTarifario()->getRota()->getPortoOrigem()->getNome())
        ->setEmbarque($item->getTarifario()->getRota()->getPortoEmbarque()->getNome())
        ->setDesembarque($item->getTarifario()->getRota()->getPortoDesembarque()->getNome())
        ->setDestino($item->getTarifario()->getRota()->getPortoFinal()->getNome())
        ->setUnOrigem($item->getTarifario()->getRota()->getPortoOrigem()->getUnCode())
        ->setUnEmbarque($item->getTarifario()->getRota()->getPortoEmbarque()->getUnCode())
        ->setUnDesembarque($item->getTarifario()->getRota()->getPortoDesembarque()->getUnCode())
        ->setUnDestino($item->getTarifario()->getRota()->getPortoFinal()->getUnCode())        
        ->setIdTarifario((int)$item->getTarifario()->getId())
        ->setMercadoria($item->getMercadoria())
        ->setObservacaoCliente($item->getObservacaoCliente())
        ->setObservacaoInterna($item->getObservacaoInterna())
        ->setLabelsFretesAdicionais($taxas_serializadas['label_taxas_adicionais'])
        ->setLabelsTaxasLocais($taxas_serializadas['label_taxas_locais'])
        ->setFreteAdicionais($taxas_serializadas['value_taxas_adicionais'])
        ->setTaxasLocais($taxas_serializadas['value_taxas_locais'])
        ->setInicio($item->getInicio()->format('d-m-Y'))
        ->setValidade($item->getValidade()->format("d-m-Y"))
        ->setAntiCache(time());        
        
        return $sessao->inserirItemNaSessao();
        
    }//END FUNCTION
    
    /**
     * excluirItemDaPropostaPeloIdDoItem
     * 
     * Exlui um item de uma proposta pelo id do item
     * 
     * @name excluirItemDaPropostaPeloIdDoItem 
     * @access public
     * @param Item_Proposta $item
     * @return boolean
     */
    public function excluirItemDaPropostaPeloIdDoItem(Item_Proposta $item) 
    {
    	$this->load->model("Taxas/taxa_model");
    	
        /** Exclui às taxas que estão relacionadas a o item **/
        $taxa_model = new Taxa_Model();
        
        $taxa_model->exluirTaxasPorItemDeProposta($item);
        
        return $this->db->delete("CLIENTES.itens_proposta",Array("id_item_proposta" => $item->getId()));
                
    }//END FUNCTION
    
    /**
     * excluirItensDaPropostaPeloIdDaProposta
     * 
     * Exlui todas os item de uma proposta pelo id da proposta
     * 
     * @name excluirItemDaPropostaPeloIdDoItem 
     * @access public
     * @param Item_Proposta $item
     * @return boolean
     */
    public function excluirItensDaPropostaPeloIdDaProposta(Proposta $proposta) 
    {
        
        return $this->db->delete("CLIENTES.itens_proposta", Array("id_proposta" => $proposta->getId()));        
        
    }//END FUNCTION
    
    /**
     * buscarItemPorIdDoItem
     *
     * Encontra um item de proposta pelo id do iem da proposta
     *
     * @name buscarItemPorIdDoItem
     * @access public
     * @param Item_Proposta $item
     * @return void
     */ 	
    public function buscarItemPorIdDoItem(Item_Proposta $item, $sentido) 
    {
    	
    	$this->load->model("Tarifario/Factory/concrete_factory");
    	$this->load->model("Tarifario/Factory/concrete_importacao_factory");
    	$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
    	$this->load->model("Tarifario/Factory/factory");
    	$this->load->model("Propostas/item_proposta");
    	$this->load->model("Propostas/status_item");
    	$this->load->model("Taxas/item_proposta_taxa_model");
    	
    	$id_item = $item->getId();
    	
    	if( is_null($id_item) || $id_item == "0" || $id_item == "" )
    	{
    		throw new InvalidArgumentException("Nenhum id foi atribuido ao item da proposta para realizar a busca!");
    	}	
    	
    	/** Encontra o na base de dados baseado no id do item **/    	    	
    	$this->db->
    			select("id_item_proposta, id_tarifario_pricing, numero_proposta,
                		 mercadoria, pp, cc, imo, peso, cubagem, volumes, observacao_cliente,
                 		 observacao_interna, id_status_item, data_inicial, validade")->
    	        from("CLIENTES.itens_proposta")->
    	        where("itens_proposta.id_item_proposta",$item->getId());
    	 
    	$rs = $this->db->get();

    	$linhas = $rs->num_rows();
    	
    	if( $linhas < 1 )
    	{
    		throw new OutOfBoundsException("Nenhum Item Encontrado! " . $item->getId());
    	}	
    	
    	$item_encontrado = $rs->row();
    	    	    	
    	/** Cria os objetos de tarifario necessários baseado no tipode proposta **/
    	$factory = new Concrete_Factory();
    	 
    	$concrete_factory = Factory::factory($sentido);
    	
    	$tarifario = $factory->CreateTarifarioObject($concrete_factory);
    	$tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
    	 
    	$tarifario->setId((int) $item_encontrado->id_tarifario_pricing);
    	
    	$tarifario = $tarifario_model->findById($tarifario,"A",new DateTime($item_encontrado->data_inicial), new DateTime($item_encontrado->validade) );
    	/**
        if( $_SESSION['matriz'][4] == "CPD" )
        {
            pr($tarifario);
        }    
        **/

        if( ! $tarifario instanceof Tarifario )
        {
            throw new RunTimeException("Não foi possivel encontrar o tarifário para este item", 1);            
        }    

    	$item = new Item_Proposta($tarifario);
    	 
    	$data_inicial = new DateTime($item_encontrado->data_inicial);
    	$validade = new DateTime($item_encontrado->validade);
    	 
    	$item->setId((int) $item_encontrado->id_item_proposta);
    	$item->setNumero($item_encontrado->numero_proposta);
    	$item->setMercadoria($item_encontrado->mercadoria);
    	$item->setPp($item_encontrado->pp);
    	$item->setCc($item_encontrado->cc);
        $item->setImo($item_encontrado->imo);
    	$item->setPeso($item_encontrado->peso);
    	$item->setCubagem($item_encontrado->cubagem);
    	$item->setVolumes($item_encontrado->volumes);
    	$item->setInicio($data_inicial);
    	$item->setValidade($validade);
    	$item->setObservacaoCliente($item_encontrado->observacao_cliente);
    	$item->setObservacaoInterna($item_encontrado->observacao_interna);
    	
    	/** Define o status do item **/
    	$status = new Status_Item();
    	$status->findById((int)$item_encontrado->id_status_item);    
    	$item->setStatus($status);
    	    	    	 
    	$item_proposta_taxa_model = new Item_Proposta_Taxa_Model();
    	
    	$item_proposta_taxa_model->buscaTaxasDoItemDaProposta($item);    

    	return $item;
    }
       
}//END CLASS

