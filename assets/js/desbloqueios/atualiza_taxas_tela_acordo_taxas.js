$(document).ready(function(){
	
	//Atualiza às taxas na tela da proposta
	var label = $("#label").val();
	var value = $("#value").val();	
	var posicao_combo = $("#posicao_combo").val();
	
	$("#taxas_selecionadas option:eq("+posicao_combo+")",window.parent.document).text(label);
    $("#taxas_selecionadas option:eq("+posicao_combo+")",window.parent.document).val(value);
        	
	$("#pop",window.parent.document).hide();
	
});