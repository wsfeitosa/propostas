$(document).ready(function(){
	
	//Atualiza às taxas na tela da proposta
	var label = $("#label").val();
	var value = $("#value").val();
	var combo = $("#nome_combo").val();
	var posicao_combo = $("#posicao_combo").val();
	
	$("#" + combo + " option:eq("+posicao_combo+")",window.parent.document).text(label);
    $("#" + combo + " option:eq("+posicao_combo+")",window.parent.document).val(value);
        	
	$("#pop",window.parent.document).hide();
	
});