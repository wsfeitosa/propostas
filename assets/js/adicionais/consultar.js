$(document).ready(function(){
	
	// Dicas de preenchimento da tela
	$( document ).tooltip();
	
	$("#novo").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/novo/';
	});
	
	$("#alterar").click(function(){
		window.location = "/Clientes/propostas/index.php/adicionais/adicionais/alterar/" + $("#id_acordo").val();
	});
	
	$("#localizar").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/filtrar_busca/';
	});
	
	$("#voltar").click(function(){
		window.location = '/modulo.php';
	});
		
});