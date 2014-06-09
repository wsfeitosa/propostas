<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/Conexao/conecta.inc";

$propostas_duplicadas = array(
                                "PT0514SP056660000","PT0614SP072610000"
);

$conn = Zend_Conn();

$sql_id = $conn->select()->from("CLIENTES.propostas")->where("numero_proposta = ?",$propostas_duplicadas[0]);

$proposta_excluir = $conn->fetchRow($sql_id);

$id_proposta_excluir = $proposta_excluir['id_proposta'];

$sql_id = $conn->select()->from("CLIENTES.propostas")->where("numero_proposta = ?",$propostas_duplicadas[1]);

$proposta_manter = $conn->fetchRow($sql_id);

$id_proposta_manter = $proposta_manter['id_proposta'];

/** Seleciona todos os itens da proposta onde os itens deve ser excluidos **/
$sql_proposta_exluir = $conn->select()->from("CLIENTES.itens_proposta")->where("id_proposta = ?",$id_proposta_excluir);

$propostas_excluir = $conn->fetchAll($sql_proposta_exluir);

if( count($proposta_excluir) < 1 )
{
    die("Não há itens a excluir");
}    

foreach ($propostas_excluir as $proposta_excluir) 
{
    /**
     * Busca na outra proposta, se existe algun item com o mesmo id de tarifário
     * se existir então exlui o item da primeira proposta
     */
    $sql_busca_duplicado = $conn->
                                select()->
                                from("CLIENTES.itens_proposta")->
                                where("id_proposta = ?",$id_proposta_manter)->
                                where("id_tarifario_pricing = ?",$proposta_excluir['id_tarifario_pricing']);
    
    $existe_duplicado = $conn->fetchAll($sql_busca_duplicado);
    
    if( count($existe_duplicado) > 0 )
    {
        print "Item ".$proposta_excluir['numero_proposta']." Removido<br >";
        $conn->query(
                      "DELETE FROM
                          CLIENTES.itens_proposta
                       WHERE
                          itens_proposta.id_item_proposta =" . $proposta_excluir['id_item_proposta']
        );
    }    
    
}