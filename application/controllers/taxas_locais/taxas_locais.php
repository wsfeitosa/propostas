<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package  Controllers/taxas_locais
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 13/05/2013
 * @version  1.0
 * Controla o fluxo da aplicação para o cadastro de acordos de taxas locais
 */

//include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Taxas_Locais extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("html","form","url"));
		$this->load->library('form_validation');		
		$this->load->model("Taxas_Locais_Acordadas/buscador_taxas_locais");
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->output->enable_profiler(FALSE);
	}
	
	public function index()
	{
		
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'NOVO ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/cadastro_taxas_acordadas.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Busca os portos disponiveis para cadastramento das taxas **/
		$this->load->model("Taxas_Locais_Acordadas/portos_taxas");
		$portos_taxas = new Portos_Taxas();
		
		$data['portos'] = $portos_taxas->obterPortosDasTaxasLocais();
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/cadastro_taxas_acordadas",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function find( $porto, $clientes, $sentido )
	{
							
		/** Valida às informações **/
		if( $sentido != "IMP" && $sentido != "EXP" )
		{
			$msg = "Sentido inválido informado, para o cadastro de taxas locais, precisa ser imp ou exp";
			log_message('error',$msg);
			show_error($msg);
		}		
		
		if( empty($clientes) )
		{
			$msg = "Nenhum cliente informado para a consulta";
			log_message('error',$msg);
			show_error($msg);
		}	
		
		if( empty($porto) )
		{
			$msg = "Nenhum cliente informado para a consulta";
			log_message('error',$msg);
			show_error($msg);
		}	
		
		/** Faz o replace dos caracteres | por :, pois no php 5.2 existe um bug na função parse_url **/
		$clientes = str_replace("|", ":", urldecode($clientes));

		$finder = new Buscador_Taxas_Locais();
		
		$taxas_locais_encontradas = $finder->buscarTaxasLocais( $porto, $clientes, $sentido );
						
		$data["taxas_locais"] = $taxas_locais_encontradas;
		
		$this->load->view("Taxas/xml_taxas_encontradas", $data);
		
	}
	
	public function add( $js_file, $sentido = NULL, $tipo = NULL )
	{
		
		$header['form_title'] = 'Scoa - Adicionar Taxa';
		$header['form_name'] = 'ADICIONAR TAXA';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/'.$js_file,"jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Busca às taxas disponiveis para adição na tela **/
		$this->load->model("Taxas/taxa_model");
		
		$taxa_model = new Taxa_Model();
		    
        /**
         * Filtra às taxas permitidas de Exportação
         */                
        $taxas_autorizadas = array();
        
        if( ! is_null($sentido) && ! is_null($tipo) )
        {
            include_once $_SERVER['DOCUMENT_ROOT'] . "/permissoes_taxas.php";
            
            if( $sentido == "EXP" )
            {
                $sentido_selecionado = "exportacao_".$tipo;
            }
            else
            {
                $sentido_selecionado = "importacao_".$tipo;
            }
                        
            $taxas_completas = $taxa_model->retornaTodasAsTaxas();
            
            if( count($controle_taxas[$sentido_selecionado]) > 0 )
            {            
                foreach( $controle_taxas[$sentido_selecionado] as $taxa_permitida )
                {
                    if(array_key_exists($taxa_permitida, $taxas_completas) )
                    {
                        $taxas_autorizadas[$taxa_permitida] = $taxas_completas[$taxa_permitida];
                    }    
                }
                
                $data["taxas"] = $taxas_autorizadas;
            }
            else
            {
                $data["taxas"] = $taxas_completas;
            }    
                                   
        } 
        else
        {
            $data["taxas"] = $taxa_model->retornaTodasAsTaxas();
        }    
						
		/** Busca às unidades de cobrança **/
		$this->load->model("Taxas/unidade_model");
		
		$unidade_model = new Unidade_Model();
		
		$data["unidades"] = $unidade_model->retornaTodasAsUnidades();
				
		/** Busca às moedas **/
		$this->load->model("Taxas/moeda_model");
		
		$moeda_model = new Moeda_Model();
		
		$data["moedas"] = $moeda_model->retornaTodasAsMoedas();
				 	
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/add",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function save()
	{
		
		$this->form_validation->set_rules("sentido","Sentido","required");
		$this->form_validation->set_rules("inicio","Data Inicial","required");
		$this->form_validation->set_rules("validade","Data Final","required");
		$this->form_validation->set_rules("clientes_selecionados","Clientes","required");
		$this->form_validation->set_rules("portos_selecionados","Portos","required");
		$this->form_validation->set_rules("taxas_selecionadas","Taxas","required");
		
		if( ! $this->form_validation->run() )
		{
			show_error( validation_errors() );
		}	
		
		/** Carrega a classe que vai salvar o acordo **/
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
		
		$facade = new Acordos_Taxas_Facade();
		
		$id_acordo = $facade->salvarAcordoTaxasLocais($this->input->post());
		
		echo "<script language='javascript'>
				alert('Acordo salvo com sucesso!');
				window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/update/{$id_acordo}';		
			  </script>";

		//redirect("index.php/taxas_locais/taxas_locais/update/".$id_acordo);
			
	}
	
	public function view( $id_acordo )
	{
		if( empty($id_acordo) )
		{
			$message = "Id inválido para buscar o acordo de taxas locais!";
			log_message('error',$message);
			show_error($message);
		}
		
		$header['form_title'] = 'Scoa - Taxas Locais';
		$header['form_name'] = 'NOVO ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/view.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/alterar.gif', 'id' => 'alterar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		/** Busca os portos disponiveis para cadastramento das taxas **/
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
		$this->load->library("formata_taxa");
		$facade = new Acordos_Taxas_Facade();
		
		try{
			
			$data['acordo'] = $facade->recuperarAcordoTaxasLocais((int)$id_acordo);
			
			$this->load->view("Padrao/header_view",$header);
			$this->load->view("Taxas/view",$data);
			$this->load->view("Padrao/footer_view",$footer);
			
		} catch(InvalidArgumentException $e) {
			
			echo "<script language='javascript'>
					alert('".$e->getMessage()."');
					window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/index/';		
				   </script>";
			exit;
						
		} catch(RuntimeException $e) {
			
			echo "<script language='javascript'>
					alert('".$e->getMessage()."');
					window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/index/';	
				   </script>";
			exit;
			
		}
										
	}
	
	public function update( $id_acordo = NULL )
	{
		if( is_null($id_acordo) )
		{
			$message = "Nenhum Id de acordo foi definido para a alteração!";
			log_message('error', $message);
			show_error($message);
		}	
		
		unset($_SESSION['Desbloqueios'][$id_acordo]);
		
		/** Busca os portos disponiveis para cadastramento das taxas **/
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
		$this->load->library("formata_taxa");
		$facade = new Acordos_Taxas_Facade();
		
		/** Busca os portos disponiveis para cadastramento das taxas **/
		$this->load->model("Taxas_Locais_Acordadas/portos_taxas");
		$portos_taxas = new Portos_Taxas();
		
		$data['portos'] = $portos_taxas->obterPortosDasTaxasLocais();
		
		$data['acordo'] = $facade->recuperarAcordoTaxasLocais((int)$id_acordo);
								
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'NOVO ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/update.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/update",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function search()
	{
		
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'PROCURAR ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/search.js'));
		
		$imagens = "";		
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;

		$data['tipo_busca'] = Array(
									0 => "Selecione",
									"numero" => "Número",
									"cliente" => "Cliente",
									"vencimento" => "Vencimento",
									//"porto" => "Porto",
							  );
		
		$data['vencidas'] = Array(
                                "N" => "Não",
                                "S" => "Sim"
        );

		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/search",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function listView( $tipo_busca, $dado_busca, $vencidas )
	{
				
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'LISTAR TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/list_view.js'));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';		
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
				
		$this->load->model("Taxas_Locais_Acordadas/Buscas/acordo_factory");
		
		try {
			
			$search_driver = Acordo_Factory::acordo_factory($tipo_busca);

			$acordos_encontrados = $search_driver->search($dado_busca,$vencidas);
						
		} catch (Exception $e) {
			
			echo "<script language='javascript'>
					alert('".$e->getMessage()."');
					window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/search/';
				   </script>";
			exit;
			
		}		
				
		$data['acordos'] = $acordos_encontrados;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/list_view",$data);
		$this->load->view("Padrao/footer_view",$footer);
		
	}
	
	public function check_before_save( $clientes, $portos, $sentido, $inicio, $validade, $id_acordo = 0 )
	{
						
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");		
		$facade = new Acordos_Taxas_Facade();
		
		$acordos_encontrados = $facade->valida_acordos_cadastrados($clientes, $portos, $sentido, $inicio, $validade, $id_acordo);
						
		if( ! $acordos_encontrados )
		{
			$data['duplicacao'] = FALSE;
		}
		else
		{
			$data['duplicacao'] = TRUE;
		}		
		
		$data['acordos_duplicados'] = $acordos_encontrados;
		
		$this->load->view("Taxas/xml_procura_acordos_duplicados",$data);
		
	}
    
    public function revalidate($id_acordo = NULL, $meses = NULL)
    {
    	pr($_SESSION);exit(0);

        if( empty($id_acordo) || is_null($meses) )
        {
            die("Não foi possivel processar o envio da solicitação");
        }    
        
        /** Carrega a classe que vai salvar o acordo **/
		$this->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");
		
		$facade = new Acordos_Taxas_Facade();
		
		$acordo = $facade->recuperarAcordoTaxasLocais($id_acordo);
        
        $facade->revalidarAcordo($acordo, $meses);
                		
		echo "<script language='javascript'>
				alert('Operação efetuada com sucesso!');
				window.close();		
			  </script>";
    }    
    
    public function pesquisar_log()
    {
    	$header['form_title'] = 'Scoa - Log Acordo Taxas Locais';
    	$header['css'] = '';
    	
    	$imagens = "";    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	
    	$footer['footer'] = $imagens;
    	
    	$header['form_name'] = "Pesquisar Log";
    	$header['js'] = load_js(array('logs/pesquisar_log_taxas_locais.js'));

    	$data['tipos_consultas'] = Array("numero"=>"Numero Acordo");
    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("Logs/pesquisar_logs_taxas_locais",$data);
    	$this->load->view("Padrao/footer_view",$footer);
    }
    
    public function encontrar_logs()
    {    	
    	/** Pesquisa pelos logs encontrados baseado no número **/	
    	$rs = $this->db->
    					select("log_acordo_taxas_locais.*")->
    					from("CLIENTES.log_acordo_taxas_locais")->
    					like("log_acordo_taxas_locais.numero_acordo",$this->input->post('numero'));
    	
    	if( $this->input->post('numero') == null )
    	{
    		$this->db->limit("50");
    	}	
    	
    	$rs = $this->db->get();
    	
    	$linhas = $rs->num_rows();
    	    	    	
    	if( $linhas < 0 )
    	{
    		echo "<script language='javascript'>
    			   alert('Nenhum Log encontrado!');    	
    			   window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/pesquisar_log_taxas_locais/';			   	
    			   </script>";
    		exit;    				
    	}	
    	
    	$header['form_title'] = 'Scoa - Log Taxas Locais';
    	$header['css'] = '';
    	 
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	 
    	$footer['footer'] = $imagens;
    	 
    	$header['form_name'] = "Pesquisar Log";
    	$header['js'] = load_js(array('logs/selecionar_log_taxas_locais.js'));
    	
    	$data['logs'] = $rs->result();
    	 
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("Logs/selecionar_log_taxas_locais",$data);
    	$this->load->view("Padrao/footer_view",$footer);    	
    	
    }
    
    public function exibir_historico($id_log)
    {
        $this->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
        $this->load->model("Taxas_Locais_Acordadas/Memento/care_taker");
        $this->load->library("formata_taxa");
        
        $careTaker = new Care_Taker();
        
        $acordo = new Acordo_Taxas_Entity();
        
        $memento = $careTaker->LoadState($id_log);
        
        $acordo_log = $acordo->SetMemento($memento);
                        
        $header['form_title'] = 'Scoa - Taxas Locais';
		$header['form_name'] = 'ACORDO TAXAS LOCAIS';
		$header['css'] = '';
		$header['js'] = load_js(array('taxas/view.js'));
		
		$imagens = "";
        /**
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/alterar.gif', 'id' => 'alterar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		**/
		$footer['footer'] = $imagens;
        
        $data['acordo'] = $acordo_log;
			
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Taxas/view",$data);
		$this->load->view("Padrao/footer_view",$footer);
        
    }
        	
}//END CLASS