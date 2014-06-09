$(document).ready(function(){
	
	$("#alterar").click(function(){
		
		window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/update/" + $("#id_acordo").val();
		
	});
	
	$("#localizar").click(function(){
		
		window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/search/";
		
	});
			
    $("#voltar").click(function(){
		window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/";
	});
    
    $("#novo").click(function(){
		window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/";
	});
	
});