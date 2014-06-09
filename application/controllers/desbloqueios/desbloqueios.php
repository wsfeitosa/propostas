<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once $_SERVER['DOCUMENT_ROOT'] . '/Gerais/autenticacao.php';

class Desbloqueios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(Array("html","form","url"));
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Desbloqueios/solicitacao_desbloqueio_taxa_facade");
		$this->load->model("Taxas/serializa_taxas");
		$this->load->model("Taxas_Locais_Acordadas/conversor_taxas");
		$this->load->model("Taxas/taxa_local");
	}

	public function index()
	{
		
	}
	
	public function taxa( $sentido = NULL, $taxa_value = NULL, $tipo_taxa, $id_item = NULL, $index = NULL, $modulo = NULL )
	{		
		if( is_null($taxa_value) || is_null($modulo) || is_null($id_item) || is_null($index) )
		{
			show_error("Impossivel solicitar o desbloqueio, taxa invalida!");
		}
				
		$taxa_value = htmlspecialchars_decode(utf8_decode(urldecode($taxa_value)));
		/** Converte a entidade especial html de volta no caractere de moeda ($) **/		
		$taxa_value = str_replace("&#36;", "$", $taxa_value);
		
		switch( $modulo )
		{
			case "proposta":
				$serializador = new Serializa_Taxas();
				
				$taxa = $serializador->deserializaTaxasProposta( $taxa_value."---", $tipo_taxa );	
				$data['form_action'] = "index.php/desbloqueios/desbloqueios/salvar_desbloqueio_taxa/";			
			break;

			case "taxa_local":				
				$serializador = new Conversor_Taxas();
				
				$taxa_deserializada = $serializador->deserializaTaxa($taxa_value);
				
				$taxa[0] = $taxa_deserializada;
				$data['form_action'] = "index.php/desbloqueios/desbloqueios/salvar_desbloqueio_taxa_local/";
			break;
									
			default:
				show_error("Módulo desconhecido para solicitar o desbloqueio!"); exit;
		}
				
		$header['form_title'] = 'Scoa - Solicitar Desbloqueio';
		$header['form_name'] = 'SOLICITAR DESBLOQUEIO';
		$header['css'] = '';
		$header['js'] = load_js(array('desbloqueios/'.$modulo.".js","jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
		
		$imagens = "";
		$imagens .= '<a href="#">'.img(Array('src' => 'http://'.$_SERVER['HTTP_HOST'].'/Imagens/gravar.jpg', 'id' => 'salvar' , 'border' => 0)).'</a>';
		
		$footer['footer'] = $imagens;
		
		$taxa[0]->setIdItem((int)$id_item);
						
		$data['taxa'] = $taxa[0];
		/** O valor da taxa serializada será utilizado para comparar a taxa depois que ela for alterada **/
		$data['taxa_serializada'] = $taxa_value;
		$data['modulo'] = $modulo;
		$data['index_taxa'] = $index;
							
		/** Busca às unidades de cobrança **/
		$this->load->model("Taxas/unidade_model");
		
		$unidade_model = new Unidade_Model();
		
		$data["unidades"] = $unidade_model->retornaTodasAsUnidades();
				
		/** Busca às moedas **/
		$this->load->model("Taxas/moeda_model");
		
		$moeda_model = new Moeda_Model();
		
		$data["moedas"] = $moeda_model->retornaTodasAsMoedas();
		
		/** Busca os volumes e notas que serão informados para o usuário **/
		$this->load->model("Desbloqueios/notas_model");
		
		$notas_model = new Notas_Model();
		
		$notas_encontradas = $notas_model->listAll($sentido);
		
		$notas = Array();
		
		foreach( $notas_encontradas as $nota )
		{					
			$notas[$nota->getId()] = $nota->getValorMimimo() . " | " . $nota->getValorMaximo();
		}	
		
		$data['notas'] = $notas;
		
		$this->load->view("Padrao/header_view",$header);
		$this->load->view("Desbloqueios/taxa",$data);
		$this->load->view("Padrao/footer_view",$footer);
	
	}
	
	public function salvar_desbloqueio_taxa_local()
	{
						
		$facade = new Solicitacao_Desbloqueio_Taxa_Facade();
		
		$serializador = new Conversor_Taxas();
		
		$taxaAntesDaAlteracao = $serializador->deserializaTaxa($this->input->post('taxa_serializada'));
		
		$taxa_alterada = new Taxa_Adicional();
		
		$taxa_alterada->setIdItem((int)$this->input->post('id_item'));
		$taxa_alterada->setId((int)$this->input->post('id_taxa'));
		$taxa_alterada->setValor((float)$this->input->post('valor'));
		$taxa_alterada->setValorMinimo((float)$this->input->post('valor_minimo'));
		$taxa_alterada->setValorMaximo((float)$this->input->post('valor_maximo'));
		
		$moeda = new Moeda();
		$moeda_model = new Moeda_Model();
		$moeda->setId((int)$this->input->post('moeda'));
		$moeda_model->findById($moeda);
		$taxa_alterada->setMoeda($moeda);
		
		$unidade = new Unidade();
		$unidade_model = new Unidade_Model();
		$unidade->setId((int)$this->input->post('unidade'));
		$unidade_model->findById($unidade);
		$taxa_alterada->setUnidade($unidade);
		
		$taxa_model = new Taxa_Model();
		$taxa_model->obterNomeTaxaAdicional($taxa_alterada);
		
		$nota = $this->input->post('nota');
		
		$observacao = strtoupper($this->input->post('justificativa'));
				
		/** 
		 * Verifica se à taxa foi alterada para um valor maior ou menor do que
		 * o valor anterior se foi maior apenas atualiza o valor na tela, se foi 
		 * para menor então solicita o desbloqueio. Ou se a moeda ou a unidade foi alterada
		 */
		if( ( $taxa_alterada->getMoeda()->getId() != $taxaAntesDaAlteracao->getMoeda()->getId() ) || 
			( $taxa_alterada->getUnidade()->getId() != $taxaAntesDaAlteracao->getUnidade()->getId() ) 
		)
		{					
			echo "<script language='javascript'>
					alert('Desbloqueio solicitado com sucesso!');
					window.parent.document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/';
					window.parent.document.getElementById('pop').style.display = 'none';
				   </script>";
			$facade->solicitaDesbloqueioTaxa($taxa_alterada, "taxa_local", $nota, $observacao);			
		}
		else
		{	
			//Verifica se o valor foi alterado para maior ou para menor
			if( $taxa_alterada->getValor() >= $taxaAntesDaAlteracao->getValor() )
			{
				//Altera na tela
				$taxa_alterada_serializada = $serializador->serializaTaxa($taxa_alterada);
												
				$data['label'] = $taxa_alterada_serializada['label'];
					
				$data['value'] = $taxa_alterada_serializada['value'];
					
				$data['index_taxa'] = $this->input->post("index_taxa");
													
				$header['js'] = load_js(array('desbloqueios/atualiza_taxas_tela_acordo_taxas.js',"jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
					
				$imagens = "";
					
				$footer['footer'] = $imagens;
					
				$this->load->view("Padrao/header_view",$header);
				$this->load->view("Desbloqueios/atualiza_taxas_tela_acordo_taxas",$data);
				$this->load->view("Padrao/footer_view",$footer);
			}
			else
			{				
				echo "<script language='javascript'>
						alert('Desbloqueio solicitado com sucesso!');
						window.parent.document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/';
						window.parent.document.getElementById('pop').style.display = 'none';
					   </script>";
				$facade->solicitaDesbloqueioTaxa($taxa_alterada, "taxa_local", $nota, $observacao);				
			}		
		}					
		
	}
	
	public function salvar_desbloqueio_taxa()
	{	
		
		$tipo_taxa = $this->input->post('tipo_taxa');
		
		$taxa = new $tipo_taxa();
		
		$taxa->setIdItem((int)$this->input->post('id_item'));
		$taxa->setId((int)$this->input->post('id_taxa'));
		$taxa->setValor((float)$this->input->post('valor'));
		$taxa->setValorMinimo((float)$this->input->post('valor_minimo'));
		$taxa->setValorMaximo((float)$this->input->post('valor_maximo'));
		
		$moeda = new Moeda();
		$moeda_model = new Moeda_Model();
		$moeda->setId((int)$this->input->post('moeda'));
		$moeda_model->findById($moeda);
		$taxa->setMoeda($moeda);
		
		$unidade = new Unidade();
		$unidade_model = new Unidade_Model();
		$unidade->setId((int)$this->input->post('unidade'));
		$unidade_model->findById($unidade);
		$taxa->setUnidade($unidade);
		
		$taxa_model = new Taxa_Model();
		$taxa_model->obterNomeTaxaAdicional($taxa);	

		$nota = $this->input->post('nota');
		
		$observacao = strtoupper($this->input->post('justificativa'));
						
		$facade = new Solicitacao_Desbloqueio_Taxa_Facade();
						
		/** Verifica se o item existe de fato na sessão do usuário **/
		$facade->verificaItemSessao($taxa);
		
		/** verifica se é um desbloqueio ou se taxa pode ser alterada pelo usuário **/
		if($facade->verificaTaxaAbaixoDoValor($taxa))
		{
			//Solicita o desbloqueio						
			 echo "<script language='javascript'>
					alert('Desbloqueio solicitado com sucesso!');
					window.parent.document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/';
					window.parent.document.getElementById('pop').style.display = 'none';
				   </script>";
			$facade->solicitaDesbloqueioTaxa($taxa, $this->input->post('modulo'), $nota, $observacao);					
		}
		else
		{
			//Altera a taxa na sessão do usuário e na tela
			$serializador = new Serializa_Taxas();
			$taxa_serializada = $serializador->SerializaTaxasParaSessao(Array($taxa));
			
			$label_option = str_replace("---", "", $taxa_serializada['label_taxas']);
			$value_option = str_replace("---", "", $taxa_serializada['value_taxas']);

			$data['label'] = $label_option;
			
			$data['value'] = $value_option;
			
			$data['index_taxa'] = $this->input->post("index_taxa");
			
			$data['nome_combo'] = $tipo_taxa == "Taxa_Local" ? "taxas_locais":"frete_adicionais";
			
			$header['js'] = load_js(array('desbloqueios/atualiza_taxas_tela_proposta.js',"jquery.price_format.1.7.min.js","jquery.price_format.1.7.js"));
			
			$imagens = "";
									
			$footer['footer'] = $imagens;
			
			$this->load->view("Padrao/header_view",$header);
			$this->load->view("Desbloqueios/atualiza_taxas_tela_proposta",$data);
			$this->load->view("Padrao/footer_view",$footer);
		}		
		
	}
	
		
	public function periodo()
	{

	}

}//END CLASS

/* End of file desbloqueios.php */
/* Location: ./application/controllers/desbloqueios/desbloqueios.php */
