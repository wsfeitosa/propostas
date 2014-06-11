<?php
/**
 * Description of aplica_filtros_busca
 *
 * @author wsfall
 */
class Aplica_Filtros_Busca extends CI_Model {
    
    protected $filtros = Array();

    public function __construct() 
    {
        parent::__construct();
        //$this->output->enable_profiler(FALSE);
    }
    
    public function adicionarFiltro(Filtro $filtro, $nome_index = NULL)
    {
        if(is_null($nome_index) )
        {
            $this->filtros[] = $filtro;
        }
        else
        {
            $this->filtros[$nome_index] = $filtro;
        }    
        
        return $this;        
    }    
    
    public function removerFiltro( $index = NULL )
    {
        if( is_null($index) )
        {
            return $this->filtros;
        }
        else 
        {
            return $this->filtros[$index];
        }
    }    
    
    public function aplicarFiltrosDePesquisa()
    {
        $propostas_encontradas= Array();        
        
        if(count($this->filtros) < 1 ) 
        {
            throw new RuntimeException("Nenhum filtro foi informado para realizar a busca");
        }    
        
        $this->db->
                select("itens_proposta.*,propostas.sentido, propostas.tipo_proposta, 
                        propostas.id_proposta, propostas.numero_proposta, propostas.nome_nac")->
                from("CLIENTES.itens_proposta")->
                join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
                join("CLIENTES.clientes_x_propostas", "clientes_x_propostas.id_proposta = propostas.id_proposta")->                
                join("FINANCEIRO.tarifarios_pricing", "itens_proposta.id_tarifario_pricing = tarifarios_pricing.id_tarifario_pricing");                
        
        /**
         * Aplica os filtros
         */  
        foreach( $this->filtros as $filtro )
        {
            $filtro->retornar();
        }    
        
        /**
         * Agrupa os itens pela proposta
         */
        $this->db->group_by("propostas.id_proposta");
        
        $rowSet = $this->db->get();
        
        if( $rowSet->num_rows() > 0 )
        {
            $this->load->model("Clientes/cliente_model");
            $this->load->model("Propostas/proposta_model");
            $this->load->model("Propostas/Factory/proposta_factory");
            
            $cliente_model = new Cliente_Model();
            
            foreach ($rowSet->result() as $proposta) 
            {
                $proposta_encontrada = Proposta_Factory::factory($proposta->tipo_proposta);
            
                $proposta_encontrada->setId($proposta->id_proposta);
                $proposta_encontrada->setSentido($proposta->sentido);
                $proposta_encontrada->setTipoProposta($proposta->tipo_proposta);
                $proposta_encontrada->setNumero($proposta->numero_proposta);
                $proposta_encontrada->setNomeNac($proposta->nome_nac);
                
                $cliente_model->findByIdDaProposta($proposta_encontrada);
                $propostas_encontradas[] = $proposta_encontrada;
            }
        }    
        
        return $propostas_encontradas;
        
    }    
    
}
