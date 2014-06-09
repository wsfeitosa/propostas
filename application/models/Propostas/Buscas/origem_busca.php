<?php
/**
 * Realiza a busca pela origem da proposta
 *
 * Realiza a busca pela origem da proposta e implementa a interface de buscas
 * 
 * @package Propostas/Buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - date
 * @name Origem_Busca
 * @version 1.0
 */
include 'busca.php';

class Origem_Busca extends CI_Model implements Busca {
    
    public function __construct() {
        parent::__construct();        
        $this->load->model("Propostas/proposta_model");
        $this->load->model("Propostas/Factory/proposta_factory");
        $this->load->model("Tarifario/Factory/factory");
        $this->load->model("Tarifario/Factory/concrete_factory");
        $this->load->model("Tarifario/porto");
    }
    
    public function buscar($origem, $sentido, $vencidas)
    {

       	$factory = Factory::factory($sentido);

        $concrete_factory = new Concrete_Factory(); 

        $porto_model = $concrete_factory->CreatePortoModel($factory);

        $portos_encontrados = $porto_model->findByName($origem,"ORIGEM");

        $proposta_model = new Proposta_Model();

        $propostas_encontradas = Array();

        foreach($portos_encontrados as $porto)
        {
            
            /** Pesquisa pelos tarifários que contém o porto selecionado **/
            $tarifarios = $this->buscarTarifarioPorPorto($porto,$sentido);

            /** Pesquisa quais propostas contém o tarifário selecionado **/
            foreach( $tarifarios as $tarifario )
            {

                $propostas = $this->buscarPropostaPeloIdTarifario( $tarifario->id_tarifario_pricing, $vencidas );
                    
                foreach ($propostas as $proposta) 
                {

                    if( ! array_key_exists($proposta->id_proposta, $propostas_encontradas) )
                    {                       
                        $proposta_encontrada = Proposta_Factory::factory($proposta->tipo_proposta);
            
                        $proposta_encontrada->setId($proposta->id_proposta);
            
                        $proposta_model->buscarPropostaPorId($proposta_encontrada);

                        $propostas_encontradas[$proposta->id_proposta] = $proposta_encontrada;                  
                    }   

                }

            }       

        }   

        return $propostas_encontradas;
    	    	    	
    }   

    public function buscarPropostaPeloIdTarifario( $id_tarifario, $vencidas )
    {

        if( empty($id_tarifario) )
        {
            show_error("Impossivel encontrar o tarifario selecionado");
        }   

        $this->db->
                select("propostas.id_proposta, tipo_proposta")->
                from("CLIENTES.itens_proposta")->
                join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
                where("id_tarifario_pricing",$id_tarifario);

        if( strtoupper($vencidas) == 'N' )
        {
            $this->db->where("itens_proposta.validade >=",date('Y-m-d'));
        }

        $this->db->group_by("id_proposta");

        $rs = $this->db->get();     
                
        return  $rs->result();

    }

    public function buscarTarifarioPorPorto(Porto $porto, $sentido)
    {

        $this->db->
                select("id_tarifario_pricing")->
                from("FINANCEIRO.tarifarios_pricing")->
                where("id_place_receipt",$porto->getId())->
                where("ativo","S")->
                where("modulo",$sentido);

        $rs = $this->db->get();

        return $rs->result();

    }     
    
}


