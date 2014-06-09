$(document).ready(function(){
	
	$("input:checkbox").click(function(){
		
		$("#un_desembarque", window.parent.document).val($(this).attr("id"));
		$("#desembarque", window.parent.document).val($(this).val());
		
		/** Zera o porto de destino e às taxas **/
		$("#frete_adicionais option", window.parent.document).empty();
		$("#taxas_locais option", window.parent.document).empty();
		$("#destino", window.parent.document).val("");
		$("#un_destino", window.parent.document).val("");
		
		window.location = "/Clientes/propostas/index.php/loading/";
		$("#pop",window.parent.document).hide();
		
	});
	
	
	
});//END FILE