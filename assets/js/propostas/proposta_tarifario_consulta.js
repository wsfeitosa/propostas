$(document).ready(function(){
    
    $("#accordion").accordion();
    /**
    $("#gerar_excel").click(function(){
    	window.location = "/Clientes/propostas/index.php/propostas/propostas/gerar_excel/" + $("#id_proposta").val() + "/" + $("#tipo_proposta").val();
    });
    **/
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

    $("#ver_mais").click(function(){

        $("#ver_mais_carregando_direita").html("<img src='/Imagens/loading.gif' border='0'>");
        $("#ver_mais_carregando_esquerda").html("<img src='/Imagens/loading.gif' border='0'>");

        var url = "/Clientes/propostas/index.php/propostas/propostas/ver_mais/" + $("#id_proposta").val() + "/" + $("#limit").val();

        $.get( url, function( data ) {
            $("#accordion").append(data).accordion('destroy').accordion();

            var limit = ($("#limit").val() * 1);

            limit = (limit + 5);

            $("#limit").val(limit);
            
            $("#ver_mais_carregando_direita").html("");
            $("#ver_mais_carregando_esquerda").html("");
        });

    });

    
});//END FILE

