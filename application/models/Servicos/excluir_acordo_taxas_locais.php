<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/Conexao/conecta.inc";

//Coloque no array abaixo os numeros dos acordos que você quer excluir
$acordosParaExcluir = array(
							"TX1113SP00425",
							"TX1113SP00426",
							"TX1113SP00427"
);

$conn = Zend_Conn();

foreach( $acordosParaExcluir as $acordo )
{
	$sql = $conn->select("*")->from("CLIENTES.acordos_taxas_locais_globais")->where("numero = ?",$acordo);

	$row = $conn->fetchRow($sql);
	
	/** Seleciona e exclui os clientes do acordo **/
	$sql = $conn->select("*")->from("CLIENTES.clientes_x_acordos_taxas_locais_globais")->where("id_acordos_taxas_locais = ?",$row['id']);

	$rowClientes = $conn->fetchAll($sql);

	if( count($rowClientes) > 0 )
	{
		foreach ($rowClientes as $cliente) 
		{
			$conn->delete('CLIENTES.clientes_x_acordos_taxas_locais_globais',"id = ".$cliente['id']);
		}
	}

	/** Selciona e exclui os portos do acordo **/
	$sql = $conn->select("*")->from("CLIENTES.portos_x_acordos_taxas_globais")->where("id_acordo = ?",$row['id']);

	$rowPortos = $conn->fetchAll($sql);

	if( count($rowPortos) > 0 )
	{
		foreach ($rowPortos as $porto) 
		{
			$conn->delete('CLIENTES.portos_x_acordos_taxas_globais',"id = ".$porto['id']);
		}
	}	

	/** Seleciona e exclui às taxas do acordo **/	
	$sql = $conn->select("*")->from("CLIENTES.taxas_x_acordos_taxas_locais_globais")->where("id_acordos_taxas_locais = ?",$row['id']);

	$rowTaxas = $conn->fetchAll($sql);

	if( count($rowTaxas) > 0 )
	{
		foreach ($rowTaxas as $taxa) 
		{
			$conn->delete('CLIENTES.taxas_x_acordos_taxas_locais_globais',"id = ".$taxa['id']);
		}
	}

	//Exclui o acordo de taxas locais
	if( is_array($row) )
	{
		$conn->delete('CLIENTES.acordos_taxas_locais_globais',"id = ".$row['id']);	
	}	

	error_log(date('d-m-Y H:i:s') . " - ".$row['numero']."\r\n", 3, dirname(__FILE__)."/log_exclusao_acordo_taxas_locais.log");

	echo "Acordo " . $row['numero'] . " Excluido<br />";

}		

echo "FIM!";