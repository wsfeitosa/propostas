<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Acordo_Taxas_Locais_Model
 *
 * Classe que realiza às operações de banco de dados relativas à os
 * dados do acordo de taxas locais 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/05/2013
 * @version  versao 1.0
*/
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/database_operations.php";

class Acordo_Taxas_Locais_Model extends CI_Model implements Database_Operations {
	
	public function __construct() {
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/gera_numero_acordo");
		$this->load->model("Taxas_Locais_Acordadas/taxa_acordo_model");
	}
	
	/**
	 * save
	 *
	 * Salva um acordo de taxas locais
	 *
	 * @name save
	 * @access public
	 * @param Bean $acordo
	 * @return boolean
	 */ 	
	public function save( Entity $acordo )
	{
		
		if( ! isset($_SESSION['matriz']) )
		{
			show_error("Impossivel continuar, sua sessão expirou");
		}	
		
		$id_acordo = $acordo->getId();
		
		if( isset($id_acordo) && ! is_null($id_acordo) ) //Update
		{
			
			unset($id_acordo);
			
			$dados_para_salvar = Array(					
					"observacoes_internas" => strtoupper($acordo->getObservacao()),
					"data_inicial" => $acordo->getInicio()->format("Y-m-d"),
					"validade" => $acordo->getValidade()->format("Y-m-d"),
					"id_usuario_ultima_alteracao" => $_SESSION['matriz'][7],
					"data_ultima_alteracao" => date('Y-m-d H:i:s'),					
			);
									
			$acordo_salvo = $this->db->update("CLIENTES.acordos_taxas_locais_globais", $dados_para_salvar, "id = {$acordo->getId()}");
			
			$this->LimparTabelasAntesDaAlteracao($acordo);
			
			$id_acordo_salvo = $acordo->getId();
			
		}	
		else //Save
		{
			$numero_gerador = new Gera_Numero_Acordo();
			
			$numero_acordo = $numero_gerador->gerarNumeroAcordo();
			
			$dados_para_salvar = Array(
					"numero" => $numero_acordo,
					"sentido" => $acordo->getSentido(),
					"observacoes_internas" => strtoupper($acordo->getObservacao()),
					"data_inicial" => $acordo->getInicio()->format("Y-m-d"),
					"validade" => $acordo->getValidade()->format("Y-m-d"),
					"id_usuario_cadastro" => $_SESSION['matriz'][7],
					"data_cadastro" => date('Y-m-d H:i:s'),
					"registro_ativo" => "S",
			);
			
			$acordo_salvo = $this->db->insert("CLIENTES.acordos_taxas_locais_globais", $dados_para_salvar);
			
			$id_acordo_salvo = $this->db->insert_id();
			$acordo->setId((int)$id_acordo_salvo);
			
		}	
								
		if( ! $acordo_salvo )
		{
			$message = "Não foi possível salvar o acordo de taxas locais";
			log_message('error',$message);
			throw new Exception($message);
		}	
						
		/** Salva os cliente do acordo de taxas **/
		$this->load->model("Taxas_Locais_Acordadas/clientes_acordos_taxas_model");
		$this->load->model("Taxas_Locais_Acordadas/cliente_acordo_entity");
		
		foreach( $acordo->getClientes() as $cliente )
		{			
			$acordo_cliente = new Cliente_Acordo_Entity();
			$acordo_cliente->setIdAcordo((int)$id_acordo_salvo);
			$acordo_cliente->setIdCliente((int)$cliente->getId());
			
			$acordo_cliente_model = new Clientes_Acordos_Taxas_Model();
			$acordo_cliente_model->save($acordo_cliente);
		}	
		
		/** Salvar os portos dos acordos **/
		$this->load->model("Taxas_Locais_Acordadas/portos_acordos_entity");
		$this->load->model("Taxas_Locais_Acordadas/portos_acordos_Model");
		
		foreach( $acordo->getPortos() as $porto )
		{
			$portos_acordos_entity = new Portos_Acordos_Entity();
			$portos_acordos_entity->setAcordo($acordo);
			$portos_acordos_entity->setPorto($porto);
			
			$portos_acordos_model = new Portos_Acordos_Model();
			$portos_acordos_model->save($portos_acordos_entity);
		}	
		
		/** Salva às taxas do acordo **/
		$model_taxas = new Taxa_Acordo_Model();
		
		foreach( $acordo->getTaxas() as $taxa )
		{
			$taxa->setIdItem((int)$id_acordo_salvo);
			$model_taxas->save($taxa);
		}	
		
		return $id_acordo_salvo;
		
	}
	
	public function findById( Entity $acordo )
	{		
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			throw new InvalidArgumentException("Id do acordo não definido na entidade para realizar a busca");
		}	
				
		$rs = $this->db->get_where("CLIENTES.acordos_taxas_locais_globais", "id = {$acordo->getId()} AND registro_ativo = 'S'");
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Nenhum Registro encontrado");
		}	
		
		$resultSetAcordo = $rs->row();
		
		$acordo->setNumero($resultSetAcordo->numero);
		$acordo->setInicio(new DateTime($resultSetAcordo->data_inicial));
		$acordo->setValidade(new DateTime($resultSetAcordo->validade));
		$acordo->setSentido($resultSetAcordo->sentido);
		$acordo->setObservacao($resultSetAcordo->observacoes_internas);		
		$acordo->setRegistroAtivo($resultSetAcordo->registro_ativo);

		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		
		$usuario_inclusao = new Usuario();
		$usuario_inclusao->setId((int)$resultSetAcordo->id_usuario_cadastro);
		
		$usuario_model = new Usuario_Model();
		$usuario_model->findById($usuario_inclusao);
		
		$acordo->setUsuarioInclusao($usuario_inclusao);
		
		$data_inclusao = new DateTime($resultSetAcordo->data_cadastro);
		$acordo->setDataInclusao($data_inclusao);
		
		$usuario_alteracao = new Usuario();
			
		$usuario_alteracao->setId((int)$resultSetAcordo->id_usuario_ultima_alteracao);
		
		$data_ultima_alteracao = new DateTime($resultSetAcordo->data_ultima_alteracao);
			
		$acordo->setUsuarioAlteracao($usuario_alteracao);
		$acordo->setDataAlteracao($data_ultima_alteracao);
		
		if( ! empty($resultSetAcordo->id_usuario_ultima_alteracao) )
		{
									
			$usuario_model->findById($usuario_alteracao);
						
		}				
	}
	
	/**
	 * LimparTabelasAntesDaAlteracao
	 *
	 * Limpa todas às tabelas relacionadas a os acordos antes de efetuar um update
	 *
	 * @name LimparTabelasAntesDaAlteracao
	 * @access protected
	 * @param $acordo Acordo_Entity
	 * @return boolean $tabelas_limpas
	 */
	protected function LimparTabelasAntesDaAlteracao( Entity $acordo )
	{
		$id_acordo = $acordo->getId();
		
		if( empty($id_acordo) )
		{
			throw new InvalidArgumentException("Id do acordo não definido na entidade para realizar a busca");
		}
		
		$tabelas_limpas = FALSE;
		
		/** Limpa a tabela clientes_x_acordos_taxas_locais_globais **/
		$tabelas_limpas = $this->db->delete("CLIENTES.clientes_x_acordos_taxas_locais_globais", "id_acordos_taxas_locais = {$acordo->getId()}");
		
		/** Limpa a tabela portos_x_acordos_taxas_globais **/
		$tabelas_limpas = $this->db->delete("CLIENTES.portos_x_acordos_taxas_globais", "id_acordo = {$acordo->getId()}");
		
		/** Limpa a tabela taxas_x_acordos_taxas_locais_globais **/
		$tabelas_limpas = $this->db->delete("CLIENTES.taxas_x_acordos_taxas_locais_globais", "id_acordos_taxas_locais = {$acordo->getId()}");
		
		return $tabelas_limpas;
		
	}
	
	public function update( Entity $bean ){}
	public function delete( Entity $bean ){}
		
}//END CLASS