$(document).ready(function(){
	
	$("#valor").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#valor_minimo").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#valor_maximo").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#salvar").click(function(){
		
		var id_taxa = $("#taxa option:selected").val();
		var nome_taxa = $("#taxa option:selected").text();
		var id_unidade = $("#unidade option:selected").val();
		var unidade = $("#unidade option:selected").text();
		var id_moeda = $("#moeda option:selected").val();
		var moeda = $("#moeda option:selected").text();
		var modalidade = $("input:radio:checked").val();
		var valor = $("#valor").val();
		var valor_minimo = $("#valor_minimo").val();
		var valor_maximo = $("#valor_maximo").val();
						
		var value = id_taxa + ";" + nome_taxa + ";" + valor + ";" +
		valor_minimo + ";" + valor_maximo + ";" +
		id_moeda + ";" + moeda + ";" + id_unidade + ";" + unidade + ";" + modalidade;

		var label = nome_taxa + " | " + moeda + " " + valor + " " + unidade + " | MIN " + valor_minimo + " | MAX " + valor_maximo + " " + modalidade; 

		$("#taxas_selecionadas", window.parent.document).append(new Option(label, value));
		
		document.getElementById("frame").src = "/Clientes/propostas/index.php/loading/";
		
		$("#pop",window.parent.document).hide();
		
	});
	
});