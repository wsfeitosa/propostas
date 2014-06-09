<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @package  Models/Taxas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 27/02/2013
* @version  1.0
* Classe que serializa e deserializa às taxas que estão na sessão
*/
class Serializa_Taxas extends CI_Model {
    
    const caracter_separador = "---";
    const caracter_separador_taxa = ":";

    public function __construct() {
        parent::__construct();       
        //$this->output->enable_profiler(FALSE);
    }

    /**
     * DeserializaTaxas
     * 
     * Deserializa às taxas que estão na sessão e converte todas para objetos de tipo taxa
     * informado
     * 
     * @name deserializaTaxas 
     * @access public
     * @param string $taxasSerializadas
     * @param string $tipoTaxa
     * @return Array
     */
    public function deserializaTaxasProposta( $taxasSerializadas = NULL, $tipoTaxa = NULL ) {
        
        if(is_null($tipoTaxa) || is_null($taxasSerializadas) )
        {
            log_message('error','Taxas serializadas em formato desconhecido, impossivel a conversão!');
            show_error('Impossivel converter às taxas');
        }    
                
        include_once APPPATH.'models/Taxas/unidade.php';
        include_once APPPATH.'models/Taxas/unidade_model.php';
        include_once APPPATH.'models/Taxas/moeda.php';
        include_once APPPATH.'models/Taxas/moeda_model.php';
        include_once APPPATH.'models/Taxas/taxa_model.php';
        
        /** Explode a string onde existe o caracter separador **/
        $taxas = explode(Serializa_Taxas::caracter_separador, $taxasSerializadas);

        array_pop($taxas);
            
        $this->load->model("Taxas/".strtolower($tipoTaxa),"taxa");
                        
        $taxas_deserializadas = Array();
        
        $unidade_model = new Unidade_Model();
        $moeda_model = new Moeda_Model();
        $taxa_model = new Taxa_Model();
        
        /** Cria um objeto para cada uma das taxas do array **/
        foreach( $taxas as $taxa_serializada )
        {
            
            /** separa os parametros das taxas **/
            $dados_da_taxa = explode(Serializa_Taxas::caracter_separador_taxa,$taxa_serializada);
            
            $taxa_convertida = new $tipoTaxa();
            
            $taxa_convertida->setId((int)$dados_da_taxa[0]);
            $taxa_convertida->setValor((float)$dados_da_taxa[3]);
            $taxa_convertida->setValorMinimo((float)$dados_da_taxa[4]);
            $taxa_convertida->setValorMaximo((float)$dados_da_taxa[5]);
            $taxa_convertida->setPPCC($dados_da_taxa[6]);
            
            try{

                $unidade = new Unidade();
                
                $unidade->setId((int)$dados_da_taxa[2]);
                $unidade_model->findById($unidade);
                $taxa_convertida->setUnidade($unidade);

                $moeda = new Moeda();
            
                $moeda->setId((int)$dados_da_taxa[1]);
                $moeda_model->findById($moeda);
                $taxa_convertida->setMoeda($moeda);

            } catch (Exception $e) {
                
                /** BUG 001 - Reajusta às posições do array de valores das taxas serializadas **/ 
                $taxa_convertida->setId((int)$dados_da_taxa[0]);
                $taxa_convertida->setValor((float)$dados_da_taxa[2]);
                $taxa_convertida->setValorMinimo((float)$dados_da_taxa[3]);
                $taxa_convertida->setValorMaximo((float)$dados_da_taxa[4]);
                $taxa_convertida->setPPCC($dados_da_taxa[9]);
                
                try{
                    //pr($dados_da_taxa);exit(0);
                    $moeda->setId((int)$dados_da_taxa[5]);
                    $moeda_model->findById($moeda);
                    $taxa_convertida->setMoeda($moeda);

                    $unidade->setId((int)$dados_da_taxa[7]);
                    $unidade_model->findById($unidade);
                    $taxa_convertida->setUnidade($unidade);
                } catch (Exception $e) {
                    show_error($e->getMessage());
                }

            }    
                                   
            $taxa_model->obterNomeTaxaAdicional( $taxa_convertida );

            array_push($taxas_deserializadas, $taxa_convertida);
                                    
        }    
         
        return $taxas_deserializadas;
        
    }//END FUNCTION
    
    /**
     * serializaTaxasDItemDaProposta
     * 
     * Serializa às taxas de um item de proposta para inclusão na sessão do PHP e para utilização no javascript da proposta
     * 
     * @name serializaTaxasDItemDaProposta
     * @access public
     * @param Item_Proposta $item
     * @return array $taxasSerializadas Retorna às taxas serializadas
     */
    public function serializaTaxasProposta( Item_Proposta $item )
    {
      
        $label_taxas_locais = "";
        $value_taxas_locais = "";
        $label_taxas_adicionais = "";
        $value_taxas_adicionais = "";
        
        foreach( $item->getTarifario()->getTaxa() as $taxa )
        {          
            
            if( $taxa instanceof Taxa_Local )
            {
                $value_taxas_locais .= $taxa->getId() . self::caracter_separador_taxa .
                                       $taxa->getMoeda()->getId() . self::caracter_separador_taxa . 
                                       $taxa->getUnidade()->getId() . self::caracter_separador_taxa .
                                       number_format($taxa->getValor(),2) . self::caracter_separador_taxa.
                                       number_format($taxa->getValorMinimo(),2) . self::caracter_separador_taxa . 
                                       number_format($taxa->getValorMaximo(),2) . self::caracter_separador_taxa .
                                       $taxa->getPPCC() . self::caracter_separador;
            
                $label_taxas_locais .= $taxa->getNome() . " | " . $taxa->getMoeda()->getSigla() . " " .
                                       number_format($taxa->getValor(),2) . " " . $taxa->getUnidade()->getUnidade() . " | MIN " .
                                       number_format($taxa->getValorMinimo(),2) . " | MAX " . 
                                       number_format($taxa->getValorMaximo(),2) . " " . $taxa->getPPCC() . self::caracter_separador;
            }
            else 
           {
                $value_taxas_adicionais .= $taxa->getId() . self::caracter_separador_taxa .
                                           $taxa->getMoeda()->getId() . self::caracter_separador_taxa . 
                                           $taxa->getUnidade()->getId() . self::caracter_separador_taxa .
                                           number_format($taxa->getValor(),2) . self::caracter_separador_taxa.
                                           number_format($taxa->getValorMinimo(),2) . self::caracter_separador_taxa . 
                                           number_format($taxa->getValorMaximo(),2) . self::caracter_separador_taxa . 
                                           $taxa->getPPCC() . self::caracter_separador;
            
                $label_taxas_adicionais .= $taxa->getNome() . " | " . $taxa->getMoeda()->getSigla() . " " .
                                           number_format($taxa->getValor(),2) . " " . $taxa->getUnidade()->getUnidade() . " | MIN " .
                                           number_format($taxa->getValorMinimo(),2) . " | MAX " . 
                                           number_format($taxa->getValorMaximo(),2) . " " . $taxa->getPPCC() . self::caracter_separador;
            }
            
            
        }    
		       
        include_once BASEPATH."/libraries/Scoa/Url.php";
        
        $url = new Url();
        
        $taxas_serializadas = Array( 
                                    'value_taxas_locais' => $value_taxas_locais, 
        							'label_taxas_locais' => $label_taxas_locais,
                                    'value_taxas_adicionais' => $value_taxas_adicionais, 
        							'label_taxas_adicionais' => $label_taxas_adicionais
        );
        
        return $taxas_serializadas;
        
    }//END FUNCTION        
    
    /**
     * SerializaTaxasParaSessao
     *
     * Serializa Um array de taxas para guardar na sessão
     *
     * @name SerializaTaxasParaSessao
     * @access public
     * @param Array $taxas
     * @return Array $taxas_serializadas
     */ 	
    public function SerializaTaxasParaSessao(Array $taxas) 
    {
    	
    	if( count($taxas) < 1 )
    	{
    		return false;
    	}

    	foreach( $taxas as $taxa )
    	{
    		$value_taxas_locais .= $taxa->getId() . self::caracter_separador_taxa .
						    	   $taxa->getMoeda()->getId() . self::caracter_separador_taxa .
						    	   $taxa->getUnidade()->getId() . self::caracter_separador_taxa .
						    	   number_format($taxa->getValor(),2) . self::caracter_separador_taxa.
						    	   number_format($taxa->getValorMinimo(),2) . self::caracter_separador_taxa .
						    	   number_format($taxa->getValorMaximo(),2) . self::caracter_separador_taxa .
                                   $taxa->getPPCC() . self::caracter_separador;
    		
    		$label_taxas_locais .= $taxa->getNome() . " | " . $taxa->getMoeda()->getSigla() . " " .
				    			   number_format($taxa->getValor(),2) . " " . $taxa->getUnidade()->getUnidade() . " | " .
				    			   number_format($taxa->getValorMinimo(),2) . " | " .
				    			   number_format($taxa->getValorMaximo(),2). " " . $taxa->getPPCC() .self::caracter_separador;
    	}	
    	
    	$taxas_serializadas = Array(
					    			'value_taxas' => $value_taxas_locais,
					    			'label_taxas' => $label_taxas_locais,					    			
    	);
    	
    	return $taxas_serializadas;
    	
    }
    
}//END CLASS


