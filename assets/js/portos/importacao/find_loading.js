$(document).ready(function(){
	
	$("input:checkbox").click(function(){
		
		$("#un_embarque", window.parent.document).val($(this).attr("id"));
		$("#embarque", window.parent.document).val($(this).val());
		
		window.location = "/Clientes/propostas/index.php/loading/";
		$("#pop",window.parent.document).hide();
		
	});
	
	
	
});//END FILE