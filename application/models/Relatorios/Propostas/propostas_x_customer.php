<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Propostas_x_Customer extends CI_Controller {

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        
    }

    public function Gerar()
    {
    	$data_inicial = new DateTime();
    	$data_inicial->modify("-6 Months");

    	$data_final = new DateTime();

    	$this->db->
    			select("*")->
    			from("CLIENTES.propostas")->
    			where("SUBSTRING(propostas.data_inclusao,1,10) >=", $data_inicial->format('Y-m-d'))->
    			where("SUBSTRING(propostas.data_inclusao,1,10) <=", $data_final->format('Y-m-d'));

    	$rowSet = $this->db->get();
    	
    	if( $rowSet->num_rows() < 1 )
    	{
    		echo "Não existem propostas no periodo!";
    	}		

    	$this->load->model("Usuarios/usuario");
    	$this->load->model("Usuarios/usuario_model");
    	$usuario_model = new Usuario_Model();

    	$propostas = $rowSet->result();

    	$filiais = array();

    	foreach ($propostas as $proposta) 
    	{            
    		$usuario = new Usuario();
    		$usuario->setId((int)$proposta->id_usuario_inclusao);

    		$usuario_model->findById($usuario);
            
    		if( $proposta->tipo_proposta == "proposta_spot" )
    		{
                /** Procura pela reserva correspondente **/
    		}	

            /** Procura pela quantidade de itens de cada proposta **/
            $this->db->select("id_item_proposta")->from("CLIENTES.itens_proposta")->where("id_proposta",$proposta->id_proposta);

            $rowSetItens = $this->db->get();

    		$filiais[$usuario->getFilial()->getSiglaFilial()][$usuario->getNome()][$proposta->tipo_proposta][] = 

            array('propostas' => $proposta, 'quantidade_itens' => $rowSetItens->num_rows());
	   	}

	   	$conteudo = "<html>
                        <head>
                            <style>
                            th {
                                background-color: yellow;
                            }
                            td {
                                color: #063380;                                
                            }   
                            </style>
                        </head>
                        <body>
                            <table border='1'>
                                <tr>
                                    <th>Filial</th>
                                    <th>Cadastrado Por</th>
                                    <th>Propostas Tarifario</th>
                                    <th>Quantidade de Itens</th>
                                    <th>Propostas Cotacao</th>
                                    <th>Quantidade de Itens</th>
                                    <th>Propostas Spot</th>
                                    <th>Quantidade de Itens</th>
                                    <th>Fechamentos Spot</th>
                                    <th>Propostas NAC</th>   
                                    <th>Quantidade de Itens</th>
                                </tr>";

        foreach ($filiais as $filial => $usuarios) 
        {
            foreach($usuarios as $usuario => $propostas)
            {        
                $quantidade_itens = array(
                                            "proposta_spot" => 0,
                                            "proposta_nac" => 0,
                                            "proposta_tarifario" => 0,
                                            "proposta_cotacao" => 0,
                                            "propostas_fechadas" => 0,
                );    

                /** Conta os itens de cada tipo de proposta **/
                foreach ($propostas as $tipo_proposta => $proposta) 
                {
                   foreach ($proposta as $value) 
                   {
                       $quantidade_itens[$tipo_proposta] += $value['quantidade_itens'];
                       
                       if( $tipo_proposta == "proposta_spot" )
                       {
                            //pr($value['propostas']->id_proposta);exit(0);
                            $quantidade_itens['propostas_fechadas'] += $this->retornaQuantidadeDeReservasSpotFechadas($value['propostas']->id_proposta);  
                       } 
                   }                        
                }

                $conteudo .= "<tr>
                                <td>{$filial}</td>
                                <td>{$usuario}</td>
                                <td>".count($propostas['proposta_tarifario'])."</td>
                                <td>".$quantidade_itens['proposta_tarifario']."</td>
                                <td>".count($propostas['proposta_cotacao'])."</td>
                                <td>".$quantidade_itens['proposta_cotacao']."</td>                                
                                <td>".count($propostas['proposta_spot'])."</td>
                                <td>".$quantidade_itens['proposta_spot']."</td>
                                <td>".$quantidade_itens['propostas_fechadas']."</td>
                                <td>".count($propostas['proposta_nac'])."</td>
                                <td>".$quantidade_itens['proposta_nac']."</td>   
                              </tr>";        
            }
        }   

        $conteudo .= "</table></body></html>";                     
    	
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="propostas_x_customer.xls"');
        header('Cache-Control: max-age=0');                
        echo $conteudo;

    } 

    protected function retornaQuantidadeDeReservasSpotFechadas( $id_proposta )
    {
        $this->db->
                select("id_item_proposta")->
                from("CLIENTES.itens_proposta")->
                where("id_proposta",$id_proposta)->
                where("utilizada","S");

        $rowSet = $this->db->get();

        return $rowSet->num_rows();
    }    

}
