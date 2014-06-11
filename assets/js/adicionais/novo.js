$(document).ready(function(){
	
	$("#progressbar").hide();
	
	$("#dialog-confirm").hide();

	// Dicas de preenchimento da tela
	$( document ).tooltip();
	
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
	
	$("#pesquisar_cliente").click(function(){
						
		var url = "/Clientes/propostas/index.php/clientes/clientes/find/" + $("#cliente").val() + "/adicionais_frete.js";
				
		NovaJanela(url, "Clientes Encontrados", 1024, 768, "yes");
		
	});
	
	$("#localizar").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/filtrar_busca/';
	});
	
	$("#voltar").click(function(){
		window.location = '/modulo.php';
	});
	
	/** Autocompletar do nome dos clientes **/
	$("#cliente").autocomplete({
		
		source: "/Libs/autocompletar/clientes.php",
		minLength: 3,
		select: function( event, ui ){
			
			url = "/Clientes/propostas/index.php/adicionais/adicionais/existe_acordo_cliente/" + ui.item.id;
			
			$.get(url,function(data){
								
				var existe_acordo = $(data).find('existe_acordo').text();
				
				if( existe_acordo == 1 )
				{										
					alert("Este cliente ja possui um acordo de adicionais vigente cadastrado no sistema!\nSe existirem conflitos de informacoes o acordo podera nao ser salvo!");										
				}
				
				$("#cliente").val("");
				$("#clientes_selecionados").append(new Option(ui.item.label, ui.item.id));
								
			},"xml");	
												
		}	
		
	});
	/**
	$("#cliente").blur(function(){
		$(this).val("");
	});
	**/
	
	$("#novo").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/novo/';
	});
	
	$("#clientes_selecionados").change(function(){
		$("#cliente").val("");
	});
	
	$("#excluir_cliente").click(function(){
		$("#clientes_selecionados option:selected").remove();
	});
	
	$("#excluir_taxa").click(function(){
		$("#taxas_selecionadas option:selected").remove();
	});
	
	$("#incluir_taxa").click(function(){
		
		var url = "/Clientes/propostas/index.php/adicionais/adicionais/adicionar_taxa/";
		
		NovaJanela(url, "Incluir Taxa", 800, 600, "yes");
	});
	
	function NovaJanela(pagina,nome,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        win = window.open(pagina,nome,settings);
    }
	
	$("#salvar").click(function(){
		
		var msg = "";
		var erro = false;
						
		if( $("#sentido").val() == "0" )
		{
			msg += "Infome um sentido (IMP ou EXP)!\n";
			erro = true;
		}
		
		if( $("#clientes_selecionados option").length == "0" )
		{
			msg += "Selecione pelo menos um cliente antes de salvar!\n";
			erro = true;
		}	
						
		if( $("#taxas_selecionadas option").length == "0" )
		{
			msg += "Inclua pelo menos uma taxa no acordo antes de salvar!\n";
			erro = true;
		}	
						
		if( erro == true )
		{
			alert(msg);
			return false;
		}
		else
		{			
			/**
			 * Influênciar os retroativos
			 */			
			$("#dialog-confirm").dialog({
				 resizable: true,
				 height:200,
				 modal: true,
				 buttons: {
					 	"Apenas Para Novas": function() {
					 		
					 	$("#alterar_retroativos").val("N");
					 	
					 	// Seleciona todas as taxas dos combos antes de submeter o formulario
			            $("#clientes_selecionados option").each(function() {
			                $(this).attr("selected", "selected");
			            });
			            
			            $("#taxas_selecionadas option").each(function() {
			                $(this).attr("selected", "selected");
			            });
			            
			            $("#progressbar").show();
			            $("#corpo").hide();
			            
			            $('form').submit().delay(4000);
					 	
					 },
				 		"Novas e Existentes": function() {
				 			
				 		$("#alterar_retroativos").val("S");
				 		
				 		// Seleciona todas as taxas dos combos antes de submeter o formulario
			            $("#clientes_selecionados option").each(function() {
			                $(this).attr("selected", "selected");
			            });
			            
			            $("#taxas_selecionadas option").each(function() {
			                $(this).attr("selected", "selected");
			            });
			            
			            $("#progressbar").show();
			            $("#corpo").hide();
			            
			            $('form').submit().delay(4000);
			            
				 	}
				 }
			});	
		}	
		
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
	
});