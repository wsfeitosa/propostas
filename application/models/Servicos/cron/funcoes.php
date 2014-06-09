<?php
//const SERVER_ROOT = "/var/www/html/allink"; 

include_once SERVER_ROOT.'/Conexao/conecta.inc';

function retornaNomePorto($id_porto = NULL)
{
    $db = Zend_Conn();

	if( is_null($id_porto) )
	{
		throw new Exception("Nenhum porto informado", 1);		
	}	

	$sql = $db->select('porto')->from("USUARIOS.portos")->where("portos.id_porto = ?",$id_porto);

	$cidade = $db->fetchRow($sql);

	return $cidade['porto'];
}

function retornaCidade($id_cidade = NULL)
{
    $db = Zend_Conn();

	if( is_null($id_cidade) )
	{
		throw new Exception("Nenhuma cidade informada", 1);		
	}	

	$sql = $db->select('cidade')->from("CLIENTES.cidade")->where("cidade.id_cidade = ?",$id_cidade);

	$cidade = $db->fetchRow($sql);

	return $cidade['cidade'];
}

function retornaFilialUsuario($id_user = NULL)
{
    $db = Zend_Conn();

	if( is_null($id_user) )
	{
		throw new Exception("Nenhum usuario informado", 1);		
	}	

	$sql = $db->
            select()->
            from("USUARIOS.usuarios",array())->
            join("USUARIOS.filiais","filiais.id_filial = usuarios.id_filial",array('nomefilial'))->
            where("usuarios.id_user = ?",$id_user);

	$usuario = $db->fetchRow($sql);

	return $usuario['nomefilial'];
}

function retornaNomeUsuario( $id_user = NULL )
{
	$db = Zend_Conn();

	if( is_null($id_user) )
	{
		throw new Exception("Nenhum usuario informado", 1);		
	}	

	$sql = $db->select('nome')->from("USUARIOS.usuarios")->where("usuarios.id_user = ?",$id_user);

	$usuario = $db->fetchRow($sql);

	return $usuario['nome'];
}

function retornaEmailUsuario( $id_user = NULL ) 
{

	$db = Zend_Conn();

	if( is_null($id_user) )
	{
		throw new Exception("Nehhum usuario informado para buscar o email", 1);		
	}	

	$sql = $db->select('email')->from("USUARIOS.usuarios")->where("usuarios.id_user = ?",$id_user);

	$usuario = $db->fetchRow($sql);

	return $usuario['email'];
}

function reatornaPortosTarifario( $id_tarifario = NULL )
{

	$db = Zend_Conn();

	if( is_null($id_tarifario) )
	{
		throw new Exception("Nenhum tarifario informado para buscar o porto", 1);		
	}	

	$sql = $db->
				select('id_place_receipt, id_port_loading, id_via, id_place_delivery, sentido')->
				from("FINANCEIRO.tarifarios_pricing")->
				where("id_tarifario_pricing = ?", $id_tarifario);

	$tarifario = $db->fetchRow($sql);

	$portos = Array();

	if( $tarifario['modulo'] == "EXP" )
	{

		$sql_receipt = $db->
							select()->
							from("USUARIOS.portos")->
							where("id_porto = ?", $tarifario['id_place_receipt']);

		$receipt = $db->fetchRow($sql_receipt);	

		$portos['origem'] = $receipt['porto'];

		$sql_loading = $db->
							select()->
							from("USUARIOS.portos")->
							where("id_porto = ?", $tarifario['id_port_loading']);

		$loading = $db->fetchRow($sql_loading);	
		
		$portos['embarque'] = $loading['porto'];

		$sql_discharge = $db->
							select()->
							from("GERAIS.vias")->
							where("id_via = ?", $tarifario['id_via']);

		$discharge = $db->fetchRow($sql_discharge);	
		
		$portos['desembarque'] = $discharge['via'];

		$sql_delivery = $db->
							select()->
							from("GERAIS.destinos")->
							where("id_destino = ?", $tarifario['id_place_delivery']);

		$delivery = $db->fetchRow($sql_delivery);	
		
		$portos['destino'] = $delivery['destino'];

	}
	else 
	{
		
		$sql_receipt = $db->
							select()->
							from("GERAIS.porto")->
							where("id_porto = ?", $tarifario['id_place_receipt']);

		$receipt = $db->fetchRow($sql_receipt);		

		$portos['origem'] = $receipt['porto'];

		$sql_loading = $db->
							select()->
							from("GERAIS.porto")->
							where("id_porto = ?", $tarifario['id_port_loading']);

		$loading = $db->fetchRow($sql_loading);	

		$portos['embarque'] = $loading['porto'];

		$sql_discharge = $db->
							select()->
							from("GERAIS.porto")->
							where("id_porto = ?", $tarifario['id_via']);

		$discharge = $db->fetchRow($sql_discharge);	
		
		$portos['desembarque'] = $discharge['porto'];

		$sql_delivery = $db->
							select()->
							from("USUARIOS.portos")->
							where("id_porto = ?", $tarifario['id_place_delivery']);

		$delivery = $db->fetchRow($sql_delivery);	
		
		$portos['destino'] = $delivery['porto'];

	}	
		
	return $portos;
}

function validaEmail($email) 
{
	$conta = "^[a-zA-Z0-9\._-]+@";
	$domino = "[a-zA-Z0-9\._-]+.";
	$extensao = "([a-zA-Z]{2,4})$";

	$pattern = $conta.$domino.$extensao;

	if (ereg($pattern, $email))
	{	
		return true;
	}	
	else
	{	
		return false;
	}	
}