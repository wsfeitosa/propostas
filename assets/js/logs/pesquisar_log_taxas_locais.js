$(document).ready(function(){
	
	$("#localizar").click(function(){
		$("#realizar_busca").submit();
	});
	
	$("#voltar").click(function(){
		window.location = "/modulo.php";
	});
	
});