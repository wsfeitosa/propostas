<?php
class Busca_Acordos_Adicionais extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
		$this->output->enable_profiler(false);
	}
	
	public function aplicarFiltrosDeBusca()
	{		
		$this->load->model("Adicionais/clientes_acordo_adicionais_model");
		$this->load->model("Adicionais/acordo_adicionais");
		
		$quantidadeDeFiltros = 0;
		$acordosEncontrados = array();
		$clientesAcordoModel = new Clientes_Acordo_Adicionais_model();
		
		$this->db->
				select("acordo_adicionais.id, acordo_adicionais.numero_acordo, inicio, validade")->
				from("CLIENTES.acordo_adicionais")->
				join("CLIENTES.clientes_x_acordo_adicionais","acordo_adicionais.id = clientes_x_acordo_adicionais.id_acordo_adicionais")->
				join("CLIENTES.clientes", "clientes.id_cliente = clientes_x_acordo_adicionais.id_cliente");		
		
		if( ! empty($_REQUEST["numero"]) )
		{
			$this->db->like("numero_acordo",$this->input->post('numero'));
			$quantidadeDeFiltros += 1;
		}	
		
		if( ! empty($_REQUEST['data_inicial'])  )
		{
			$dataInicial = new DateTime($this->input->post('data_inicial'));
			$this->db->where("inicio >=",$dataInicial->format('Y-m-d'));
			$quantidadeDeFiltros += 1;
		}

		if( ! empty($_REQUEST['data_final'])  )
		{
			$dataFinal = new DateTime($this->input->post('data_final'));
			$this->db->where("validade <=",$dataFinal->format('Y-m-d'));
			$quantidadeDeFiltros += 1;
		}
		
		if( ! empty($_REQUEST['tipo_cliente_busca']) )
		{
			switch( $this->input->post('tipo_cliente_busca') )
			{
				case "1":					
					$this->db->where("clientes.id_cliente",$this->input->post("id_cliente"));
				break;

				case "2":
					$this->db->join("CLIENTES.grupo_comercial", "clientes.id_grupo_comercial = grupo_comercial.idgrupo_comercial");
					$this->db->where("clientes.id_grupo_comercial",$this->input->post('id_grupo_comercial'));
				break;
				
				case "3":
					$this->db->join("CLIENTES.grupo_cnpj", "clientes.id_grupo_cnpj = grupo_cnpj.idgrupo_cnpj");
					$this->db->where("clientes.id_grupo_cnpj",$this->input->post('id_grupo_cnpj'));
				break;
				
				default:
					show("Nenhuma informa��o sobre o cliente foi fornecida para efetuar a busca!");
				
			}
			$quantidadeDeFiltros += 1;
		}	
		
		if( ! empty($_REQUEST['id_taxa']) )
		{
			$this->db->join("CLIENTES.taxas_acordo_adicionais","taxas_acordo_adicionais.id_acordo_adicional = acordo_adicionais.id");
			$this->db->where("id_taxa",$this->input->post('id_taxa'));
			$quantidadeDeFiltros += 1;
		}	

		if( ! empty($_REQUEST['id_vendedor']) )
		{
			//Considera sempre apenas o vendedor de exporta��o			
			$this->db->where("clientes.responsavel",$this->input->post('id_vendedor'));
			$quantidadeDeFiltros += 1;
		}	
		
		if( ! empty($_REQUEST['id_customer']) )
		{
			//Conseidera sempre apenas o customer de exporta��o
			$this->db->where("clientes.customer_exportacao",$this->input->post('id_customer'));
			$quantidadeDeFiltros += 1;
		}	
		
		if( ! empty($_REQUEST['id_usuario_cadastro']) )
		{
			$this->db->where("acordo_adicionais.id_usuario_cadastro",$this->input->post('id_usuario_cadastro'));
			$quantidadeDeFiltros += 1;
		}	
		
		if( isset($_REQUEST['apenas_meus']) )
		{
			$this->db->where("acordo_adicionais.id_usuario_cadastro",$_SESSION['matriz'][7]);
			$quantidadeDeFiltros += 1;
		}	

		if( ! empty($_REQUEST['filial']) )
		{
			$this->db->where("SUBSTRING(numero_acordo,8,2)",$this->input->post('filial'));
			$quantidadeDeFiltros += 1;
		}		
		
		if( ! empty($_REQUEST['status']) )
		{
			
			if( $this->input->post('status') == '1' )
			{
				$ativo = "S";	
			}
			else 
			{
				$ativo = "N";
			}		
			
			$this->db->where("acordo_adicionais.ativo",$ativo);		
			$quantidadeDeFiltros += 1;
		}	
		
		$this->db->group_by("acordo_adicionais.id");
		
		if( $quantidadeDeFiltros < 1 )
		{
			show_error("Voc� n�o selecionou nenhum filtro para realizar a sua busca, volte a tela de buscas e refa�a a busca desta vez com filtros.");
		}	
		
		$rowSetAcordos = $this->db->get();
		
		if( $rowSetAcordos->num_rows() > 0 )
		{
			foreach( $rowSetAcordos->result() as $acordoEncontrado )
			{
				//Busca os cliente do acordo encontrado
				$acordo = new Acordo_Adicionais();
				$acordo->setId((int)$acordoEncontrado->id);

				$clientesAcordoModel->buscaClientesDoAcordoDeAdicionais($acordo);
				$stringDeClientesFormatados = "";
				
				foreach( $acordo->getClientes() as $cliente )
				{
					$stringDeClientesFormatados .= $cliente->getCNPJ() . " - " . $cliente->getRazao() . " -> " . $cliente->getCidade()->getNome() . "<br />";
				}	
				
				$resultadoBusca = array(
										"id_acordo" => $acordoEncontrado->id,
										"numero_acordo" => $acordoEncontrado->numero_acordo,
										"clientes" => $stringDeClientesFormatados,
										"data_inicial" => new DateTime($acordoEncontrado->inicio),
										"data_final" => new DateTime($acordoEncontrado->validade),
				);
				
				$acordosEncontrados[] = $resultadoBusca;				
			}
		}			
		
		return $acordosEncontrados;
	}
	
}