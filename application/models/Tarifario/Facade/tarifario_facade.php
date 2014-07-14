<?php
/**
 * Façade para o subsistema de tarifario
 *
 * Esta classe fornece uma interface simplificada para todas às operações
 * das classes de tarifário, criando um subsistema tarifário, que é de 
 * utilização simplificada graças ao façade, assim os controllers podem 
 * ser mais enxutos e ter menos código, tornando-se mais genéricos e reaproveitáveis
 * 
 * @package Tarifario/Facade
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 04/04/2013
 * @name Tarifario_Facade
 * @version 1.0
 */
class Tarifario_Facade extends CI_Model {
  
    public function __construct() {
        parent::__construct();        
    }
    
    /**
     * Listar Tarifários
     * 
     * Lista todas às rotas possiveis para uma determinada rota,
     * em um determinado sentido, importação ou exportação
     * 
     * @name ListarTarifarios
     * @access public
     * @param int
     * @return Array $tarifarios
     */    
    public function ListarTarifarios( ArrayObject $dados_enviados )
    {
    	$this->load->model("Clientes/cliente");
    	$this->load->model("Clientes/cliente_model");    	
    	$this->load->model("Tarifario/porto");
    	$this->load->model("Tarifario/Factory/factory");
    	$this->load->model("Tarifario/Factory/concrete_factory");
    	$this->load->model("Tarifario/Factory/concrete_exportacao_factory");
    	$this->load->model("Tarifario/Factory/concrete_importacao_factory");    	
    	$this->load->model("Tarifario/rota");
    	$this->load->model("Propostas/Buscas/busca_proposta_existente");
    	$this->load->model("Taxas/taxa_model");
    	$this->load->model("Propostas/item_proposta");
        $this->load->model("Taxas/remove_taxa_imo");
    	    	
        /** 
		 * Verifica se todos os clientes são do mesmo tipo direto ou forwarder 
		 * se forem de tipos diferentes então não pode prosseguir 
		 **/			
		$cliente_model = new Cliente_Model();
		
		if( ! $cliente_model->verificarModalidadeDosClientes($dados_enviados->offsetGet('clientes')) )
		{
			show_error("Existem Clientes com diferentes classificações (Direto e Forwarder) na mesma proposta e isso não é permitido!");
		}
		
        /** Cria os objetos dos portos **/
		$porto_origem = new Porto();
		$porto_desembarque = new Porto();
		$porto_embarque = new Porto();
		$porto_destino = new Porto();
        	
		$concrete_factory = Factory::factory($dados_enviados->offsetGet("sentido"));
		
		$factory = new Concrete_Factory();
						
		/** Model dos portos **/
		$porto_model = $factory->CreatePortoModel($concrete_factory);
		
        try{
			
			$porto_origem->setUnCode($dados_enviados->offsetGet("origem"));
			$porto_embarque->setUnCode($dados_enviados->offsetGet("embarque"));
			$porto_desembarque->setUnCode($dados_enviados->offsetGet("desembarque"));
			$porto_destino->setUnCode($dados_enviados->offsetGet("destino"));
			
			if( $porto_origem->getUnCode() != 'NULL' && ! is_null($porto_origem->getUnCode()) )
			{
				$porto_model->findByUnCode($porto_origem,'origem');
			}
			
			if( $porto_embarque->getUnCode() != 'NULL' && ! is_null($porto_embarque->getUnCode()) )
			{	
				$porto_model->findByUnCode($porto_embarque,'embarque');
			}
			
			if( $porto_desembarque->getUnCode() != 'NULL' && ! is_null($porto_desembarque->getUnCode()) )
			{	
				$porto_model->findByUnCode($porto_desembarque,'desembarque');
			}
			
			if( $porto_destino->getUnCode() != 'NULL' && ! is_null($porto_destino->getUnCode()) )
			{	
				$porto_model->findByUnCode($porto_destino,'destino');	 
			}
				
			/** Cria a rota **/		
			$rota = new Rota();
			$rota->setPortoOrigem($porto_origem);
			$rota->setPortoEmbarque($porto_embarque);
			$rota->setPortoDesembarque($porto_desembarque);
			$rota->setPortoFinal($porto_destino);
			 
			$tarifario_model = $factory->CreateTarifarioModel($concrete_factory);
            
			/** Transforma às datas informadas pelo usuário em objetos do tipo DateTime **/
			$data_inicial = new DateTime($dados_enviados->offsetGet('inicio'));
			$data_final = new DateTime($dados_enviados->offsetGet('validade'));
							
            $tarifarios = $tarifario_model->obterTarifarios($rota, $data_inicial, $data_final);
            
            /**
             * Verifica se existe alguma proposta já cadastrada para algum dos clientes
             * na mesma rota que está sendo solicitada
             */
			$finder = new Busca_Proposta_Existente();
            
            /** Cria objetos com os clientes que vão ser verificados **/
            $clientes_para_verificacao = new ArrayObject(explode("|", $dados_enviados->offsetGet("clientes")));
            
            $iterator = $clientes_para_verificacao->getIterator();
            
            (bool) $proposta_ja_cadastrada = FALSE;
            
            $taxa_model = new Taxa_Model();
            
            $mensagem_tarifarios_duplicados_encontrados = "";
            $itens_duplicados_encontrados = FALSE;
            
            while( $iterator->valid() )
            {
                
                /** Cria um novo objeto do tipo cliente para verificação **/
                $cliente = new Cliente();
                $cliente->setId((int)$iterator->current());
                
                /** Testa todos os clientes com todos os tarifários encontrados **/
                foreach ($tarifarios as $chave => $tarifario) 
                {             
                    /** Adiciona a propriedade id_item_proposta dinamicamente a todos os tarifários encontrados **/
                    $tarifario->id_item_proposta = NULL;
                                                            
                    /** Verifica se o tipo imo setado para true, se sim então excluí às rotas imo **/                    
                    if( $dados_enviados->offsetGet('imo') == "S" && $tarifario->getAceitaImo() == "N" )
                    {
                    	//unset($tarifarios[$chave]);
                    	//continue;
                    	$tarifario->mensagem_imo = "Esta Rota não aceita cargas IMO!";
                    }	
                    
                    
                    /**
                     * Verifica se a modalidade CC foi informada e se a rota aceita esta modalidade,
                     * caso não aceite, então não lista a rota
                     */                    
                    $modalidades = explode("|", $dados_enviados->offsetGet('modalidade'));

                    if( in_array("CC", $modalidades) && $tarifario->getAceitaFreteCc() == "N" )
                    {
                    	//unset($tarifarios[$chave]);
                    	//continue;
                    	$tarifario->mensagem_collect = "Esta rota não aceita cargas CC";
                    }
					
                    /** Verifica se a proposta é do tipo proposta_tarifario ou proposta_nac e retira às rotas não pricipais **/
                    if( $dados_enviados->offsetGet('tipo_proposta') == "proposta_tarifario" || $dados_enviados->offsetGet('tipo_proposta') == "proposta_nac" )
                    {                    	
                    	if( $tarifario->getRotaPrincipal() == "N" )
                    	{                    		
                    		unset($tarifarios[$chave]);
                    		continue;
                    	}                    		
                    }
                    
                    $proposta_ja_cadastrada = $finder->verificarSeClienteJaPossuiPropostaValida($cliente, $tarifario, $data_inicial, $data_final);
                                                            
                    /** Se já existir uma proposta cadastrada para um dos clientes então emite uma mensagem **/
                    if( $proposta_ja_cadastrada !== FALSE  && $dados_enviados->offsetGet('tipo_proposta') != 'proposta_nac' &&  $dados_enviados->offsetGet('tipo_proposta') != 'proposta_spot' )
                    {
                    	$itens_duplicados_encontrados = TRUE;
                    	
                    	$cliente_model->findById($cliente);
                    	
                    	$mensagem_tarifarios_duplicados_encontrados .= "{$cliente->getCNPJ()} -> {$cliente->getRazao()} - Proposta: {$proposta_ja_cadastrada}"."\\n";                        
                    }                                                          
                }
                                              
                $iterator->next();
            }
            
            /** 
             * Se algum dos tarifários foi encontrado já cadastrado em outra proposta do mesmo cliente
             * então emite a mensagem de alerta
             */
            if( $itens_duplicados_encontrados == TRUE )
            {
            	echo "<script language='javascript'>
		            	if( ! confirm('Já existe uma proposta(s) para o(s) cliente(s)\\n {$mensagem_tarifarios_duplicados_encontrados} para esta rota, se você salvar esta proposta, a proposta anterior desse cliente nessa rota, será cancelada! Deseja Prosseguir?') )
		            	{
		            		window.parent.document.getElementById('pop').style.display = 'none';
		            	}
		               </script>";              	         	
            }	
            
            /** Sobrescreve os valores do tarifario encontrado com os valores da proposta encontrada caso houver **/
            /**
            $iterator->rewind();

            while( $iterator->valid() )
            {                
                
                //Cria um novo objeto do tipo cliente para verificação 
                $cliente = new Cliente();
                $cliente->setId((int)$iterator->current());
                
                foreach ($tarifarios as $tarifario) 
                {
                                              
                    $id_item_proposta = $finder->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente , $tarifario, $data_inicial, $data_final);
                    
                    if( $id_item_proposta != FALSE )
                    {
                        $tarifario_diferenciado = clone $tarifario;
                        
                        $tarifario_diferenciado->id_item_proposta = $id_item_proposta;
                        
                        $item = new Item_Proposta();
                        
                        $item->setId((int)$id_item_proposta);
                        
                        //Obtem às taxas já cadastradas que vão se sobrepor às taxas trazidas pelo tarifáro 
                        $taxas_cadastradas = $taxa_model->retornaTaxasDaProposta($item);
                        
                        $tarifario_diferenciado->limparTaxasTarifario();
                        
                        //Insere às novas taxas no objeto tarifário            
                        $taxas_cadastradas_iterador = $taxas_cadastradas->getIterator();
                        
                        while( $taxas_cadastradas_iterador->valid() )
                        {
                            $tarifario_diferenciado->adicionarNovaTaxa($taxas_cadastradas_iterador->current());
                            
                            $taxas_cadastradas_iterador->next();
                        }    
                        
                        $tarifarios[] = $tarifario_diferenciado;
                        
                    }    
                    
                }
                
                $iterator->next();                
            } 
            **/
			return $tarifarios;
										
		} catch (Exception $e) {
			echo show_error($e->getMessage());die();
		}
        
    }        
    
    /**
     * Encontrar um tarifário pelo Id
     *  
     * Encontra um tarifário especifico pelo id do tarifário
     * 
     * @name BuscarTarifarioPeloId
     * @access public
     * @param int
     * @return boolean
     */
    public function BuscarTarifarioPeloId( $id_tarifario, $sentido, $clientes, $id_item_proposta = "0", DateTime $data_inicial, DateTime $validade, $imo = NULL, $pp = NULL, $cc = NULL )
    {          
    	
    	include_once APPPATH."models/Clientes/cliente_model.php";
    	include_once APPPATH."models/Clientes/cliente.php";
    	include_once APPPATH."models/Clientes/define_classificacao.php";
    	include_once APPPATH."models/Tarifario/Factory/concrete_factory.php";
    	include_once APPPATH."models/Tarifario/Factory/concrete_factory.php";
    	include_once APPPATH."models/Tarifario/Factory/concrete_importacao_factory.php";
    	include_once APPPATH."models/Tarifario/Factory/concrete_exportacao_factory.php";
    	include_once APPPATH."models/Tarifario/Factory/factory.php";
    	include_once APPPATH."models/Propostas/item_proposta.php";
    	include_once APPPATH."models/Taxas/taxa_model.php";
        include_once APPPATH."models/Taxas/remove_taxa_imo.php";
        include_once APPPATH."models/Taxas/remove_taxas_exportacao.php";
                
        /**
		 * Verifica se todos os clientes são do mesmo tipo direto ou forwarder
		 * se forem de tipos diferentes então não pode prosseguir
		 **/
		$cliente_model = new Cliente_Model();
		
		if( ! $cliente_model->verificarModalidadeDosClientes($clientes) )
		{
			show_error("Existem Clientes com diferentes classificações (Direto e Forwarder) na mesma proposta e isso não é permitido!");
		}
          
        /** obtem a classificação do cliente para passar ao tarifário **/
        $clientes_selecionados = new ArrayObject(explode("|", $clientes));
        
        $cliente = new Cliente();
        
        $cliente->setId( (int)$clientes_selecionados->offsetGet(0) ); 
                     
        $cliente_model->findById($cliente);
        
        $definidor_de_classificacao = new Define_Classificacao();
        
        $classificacao_cliente = $definidor_de_classificacao->ObterClassificacao($cliente);

        /** Cria fabrica de objetos que será utilizada para buscar o tarifário **/
        if( $sentido == "Tarifario_Exportacao" )
        {
            $concrete_factory = new Concrete_Exportacao_Factory();
        }
        else
        {
            $concrete_factory = new Concrete_Importacao_Factory();
        }	 	

        $factory = new Concrete_Factory();

        $tarifario_model = $factory->CreateTarifarioModel($concrete_factory);

        $tarifario = $factory->CreateTarifarioObject($concrete_factory);

        $tarifario->setId((int)$id_tarifario);

        $tarifario_model->findById( $tarifario , $classificacao_cliente, $data_inicial, $validade );
       
        /** Verifica se existem acordos de taxas locais para o(s) cliente(s) **/        
        include_once APPPATH."models/Taxas_Locais_Acordadas/busca_acordo_taxas_locais_cliente.php";
        
        $buscador_acordo_taxas_locais = new Busca_Acordo_Taxas_Locais_Cliente();
        	
        if( $sentido == "Tarifario_Exportacao" )
        {
        	$porto_acordo_taxas = $tarifario->getRota()->getPortoOrigem();
        	$sentido = "EXP";
        }
        else
       {       		
       		$porto_acordo_taxas = $tarifario->getRota()->getPortoFinal();
       		$sentido = "IMP";
        } 	
        
        //$acordo_taxas = $buscador_acordo_taxas_locais->buscarAcordoTaxasCliente($sentido, $cliente, $porto_acordo_taxas, $data_inicial, $validade);
        $acordo_taxas = $buscador_acordo_taxas_locais->buscarAcordoTaxasCliente($sentido, $cliente, $porto_acordo_taxas, new DateTime(),new DateTime());
                
        if( $acordo_taxas instanceof Acordo_Taxas_Entity )
        {     	
        	/** Compara às taxas que são o padrão do porto com às taxas do acordo **/        	
        	include_once APPPATH . "models/Taxas/compara_taxas.php";
        	
        	$comparador = new Compara_Taxas($tarifario->getTaxa(), $acordo_taxas->getTaxas());
        	
        	$taxas_locais_acordadas = $comparador->comparar_taxas();
        	
        	unset($comparador);
        	
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
        
        /** 
         * Verifica se foi informado algum id de item de proposta,
         * em caso positivo busca às taxas desse item e sobreescreve às taxas padrões do tarifário.
         */        
        if( ! is_null($id_item_proposta) && $id_proposta != 0 )
        {
            /** 
             * Se for uma rota com uma proposta então, retira a mensagem do aacordo de incentivos (Adicionais sobre o frete)            
             */
            $tarifario->adicional_negociado = NULL;
            
            $item = new Item_Proposta();
                        
            $item->setId((int)$id_item_proposta);
                
            $taxa_model = new Taxa_Model();
            
            /** Obtem às taxas já cadastradas que vão se sobrepor às taxas trazidas pelo tarifáro **/
            $taxas_cadastradas = $taxa_model->retornaTaxasDaProposta($item);
            
            $tarifario->limparTaxasTarifario();

            /** Insere às novas taxas no objeto tarifário **/                        
            $taxas_cadastradas_iterador = $taxas_cadastradas->getIterator();

            while( $taxas_cadastradas_iterador->valid() )
            {
                $tarifario->adicionarNovaTaxa($taxas_cadastradas_iterador->current());

                $taxas_cadastradas_iterador->next();
            }
        }    
        
        /** 
         * Se a carga for não for imo então remove a taxa de imo.         
         */
                        
		$tarifario->solicitacao_imo = $imo;		
        $remove_imo = new Remove_Taxa_Imo(); 
        $remove_imo->removerTaxa($tarifario);   
		
        /**
         * Como a proposta pode ter ambas às modalidades pp ou cc, então
         * tenho de fazer está validação antes de passar os valores ao objeto
         */
        if( $cc == 'CC' )
        {
        	$tarifario->solicitacao_ppcc = $cc;
        }
        else 
        {
        	$tarifario->solicitacao_ppcc = $pp;
        }		
                
        $remove_taxas_exp = new Remove_Taxas_Exportacao();
        $remove_taxas_exp->removerTaxa($tarifario);        
        
        return $tarifario;
        
    }        
       
}//END CLASS

