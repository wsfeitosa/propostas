<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}
/**
 * Solicitacao_Desbloqueio_Periodo_Facade
 *
 * Um Facade com um api simplificada para às operações de solicitação de desbloqueio de validades
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/06/2013
 * @version  versao 1.0
*/
class Solicitacao_Desbloqueio_Periodo_Facade extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Desbloqueios/solicitacao_periodo");
	}
	
	/**
	 * solicitaDesbloqueioPeriodo
	 *
	 * Solicita um desbloqueio de validade para uma proposta
	 *
	 * @name solicitaDesbloqueioPeriodo
	 * @access public
	 * @param  Solicitacao_Periodo_Entity $solicitacao
	 * @return boolean
	 */ 	
	public function solicitaDesbloqueioPeriodo(Solicitacao_Entity $solicitacao) 
	{		

		$validade = new DateTime($solicitacao->getValidade());

		$dados_para_salvar = Array(
									'id_item' =>  $solicitacao->getIdItem(),
									'validade_solicitada' => $validade->format('Y-m-d'),
									'status' => 'P',
									'id_usuario_solicitacao' => $_SESSION['matriz'][7],
									'data_solicitacao' => date('Y-m-d H:i:s'),
									'modulo' => $solicitacao->getModulo(),
							 );
		
		$incluido = $this->db->insert("CLIENTES.desbloqueios_validades",$dados_para_salvar);
		
		/** Muda o status do item da proposta **/
		$status = $this->db->update("CLIENTES.itens_proposta",Array("id_status_item" => '2'),"id_item_proposta = ".$solicitacao->getIdItem());
		
		/** Envia a solicitação **/
		$solicitacao_periodo = new Solicitacao_Periodo();
		$solicitacao_periodo->solicitar_desbloqueio($solicitacao);
		
		return $incluido;						
	}

	/**
	 * solicitaDesbloqueioPeriodoGrupo
	 *
	 * Solicita um desbloqueio de validade para uma proposta inteira de uma única vez
	 *
	 * @name solicitaDesbloqueioPeriodo
	 * @access public
	 * @param  $array_solicitacoes
	 * @return boolean
	 */
	public function solicitaDesbloqueioPeriodoGrupo(Array $array_solicitacoes)
	{

		foreach ($array_solicitacoes as $solicitacao) 
		{

			$validade = new DateTime($solicitacao->getValidade());

			$dados_para_salvar = Array(
										'id_item' =>  $solicitacao->getIdItem(),
										'validade_solicitada' => $validade->format('Y-m-d'),
										'status' => 'P',
										'id_usuario_solicitacao' => $_SESSION['matriz'][7],
										'data_solicitacao' => date('Y-m-d H:i:s'),
										'modulo' => $solicitacao->getModulo(),
								 );
		
			$incluido = $this->db->insert("CLIENTES.desbloqueios_validades",$dados_para_salvar);
			
			/** Muda o status do item da proposta **/
			$status = $this->db->update("CLIENTES.itens_proposta",Array("id_status_item" => '2'),"id_item_proposta = ".$solicitacao->getIdItem());
							
		}

		/** Envia a solicitação **/
		$solicitacao_periodo = new Solicitacao_Periodo();
		$solicitacao_periodo->solicitar_desbloqueio_grupo($array_solicitacoes);

	}
	
	/**
	 * verificaSeEstaDentroDaValidade
	 *
	 * Verifica se uma proposta ou acordo de taxas locais está dentro da data limite
	 *
	 * @name verificaSeEstaDentroDaValidade
	 * @access public
	 * @param string $entity 
	 * @return boolean
	 */ 	
	public function verificaSeEstaDentroDaValidade(Solicitacao_Entity $entity, $sentido ) 
	{
						
		include_once $_SERVER['DOCUMENT_ROOT'] . "/Libs/ultimo_dia_mes.php";
			
		$ultimo_dia_mes = ultimo_dia_mes(date('m'),date('Y'));
			
		$data_limite = new DateTime($ultimo_dia_mes);
		
		$validade = new DateTime($entity->getValidade());
						
		if( strtoupper($sentido) == "IMP" )
		{									
			if( strtotime($validade->format('Y-m-d')) > strtotime($data_limite->format('Y-m-d')) )
			{
				return TRUE;
			}	
			else
			{
				return FALSE;
			}				
		}
		else
		{
			if( strtotime($validade->format('Y-m-d')) > strtotime($data_limite->format('Y-m-d')) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}						
		}		
				
	}
	
}//END FILE