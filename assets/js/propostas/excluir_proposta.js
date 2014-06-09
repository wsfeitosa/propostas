$(document).ready(function($) {
	
	$("#enviar").click(function(){

		if( $("#numero_proposta").val() == "" )
		{
			alert("Preencha o numero da proposta!");
			return false;
		}
		else
		{
			$("#form_excluir").submit();	
		}	

	});	

});