<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  Taxas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 31/01/2013
* @version  1.0
* Aplica às regras de negócio que são comuns a todos os tipos de taxas
*/
include_once "/var/www/html/allink/Libs/remove_acentos.php";

class Taxa_Model extends CI_Model {


	public function __construct()
	{
		parent::__construct();		
	}
		
	/**
	  * obterNomeTaxaAdicional
	  * 
	  * Obtem o nome de uma taxas adicional qualquer
	  * 
	  * @name obterNomeTaxaAdicional
	  * @access public
	  * @param Taxa
	  * @return void
	  */
	public function obterNomeTaxaAdicional( Taxa $taxa )
	{
		
		$this->db->
		select("taxa_adicional")->
		from("FINANCEIRO.taxas_adicionais")->
		where("id_txadicional",$taxa->getId());
		//where("conta_contabil",1);
		
		$this->db->cache_on();
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			log_message('error',"Impossivel encontrar a taxa selecionada");
			throw new Exception("Impossivel encontrar a taxa selecionada");
		}	

		$row = $rs->row(); 
		
        $nome_taxa = str_replace("/", " ", $row->taxa_adicional);
        $nome_taxa = remove_acentos($nome_taxa);

		$taxa->setNome($nome_taxa);
		$this->db->cache_off();
	}//END FUNCTION
    
    /**
     * salvarTaxa
     * 
     * Salva às taxas referentes às propostas independente do tipo (locais ou adicionais)
     * 
     * @name salvarTaxa 
     * @access public
     * @param Taxa
     * @return boolean
     */    
    public function salvarTaxa(Taxa $taxa, $id_item = NULL) 
    {
        
        if( empty($id_item) )
        {
            log_message('error','Não é possível salvar às taxas pois o id do item não foi definido!');
            show_error('Não é possível salvar às taxas pois o id do item não foi definido!');
        }    
        
        $taxa_portuaria = "N";
        
        if( $taxa instanceof Taxa_Local )
        {
            $taxa_portuaria = "S";
        }    
        
        /**
		 * Quando às taxas são inseridas manualmente na proposta, estão ficando com a 
		 * modalidade us ou eu e para coorigir este problema estou fazendo uma validação antes de salvar
		 * se a taxa estiver com a modalidade errada então seta como AF	
         */
        
        $dados_para_salvar = Array(
                                    "id_item_proposta" => $id_item,
                                    "id_taxa_adicional" => $taxa->getId(),
                                    "id_unidade" => $taxa->getUnidade()->getId(),
                                    "id_moeda" => $taxa->getMoeda()->getId(),
                                    "valor" => $taxa->getValor(),
                                    "valor_minimo" => $taxa->getValorMinimo(),
                                    "valor_maximo" => $taxa->getValorMaximo(),
                                    "taxa_portuaria" => $taxa_portuaria,
                                    "ppcc" => $taxa->getPPCC(),             
        );
        
        $rs = $this->db->insert("CLIENTES.taxas_item_proposta",$dados_para_salvar);
        
        $taxa->setIdItem((int)$this->db->insert_id());
        
        return $rs;
                       
    }//END FUNCTION
    
    /**
     * exluirTaxasPorItemDeProposta
     * 
     * Excluir todas às taxas que estão relacionadas a um determinado item de proposta
     * 
     * @name exluirTaxasPorItemDeProposta
     * @access public
     * @param Item_Proposta $item
     * @return boolean
     */
    public function exluirTaxasPorItemDeProposta(Item_Proposta $item) 
    {
        //pr($item);
        $id_item = $item->getId();
        
        if( empty($id_item) )
        {
            throw new InvalidArgumentException("O id do item da proposta não foi definido para efetuar a exclusão das taxas!");
        }    
        
        $this->db->
                select("taxas_item_proposta.id_taxa_item")->
                from("CLIENTES.taxas_item_proposta")->
                where("taxas_item_proposta.id_item_proposta",$item->getId());
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            return FALSE;
        }    
        
        $taxas_relacionadas_item = $rs->result();
        
        foreach ($taxas_relacionadas_item as $taxa_relacionada) 
        {
            $this->db->delete("CLIENTES.taxas_item_proposta", Array("id_taxa_item" => $taxa_relacionada->id_taxa_item));
        }
        
        return TRUE;
        
    }//END FUNCTION
    
    /**
     * retornaTaxasDaProposta
     * 
     * Retorna às taxas já cadastradas de uma proposta
     * 
     * @name retornaTaxasDaProposta
     * @access public
     * @param Item_Proposta $proposta
     * @return ArrayObject $taxas_encontradas
     */
    public function retornaTaxasDaProposta( Item_Proposta $item )
    {
        
    	include_once APPPATH."models/Taxas/taxa_local.php";
    	include_once APPPATH."models/Taxas/taxa_adicional.php";
    	include_once APPPATH."models/Taxas/unidade.php";
    	include_once APPPATH."models/Taxas/unidade_model.php";
    	include_once APPPATH."models/Taxas/moeda.php";
    	include_once APPPATH."models/Taxas/moeda_model.php";
    	
        $this->db->
                select("id_taxa_adicional, valor, valor_minimo, valor_minimo, id_moeda, id_unidade, ppcc")->
                from("CLIENTES.taxas_item_proposta")->
                where("taxas_item_proposta.id_item_proposta",$item->getId());
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {            
            throw new RuntimeException("Impossivel recuperar às taxas dos itens da proposta!");
        }    
        
        $taxas_encontradas = new ArrayObject();

        $moeda_model = new Moeda_Model();
        
        $unidade_model = new Unidade_Model();
        
        foreach( $rs->result() as $taxa )
        {
            
            if( $taxa->taxa_portuaria == "S" )
            {
                $taxa_encontrada = new Taxa_Local();
            }
            else
           {
                $taxa_encontrada = new Taxa_Adicional();
            }    
            
            $taxa_encontrada->setId((int)$taxa->id_taxa_adicional);
            $taxa_encontrada->setIdItem((int)$item->getId());
            $taxa_encontrada->setValor((float)$taxa->valor);
            $taxa_encontrada->setValorMinimo((float)$taxa->valor_minimo);
            $taxa_encontrada->setValorMaximo((float)$taxa->valor_maximo);
            $taxa_encontrada->getPPCC($taxa->ppcc);

            /** Obtem o nome da taxa **/
            $this->obterNomeTaxaAdicional($taxa_encontrada);
            
            $moeda = new Moeda();
                                   
            $moeda->setId((int)$taxa->id_moeda);
            
            $moeda_model->findById($moeda);
            
            $taxa_encontrada->setMoeda($moeda);
            
            $unidade = new Unidade();
                                    
            $unidade->setId((int)$taxa->id_unidade);
            
            $unidade_model->findById($unidade);
            
            $taxa_encontrada->setUnidade($unidade);
            
            $taxas_encontradas->append($taxa_encontrada);
                        
        }    
        
        return $taxas_encontradas;
        
    }        
    
    /**
     * retornaTodasAsTaxas
     *
     * Retorna todas às taxas cadastradas no sistema
     *
     * @name retornaTodasAsTaxas
     * @access public  
     * @return Array $taxas_encontradas
     */ 	
    public function retornaTodasAsTaxas() 
    {
    	
    	$this->db->
    			select("id_txadicional as id_taxa, taxa_adicional as taxa")->
    			from("FINANCEIRO.taxas_adicionais")->
    			where("conta_contabil",1)->
    			//where("id_txadicional !=",10)->
                group_by("taxa");
    	
    	$this->db->cache_on();
    	$rs = $this->db->get();
    	
    	if( $rs->num_rows() < 1 )
    	{
    		$message = "Nenhuma Taxa Cadastrada No Sistema!";
    		log_message('error',$message);
    		show_error($message);
    	}	
    	
    	$taxas_encontradas = Array();
    	
    	foreach( $rs->result() as $taxa )
    	{
    		$taxas_encontradas[$taxa->id_taxa] = $taxa->taxa;
    	}	
    	$this->db->cache_off();    	    	
    	return $taxas_encontradas;
    	
    }
    
}//END CLASS