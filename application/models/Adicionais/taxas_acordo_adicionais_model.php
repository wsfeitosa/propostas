<?php
class Taxas_Acordo_Adicionais_Model extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function salvarTaxasDoAcordoDeAdicionais( Acordo_Adicionais $acordo )
	{		
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			log_message('O id do acordo não foi definido para salvar às taxas do acordo');
			show_error("Impossivel salvar às taxas do acordo");
		}	
						
		foreach( $acordo->getTaxas() as $taxaAcordo )
		{			
			$dadosDaTaxaParaSalvar = array(					
					'id_acordo_adicional' => $acordo->getId(),
					'id_taxa' => $taxaAcordo->getId(),
					'id_unidade' => $taxaAcordo->getUnidade()->getId(),
					'id_moeda' => $taxaAcordo->getMoeda()->getId(),
					'valor' => str_replace(",", ".", $taxaAcordo->getValor()),
					'valor_minimo' => str_replace(",", ".", $taxaAcordo->getValorMinimo()),
					'valor_maximo' => str_replace(",", ".", $taxaAcordo->getValorMaximo()),
					'ppcc' => $taxaAcordo->getPPCC()									
			);
			
			// Verifica se a taxa já existe no acordo, se já existir é uma alteração
			$this->db->
					select("taxas_acordo_adicionais.*")->
					from("CLIENTES.taxas_acordo_adicionais")->
					where("id_acordo_adicional",$acordo->getId())->
					where("id_taxa",$taxaAcordo->getId());
			
			$rowSetTaxas = $this->db->get();

			if( $rowSetTaxas->num_rows() > 0 )
			{
				$taxaParaAlterar = $rowSetTaxas->row();
				
				//Verifica se algo foi alterado na taxa
				$taxaFoiAlterada = false;
				
				if( $taxaAcordo->getUnidade()->getId() != $taxaParaAlterar->id_unidade )
				{
					$taxaFoiAlterada = true;
				}

				if( $taxaAcordo->getMoeda()->getId() != $taxaParaAlterar->id_moeda )
				{
					$taxaFoiAlterada = true;
				}	

				if( $taxaAcordo->getValor() != $taxaParaAlterar->valor )
				{
					$taxaFoiAlterada = true;
				}	
				
				if( $taxaAcordo->getValorMinimo() != $taxaParaAlterar->valor_minimo )
				{
					$taxaFoiAlterada = true;
				}	
				
				if( $taxaAcordo->getValorMaximo() != $taxaParaAlterar->valor_maximo )
				{
					$taxaFoiAlterada = true;
				}
				
				if( $taxaAcordo->getPPCC() != $taxaParaAlterar->ppcc )
				{
					$taxaFoiAlterada = true;
				}
												
				if( $taxaFoiAlterada === true )
				{				
					$dadosDaTaxaParaSalvar['id_usuario_alteracao'] = $_SESSION['matriz'][7];
					$dadosDaTaxaParaSalvar['data_alteracao'] = date('Y-m-d H:i:s');
															
					$this->db->where("id",$taxaParaAlterar->id);
					$this->db->update("CLIENTES.taxas_acordo_adicionais",$dadosDaTaxaParaSalvar);					
				}	
			}
			else 
			{
				$dadosDaTaxaParaSalvar['id_usuario_cadastro'] = $_SESSION['matriz'][7];
				$dadosDaTaxaParaSalvar['data_cadastro'] = date('Y-m-d H:i:s');
				$dadosDaTaxaParaSalvar['id_usuario_alteracao'] = $_SESSION['matriz'][7];
				$dadosDaTaxaParaSalvar['data_alteracao'] = date('Y-m-d H:i:s');
				
				$this->db->insert("CLIENTES.taxas_acordo_adicionais",$dadosDaTaxaParaSalvar);
			}								
		}	
	}
	
	public function buscaTaxasDoAcordoDeAdicionais( Acordo_Adicionais $acordo )
	{
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/moeda");
		$this->load->model("Taxas/moeda_model");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/unidade_model");
		$this->load->model("Taxas/taxa_model");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
	
		$id_acordo = $acordo->getId();
	
		if(empty($id_acordo))
		{
			throw new Exception("O id do acordo não foi informado para realizar a busca!");
		}
	
		$this->db->
		select("*")->
		from("CLIENTES.taxas_acordo_adicionais")->
		where("id_acordo_adicional",$acordo->getId());
	
		$rowSetTaxasAcordo = $this->db->get();
	
		if( $rowSetTaxasAcordo->num_rows() < 1 )
		{
			throw new RuntimeException("Este acordo não tem nenhuma taxa atribuida");
		}
	
		$taxa_model = new Taxa_Model();
		$unidade_model = new Unidade_Model();
		$moeda_model = new Moeda_Model();
		$usuario_model = new Usuario_Model();	
		
		foreach( $rowSetTaxasAcordo->result() as $taxaAcordo )
		{
			$taxa = new Taxa_Adicional();
				
			$taxa->setId((int)$taxaAcordo->id_taxa);
			$taxa->setIdItem((int)$acordo->getId());
			$taxa_model->obterNomeTaxaAdicional($taxa);
			$taxa->setValor((float)$taxaAcordo->valor);
			$taxa->setValorMinimo((float)$taxaAcordo->valor_minimo);
			$taxa->setValorMaximo((float)$taxaAcordo->valor_maximo);
			$taxa->setPPCC($taxaAcordo->ppcc);
				
			$unidade = new Unidade();
			$unidade->setId((int)$taxaAcordo->id_unidade);
			$unidade_model->findById($unidade);
				
			$taxa->setUnidade($unidade);
				
			$moeda = new Moeda();
			$moeda->setId((int)$taxaAcordo->id_moeda);
			$moeda_model->findById($moeda);
				
			$taxa->setMoeda($moeda);
			$acordo->setTaxas($taxa);
			
			$usuario = new Usuario();
			$usuario->setId((int)$taxaAcordo->id_usuario_cadastro);
			$usuario_model->findById($usuario);
						
			$taxa->addDecorator("usuario_cadastro", $usuario);
			
			$data_cadastro = new DateTime($taxaAcordo->data_cadastro);
			$taxa->addDecorator("data_cadastro", $data_cadastro);
			
			$usuario_alteracao = new Usuario();
			$usuario_alteracao->setId((int)$taxaAcordo->id_usuario_alteracao);
			$usuario_model->findById($usuario_alteracao);
			
			$taxa->addDecorator("usuario_alteracao", $usuario_alteracao);

			$data_alteracao = new DateTime($taxaAcordo->data_alteracao);
			$taxa->addDecorator("data_alteracao", $data_alteracao);
		}
	
	}
	
}
