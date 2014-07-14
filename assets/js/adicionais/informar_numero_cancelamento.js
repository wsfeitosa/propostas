$(function(){
    
    $("#progressbar").hide();
    
    // Dicas de preenchimento da tela
    $( document ).tooltip({
        position: { my: "left top+15", at: "left top-65", collision: "flipfit" }
    });
    
    $("#novo").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/novo/';
	});
    
    $("#localizar").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/filtrar_busca/';
	});
	
	$("#voltar").click(function(){
		window.location = '/modulo.php';
	});
    
    $("#salvar").click(function(){
        
        if($("#numero").val() == "")
        {
            alert("Informe um numero de acordo para ser cancelado!");            
        }
        else
        {
            $("#progressbar").show();
			$("#corpo").hide();
			            
			$('form').submit().delay(4000);
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