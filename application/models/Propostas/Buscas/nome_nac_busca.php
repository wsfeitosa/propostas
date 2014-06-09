<?php
include 'busca.php';

class Nome_Nac_Busca extends CI_Model implements Busca{

	public function __construct() {
        parent::__construct();  
        $this->output->enable_profiler(FALSE);     
    }

    public function buscar($nome_nac, $sentido, $vencidas)
    {
    	$this->load->model("Clientes/cliente_model");        
        $this->load->model("Propostas/proposta_model");
        include_once APPPATH."/models/Propostas/Factory/proposta_factory.php";

        $this->db->
                select("itens_proposta.*,propostas.sentido, propostas.tipo_proposta, 
                        propostas.id_proposta, propostas.numero_proposta, propostas.nome_nac")->
                from("CLIENTES.itens_proposta")->
                join("CLIENTES.propostas", "propostas.id_proposta = itens_proposta.id_proposta")->
                where("propostas.sentido",$sentido)->
                like("propostas.nome_nac", $nome_nac);
        
        if( strtoupper($vencidas) == 'N' )
        {
            $this->db->where("itens_proposta.validade >=",date('Y-m-d'));
        }           

        $this->db->group_by("propostas.id_proposta");        
        
        $rs = $this->db->get();
        
        $propostas_encontradas = Array();
        
        $proposta_model = new Proposta_Model();
        $cliente_model = new Cliente_Model();
        
        foreach ($rs->result() as $proposta) 
        {            
            try{
                $proposta_encontrada = Proposta_Factory::factory($proposta->tipo_proposta);
                         
                $proposta_encontrada->setId($proposta->id_proposta);
                $proposta_encontrada->setSentido($proposta->sentido);
                $proposta_encontrada->setTipoProposta($proposta->tipo_proposta);
                $proposta_encontrada->setNumero($proposta->numero_proposta);
                $proposta_encontrada->setNomeNac($proposta->nome_nac);
                
                 /** 
                  * Este trecho buscava por todos os itens de todas às propostas
                  * como a demora estava sendo bem grande então decidi mudar o esquema de busca
                  * e agora a função busca apenas os dados básicos para exibir na tela de consulta
                  */ 
                //$proposta_model->buscarPropostaPorId($proposta_encontrada);

                $cliente_model->findByIdDaProposta($proposta_encontrada);
                $propostas_encontradas[] = $proposta_encontrada;
            } catch(Exception $e) {
                show_error($e->getMessage());exit;
            }
        }
        
        return $propostas_encontradas;
    }	

}
