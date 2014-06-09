$(document).ready(function(){
    
    $("#accordion").accordion();
    
    $("#novo").click(function(){
        window.location = "/Clientes/propostas/index.php";
    });
    
    $("#alterar").click(function(){
    	
    	$("#form_consulta").attr("action","/Clientes/propostas/index.php/propostas/propostas/alterar/");    	
    	$("#form_consulta").submit();
    	
    });
    
    $("#localizar").click(function(){
        window.location = "/Clientes/propostas/index.php/propostas/propostas/realizar_busca/";
    });
    
});//END FILE

