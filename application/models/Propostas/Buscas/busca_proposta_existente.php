<?php
/**
 * Busca_Proposta_Existente
 *
 * Esta classe verifica se já existe algum item de proposta cadastrado para determinado cliente.
 *
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 04/04/2013
 * @name Busca_Proposta_Existente
 * @version 1.0
 */
include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/Buscas/finder.php";

class Busca_Proposta_Existente extends CI_Model implements Finder {
	
	public function __construct(){
		parent::__construct();		
	}
	
	/**
	  * verificarSeClienteJaPossuiPropostaValida
	  * 
	  * verifica se existe alguma proposta valida para um determinado cliente em uma
	  * determinada rota (Tarifário)
	  * 
	  * @name verificarSeClienteJaPossuiPropostaValida
	  * @access public
	  * @param Cliente $cliente
	  * @param Tarifario $tarifario
	  * @return boolean
	  */
	public function verificarSeClienteJaPossuiPropostaValida( Cliente $cliente, Tarifario $tarifario, DateTime $inicio, DateTime $validade )
	{

		$this->db->
				select("clientes_x_propostas.id, itens_proposta.data_inicial, itens_proposta.validade, itens_proposta.numero_proposta")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId())->
				where("propostas.tipo_proposta !=","proposta_spot")->
				where("propostas.tipo_proposta !=","proposta_especial")->
				where("propostas.tipo_proposta !=","proposta_nac");

		$rs = $this->db->get();

		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		else
		{
			/** Testa o período de validade da proposta **/
			$this->load->helper(array('intervalo_datas'));

			$existe_conflito = FALSE;

			//$row = $rs->row();

			foreach ($rs->result() as $row) 
			{
				$existe_conflito = verifica_intervalo($row->data_inicial,$row->validade,$inicio->format('Y-m-d'),$validade->format('Y-m-d'));				
			
				if( $existe_conflito == true )
				{
					return $row->numero_proposta;
				}	
			}

			return $existe_conflito;
		}	
				
	}
    
    /**
	  * verificarSeClienteJaPossuiPropostaValidaERetornaId
	  * 
	  * verifica se existe alguma proposta valida para um determinado cliente em uma
	  * determinada rota (Tarifário), e se houver retorna o id da proposta
	  * 
	  * @name verificarSeClienteJaPossuiPropostaValidaERetornaId
	  * @access public
	  * @param Cliente $cliente
	  * @param Tarifario $tarifario
	  * @return boolean
	  */
	public function verificarSeClienteJaPossuiPropostaValidaERetornaId( Cliente $cliente, Tarifario $tarifario, DateTime $inicio, DateTime $validade )
	{
		
		$this->db->
				select("itens_proposta.id_item_proposta, itens_proposta.data_inicial, itens_proposta.validade")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId())->
				where("itens_proposta.validade >=",date('Y-m-d'))->
				where("propostas.tipo_proposta !=","proposta_spot")->
				where("propostas.tipo_proposta !=","proposta_especial")->
				where("propostas.tipo_proposta !=","proposta_nac");
        		
		$rs = $this->db->get();
        
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		else
		{			
			/** Testa o período de validade da proposta **/
			$this->load->helper(array('intervalo_datas'));

			$row = $rs->row();

			$existe_conflito = verifica_intervalo($row->data_inicial,$row->validade,$inicio->format('Y-m-d'),$validade->format('Y-m-d'));
			//FIXME está com um problema no model que verifica o intervalo de datas
			$existe_conflito = TRUE;

			if( $existe_conflito )
			{
				return $rs->row()->id_item_proposta;
			}
			else
			{
				return FALSE;
			}	
			
		}	
				
	}
	
    /**
     * buscaPorItensDuplicadosDeUmaNovaProposta
     * 
     * Busca por itens de uma proposta que já estejam cadastrados em outras propostas antes de salvar
     * 
     * @name buscaPorItensDuplicadosDeUmaNovaProposta
     * @access public
     * @param Proposta $proposta     
     * @return ArrayObject $itens_duplicados
     */
    public function buscaPorItensDuplicadosDeUmaNovaProposta(Proposta $proposta)
    {
        
        $clientes_da_proposta = $proposta->getClientes();
                        
        if(count($clientes_da_proposta) < 1)
        {
            $error_message = "Impossivel validar a proposta, nenhum cliente foi informado!";
            log_message('error',$error_message);
            throw new Exception($error_message);
        }
        
        $itens_da_proposta = $proposta->getItens();
        
        if(count($itens_da_proposta) < 1)
        {
            $error_message = "Impossivel validar a proposta, nenhum item informado!";
            log_message('error',$error_message);
            throw new Exception($error_message);
        }
        
        $itens_duplicados = new ArrayObject(Array());
        
        $itensDaProposta = $proposta->getItens();
        
        foreach ($clientes_da_proposta as $cliente)
        {
            
            foreach ($itensDaProposta as $item)
            {
            	
                $id_item_excluir = $this->verificarSeClienteJaPossuiPropostaValidaERetornaId($cliente, $item->getTarifario(), new DateTime($item->getInicio()), new DateTime($item->getValidade()));
                                
                if( $id_item_excluir !== FALSE && $item->getId() != $id_item_excluir && get_class($proposta) == "Proposta_NAC" && get_class($proposta) == "Proposta_Spot" )
                {
                    $itens_duplicados->append($id_item_excluir);
                }    
                
            }
            
        }
        
        return $itens_duplicados;
        
    }

    /**
	  * verificarSeClienteJaPossuiPropostaValidaNacERetornaId
	  * 
	  * verifica se existe alguma proposta valida para um determinado cliente em uma
	  * determinada rota (Tarifário), e se houver retorna o id da proposta
	  * 
	  * @name verificarSeClienteJaPossuiPropostaValidaERetornaId
	  * @access public
	  * @param Cliente $cliente
	  * @param Tarifario $tarifario
	  * @return boolean
	  */
	public function verificarSeClienteJaPossuiPropostaValidaNacERetornaId( Cliente $cliente, Tarifario $tarifario, DateTime $inicio, DateTime $validade )
	{
		
		$this->db->
				select("itens_proposta.id_item_proposta, itens_proposta.data_inicial, itens_proposta.validade")->
				from("CLIENTES.clientes_x_propostas")->
				join("CLIENTES.itens_proposta","itens_proposta.id_proposta = clientes_x_propostas.id_proposta")->
				join("CLIENTES.propostas","itens_proposta.id_proposta = propostas.id_proposta")->
				where("id_tarifario_pricing",$tarifario->getId())->
				where("id_cliente",$cliente->getId())->
				where("itens_proposta.validade >=",date('Y-m-d'))->
				where("propostas.tipo_proposta =","proposta_nac");
        		
		$rs = $this->db->get();
        
		if( $rs->num_rows() < 1 )
		{
			return FALSE;
		}	
		else
		{            

			$encontrados = Array();
			
			/** Testa o período de validade da proposta **/
			$this->load->helper(array('intervalo_datas'));

			$row = $rs->row();

			$existe_conflito = verifica_intervalo($row->data_inicial,$row->validade,$inicio->format('Y-m-d'),$validade->format('Y-m-d'));
			
			$existe_conflito = TRUE;
			
			if( $existe_conflito )
			{
				return $rs->row()->id_item_proposta;
			}
			else
			{
				return FALSE;
			}	
		}	
				
	}
    
}//END LASS