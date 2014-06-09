<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! isset($_SESSION) )
{    
    session_start();
} 

include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';
 
class Propostas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper(Array("form","html","url"));		
	}

	public function index()
	{

		$this->output->cache(120);
		
		$header['form_title'] = 'Scoa - Propostas';
		$header['form_name'] = 'NOVA PROPOSTA';
		$header['css'] = '';
		$header['js'] = load_js(array('nova_proposta/nova_proposta.js'));
		
		$data['tipos_propostas'] = Array(
											0 => "Selecione",
											"Proposta_Cotacao" => "Proposta Cotação",
											"Proposta_Tarifario" => "Proposta Tarifario",	
											"Proposta_Spot" => "Proposta Spot",
											//"Proposta_Especial" => "Proposta Especial",
											"Proposta_NAC" => "Proposta NAC"	
		);
		
		$data['sentidos'] = Array(
									0 => "Selecione",
									"IMP" => "Importação",
									"EXP" => "Exportação"		
		);
		
		$footer['footer'] = ""; 
                		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("nova_proposta",$data);
		$this->load->view("Padrao/footer_view",$footer);		
	}

	public function nova_proposta()
	{
		/** 
		 * Destroi a variável de sessão armazena os itens das propostas,
		 * sempre que uma nova proposta é criada, com isso apenas uma proposta poderá 
		 * ser aberta por vez.
		 */	
		if( isset($_SESSION['itens_proposta']) )
		{
			unset($_SESSION['itens_proposta']);	
		}		
						
		$header['form_title'] = 'Scoa - Propostas';
		$header['css'] = '';
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		$header['form_name'] = strtoupper(str_replace("_", " ", $this->input->post("tipo_proposta")));
		$header['js'] = load_js(array('propostas/'.strtolower($this->input->post("tipo_proposta")).'.js','jquery.price_format.1.7.js'));
		 
		$data["sentido"] = $this->input->post("sentido");
			
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("propostas/".strtolower($this->input->post("tipo_proposta")),$data);
		$this->load->view("Padrao/footer_view",$footer);		
	}
    
    public function salvar()
    {
    	
    	$this->load->model("Propostas/proposta_model");
    	    	
        /** valida os dados do form **/
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('sentido', 'Sentido', 'required');
        $this->form_validation->set_rules('clientes_selecionados', 'Clientes Selecionados', 'required');
        $this->form_validation->set_rules('rotas_adicionadas', 'Rotas Selecionadas', 'required');
        
        if ($this->form_validation->run() == FALSE)
		{
			show_error(validation_errors());
		}
		        
        $proposta = $this->proposta_model->salvarProposta();   
                  
        redirect(base_url()."/index.php/propostas/propostas/consultar/". $proposta->getId() ."/" . strtolower(get_class($proposta)));
                     
    }        
	
    public function consultar( $id_proposta = NULL, $tipo_proposta = NULL, $initial = 0, $limit = 10 )
    {

    	$this->load->model("Propostas/proposta_model");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	$this->load->model("Desbloqueios/verifica_desbloqueio_pendente");
    	    	
        if( is_null($id_proposta) || is_null($tipo_proposta) )
        {
            show_error("Id ou o tipo da proposta informado é invalido para efetuar a consulta!");
        }    
                                
        /** Carrega a classe correta de acordo com tipo de proposta **/  
        $nome_do_arquivo = strtolower($tipo_proposta);
        
        $proposta = Proposta_Factory::factory($nome_do_arquivo);
        
        $proposta->setId((int) $id_proposta);     
        
        $proposta_model = new Proposta_Model();
        
        try {
                    	        	
            $proposta_model->buscarPropostaPorId($proposta,$initial,$limit);
            
        } catch (UnexpectedValueException $uneExp) {
            
            log_message('error',$uneExp->getTraceAsString());
            show_error($uneExp->getMessage());
        
        } catch (InvalidArgumentException $e) {
            
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
            
        } catch (Exception $e) {
            
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
            
        }
        
        $header['form_title'] = 'Scoa - Propostas';
		$header['css'] = '';
		
		$imagens = "";
		
		
		$footer['footer'] = $imagens;
		
		/** Seleciona o tipo de tela que deverá ser carregado **/
		switch ($nome_do_arquivo)
		{
			case "proposta_cotacao":				
				$header['form_name'] = 'PROPOSTA COTAÇÃO';                
				$header['js'] = load_js(array('propostas/proposta_cotacao_consulta.js'));								
			break;

			case "proposta_tarifario":
				$header['form_name'] = 'PROPOSTA TARIFARIO';
				$header['js'] = load_js(array('propostas/proposta_tarifario_consulta.js'));		
				$img_excel = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/microsoft_office_excel.png', 'id' => 'gerar_tarifario' , 'border' => 0, 'width' => '32', 'height' => '32', 'title' => 'Exportar Tarifário do cliente',));
				$imagens .= anchor_popup("index.php/propostas/propostas/exportar_tarifario_cliente/".$id_proposta."/",$img_excel,Array());
				$img = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/excel.jpg', 'id' => 'gerar_excel' , 'border' => 0, 'title' => 'Exportar proposta para alteração'));
				$imagens .= anchor_popup("index.php/propostas/propostas/gerar_excel/".$id_proposta."/Proposta_Tarifario",$img,Array());								
			break;
			
			case "proposta_spot":
				$header['form_name'] = 'PROPOSTA SPOT';
				$header['js'] = load_js(array('propostas/proposta_spot_consulta.js'));				
			break;
			
			case "proposta_especial":
				$header['form_name'] = 'PROPOSTA ESPECIAL';
				$header['js'] = load_js(array('propostas/proposta_especial_consulta.js'));				
			break;
			
			case "proposta_nac":
				$header['form_name'] = 'PROPOSTA NAC';
				$header['js'] = load_js(array('propostas/proposta_nac_consulta.js'));	
				$img_excel = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/microsoft_office_excel.png', 'id' => 'gerar_tarifario' , 'border' => 0, 'width' => '32', 'height' => '32', 'title' => 'Exportar Tarifário do cliente',));
				$imagens .= anchor_popup("index.php/propostas/propostas/exportar_tarifario_cliente/".$id_proposta."/",$img_excel,Array());
				$img = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/excel.jpg', 'id' => 'gerar_excel' , 'border' => 0, 'title' => 'Exportar proposta para alteração'));
				$imagens .= anchor_popup("index.php/propostas/propostas/gerar_excel/".$id_proposta."/Proposta_Tarifario",$img,Array());
			break;
        
            default :
                show_error("Não foi possível encontrar o arquivo javascript referente ao contexto atual!");
				 	
		}//END SWITCH		
		
		$img_pdf = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/impressao_reader.jpg', 'id' => 'gerar_pdf' , 'border' => 0, 'title' => 'Exportar PDF da proposta',));
		$img_email = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/email.gif', 'id' => 'enviar_email' , 'border' => 0, 'title' => 'Envia o email da proposta',));
		
		/** 
		 * Verifica se a proposta tem algum item pendente de desbloqueio antes de enviar,
		 * se houver então não deixa o usuário envia-la.
		 */
		$existe_desbloqueio_pendente = $this->verifica_desbloqueio_pendente->ExisteDesbloqueioPendente($id_proposta);
		
		if( $existe_desbloqueio_pendente === FALSE )
		{
			$imagens .= anchor_popup("index.php/propostas/propostas/enviar_email_proposta/".$id_proposta."/",$img_email,Array());
		}	
												
		$imagens .= anchor_popup("index.php/propostas/propostas/exportar_pdf_proposta/".$id_proposta."/",$img_pdf,Array());
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/alterar.gif', 'id' => 'alterar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
		
		$data["sentido"] = $proposta->getSentido();
        $data["proposta"] = $proposta;
		
        $footer['footer'] = $imagens;
        
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("propostas/".strtolower($tipo_proposta."_consulta"),$data);
		$this->load->view("Padrao/footer_view",$footer);     
                               
    }//END FUNCTION

    public function ver_mais( $id_proposta = NULL, $initial = NULL, $limit = 5 )
    {
        if( is_null($id_proposta) || is_null($initial) )
        {
            //FIXME mudar para uma resposta de erro xml para responder a requisição ajax
            show_error("Impossivel Carregar Mais Registros !");exit(0);
        }

        $this->load->model("Propostas/proposta_model");

        $this->proposta_model->carregarItensParaViewAjax($id_proposta, $initial, $limit);        
    }        
    
    public function alterar()
    {
    	    	
    	$this->load->model("Propostas/proposta_model");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	$this->load->model("Propostas/item_proposta_model");
    	$this->load->model("Adaptadores/array_conversor");
    	    	    	    	
    	$id_proposta = $this->input->post('id_proposta');
    	$tipo_proposta = $this->input->post('tipo_proposta');
    	
    	if( empty($id_proposta) )
    	{
    		log_message('error','Não foi informado o Id da proposta para carregar o formulário de alteração!');
    		show_error('Não foi informado o Id da proposta para carregar o formulário de alteração!');
    	}	
    	  	    	
    	$proposta = Proposta_Factory::factory(strtolower($tipo_proposta));
    	
    	$proposta->setId((int) $id_proposta);
    	
    	$proposta_model = new Proposta_Model();
        
        unset($_SESSION['itens_proposta']);
    	
    	try {
    	
    		$proposta_model->buscarPropostaPorId($proposta);
            $proposta_serializada = $proposta_model->serializaDadosDaPropostaParaView($proposta);
            
            /** Iclui os itens da proposta na sessão do PHP **/            
            $item_proposta_model = new Item_Proposta_Model();
            
            foreach ($proposta->getItens() as $item) 
            {
            	unset($_SESSION['Desbloqueios'][$item->getId()]);            	
                $item_proposta_model->incluirItemDaPropostaNaSessao($item);
            }            
    	
    	} catch (UnexpectedValueException $uneExp) {
    	
    		log_message('error',$uneExp->getTraceAsString());
    		show_error($uneExp->getMessage());
    	
    	} catch (InvalidArgumentException $e) {
    	
    		log_message('error',$e->getTraceAsString());
    		show_error($e->getMessage());
    	
    	} catch (Exception $e) {
    	
    		log_message('error',$e->getTraceAsString());
    		show_error($e->getMessage());
    	
    	}
    	
    	$header['form_title'] = 'Scoa - Propostas';
    	$header['css'] = '';
    	
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/salvar_registro.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	
    	$footer['footer'] = $imagens;
    	    	
    	/** Seleciona o tipo de tela que deverá ser carregado **/
    	switch (strtolower($tipo_proposta))
    	{
    		case "proposta_cotacao":
    			$header['form_name'] = 'PROPOSTA COTAÇÃO';
    			$header['js'] = load_js(array('propostas/proposta_cotacao.js','jquery.price_format.1.7.js'));
    			break;
    	
    		case "proposta_tarifario":
    			$header['form_name'] = 'PROPOSTA TARIFARIO';
    			$header['js'] = load_js(array('propostas/proposta_tarifario.js','jquery.price_format.1.7.js'));
    			break;
    				
    		case "proposta_spot":
    			$header['form_name'] = 'PROPOSTA SPOT';
    			$header['js'] = load_js(array('propostas/proposta_spot.js','jquery.price_format.1.7.js'));
    			break;
    				
    		case "proposta_especial":
    			$header['form_name'] = 'PROPOSTA ESPECIAL';
    			$header['js'] = load_js(array('propostas/proposta_especial.js','jquery.price_format.1.7.js'));
    			break;
    				
    		case "proposta_nac":
    			$header['form_name'] = 'PROPOSTA NAC';
    			$header['js'] = load_js(array('propostas/proposta_nac.js','jquery.price_format.1.7.js'));
    			break;
    	
    		default :
    			show_error("Não foi possível encontrar o arquivo javascript referente ao contexto atual!".pr($nome_do_arquivo));
    	
    	}//END SWITCH
        
    	$data["sentido"] = $proposta->getSentido();
    	$data["proposta"] = $proposta;
        $data["itens_serializados"] = $proposta_serializada;
    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/".strtolower($tipo_proposta."_alteracao"),$data);
    	$this->load->view("Padrao/footer_view",$footer);
    	
    }//END FUNCTION
            
    public function alterar_proposta()
    {
    	$this->load->model("Propostas/proposta_model");
    	    	    	
        /** valida os dados do form **/
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('sentido', 'Sentido', 'required');
        $this->form_validation->set_rules('clientes_selecionados', 'Clientes Selecionados', 'required');
        $this->form_validation->set_rules('rotas_adicionadas', 'Rotas Selecionadas', 'required');
        $this->form_validation->set_rules('tipo_proposta','Tipo Proposta','required');
        
        if ($this->form_validation->run() == FALSE)
		{
			show_error(validation_errors());
		}
        
        $proposta_model = new Proposta_Model();
        
        try{        
            
            $proposta = $proposta_model->alterarProposta();     
            
        } catch ( InvalidArgumentException $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
        } catch ( RuntimeException $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());                      
        } catch ( Exception $e ) {
            log_message('error',$e->getTraceAsString());
            show_error($e->getMessage());
        }
        
        redirect(base_url()."/index.php/propostas/propostas/consultar/". $proposta->getId() ."/" . strtolower(get_class($proposta)));
        
    }//END FUNCTION   

    public function realizar_busca()
    {
    	//$this->output->cache(60);
    	
    	$header['form_title'] = 'Scoa - Propostas';
    	$header['form_name'] = 'REALIZAR BUSCA';
    	$header['css'] = '';
    	$header['js'] = load_js(array('propostas/realizar_busca.js'));
    	
    	$data['tipos_consultas'] = Array(
    			0 => "Selecione",
    			"numero" => "Número",
    			"cliente" => "Cliente",
                "nome_nac" => "Nome do Nac",
                "origem" => "Origem",
                "destino" => "Destino",
    			//"periodo" => "Período"
    	);
    	
    	$data['sentidos'] = Array(
    			//0 => "Selecione",
    			"IMP" => "Importação",
    			"EXP" => "Exportação"
    	);

        $data['vencidas'] = Array(
                                "N" => "Não",
                                "S" => "Sim"
        );
    	
        $data['status'] = array(
                                "0" => "Selecione",
                                "1" => "Pendente de Envio",
                                "2" => "Aguardando Desbloqueio",
                                "3" => "Enviada ao Cliente",
                                "8" => "Utilizada"
        );
        
        $data['filiais'] = Array(
								 "0" => "Selecione",
								 "SP" => "São Paulo",
								 "CT" => "Curitiba",
								 "IT" => "Itajai",
								 "PA" => "Porto Alegre",
								 "RJ" => "Rio de Janeiro",
		);       
        
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/novo.jpg', 'id' => 'novo' , 'border' => 0)).'</a>';    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	 
    	$footer['footer'] = $imagens;
    	    	    	
    	$this->load->view("Padrao/header_view_new",$header);
    	$this->load->view("propostas/realizar_busca",$data);
    	$this->load->view("Padrao/footer_view_new",$footer);
    }
    
    public function listar_resultados_busca()
    {
        pr($this->input->post());exit();
        $this->load->model("Propostas/Buscas/search_factory");
        $this->load->library("Scoa/url");
        
        $url_library = new Url();
        
        $buscador = Search_Factory::factory($this->input->post('tipo_consulta'));
              
        $propostas_encontradas = $buscador->buscar(
                                                    $url_library->decodificarUrl($this->input->post('dado_para_busca')),
                                                    $this->input->post('sentido'),
                                                    $this->input->post('vencidas')
        );
        
        $header['form_title'] = 'Scoa - Propostas Econtradas';
    	$header['form_name'] = 'Propostas Encontradas';
    	$header['css'] = '';
    	$header['js'] = load_js(array('propostas/lista_propostas.js'));
        
        $imagens = "";    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	              
        $footer['footer'] = $imagens;
        
        $data['propostas'] = $propostas_encontradas;
    	    	    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/lista_propostas",$data);
    	$this->load->view("Padrao/footer_view",$footer);
        
    }        
    
    public function gerar_excel( $id_proposta = NULL, $tipo_proposta = NULL )
    {
    	
    	$this->load->model("Relatorios/Layouts/layout_exportar_tarifario");
    	$this->load->model("Relatorios/exportar_tarifario");
    	$this->load->model("Formatos/formato_excel");
    	$this->load->model("Relatorios/relatorio_adapter");    	
    	$this->load->model("Propostas/proposta_model");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	
    	if( is_null($id_proposta) || is_null($tipo_proposta) )
    	{
    		show_error("Id ou o tipo da proposta informado é invalido para efetuar a consulta!");
    	}
    	
    	/** Carrega a classe correta de acordo com tipo de proposta **/
    	$nome_do_arquivo = strtolower($tipo_proposta);
    	
    	$proposta = Proposta_Factory::factory($nome_do_arquivo);
    	
    	$proposta->setId((int) $id_proposta);
    	
    	$proposta_model = new Proposta_Model();
    	
    	try{
    		    		
    		$proposta_model->buscarPropostaPorId($proposta);   

    		$adapter = new Relatorio_Adapter();
    		
    		$layout = new Layout_Exportar_Tarifario();
    		
    		$formato = new Formato_Excel();
    		
    		$relatorio = new Exportar_Tarifario();
    		
    		$relatorio->adicionarNovoParametro($proposta);
    		
    		$adapter->gerarRelatorio($relatorio, $formato, $layout);
    		
    		$adapter->exportar();
    		    		    		
    	} catch (Exception $e) {
    		echo "<script language='javascript'>
					alert('".$e->getMessage()."');
					window.location = '/Clientes/propostas/index.php/taxas_locais/taxas_locais/index/';
				   </script>";
    		exit;
    	}
    	
    }
    
    public function upload_planilha()
    {
    	
    	$header['form_title'] = 'Scoa - Propostas';
    	$header['form_name'] = 'Importar Planilha';
    	$header['css'] = '';
    	$header['js'] = load_js(array('propostas/upload_planilha.js'));
    	
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/atualizar_registro.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	    	
    	$footer['footer'] = $imagens;
    	 
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("propostas/upload_planilha",$data);
    	$this->load->view("Padrao/footer_view",$footer);
    	
    }
    
    public function importar_planilha()
    {    	    	
    	
    	$moved = move_uploaded_file($_FILES['arquivo']['tmp_name'], APPPATH . "cache/" . $_FILES['arquivo']['name']);

    	$arquivo = $_FILES['arquivo']['name'];
    	
    	if( ! $moved )
    	{
    		show_error("Impossivel mover o arquivo enviado!");
    	}
    	
    	$this->load->model("Propostas/upload_proposta_tarifario");
    	
    	$upload = new Upload_Proposta_Tarifario();
    	
        try {

    	   $upload->ImportarPlanilha($arquivo);
    	
        } catch ( Exception $e ) {
            echo $e->getMessage();exit();
        }
        ob_clean();
        header("HTTP/1.0 200 Ok");
        header('Content-type: text/html');

    	echo "<script language='javascript'>
    			alert('Planilha importada com sucesso!');
    			window.close();
    		  </script>";
    	   	    	    	
    }
    
    public function exportar_tarifario_cliente( $id_proposta = NULL )
    {
    	
    	$this->load->model("Propostas/proposta_model");    	
    	$this->load->model("Propostas/proposta_tarifario");    	
    	$this->load->model("Propostas/exportar_proposta_tarifario");
    	
    	if( is_null($id_proposta) )
    	{
    		show_error("Nenhuma Proposta informada para realizar a exportação!");
    	}	
        
    	$proposta = new Proposta_Tarifario();
    	
    	$proposta->setId((int)$id_proposta);
    	
    	$proposta_model = new Proposta_Model();
    	    	
    	$proposta_model->buscarPropostaPorId($proposta);
    	        	
    	$file_export = new Exportar_Proposta_Tarifario($proposta);

    	$file_export->setPropostaTarifario($proposta);
    	    	
    	$file_export->exportar();
    	
    }
    
    public function exportar_pdf_proposta( $id_proposta = NULL )
    {
    	
    	if( is_null($id_proposta) )
    	{
    		show_error("Nenhuma Proposta informada para realizar a exportação!");exit;
    	}	
    	
    	$this->load->model("Propostas/proposta_model");
    	$this->load->model("Propostas/exportar_pdf_proposta");
    	$this->load->model("Propostas/Factory/proposta_factory");
    	
    	/** verifica se a proposta existe e retorna o tipo da proposta **/
    	$this->db->select("tipo_proposta")->from("CLIENTES.propostas")->where("id_proposta",$id_proposta);
    	
    	$rs = $this->db->get();
    	
    	$linhas = $rs->num_rows();
    	
    	if( $linhas < 1 )
    	{
    		show_error("Impossivel encontrar a proposta informada!");exit;
    	}	
    	
    	$result = $rs->row();
    	
    	$proposta = Proposta_Factory::factory($result->tipo_proposta);
    	 
    	$proposta->setId((int) $id_proposta);
    	 
    	$proposta_model = new Proposta_Model();
    	
    	$proposta_model->buscarPropostaPorId($proposta);
    	
    	$gerador_pdf = new Exportar_Pdf_Proposta($proposta,FALSE);
    	
    	$gerador_pdf->gerarPdf();
    	    	
    }
    
    public function enviar_email_proposta( $id_proposta = NULL )
    {
    	if( is_null($id_proposta) )
    	{
    		show_error("Nenhuma Proposta informada para realizar a exportação!");exit;
    	}
    	    	
    	$this->load->model("Propostas/proposta_model");    	
    	$this->load->model("Propostas/Factory/proposta_factory");
    	include APPPATH.'/models/Propostas/enviar_proposta.php';
    	    	 
    	/** verifica se a proposta existe e retorna o tipo da proposta **/
    	$this->db->select("tipo_proposta")->from("CLIENTES.propostas")->where("id_proposta",$id_proposta);
    	 
    	$rs = $this->db->get();
    	 
    	$linhas = $rs->num_rows();
    	 
    	if( $linhas < 1 )
    	{
    		show_error("Impossivel encontrar a proposta informada!");exit;
    	}
    	 
    	$result = $rs->row();
    	 
    	$proposta = Proposta_Factory::factory($result->tipo_proposta);
    	
    	$proposta->setId((int) $id_proposta);
    	
    	$proposta_model = new Proposta_Model();
    	 
    	$proposta_model->buscarPropostaPorId($proposta);
    	    	    	
    	$sender = new Enviar_Proposta($proposta);
    	
    	$sender->Enviar();
    	
    }

    public function exibir_historico($id_log)
    {
        $this->load->model("Propostas/Factory/proposta_factory");
        $this->load->model("Propostas/Memento/care_taker");
        $this->load->model("Propostas/proposta_tarifario");
        $this->load->model("Propostas/proposta_cotacao");
        $this->load->model("Propostas/proposta_nac");
        $this->load->model("Propostas/proposta_spot");

        $careTaker = new Care_Taker();
        
        $proposta = Proposta_Factory::factory(strtolower("proposta_tarifario"));
        
        $memento = $careTaker->LoadState($id_log);
        
        $proposta_log = $proposta->SetMemento($memento);
        
        $header['form_title'] = 'Scoa - Propostas';
        $header['css'] = '';
        
        $imagens = "";
        
        
        $footer['footer'] = $imagens;
        
        /** Seleciona o tipo de tela que deverá ser carregado **/
        switch ($proposta_log->getTipoProposta())
        {
            case "proposta_cotacao":                
                $header['form_name'] = 'PROPOSTA COTAÇÃO';                
                $header['js'] = load_js(array('propostas/proposta_cotacao_consulta.js'));                               
            break;

            case "proposta_tarifario":
                $header['form_name'] = 'PROPOSTA TARIFARIO';
                $header['js'] = load_js(array('propostas/proposta_tarifario_consulta.js'));     
                $img_excel = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/microsoft_office_excel.png', 'id' => 'gerar_tarifario' , 'border' => 0, 'width' => '32', 'height' => '32', 'title' => 'Exportar Tarifário do cliente',));
                $imagens .= anchor_popup("index.php/propostas/propostas/exportar_tarifario_cliente/".$proposta_log->getId()."/",$img_excel,Array());
                $img = img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/excel.jpg', 'id' => 'gerar_excel' , 'border' => 0, 'title' => 'Exportar proposta para alteração'));
                $imagens .= anchor_popup("index.php/propostas/propostas/gerar_excel/".$proposta_log->getId()."/Proposta_Tarifario",$img,Array());                             
            break;
            
            case "proposta_spot":
                $header['form_name'] = 'PROPOSTA SPOT';
                $header['js'] = load_js(array('propostas/proposta_spot_consulta.js'));              
            break;
            
            case "proposta_especial":
                $header['form_name'] = 'PROPOSTA ESPECIAL';
                $header['js'] = load_js(array('propostas/proposta_especial_consulta.js'));              
            break;
            
            case "proposta_nac":
                $header['form_name'] = 'PROPOSTA NAC';
                $header['js'] = load_js(array('propostas/proposta_nac_consulta.js'));               
            break;
        
            default :
                show_error("Não foi possível encontrar o arquivo javascript referente ao contexto atual!");
                    
        }//END SWITCH       
                      
        $data["sentido"] = $proposta_log->getSentido();
        $data["proposta"] = $proposta_log;
        
        $footer['footer'] = "";
        
        $this->load->view("Padrao/header_view",$header);
        $this->load->view("propostas/".strtolower($proposta_log->getTipoProposta()."_consulta"),$data);
        $this->load->view("Padrao/footer_view",$footer);     
    }
    
    public function pesquisar_log()
    {
    	$header['form_title'] = 'Scoa - Log Propostas';
    	$header['css'] = '';
    	
    	$imagens = "";    	
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	
    	$footer['footer'] = $imagens;
    	
    	$header['form_name'] = "Pesquisar Log";
    	$header['js'] = load_js(array('logs/pesquisar_log.js'));

    	$data['tipos_consultas'] = Array("numero"=>"Numero Proposta");
    	
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("Logs/pesquisar_logs",$data);
    	$this->load->view("Padrao/footer_view",$footer);
    }
    
    public function encontrar_logs()
    {    	
    	/** Pesquisa pelos logs encontrados baseado no número **/	
    	$rs = $this->db->
    					select("log_propostas.*")->
    					from("CLIENTES.log_propostas")->
    					like("log_propostas.numero_proposta",$this->input->post('numero'));
    	
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
    			   window.location = '/Clientes/propostas/index.php/propostas/propostas/pesquisar_log/';			   	
    			   </script>";
    		exit;    				
    	}	
    	
    	$header['form_title'] = 'Scoa - Log Propostas';
    	$header['css'] = '';
    	 
    	$imagens = "";
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/localizar.gif', 'id' => 'localizar' , 'border' => 0)).'</a>';
    	$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/voltar.gif', 'id' => 'voltar' , 'border' => 0)).'</a>';
    	 
    	$footer['footer'] = $imagens;
    	 
    	$header['form_name'] = "Pesquisar Log";
    	$header['js'] = load_js(array('logs/selecionar_log.js'));
    	
    	$data['logs'] = $rs->result();
    	 
    	$this->load->view("Padrao/header_view",$header);
    	$this->load->view("Logs/selecionar_log",$data);
    	$this->load->view("Padrao/footer_view",$footer);    	
    	
    }

    public function form_exclusao()
    {
        $this->output->cache(120);
        
        $header['form_title'] = 'Scoa - Propostas';
        $header['form_name'] = 'EXCLUIR PROPOSTA';
        $header['css'] = '';
        $header['js'] = load_js(array('propostas/excluir_proposta.js'));
                              
        $footer['footer'] = ""; 
        
        $data = Array();

        $this->load->view("Padrao/header_view",$header);
        $this->load->view("propostas/excluir_proposta",$data);
        $this->load->view("Padrao/footer_view",$footer);        
    }

    public function excluir_proposta()
    {
        $this->load->model('Propostas/proposta_model');

        try{

            $this->proposta_model->excluir_proposta($this->input->post('numero_proposta'));

        } catch (Exception $e) {
            ob_clean();
            echo "<script language='javascript'>
                    alert('".$e->getMessage()."');
                    window.location = '/Clientes/propostas/index.php/propostas/propostas/form_exclusao';
                  </script>";
        }   

        echo "<script language='javascript'>
                alert('Proposta excluída com sucesso!');
                window.location = '/Clientes/propostas/index.php/propostas/propostas/form_exclusao';
              </script>";

    }
    
}//END CLASS

/* End of file propostas.php */
/* Location: ./application/controllers/propostas.php */

