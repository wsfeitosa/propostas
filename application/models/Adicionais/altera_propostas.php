<?php
class Altera_Propostas extends CI_Model {
	
	public function __construct() {		
		parent::__construct();
		$this->load->model("Adicionais/acordo_adicionais");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Adicionais/serializa_taxa");
	}
	
	public function AlterarPropostasRetroativas($id_acordo)
	{   
        //pr($id_acordo);exit(0);
		
        $handle = fopen("/var/www/html/allink/post.txt", "w+");
        fwrite($handle, 'alterar_retroativos-'.$id_acordo);
        fclose($handle);
       
        // Busca os dados do acordo que acabou de ser aprovado
        $acordo = new Acordo_Adicionais();
        $acordo->setId((int)$id_acordo);

        $this->acordo_adicionais_model->consultarAcordoAdicionaisPorId($acordo);

        //Percorre cada um dos cliente do acordo para encontrar às propostas desses clientes
        $propostasParaAlterar = Array();

        foreach($acordo->getClientes() as $clienteAcordo)
        {
            $this->db->
                    select("itens_proposta.id_item_proposta,itens_proposta.numero_proposta")->
                    from("CLIENTES.propostas")->
                    join("CLIENTES.clientes_x_propostas","propostas.id_proposta = clientes_x_propostas.id_proposta")->
                    join("CLIENTES.itens_proposta","itens_proposta.id_proposta = propostas.id_proposta")->
                    where("propostas.sentido",$acordo->getSentido())->
                    where("propostas.tipo_proposta !=","proposta_nac")->
                    where("propostas.tipo_proposta !=","proposta_spot")->
                    where("itens_proposta.validade >=",date('Y-m-d'))->
                    where("clientes_x_propostas.id_cliente",$clienteAcordo->getId());

            $rowSet = $this->db->get();		

            if( $rowSet->num_rows() < 1 )
            {
                continue;
            }

            foreach( $rowSet->result() as $itemProposta )
            {
                array_push($propostasParaAlterar, $itemProposta->id_item_proposta);
            }	

        }	

        //Remove os ids duplicados
        $propostasParaAlterar = array_unique($propostasParaAlterar);

        //percorre os itens encontrados das propostas para alterar às taxas
        foreach( $propostasParaAlterar as $idItem )
        {				
            $labelDasTaxasAlteradas = "";
            $alterarObservacaoDoItem = false;

            //Seleciona às taxas do item
            $rowSetTaxasItem = $this->db->get_where("CLIENTES.taxas_item_proposta","id_item_proposta = '{$idItem}'");

            if( $rowSetTaxasItem->num_rows() < 1 )
            {
                continue;
            }	

            foreach( $rowSetTaxasItem->result() as $taxaItem )
            {					
                foreach( $acordo->getTaxas() as $taxaAcordo )
                {
                    /**
                     * Se a mesma taxa existir no acordo de adicionais e na
                     * proposta, então atualizamos às taxas da proposta, com os valores da taxa do acordo
                     */ 
                    if( $taxaItem->id_taxa_adicional == $taxaAcordo->getId() )
                    {
                        $alterarObservacaoDoItem = true;

                        $dadosDaTaxaDoAcordo = Array(
                                                     "id_unidade" => $taxaAcordo->getUnidade()->getId(),
                                                     "id_moeda" => $taxaAcordo->getMoeda()->getId(),
                                                     "valor" => str_replace(",", ".", $taxaAcordo->getValor()),
                                                     "valor_minimo" => str_replace(",", ".", $taxaAcordo->getValorMinimo()),
                                                     "valor_maximo" => str_replace(",", ".", $taxaAcordo->getValorMaximo()),
                        );

                        $this->db->where("id_taxa_item",$taxaItem->id_taxa_item);
                        $this->db->update("CLIENTES.taxas_item_proposta",$dadosDaTaxaDoAcordo);		

                        $labelDasTaxasAlteradas .= $this->serializa_taxa->ConverterTaxaParaString($taxaAcordo)."\n";					
                    }	
                }	
            }	

            //Altera a observação interna do item da proposta se necessário
            if( $alterarObservacaoDoItem === true )
            {
                $rowSetObs = $this->db->get_where("CLIENTES.itens_proposta","id_item_proposta = '{$idItem}'");

                $rowObs = $rowSetObs->row();

                $msg = "Em: ".date('d/m/Y H:i:s')." Este item foi alterado pelo acordo de adicionais ".$acordo->getNumeroAcordo()."\n".$labelDasTaxasAlteradas;
                $msg .= $rowObs->observacao_interna . "\n";	

                $this->db->where("itens_proposta.id_item_proposta",$idItem);
                $this->db->update("CLIENTES.itens_proposta",array("observacao_interna"=>$msg));
            }

        }								
			//pr($propostasParaAlterar);exit(0);					
	}
	
}