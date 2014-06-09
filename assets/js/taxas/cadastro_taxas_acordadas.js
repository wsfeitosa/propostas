$(document).ready(function(){
	
	/** Calandários JQuery **/
	
	$( "#inicio" ).datepicker({ 
		minDate: 0,
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
			 $( "#validade" ).datepicker( "option", "minDate", selectedDate );
		 }
	}).attr("readonly","readonly");

	$( "#validade" ).datepicker({ 	
		minDate: 0,
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
			 $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
		}
	}).attr("readonly","readonly");
	
	/** Quando mudar a modalidade de IMP para EXP ou vice-versa tem que zerar às taxas **/
	$("#sentido").change(function(){
		$("#taxas_selecionadas").empty();
	});
	
	/** Faz a busca por um cliente **/
    $("#cliente").blur(function() {

        if ($(this).val() != "")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/clientes/clientes/find/" + $("#cliente").val() + "/find_cliente_taxas.js";

            $("#pop").show("slow");
        }

    });
    
    /** Adiciona uma taxa no acordo **/
    $("#incluir_taxa").click(function(){
    	document.getElementById("frame").src = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/add/acordos_taxas_add.js";

        $("#pop").show("slow");
    });
    

    
    /** Remove um cliente selecionado **/
    $("#excluir_cliente").click(function() {

        $("#clientes_selecionados option:selected").remove();

    });
    
    /** Remove uma taxa selecionada **/
    $("#excluir_taxa").click(function() {

        $("#taxas_selecionadas option:selected").remove();

    });
    
    /** Busca às taxas locais para preenchimento do acordo de taxas locais **/
    $("#portos_selecionados").change(function(){
    	
    	 var id = $(this).find("option").filter(":selected").val();
         var text = $(this).find("option").filter(":selected").text();
         var index = $(this).attr("selectedIndex");
    	    	    	
    	/** verifica se algum cliente já foi selecionado **/
    	var qtd_itens = 0;

        $("#clientes_selecionados option").each(function() {
            qtd_itens++;
        });
    	
    	if( qtd_itens < 1 )
    	{
    		alert("Selecione o cliente antes de selecionar os portos!");
    		return false;
    	}
    	
    	/** Verifica se o sentido foi selecionado (IMP ou EXP) **/
    	if( $("#sentido").val() == "0" )
    	{
    		alert("Selecione o sentido!");
    		return false;
    	}	
    	
    	/** junta todos os clientes em uma string para enviar para o controller **/
    	var clientes_selecionados = "";
    	
    	$("#clientes_selecionados option").each(function(){
    		clientes_selecionados += $(this).val() + "|";
    	});
    	
        var comprimento_str = (clientes_selecionados.length - 1)*1;
        clientes_selecionados = clientes_selecionados.substring(0,comprimento_str);

    	/** Limpa o combo de taxas **/
    	$("#taxas_selecionadas").empty();
    	
    	/** Envia uma requisição ajax para buscar às taxas dos portos **/
    	//INICIO
        $.ajax({
            type: "POST",
            url: "/Clientes/propostas/index.php/taxas_locais/taxas_locais/find/" + id + "/" + clientes_selecionados + "/" + $("#sentido").val(),
            beforeSend: function() {
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml) {
            	            	            	
                $(xml).each(function() {
                	                	                	
                	if( $(this).find("error").text() ) 
                	{
                		alert($(this).find("error").text());
                		return false;
                	}	
                	
                    $("#taxas_selecionadas").empty();

                    $(this).find("taxa_local").each(function(){
                    	
                    	var id_taxa = $(this).find("id_taxa_adicional").text();
                    	var nome_taxa = $(this).find("taxa").text();
                    	var valor = $(this).find("valor").text();
                    	var valor_minimo = $(this).find("valor_minimo").text();
                    	var valor_maximo = $(this).find("valor_maximo").text();
                    	var id_moeda = $(this).find("id_moeda").text();
                    	var moeda = $(this).find("moeda").text();
                    	var id_unidade = $(this).find("id_unidade").text();
                    	var unidade = $(this).find("unidade").text();
                    	
                    	var value = id_taxa + ";" + nome_taxa + ";" + valor + ";" +
                    				valor_minimo + ";" + valor_maximo + ";" +
                    				id_moeda + ";" + moeda + ";" + id_unidade + ";" + unidade;
                    	
                    	var label = nome_taxa + " | " + moeda + " " + valor + " " + unidade + " | MIN " + valor_minimo + " | MAX " + valor_maximo; 
                    	
                    	 $("#taxas_selecionadas").append(new Option(label, value));
                    	
                    });

                	$("#msg").html("");

                });

            }//END SUCCESS

        });
        //FIM    	
    });
            
    $("#salvar").click(function(){
    	
    	var msg = "";
    	var erro = 0;
    	
    	if( $("#sentido").val() == "0" )
    	{
    		msg += "Selecione um sentido (IMP ou EXP)!\n";
    		erro = 1;
    	}	
    	    	
    	if( $("#clientes_selecionados option").length < 1 )
    	{
    		msg += "Selecione pelo menos um cliente antes de salvar!\n";
    		erro = 1;
    	}	
    	
    	if( $("#taxas_selecionadas option").length < 1 )
    	{
    		msg += "Selecione pelo menos uma taxa antes de salvar!\n";
    		erro = 1;
    	}
    	
    	if( erro == 1 )
    	{
    		alert(msg);
    		return false;
    	}	
    	
    	/** Seleciona todos os clientes do combo antes de submeter **/	
    	$("#clientes_selecionados option").each(function(){
    		$(this).attr("selected","selected");
    	});
    	
    	/** Seleciona todas às taxas do combo de taxas antes de submenter o formulário **/
    	$("#taxas_selecionadas option").each(function(){
    		$(this).attr("selected","selected");
    	});
    	
    	/** Verifica se já existe acordo cadastrado para algum dos clientes em alguma das rotas **/
    	var clientes_selecionados = "";
    	var portos_selecionados = "";
    	
    	$("#clientes_selecionados option").each(function(){
    		clientes_selecionados += $(this).val() + ":";
    	});
    	
        var comprimento_str = (clientes_selecionados.length - 1)*1;
        clientes_selecionados = clientes_selecionados.substring(0,comprimento_str);

    	$("#portos_selecionados option:selected").each(function(){
    		portos_selecionados += $(this).val() + ":";
    	});
    	
        var comprimento_str = (portos_selecionados.length - 1)*1;
        portos_selecionados = portos_selecionados.substring(0,comprimento_str);

    	 $.ajax({
             type: "POST",
             url: "/Clientes/propostas/index.php/taxas_locais/taxas_locais/check_before_save/" + clientes_selecionados + "/" + portos_selecionados + "/" + $("#sentido").val() + "/" + $("#inicio").val() + "/" + $("#validade").val(),
             beforeSend: function() {
                 $("#msg").html("Aguarde Processando...");
             },
             dataType: "xml",
             success: function(xml) {
             	            	            	
                 $(xml).each(function() {
                 	
                	 var existem_duplicacoes = $(this).find("acordos_duplicados").text();
                 	 
                	 if( existem_duplicacoes == 1 )
                	 {
                		 var numeros_acordos_conflito = "";
                		 
                		 $(this).find("numero_acordo").each(function(){
                			 numeros_acordos_conflito += $(this).text() + " ";
                		 });
                		 
                		 alert("Os Acordos: " + numeros_acordos_conflito + " tem dados em conflito com o acordo sendo cadastrado\r\nResolva os confiltos antes de salvar este acordo!");
                	 }
                	 else
                	 {
                		 $("#nova").submit();
                	 }	 
                	 
                 });

             }//END SUCCESS

         }); //END AJAX CALL    	
            	    	
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
	
});//END FILE