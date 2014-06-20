<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package Clientes
* @author Wellington Feitosa <wellington.feitosa@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 05/02/2014
* @version  1.0
* Controller do acordo de adicionais sobre o frete
*/
include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Adicionais extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("html","form","url"));
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->output->enable_profiler(false);
	}
	
	public function novo()
	{
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'NOVO ACORDO DE ADICIONAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/novo.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0, "title" => "Abre a tela para cadastrar um novo acordo. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0, "title" => "Salva o acordo sendo cadastrado.")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0, "title" => "Abre a tela de busca de acordos. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0, "title" => "Volta para o menu principal")).'</a>';
		
		$footer['footer'] = $imagens;
		                        
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/novo");
		$this->load->view("Padrao/footer_view_new",$footer);
	    
	}
	
	public function alterar($id_acordo = null)
	{
		
		if( empty($id_acordo) )
		{
			show("Nenhum acordo foi informado para realizar a alteração!");
		}
		
		$this->load->model("Adicionais/adicionais_facade");
		$this->load->model("Adicionais/serializa_taxa");
		
		$facade = new Adicionais_Facade();
		$serializador = new Serializa_Taxa();
		
		try {
			$acordo = $facade->consultarAcordo($id_acordo);
		
			$data['acordo'] = $acordo;
			
			$valueComboTaxas = Array();
			
			//Formata os dados para o combo de taxas
			foreach( $acordo->getTaxas() as $taxa )
			{
				$valueComboTaxas[$serializador->ConverterTaxaParaComboValue($taxa)] = $serializador->ConverterTaxaParaString($taxa);
			}				
			
			$data['combo_taxas'] = $valueComboTaxas;
			
			//Formata os dados do combo de clientes
			$valueComboClientes = Array();
			
			foreach( $acordo->getClientes() as $cliente )
			{
				$valueComboClientes[$cliente->getId()] = $cliente->getCNPJ() . " - " . $cliente->getRazao() . " -> ". $cliente->getCidade()->getNome();
			}	
			
			$data['combo_clientes'] = $valueComboClientes;
			
		} catch (RuntimeException $e) {
			echo "<script>
					alert('".$e->getMessage()."');
				  </script>";
		} catch(Exception $e) {
			show_error($e->getMessage());
		}
		
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'ALTERAÇÃO DO ACORDO DE ADICIONAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/alterar.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0, "title" => "Abre a tela para cadastrar um novo acordo. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0, "title" => "Salva o acordo sendo alterado.")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0, "title" => "Abre a tela de busca de acordos. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0, "title" => "Volta para o menu principal")).'</a>';
		
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/alterar",$data);
		$this->load->view("Padrao/footer_view_new",$footer);
	}
	
	public function adicionar_taxa( $value_combo = NULL, $index_combo = NULL )
	{
		
		$nome_taxa = "";
		$id_taxa = 0;
		$unidade_selecionada = "";
		$moeda_selecionada = "";
		$valor = "0.00";
		$valor_minimo = "0.00";
		$valor_maximo = "0.00";
		$ppcc = "";
		
		
		if( ! is_null($value_combo) )
		{
			//Selciona os dados da taxa
			$this->load->model("Taxas/taxa_adicional");
			$this->load->model("Taxas/taxa_model");
			
			$dadosDaTaxaSelecionada = explode(";", $value_combo);
			
			$taxa = new Taxa_Adicional();
			$taxa_model = new Taxa_Model();
			
			$taxa->setId((int)$dadosDaTaxaSelecionada[0]);
			$taxa_model->obterNomeTaxaAdicional($taxa);
			$id_taxa = (int)$dadosDaTaxaSelecionada[0];
			$nome_taxa = $taxa->getNome();
			
			$unidade_selecionada = $dadosDaTaxaSelecionada[1];
			$moeda_selecionada = $dadosDaTaxaSelecionada[2];
			$ppcc = $dadosDaTaxaSelecionada[3];
			$valor = number_format($dadosDaTaxaSelecionada[4],2,".","");
			$valor_minimo = number_format($dadosDaTaxaSelecionada[5],2,".","");
			$valor_maximo = number_format($dadosDaTaxaSelecionada[6],2,".","");			
		}	
        
        /** Busca �s taxas disponiveis para adi��o na tela **/
		$this->load->model("Taxas/taxa_model");
		
		$taxa_model = new Taxa_Model();
		
		$taxas_completas = $taxa_model->retornaTodasAsTaxas();
		
        /**
         * Filtra �s taxas permitidas de Exporta��o
         */
        include_once $_SERVER['DOCUMENT_ROOT'] . "/permissoes_taxas.php";
        
        $taxas_autorizadas = array();
        
        foreach( $controle_taxas['exportacao_adicionais'] as $taxa_permitida )
        {
            if(array_key_exists($taxa_permitida, $taxas_completas) )
            {
                $taxas_autorizadas[$taxa_permitida] = $taxas_completas[$taxa_permitida];
            }    
        }    
                        
        $data["taxas"] = $taxas_autorizadas;
        
		$data['id_taxa'] = $id_taxa;
		$data['nome_taxa'] = $nome_taxa;
		$data['unidade_selecionada'] = $unidade_selecionada;
		$data['moeda_selecionada'] = $moeda_selecionada;
		$data['ppcc'] = $ppcc;
		$data['valor'] = $valor;
		$data['valor_minimo'] = $valor_minimo;
		$data['valor_maximo'] = $valor_maximo;
		$data['index_combo'] = $index_combo;
		
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'ADICIONAR TAXA';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/adicionar_taxa.js',"jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
		
		$imagens = "";		
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
				
		$footer['footer'] = $imagens;
		
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade_model");
		
		$unidade_model = new Unidade_Model();
		
		$moeda_model = new Moeda_Model();
		
		$data['unidades'] = $unidade_model->retornaTodasAsUnidades();
		
		$data['moedas'] = $moeda_model->retornaTodasAsMoedas();
		
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/adicionar_taxa",$data);
		$this->load->view("Padrao/footer_view_new",$footer);
		
	}
	
	public function salvar()
	{
		$this->load->model("Adicionais/adicionais_facade");
					
		$this->form_validation->set_rules("inicio","Data Inicial","required");
		$this->form_validation->set_rules("validade","Validade","required");
		$this->form_validation->set_rules("clientes_selecionados","Clientes","required");
		$this->form_validation->set_rules("taxas_selecionadas","Taxas","required");
		$this->form_validation->set_rules("sentido","Sentido","required|min_length[3]");
		
		if ($this->form_validation->run() == FALSE)
		{
			show_error(validation_errors());exit(0);
		}	
		
		$facade = new Adicionais_Facade();
		
		try{		
			$acordo = $facade->salvarAcordo();
		} catch (RuntimeException $rte) {
			
			echo "<script language='javascript'>										
					if( confirm('{$rte->getMessage()}\\n Deseja visualizar o acordo em conflito com o acordo que você tentou cadastrar ?') )
					{
						window.location = '/Clientes/propostas/index.php/adicionais/adicionais/consultar/{$rte->getCode()}';
					}
					else
					{
						window.location = '/Clientes/propostas/index.php/adicionais/adicionais/novo/';
					}
				  </script>";
			
			exit(0);
								
		} catch (Exception $e) {		
			show_error($e->getMessage());exit(0);
		}
						
		echo "<script language='javascript'>
				alert('Acordo de adicionais salvo com sucesso!');				
				window.location = '/Clientes/propostas/index.php/adicionais/adicionais/consultar/{$acordo->getId()}';		
			  </script>";
		
		exit;								
	}
	
	public function consultar($id_acordo = null)
	{
		if( empty($id_acordo) )
		{
			show_error("Nenhum acordo foi informado para realizar a busca!");
		}	
		
		$this->load->model("Adicionais/adicionais_facade");
		
		$facade = new Adicionais_Facade();
		
		try {		
			$acordo = $facade->consultarAcordo($id_acordo);
						
			$data['acordo'] = $acordo;
			
		} catch (RuntimeException $e) {
			echo "<script>
					alert('".$e->getMessage()."');
				  </script>";
		} catch(Exception $e) {
			show_error($e->getMessage());
		}
		
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'NOVO ACORDO DE ADICIONAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/consultar.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0, "title" => "Abre a tela para cadastrar um novo acordo. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/alterar.gif', 'id' => 'alterar' , 'border' => 0, "title" => "Abre a tela para edição do acordo")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0, "title" => "Abre a tela de busca de acordos. Qualquer informação não salva será perdida!")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0, "title" => "Volta ao menu principal")).'</a>';
		
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/consultar",$data);
		$this->load->view("Padrao/footer_view_new",$footer);
		 
	}
	
	public function listar_resultados_busca()
	{		
		$this->load->model("Adicionais/busca_acordos_adicionais");
		
		$acordosEncontrados = $this->busca_acordos_adicionais->aplicarFiltrosDeBusca();
				
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'ACORDOS ENCONTRADOS';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/listar_resultados_busca.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0, "title" => "Abre a tela para cadastrar um novo acordo. Qualquer informação não salva será perdida!")).'</a>';			
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0, "title" => "Volta para a tela de filtros de busca.")).'</a>';
		
		$footer['footer'] = $imagens;
		
		$data['acordos_encontrados'] = $acordosEncontrados;
		
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/listar_resultados_busca",$data);
		$this->load->view("Padrao/footer_view_new",$footer);
	}
	
	public function filtrar_busca()
	{
		$header['form_title'] = 'Scoa - Adicionais Frete';
		$header['form_name'] = 'FILTRAR A BUSCA POR ACORDOS';
		$header['css'] = '';
		$header['js'] = load_js(array('adicionais/filtrar_busca.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0, "title" => "Abre a tela para cadastrar um novo acordo. Qualquer informação não salva será perdida!")).'</a>';		
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0, "title" => "Realiza a busca com os filtros informados.")).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0, "title" => "Volta para o menu principal.")).'</a>';
		
		$footer['footer'] = $imagens;
		
		$data['tipo_cliente_busca'] = Array(
											"0" => "Selecione",
											"1" => "Cliente",
											"2" => "Grupo Comercial",
											"3" => "Grupo Cnpj"				
		);
		
		$data['status'] = Array(
                                "0" => "Selecione",
								"1" => "Vencida",
								"2" => "Valida",
								"3" => "Pendente Aprova��o",
                                "4" => "Cancelada",
		);
		
		$data['filiais'] = Array(
								 "0" => "Selecione",
								 "SP" => "S�o Paulo",
								 "CT" => "Curitiba",
								 "IT" => "Itajai",
								 "PA" => "Porto Alegre",
								 "RJ" => "Rio de Janeiro",
		);
		
		$this->load->view("Padrao/header_view_new",$header);
		$this->load->view("Adicionais/filtrar_busca",$data);
		$this->load->view("Padrao/footer_view_new",$footer);
	}
	
	public function existe_acordo_cliente($id_cliente = null)
	{
		
		if( is_null($id_cliente) )
		{
			log_message('error','O id_do cliente recebido para testar se j� existe um acordo estava null');
			show_error("Impossivel pesquisar por acordo existente");
		}

		$this->load->model("Adicionais/clientes_acordo_adicionais_model");	
		$this->load->model("Clientes/cliente");	
		
		$cliente = new Cliente();
		$cliente->setId((int)$id_cliente);
		
		$cliente_acordo_model = new Clientes_Acordo_Adicionais_model();
		$acordosEncontrados = $cliente_acordo_model->buscarAcordosPorIdDoCliente($cliente);
		
		(bool) $existeAcordo;
		
		if( $acordosEncontrados->count() > 0 )
		{
			$existeAcordo = true; 
		}
		else 
		{
			$existeAcordo = false;
		}

		$data['existe_acordo'] = $existeAcordo;	
		
		$this->load->view("Adicionais/acordo_existente_cliente",$data);
		
	}
	
	public function listar_solicitacoes()
	{
		$this->load->model("Adicionais/adicionais_facade");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade_model");
		
		try{
			$solicitacoesPendentes = $this->adicionais_facade->listarSolicitacoesDeDesbloqueio();
		} catch(Exception $e) {
			show_error($e->getMessage());
		}
				
		$data['solicitacoes_pendentes'] = $solicitacoesPendentes;
		$data['js'] = load_js(array('adicionais/listar_solicitacoes.js','sidr/jquery.sidr.min.js'));
		$data['moedas'] = $this->moeda_model->retornaTodasAsMoedas();
		$data['unidades'] = $this->unidade_model->retornaTodasAsUnidades();
		
		$this->load->view("Adicionais/listar_solicitacoes",$data);		
	}
	
	public function autorizar_desbloqueio()
	{		
		$this->load->model("Adicionais/adicionais_facade");
				
		try {		
			$this->adicionais_facade->responderSolicitacao($this->input->post());		
			
			echo "<script language='javascript'>
					alert('Desbloqueio efetuado com sucesso!');
					window.location = '/Clientes/propostas/index.php/adicionais/adicionais/listar_solicitacoes/';
				  </script>";
		} catch (RuntimeException $e) {
			echo "<script>
					alert('".$e->getMessage()."');
					window.location = '/Clientes/propostas/index.php/adicionais/adicionais/listar_solicitacoes/';		
				  </script>";
		} catch(Exception $e) {
			show_error($e->getMessage());
		}
			
	}

	public function excluir_solicitacao($id_acordo)
	{		
		$this->load->model("Adicionais/acordo_adicionais");
        $this->load->model("Adicionais/adicionais_facade");
        
        $acordo = new Acordo_Adicionais();
        $acordo->setId((int)$id_acordo);
        
        $facade = new Adicionais_Facade();
        $facade->excluirSolicitacaoDeDesbloqueio($acordo);        
        
		echo "<script language='javascript'>
				alert('Solicita��o excluida com sucesso!');
				window.location = '/Clientes/propostas/index.php/adicionais/adicionais/listar_solicitacoes/';
			  </script>";
	}

	public function exibir_taxas_permitidas()
	{
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");

		$idsTaxasPermitidas = Array(
									array(1082,42,4,"PP"), array(1195,42,4,"PP"),
									array(14,42,4,"AF"), array(1194,42,4,"AF"),
									array(18,42,4,"PP"), array(1115,113,4,"PP"),
									array(13,42,5,"AF"), array(1146,42,4,"AF"),
									array(1154,42,4,"AF"), array(1120,42,3,"AF"),
									array(1121,42,4,"AF"), array(1139,42,4,"AF"),
									array(30,42,4,"AF"), array(1060,42,3,"AF"),
									array(1164,42,4,"PP"), array(1103,42,3,"AF"),
									array(1192,42,3,"AF"), array(1185,42,3,"AF"),
									array(3,42,3,"CC"), array(34,42,4,"AF"),
									array(1030,42,3,"AF"), array(27,42,3,"AF"),			
		);	

		$taxasExpo = array();

		foreach( $idsTaxasPermitidas as $dadosTaxa )
		{
			$taxa = new Taxa_Adicional();
			$taxa->setId((int)$dadosTaxa[0]);
			$taxa->setPPCC($dadosTaxa[3]);

			$moeda = new Moeda();
			$moeda->setId((int)$dadosTaxa[1]);
			$this->moeda_model->findById($moeda);

			$unidade = new Unidade();
			$unidade->setId((int)$dadosTaxa[2]);
			$this->unidade_model->findById($unidade);

			$taxa->setUnidade($unidade);
			$taxa->setMoeda($moeda);

			$this->taxa_model->obterNomeTaxaAdicional($taxa);
			
			array_push($taxasExpo, $taxa);
		}	

		$this->load->view("Adicionais/taxas_permitidas",array("taxas" => $taxasExpo));
	}
    
    public function revalidar($id_acordo = NULL, $meses = NULL)
    {
        if( empty($id_acordo) || is_null($meses) )
        {
            die("N�o foi possivel processar o envio da solicita��o");
        }    
        
        /** Carrega a classe que vai salvar o acordo **/
		$this->load->model("Adicionais/adicionais_facade");
        		        
		$facade = new Adicionais_Facade();
		
		$acordo = $facade->consultarAcordo($id_acordo);
        $acordo->alterar_retroativos = "N";      
        
        $facade->revalidarAcordo($acordo, $meses);
                		
		echo "<script language='javascript'>
				alert('Opera��o efetuada com sucesso!');
				window.close();		
			  </script>";
    }
	
}