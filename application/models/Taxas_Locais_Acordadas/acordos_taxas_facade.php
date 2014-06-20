<?php

if( ! isset($_SESSION['matriz']) )
{
	session_start();
}

/**
 * Acordos_Taxas_Facade
 *
 * Esta classe é Façade que fornece um conjuntos de métodos simplificados
 * para ser usados nos controllers da aplição
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 20/05/2013
 * @version  versao 1.0
*/
class Acordos_Taxas_Facade extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
	}

	/**
	 * salvarAcordoTaxasLocais
	 *
	 * salva um acordo de taxas locais
	 *
	 * @name salvarAcordoTaxasLocais
	 * @access public
	 * @param Array $post
	 * @return int $id_acordo
	 */
	public function salvarAcordoTaxasLocais( Array $post )
	{
		$this->load->model("Clientes/cliente");
		$this->load->model("Tarifario/porto");
		$this->load->model("Taxas_Locais_Acordadas/conversor_taxas");
		$this->load->model("Desbloqueios/solicitacao_periodo_entity");
		$this->load->model("Desbloqueios/solicitacao_desbloqueio_periodo_facade");
		$this->load->model("Usuarios/filial");
		$this->load->model("Desbloqueios/Solicitacao_Taxa_Entity");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Desbloqueios/solicitacao_taxa");
		$this->load->model("Email/email");

		$entity = new Acordo_Taxas_Entity();

		$solicitacao = new Solicitacao_Taxa();

		/** Verifica de o id do acordo já veio definido no post, se sim então é uma alteração **/
		if( isset($post['id_acordo']) )
		{
			$entity->setId((int)$post['id_acordo']);
		}

		$entity->setSentido($post['sentido']);

		$entity->setObservacao($post['observacao_interna']);

		/** Data de inicio do acordo **/
		$entity->setInicio(new DateTime($post['inicio']));
		/** Validade do acordo **/
		$entity->setValidade(new DateTime($post['validade']));

		/** Cria os objetos do tipo porto que serão passados ao entity **/
		foreach( $post['portos_selecionados'] as $porto_selecionado )
		{
			/** Se vier zerado, então o acordo é para todos os portos **/
			if( $porto_selecionado == "0" )
			{

				$this->db->
						select("id_porto")->
						from("USUARIOS.portos")->
						where("ativo","S");

				$rs = $this->db->get();

				$rsPorto = $rs->result();

				foreach( $rsPorto as $id_porto )
				{
					$porto = new Porto();
					$porto->setId((int)$id_porto->id_porto);
					$entity->setPortos($porto);
				}

			}
			else
			{
				$porto = new Porto();
				$porto->setId((int)$porto_selecionado);
				$entity->setPortos($porto);
			}

		}

		/** Cria os objetos do tipo cliente para passar ao entity **/
		foreach( $post['clientes_selecionados'] as $cliente_selecionado )
		{
			$cliente = new Cliente();
			$cliente->setId((int)$cliente_selecionado);
			$entity->setClientes($cliente);
		}

		/** Deserializa e cria os objetos do tipo Taxa_Adicional **/
		$conversor = new Conversor_Taxas();

		foreach( $post['taxas_selecionadas'] as $taxa_selecionada )
		{
			$taxa_adicional = $conversor->deserializaTaxa($taxa_selecionada);

			$entity->setTaxas($taxa_adicional);
		}

		$acordo_model = new Acordo_Taxas_Locais_Model();

		$id_acordo = $acordo_model->save($entity);

		/**
		 * Verifica se existem desbloqueios pendentes para este item,
		 * se houver, então salva e envia os desbloqueios.
		 */
		if( isset($_SESSION['Desbloqueios'][$id_acordo]) )
		{
			foreach($_SESSION['Desbloqueios'][$id_acordo] as $solicitacoes_sessao)
			{
				//$solicitacao->solicitar_desbloqueio(unserialize($solicitacoes_sessao));
			}
		}

		unset($_SESSION['Desbloqueios']);
                        
		/** Verifica se o item da proposta está dentro da validade **/
		$solicitacao_facade = new Solicitacao_Desbloqueio_Periodo_Facade();
		$solicitacao_entity = new Solicitacao_Periodo_Entity();

		$solicitacao_entity->setDataSolicitacao(new DateTime());
		$solicitacao_entity->setIdItem((int)$id_acordo);
		$solicitacao_entity->setInicio($entity->getInicio()->format('Y-m-d H:i:s'));
		$solicitacao_entity->setValidade($entity->getValidade()->format('Y-m-d H:i:s'));
		$solicitacao_entity->setStatus("P");
		$solicitacao_entity->setUsuarioSolicitacao($_SESSION['matriz'][7]);
		$solicitacao_entity->setModulo("taxa_local");

		$necessita_desbloqueio = $solicitacao_facade->verificaSeEstaDentroDaValidade($solicitacao_entity,$entity->getSentido());
        
		if( $necessita_desbloqueio === TRUE )
		{
			//$solicitacao_facade->solicitaDesbloqueioPeriodo($solicitacao_entity);
		}
               
        /**
         * Salva o log do acordo através do memento
         */              
        $this->load->model("Taxas_Locais_Acordadas/Memento/care_taker");
               
        $care_taker = new Care_Taker();
        
        /**
         * Verifica se o numero do acordo ja foi informado na classe
         */
        if( $entity->getNumero() == NULL )
        {
            $this->db->select("numero")->from("CLIENTES.acordos_taxas_locais_globais")->where("id",$entity->getId());
            
            $rowSet = $this->db->get();
            
            if( $rowSet->num_rows() > 0 )
            {           
                $resultRow = $rowSet->row();
                
                $entity->setNumero($resultRow->numero);
            }
            
        }    
                
        $care_taker->SaveState($entity->CreateMemento());
        
		return $id_acordo;
	}

	/**
	 * recuperarAcordoTaxasLocais
	 *
	 * Busca um acordo de taxas locais baseado no id do acordo
	 *
	 * @name recuperarAcordoTaxasLocais
	 * @access public
	 * @param int $id_acordo
	 * @return Acordo_Taxas_Entity $acordo
	 */
	public function recuperarAcordoTaxasLocais($id_acordo = NULL)
	{

		if( is_null($id_acordo) || $id_acordo === 0 )
		{
			throw new InvalidArgumentException("Id do acordo inválido para pesquisar o acordo!");
		}

		/** Recupera os dados do acordo baseado no id **/
		$acordo_entity = new Acordo_Taxas_Entity();

		$acordo_entity->setId($id_acordo);

		$acordo_model = new Acordo_Taxas_Locais_Model();

		$acordo_model->findById($acordo_entity);

		/** Recupera os cliente do acordo baseado no id do acordo **/
		$this->load->model("Taxas_Locais_Acordadas/clientes_acordos_taxas_model");
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");

		$clientes_acordos_model = new Clientes_Acordos_Taxas_Model();

		$clientes_x_acordo = $clientes_acordos_model->findById($acordo_entity);

		/** Cria os objetos Cliente Registra-os no objeto Acordo **/
		$iterator = $clientes_x_acordo->getIterator();

		while( $iterator->valid() )
		{
			$cliente = new Cliente();
			$cliente->setId((int)$iterator->current()->getIdCliente());
			$cliente_model = new Cliente_Model();

			$cliente_model->findById($cliente);

			$acordo_entity->setClientes($cliente);

			$iterator->next();
		}

		/** Rcupera os portos do acordo Registra-os no objeto Acordo **/
		$this->load->model("Taxas_Locais_Acordadas/portos_acordos_model");

		$portos_acordo_model = new Portos_Acordos_Model();

		$portos_encontrados = $portos_acordo_model->findById($acordo_entity);

		$iterator = $portos_encontrados->getIterator();

		while( $iterator->valid() )
		{
			$acordo_entity->setPortos($iterator->current());

			$iterator->next();
		}

		/** Busca às taxas do acordo **/
		$this->load->model("Taxas_Locais_Acordadas/taxa_acordo_model");

		$taxa_acordo_model = new Taxa_Acordo_Model();
		$taxas_acordo = $taxa_acordo_model->findById($acordo_entity);

		$iterator = $taxas_acordo->getIterator();

		while( $iterator->valid() )
		{
			$acordo_entity->setTaxas($iterator->current());

			$iterator->next();
		}

		return $acordo_entity;

	}

	/**
	 * valida_acordos_cadastrados
	 *
	 * Verifica se já existem acordos cadastrados para os clientes selecionados
	 *
	 * @name valida_acordos_cadastrados
	 * @access public
	 * @param string $clientes
	 * @param Array $portos
	 * @param string $sentido
	 * @param string $validade
	 * @return ArrayObject
	 */
	public function valida_acordos_cadastrados( $clientes, $portos, $sentido, $inicio, $validade, $id_acordo = 0 )
	{

		/** Explode a string e cria os objetos do tipo cliente **/
		$ids_dos_clientes = explode(":", $clientes);

		foreach( $ids_dos_clientes as $k=>$value )
		{
			if( $value == "NULL" || $value == "" )
			{
				unset($ids_dos_clientes[$k]);
			}
		}

		if( ! is_array($ids_dos_clientes) || count($ids_dos_clientes) < 0 )
		{
			throw new InvalidArgumentException("Nenhum Cliente Informado!");
		}

		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cliente_model");

		$acordos_encontrados = Array();

		/** Verifica se existe acordo para algum dos clientes informados **/
		foreach( $ids_dos_clientes as $id_cliente )
		{

			$rs = $this->db->get_where("CLIENTES.clientes_x_acordos_taxas_locais_globais","id_cliente = ".$id_cliente);

			if( $rs->num_rows() < 1 )
			{
				continue;
			}

			foreach( $rs->result() as $acordo )
			{
				array_push($acordos_encontrados, $acordo->id_acordos_taxas_locais);
			}

		}

		/** Se nehum acordo foi encontrado para nenhum dos clientes, então sai da função **/
		if( count($acordos_encontrados) < 1 )
		{
			return FALSE;
		}

		unset($acordo);

		/** Retira às duplicações dos acordos encontrados **/
		$acordos_encontrados = array_unique($acordos_encontrados);

		$ids_portos_informados = explode(":",$portos);

		/** Retira o último elemento do Array que pode estar em branco **/
		foreach( $ids_portos_informados as $k=>$value )
		{
			if( $value == "NULL" || $value == "" )
			{
				unset($ids_portos_informados[$k]);
			}
		}

		$acordos_portos_encontrados = Array();

		/** Verifica se algum dos acordos encontrados contem algum dos portos selecionados **/
		foreach( $acordos_encontrados as $acordo )
		{

			$rs = $this->db->get_where("CLIENTES.portos_x_acordos_taxas_globais","id_acordo = ".$acordo);

			if( $rs->num_rows() < 1 )
			{
				continue;
			}

			foreach( $rs->result() as $porto )
			{
				if( in_array($porto->id_porto, $ids_portos_informados) )
				{
					array_push($acordos_portos_encontrados, $porto->id_acordo);
				}
			}

		}

		if( count($acordos_portos_encontrados) < 1 )
		{
			return FALSE;
		}

		unset($acordo);

		/** Retira às duplicações dos portos encontrados **/
		$acordos_portos_encontrados = array_unique($acordos_portos_encontrados);

		$data_validade = new DateTime($validade);
		$data_inicio = new DateTime($inicio);

		$acordos_duplicados = new ArrayObject(Array());

		$acordo_model = new Acordo_Taxas_Locais_Model();

		/** Verifica se algum desses acordos ainda está na validade **/
		foreach( $acordos_portos_encontrados as $acordos_validados )
		{
			$sql = "SELECT acordos_taxas_locais_globais.*
					FROM (CLIENTES.acordos_taxas_locais_globais)
					WHERE id =  '{$acordos_validados}'
					AND sentido =  '{$sentido}'
					GROUP BY id";

			$rs = $this->db->query($sql);

			if( $rs->num_rows() < 1 )
			{
				continue;
			}

			foreach( $rs->result() as $acordo_duplicado )
			{
				/** Desconsidera o próprio acordo, caso o mesmo esteja sendo alterado **/
				if($acordo_duplicado->id != $id_acordo)
				{
					/** Verifica se data informada não está contida no intervalo de datas informado **/
					if(
						$data_inicio->format('Y-m-d') <= $data_validade->format('Y-m-d') &&
					 	( ($acordo_duplicado->validade <= $data_inicio->format('Y-m-d')) ||
					 	($acordo_duplicado->data_inicial >= $data_validade->format('Y-m-d')) )
					)
					{

					}
					else
					{
						$acordo = new Acordo_Taxas_Entity();
						$acordo->setId((int)$acordo_duplicado->id);

						try {
							$acordo_model->findById($acordo);

							$acordos_duplicados->append($acordo);
						} catch (Exception $e) {
							//FIXME aqui nesta parte deveria validar os acordos duplicados, mas por algum motivo
							// o select nõ consegue encontrar o acordo que está duplicado buscando pelo id.
						}


					}

				}
				else
				{
					continue;
				}
			}

		}

		if( $acordos_duplicados->count() < 1 )
		{
			return FALSE;
		}

		return $acordos_duplicados;

	}
    
    public function revalidarAcordo(Acordo_Taxas_Entity $acordo, $meses = NULL)
    {
        $this->load->model("Desbloqueios/solicitacao_periodo_entity");
		$this->load->model("Desbloqueios/solicitacao_desbloqueio_periodo_facade");
                        
        switch($meses)
        {
            case 0:
                $this->db->where("id",$acordo->getId());
                $this->db->update("CLIENTES.acordos_taxas_locais_globais", Array("avisar_vencimento"=>"N"));
            break;
        
            case 12:
                $fimDoAno = new DateTime(date('Y')."-12-31");
                $acordo->setValidade($fimDoAno);
            break;
        
            default :
                $acordo->getValidade()->modify("+{$meses} Months");
        }
        
        if( $meses !== 0 )
        {
            /** Verifica se o item da proposta está dentro da validade **/
            $solicitacao_facade = new Solicitacao_Desbloqueio_Periodo_Facade();
            $solicitacao_entity = new Solicitacao_Periodo_Entity();

            $solicitacao_entity->setDataSolicitacao(new DateTime());
            $solicitacao_entity->setIdItem($acordo->getId());
            $solicitacao_entity->setInicio($acordo->getInicio()->format('Y-m-d H:i:s'));
            $solicitacao_entity->setValidade($acordo->getValidade()->format('Y-m-d H:i:s'));
            $solicitacao_entity->setStatus("P");
            $solicitacao_entity->setUsuarioSolicitacao($_SESSION['matriz'][7]);
            $solicitacao_entity->setModulo("taxa_local");

            $solicitacao_facade->solicitaDesbloqueioPeriodo($solicitacao_entity);
        }    
               
    }    

}//END CLASS