<?php
/**
 * Buscador Taxas Locais
 *
 * Esta classe � um fa�ade, na verdade ela funciona como interface para utiliza��o
 * de v�rias outras classes que em comjunto ir�o buscar �s taxas padr�es dos portos 
 *
 * @package Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/05/2013
 * @version  versao 1.0
*/
class Buscador_Taxas_Locais extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Taxas/taxa_local_model");
		$this->load->model("Clientes/define_classificacao");		
	}
	
	/**
	 * buscarTaxasLocais
	 *
	 * Fun��o que busca �s taxas locais dos portos
	 *
	 * @name buscarTaxasLocais
	 * @access public
	 * @param 
	 * @return int
	 */ 	
	public function buscarTaxasLocais( $porto, $clientes, $sentido ) 
	{
						
		/** 
		 * Verifica se todos os clientes s�o do mesmo tipo direto ou forwarder 
		 * se forem de tipos diferentes ent�o n�o pode prosseguir 
		 **/			
		$cliente_model = new Cliente_Model();
		
		try{
			$existem_cliente_com_classificacoes_divergentes = $cliente_model->verificarModalidadeDosClientes($clientes);			
		}catch ( Exception $e ) {			
			log_message('error',$e->getMessage()."\r\n".$e->getTraceAsString());
			show_error($e->getMessage());
		}
						
		if( ! $existem_cliente_com_classificacoes_divergentes )
		{			
			$message = "Existem Clientes com diferentes classifica��es (Direto e Forwarder) no mesmo acordo e isso n�o � permitido!";
			$taxas_locais_encontradas = new ArrayObject(Array("error" => utf8_encode($message)));
			return $taxas_locais_encontradas;
		}
		
		/** Define a classifica��o dos clientes **/
		$clientes_selecionados = new ArrayObject(explode(":", $clientes));
		
		$definidor_classificacao = new Define_Classificacao();
		
		$cliente_para_definir_classificacao = new Cliente();
		$cliente_para_definir_classificacao->setId((int)$clientes_selecionados->offsetGet(0));

		$cliente_model->findById($cliente_para_definir_classificacao);
		
		$classificacao_considerada = $definidor_classificacao->ObterClassificacao($cliente_para_definir_classificacao);

		$taxa_local_model = new Taxa_Local_Model();
		
		$taxas_locais_padrao_porto = $taxa_local_model->ObterTaxasLocais($sentido, "LCL", $classificacao_considerada, $porto);
		
		$taxas_locais_encontradas = new ArrayObject($taxas_locais_padrao_porto);
		
		return $taxas_locais_encontradas;		
		
	}
		
}