<?php

if( ! isset($_SESSION['matriz']) )
{
    session_start();
}    

/**
 * Sessao
 *
 * Classe que manipula os dados da sessão que serão utilizados durante a execução da aplição,
 * em especial a manutenção de itens das propostas na sessão do PHP 
 *
 * @package Adaptadores
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 11/03/2013
 * @name Sessao
 * @version 1.0
 */
include_once "/var/www/html/allink/Libs/remove_acentos.php";

class Sessao extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();	
        $this->load->library("Scoa/url");
        $this->erro = 0;
	}
	
	protected $id_tarifario = "NULL", $mercadoria = "", $pp = NULL, $imo = NULL,
	$cc = NULL, $inicio = NULL, $validade = NULL, $peso = NULL, $cubagem = NULL, $volumes = NULL,
	$origem = NULL, $embarque = NULL, $desembarque = NULL, $destino = NULL,
	$un_origem = NULL, $un_embarque = NULL, $un_desembarque = NULL, $un_destino = NULL,
	$frete_adicionais = NULL, $labels_fretes_adicionais = NULL, $taxas_locais = NULL, $labels_taxas_locais = NULL,
	$observacao_interna = "", $observacao_cliente = "", $id_item = NULL, $anti_cache = NULL, $erro = 0;
	
    public function getIdTarifario() {
        return $this->id_tarifario;
    }

    public function setIdTarifario($id_tarifario) {
        $this->id_tarifario = $id_tarifario;
        return $this;
    }

    public function getMercadoria() {
        return $this->mercadoria;
    }

    public function setMercadoria($mercadoria) {
        $this->mercadoria = $mercadoria;
        return $this;
    }

    public function getPp() {
        return $this->pp;
    }

    public function setPp($pp) {
        $this->pp = $pp;
        return $this;
    }

    public function getCc() {
        return $this->cc;
    }

    public function setCc($cc) {
        $this->cc = $cc;
        return $this;
    }
    
    public function setInicio($inicio){
    	$this->inicio = $inicio;
    	return $this;
    }
    
    public function getInicio(){
    	return $this->inicio;
    }
    
    public function getValidade() {
        return $this->validade;
    }

    public function setValidade($validade) {
        $this->validade = $validade;
        return $this;
    }

    public function getPeso() {
        return $this->peso;
    }

    public function setPeso($peso) {
        $this->peso = $peso;
        return $this;
    }

    public function getCubagem() {
        return $this->cubagem;
    }

    public function setCubagem($cubagem) {
        $this->cubagem = $cubagem;
        return $this;
    }

    public function getVolumes() {
        return $this->volumes;
    }

    public function setVolumes($volumes) {
        $this->volumes = $volumes;
        return $this;
    }

    public function getOrigem() {
        return $this->origem;
    }

    public function setOrigem($origem) {
        $this->origem = $origem;
        return $this;
    }

    public function getEmbarque() {
        return $this->embarque;
    }

    public function setEmbarque($embarque) {
        $this->embarque = $embarque;
        return $this;
    }

    public function getDesembarque() {
        return $this->desembarque;
    }

    public function setDesembarque($desembarque) {
        $this->desembarque = $desembarque;
        return $this;
    }

    public function getDestino() {
        return $this->destino;
    }

    public function setDestino($destino) {
        $this->destino = $destino;
        return $this;
    }

    public function getUnOrigem() {
        return $this->un_origem;
    }

    public function setUnOrigem($un_origem) {
        $this->un_origem = $un_origem;
        return $this;
    }

    public function getUnEmbarque() {
        return $this->un_embarque;
    }

    public function setUnEmbarque($un_embarque) {
        $this->un_embarque = $un_embarque;
        return $this;
    }

    public function getUnDesembarque() {
        return $this->un_desembarque;
    }

    public function setUnDesembarque($un_desembarque) {
        $this->un_desembarque = $un_desembarque;
        return $this;
    }

    public function getUnDestino() {
        return $this->un_destino;
    }

    public function setUnDestino($un_destino) {
        $this->un_destino = $un_destino;
        return $this;
    }

    public function getFreteAdicionais() {
        return $this->frete_adicionais;
    }

    public function setFreteAdicionais($frete_adicionais) {
        $this->frete_adicionais = $frete_adicionais;
        return $this;
    }

    public function getLabelsFretesAdicionais() {
        return $this->labels_fretes_adicionais;
    }

    public function setLabelsFretesAdicionais($labels_fretes_adicionais) {
        $this->labels_fretes_adicionais = $labels_fretes_adicionais;
        return $this;
    }

    public function getTaxasLocais() {
        return $this->taxas_locais;
    }

    public function setTaxasLocais($taxas_locais) {
        $this->taxas_locais = $taxas_locais;
        return $this;
    }

    public function getLabelsTaxasLocais() {
        return $this->labels_taxas_locais;
    }

    public function setLabelsTaxasLocais($labels_taxas_locais) {
        $this->labels_taxas_locais = $labels_taxas_locais;
        return $this;
    }

    public function getObservacaoInterna() {
        return $this->observacao_interna;
    }

    public function setObservacaoInterna($observacao_interna) {
        $this->observacao_interna = $observacao_interna;
        return $this;
    }

    public function getObservacaoCliente() {
        return $this->observacao_cliente;
    }

    public function setObservacaoCliente($observacao_cliente) {
        $this->observacao_cliente = $observacao_cliente;
        return $this;
    }

    public function getIdItem() {
        return $this->id_item;
    }

    public function setIdItem($id_item) {
        $this->id_item = $id_item;
        return $this;
    }

    public function getAnti_cache() {
        return $this->anti_cache;
    }

    public function setAntiCache($anti_cache) {
        $this->anti_cache = $anti_cache;
        return $this;
    }
    
    public function getErro() {
        return $this->erro;
    }

    public function setErro($erro) {
        $this->erro = $erro;
        return $this;
    }

    public function setImo($imo){
        $this->imo = $imo;
        return $this;
    }

    public function getImo(){
        return $this->imo;
    }
        
    /**
     * inserirItemNaSessao
     * 
     * inserirItemNaSessao
     * 
     * @name inserirItemNaSessao
     * @access public     
     * @return int $index Indice do item na sessão
     */
    public function inserirItemNaSessao() 
    {
        
        if( $this->pp == "PP" )
        {
            $pp = TRUE;
        }    
        else
        {
            $pp = FALSE;
        }    
        
        if( $this->cc == "CC" )
        {
            $cc = TRUE;
        }    
        else
        {
            $cc = FALSE;
        }
        
        try{      
        	     
            $item_proposta = Array(
                                    "id_item" => $this->id_item,
                                    "id_tarifario" => $this->id_tarifario,
                                    "mercadoria" => remove_acentos($this->mercadoria), 
                                    "pp" => $pp,
                                    "cc" => $cc,
                                    "imo" => $this->imo,
            						"inicio" => $this->inicio,
                                    "validade" => $this->validade,
                                    "peso" => $this->peso,
                                    "cubagem" => $this->cubagem,
                                    "volumes" => $this->volumes,
                                    "origem" => remove_acentos($this->origem),
                                    "embarque" => remove_acentos($this->embarque),
                                    "desembarque" => remove_acentos($this->desembarque),
                                    "destino" => remove_acentos($this->destino),
                                    "un_origem" => $this->un_origem,
                                    "un_embarque" => $this->un_embarque,
                                    "un_desembarque" => $this->un_desembarque,
                                    "un_destino" => $this->un_destino,
                                    "frete_adicionais" => $this->frete_adicionais,
                                    "labels_frete_adicionais" => remove_acentos($this->labels_fretes_adicionais),
                                    "taxas_locais" => $this->taxas_locais,
                                    "labels_taxas_locais" => remove_acentos($this->labels_taxas_locais),
                                    "observacao_interna" => remove_acentos($this->observacao_interna),
                                    "observacao_cliente" => remove_acentos($this->observacao_cliente),	
           		
            );
           
        } catch (Exception $e) {           
            show_error($e->getMessage());
        }
        
        $this->excluirItemDaSessao();
        
        if( $this->id_item == NULL || $this->id_item == 0 )
		{
			$_SESSION['itens_proposta'][] = $item_proposta;
			
			end($_SESSION['itens_proposta']);
			$id_item_sessao = key($_SESSION['itens_proposta']);
            $this->id_item = $id_item_sessao;
		}
		else
		{
			$_SESSION['itens_proposta'][$this->id_item] = $item_proposta;
			
			$id_item_sessao = $this->id_item;
		}
		
        return $id_item_sessao;
        
    }//END FUNCTION
	
    /**
     * recuperarItemDaSessao
     * 
     * Recupera um item que estão armazenado na sessão
     * 
     * @name recuperarItemDaSessao
     * @access public
     * @param int $index Indice do item que deseja recuperar
     * @return mixed $item
     */
    public function recuperarItemDaSessao($itemIndex)
    {
        $erro = 0;
		// Verifica se o item existe na posicao solicitada, senão muda o status de erro
		if( ! isset($_SESSION['itens_proposta'][$itemIndex]) )
		{
			$this->erro = 1;
			$item_selecionado = Array(
                                        "id_item" => NULL,
                                        "id_tarifario" => NULL,
										"mercadoria" => NULL,
										"pp" => NULL,
										"cc" => NULL,
                                        "imo" => NULL,
										"inicio" => NULL,
										"validade" => NULL,
										"peso" => NULL,
										"cubagem" => NULL,
										"volumes" => NULL,
										"origem" => NULL,
										"embarque" => NULL,
										"desembarque" => NULL,
										"destino" => NULL,
										"un_origem" => NULL,
										"un_embarque" => NULL,
										"un_desembarque" => NULL,
										"un_destino" => NULL,
										"frete_adicionais" => NULL,
										"labels_frete_adicionais" => NULL,
										"taxas_locais" => NULL,
										"labels_taxas_locais" => NULL,
										"observacao_interna" => NULL,
										"observacao_cliente" => NULL
			);		
			
		}
		else 
		{    
			$item_selecionado = $_SESSION['itens_proposta'][$itemIndex];
            
            //print"<pre>";print_r($item_selecionado);die();
            
            if( $item_selecionado['pp'] == 1 || $item_selecionado['pp'] == TRUE )
            {
                $item_selecionado['pp'] = "PP";
            }    
            else
            {
                $item_selecionado['pp'] = "";
            }    

            if( $item_selecionado['cc'] == 1 || $item_selecionado['cc'] == TRUE )
            {
                $item_selecionado['cc'] = "CC";
            }    
            else
           {
                $item_selecionado['cc'] = "";
            }
             
		}
        
        return $item_selecionado;
        
    }//END FUNCTION        
    
    /**
     * excluirItemDaSessao
     * 
     * Exclui um item que está salvo na sessão
     * 
     * @name recuperarItemDaSessao
     * @access public
     * @param int $index Indice do item que deseja excluir
     * @return boolean
     */
    public function excluirItemDaSessao() 
    {
        if( isset($_SESSION['itens_proposta'][$this->id_item]) )
		{
			unset($_SESSION['itens_proposta'][$this->id_item]);
		}
    }
}//END CLASS