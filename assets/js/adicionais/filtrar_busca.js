$(document).ready(function(){
	
	// Inicializa os elemento da tela
	$("#progressbar").hide();
	
	$("#data_inicial").attr("readonly",true);
	$("#data_inicial").datepicker({
		changeYear: true , 
		changeMonth: true, 
	});
	
	$("#data_final").attr("readonly",true);
	$("#data_final").datepicker({
		changeYear: true , 
		changeMonth: true, 
	});
			
	$("div[id^='pesquisa_']").hide();
	
	// Dicas de preenchimento da tela
        $( document ).tooltip({
            position: { my: "left top+15", at: "left top-65", collision: "flipfit" }
        });
	
	$("#tipo_cliente_busca").change(function(){
				
		switch ($(this).val()) 
		{
			case "0":
				$("div[id^='pesquisa_']").hide();
				$("div[id^='pesquisa_'] input").val("");
			break;	
			
			case "1":
				$("div[id^='pesquisa_']").hide();
				$("div[id^='pesquisa_'] input").val("");
				$("#pesquisa_cliente").show('slow');
			break;
			
			case "2":
				$("div[id^='pesquisa_']").hide();
				$("div[id^='pesquisa_'] input").val("");
				$("#pesquisa_grupo_comercial").show('slow');
			break;
			
			case "3":
				$("div[id^='pesquisa_']").hide();
				$("div[id^='pesquisa_'] input").val("");
				$("#pesquisa_grupo_cnpj").show('slow');
			break;
			
			default:
				$("div[id^='pesquisa_']").hide();
				$("div[id^='pesquisa_'] input").val("");
			
		}
		
	});
	
	$( "#cliente" ).autocomplete({
		
		source: "/Libs/autocompletar/clientes.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Cliente "+ ui.item.label +" Selecionado");
			$("#id_cliente").val(ui.item.id);				

		}
	});
	
	$( "#grupo_comercial" ).autocomplete({
		
		source: "/Libs/autocompletar/grupo_comercial.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Grupo Comercial "+ ui.item.label +" Selecionado");
			$("#id_grupo_comercial").val(ui.item.id);				

		}
	});
	
	$( "#grupo_cnpj" ).autocomplete({
		
		source: "/Libs/autocompletar/grupo_cnpj.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Grupo de Cnpj "+ ui.item.label +" Selecionado");
			$("#id_grupo_cnpj").val(ui.item.id);				

		}
	});
	
	$( "#vendedor" ).autocomplete({
		
		source: "/Libs/autocompletar/usuarios.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Vendedor(a) "+ ui.item.label +" Selecionado(a)");
			$("#id_vendedor").val(ui.item.id);				

		}
	});
	
	$( "#customer" ).autocomplete({
		
		source: "/Libs/autocompletar/usuarios.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Customer "+ ui.item.label +" Selecionado");
			$("#id_customer").val(ui.item.id);				

		}
	});
	
	$( "#usuario_cadastro" ).autocomplete({
		
		source: "/Libs/autocompletar/usuarios.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Usuário(a) "+ ui.item.label +" Selecionado(a)");
			$("#id_usuario_cadastro").val(ui.item.id);				

		}
	});
	
	$( "#nome_taxa" ).autocomplete({
		
		source: "/Libs/autocompletar/taxas/taxa_adicional.php",
		minLength: 3,
		select: function( event, ui ){
			
			alert("Taxa "+ ui.item.label +" Selecionada");
			$("#id_taxa").val(ui.item.id);				

		}
	});
	
	$("#localizar").click(function(){
		
		$("form").submit().delay(4000);
		$("#progressbar").show('slow');
		
	});
	
	//Barra de Progresso     
    $( "#progressbar" ).progressbar({
        value: 100       
    });
     
    IndeterminateProgressBar($("#progressbar"));
             
    function IndeterminateProgressBar(pb) 
    {
        $(pb).css({ "padding-left": "0%", "padding-right": "90%" });
        $(pb).progressbar("option", "value", 100);
        $(pb).animate({ paddingLeft: "90%", paddingRight: "0%" }, 4000, "linear",
        function () { IndeterminateProgressBar(pb); });
    }
    
    $("#novo").click(function(){
		window.location = "/Clientes/propostas/index.php/adicionais/adicionais/novo/";
	});
	
});