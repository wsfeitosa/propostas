<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	

/**
 * Solicitacao_Desbloqueio_Taxa_Facade
 *
 * Um Facade com um api simplificada para às operações de solicitação de desbloqueio de taxas
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 10/06/2013
 * @version  versao 1.0
*/
class Solicitacao_Desbloqueio_Taxa_Facade extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Taxas/serializa_taxas");		
		$this->load->model("Desbloqueios/solicitacao_taxa_entity");
		$this->load->model("Desbloqueios/solicitacao_taxa");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
	}
	
	/**
	 * verificaItemSessao
	 *
	 * Verifica se a taxa selecionada existe realmente na sessão do usuário, em caso negativo
	 * exibe uma mensagem de erro
	 *
	 * @name verificaItemSessao
	 * @access public
	 * @param Taxa $taxa
	 * @return void
	 */	
	public function verificaItemSessao( Taxa $taxa )
	{
		
		if( ! isset($_SESSION['itens_proposta'][$taxa->getIdItem()]) )
		{
			$msg = "Impossivel recuperar o item da sessão para solicitar o desbloqueio da taxa!";
			log_message('error',$msg);
			show_error($msg);
		}
					
	}
	
	/**
	 * verificaTaxaAbaixoDoValor
	 *
	 * Verifica se o valor que usuário solicitou está acima ou abaixo da taxa cadastrada que está na sessão
	 *
	 * @name verificaTaxaAbaixoDoValor
	 * @access public
	 * @param Taxa $taxa
	 * @return boolean
	 */ 	
	public function verificaTaxaAbaixoDoValor(Taxa $taxa)
	{		
		$this->load->model("Taxas/serializa_taxas");
			
		$item = $_SESSION['itens_proposta'][$taxa->getIdItem()];
		
		if( is_null($item) || ! is_array($item) )
		{
			show_error("Não foi possivel recuperar a taxa da sessão do usuário!");
		}	
		
		switch( get_class($taxa) )
		{
			case "Taxa_Local":
				$chave_sessao_valores = 'taxas_locais';
				$chave_sessao_label = 'labels_taxas_locais';
			break;

			case "Taxa_Adicional":
				$chave_sessao_valores = 'frete_adicionais';
				$chave_sessao_label = 'labels_frete_adicionais';
			break;
			
			default:
				show_error("Tipo de Taxa Invalido Para solicitar o desbloqueio!");
		}
		
		$serializador = new Serializa_Taxas();
		
		/** Desializa às taxas da sessão **/
		$taxas_deserializadas = $serializador->deserializaTaxasProposta($item[$chave_sessao_valores],get_class($taxa));
		
		/** 
		 * percorre o array com às taxas da sessão para encontrar a taxa equivalente a taxa que foi informada 
		 * para o desbloqueio.
		 */
		foreach($taxas_deserializadas as $index => $taxa_sessao)
		{
			if( $taxa_sessao->getId() == $taxa->getId() )
			{
				/** Verifica se a moeda foi alterada **/
				if( ( $taxa->getMoeda()->getId() != $taxa_sessao->getMoeda()->getId() ) || ( $taxa->getUnidade()->getId() != $taxa_sessao->getUnidade()->getId() ) )
				{					
					/** Solicita o desbloqueio **/
					return TRUE;
				}				
				else
				{	
					if( (float)$taxa->getValor() >= (float)$taxa_sessao->getValor() )
					{
						/** Altera na sessão com o novo item **/
						$taxas_deserializadas[$index] = $taxa;	
						
						$taxas_serializadas = $serializador->SerializaTaxasParaSessao($taxas_deserializadas);
						
						$item[$chave_sessao_valores] = $taxas_serializadas['value_taxas'];
						$item[$chave_sessao_label] = $taxas_serializadas['label_taxas'];
						
						$_SESSION['itens_proposta'][$taxa->getIdItem()] = $item;
						
						return FALSE;
					}	
					else
					{
						/** Solicita o desbloqueio **/
						return TRUE;
					}	
				}	
			}	
		}	
								
	}
	
	/**
	 * solicitaDesbloqueioTaxa
	 *
	 * Solicita o desbloqueio de uma taxa
	 *
	 * @name solicitaDesbloqueioTaxa
	 * @access public
	 * @param Taxa $taxa
	 * @return boolean
	 */ 	
	public function solicitaDesbloqueioTaxa(Taxa $taxa, $modulo, $nota, $observacao) 
	{
				
		$this->load->model("Desbloqueios/solicitacao_taxa_entity");		
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
			
		$entity = new Solicitacao_Taxa_Entity();
		$usuario_solicitacao = new Usuario();
		$usuario_model = new Usuario_Model();		
		
		$usuario_solicitacao->setId((int)$_SESSION['matriz'][7]);
		$usuario_model->findById($usuario_solicitacao);
			
		$entity->setDataSolicitacao(new DateTime())
				->setTaxa($taxa)
				->setUsuarioSolicitacao($usuario_solicitacao)
				->setStatus("P")
				->setNotaExportacao($nota)
				->setNotaImportacao($nota)
				->setObservacao($observacao)
				->setModulo($modulo);
				
		$_SESSION['Desbloqueios'][$taxa->getIdItem()][$taxa->getId()] = serialize($entity);				
	}
	
}//END CLASS