$(document).ready(function(){
	
	// Dicas de preenchimento da tela
        $( document ).tooltip({
            position: { my: "left top+15", at: "left top-65", collision: "flipfit" }
        });
	
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
	
	$( "#taxa" ).autocomplete({
		
		source: "/Libs/autocompletar/taxas/taxa_adicional.php",
		minLength: 3,
		select: function( event, ui ){

			$("#id_taxa").val(ui.item.id);				

		}
	});
	
	$("#taxas_exportacao").click(function(){
		var url = "/Clientes/propostas/index.php/adicionais/adicionais/exibir_taxas_permitidas";

		NovaJanela(url,"Referencias das Taxas EXP",1024,768,"yes");
	});

	$("#salvar").click(function(){
		
		var msg = "";
		var erro = false;
		var modalidade = "";
		
		if( $("#id_taxa").val() == "0" )
		{
			msg += "Selecione uma taxa antes de salvar!\n";
			erro = true;
		}	
		
		$("input[name='modalidade']").each(function(){
									
			if( $(this).is(":checked") == true )
			{
				modalidade = $(this).val();				
			}	
			
		});
		
		if( modalidade == "" )
		{
			msg += "Selecione ao menos uma modalidade antes de salvar a taxa!\n";
			erro = true;
		}	
		
		if( erro == true )
		{
			alert(msg);
			return false;
		}
		else
		{
			
			var value = "";
			var label = "";
																					
			value = $("#id_taxa").val() + ";" + $("#unidade").val() + ";" +
                                $("#moeda").val() + ";" + modalidade + ";" +
                                $("#valor").val() + ";" + $("#valor_minimo").val() + ";" + 
                                $("#valor_maximo").val();
									
			label = $("#id_taxa option:selected").text() + " " + $("#moeda option:selected").text() + " " + 
                                $("#valor").val() + " " + $("#unidade option:selected").text() + " | MIN. " +
                                $("#valor_minimo").val() + " | MAX. " + $("#valor_maximo").val() + " " + 
                                modalidade;
			
			if( $("#index_combo").val() != "" )
			{
                            window.opener.document.getElementById("taxas_selecionadas").options[$("#index_combo").val()] = null;
			}	
			
			window.opener.$("#taxas_selecionadas").append(new Option(label, value));
			
			window.close(this);
			
		}	
		
	});

	function NovaJanela(pagina,nome,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        win = window.open(pagina,nome,settings);
    }
	
});