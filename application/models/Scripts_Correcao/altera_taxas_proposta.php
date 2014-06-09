<?php
ini_set('display__errors', 'On');
define(SERVER_ROOT,"/var/www/html/allink"); 

include_once SERVER_ROOT.'/Conexao/conecta.inc';

$conn = Zend_Conn();
db_conecta();

$id_taxa = 3;
$sentido = "EXP";

/**
 * Seleciona todas às taxas da modalidade escolhida
 */
$sql = "SELECT
            taxas_item_proposta.id_taxa_item
        FROM
            CLIENTES.taxas_item_proposta
            INNER JOIN CLIENTES.itens_proposta ON itens_proposta.id_item_proposta = taxas_item_proposta.id_item_proposta
            INNER JOIN CLIENTES.propostas ON propostas.id_proposta = itens_proposta.id_proposta
        WHERE
            propostas.sentido = '".$sentido."' AND
            taxas_item_proposta.id_taxa_adicional = ".$id_taxa;

$rowSet = $conn->fetchAll($sql);

if( count($rowSet) < 1 )
{
    die("Nenhuma taxa à atualizar");
}    

foreach ($rowSet as $taxa) 
{   
    $update = $conn->update("CLIENTES.taxas_item_proposta",array("ppcc" => "CC"),"id_taxa_item = ".$taxa['id_taxa_item']);
}

echo "FIM";