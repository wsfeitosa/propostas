$(function(){
	
	// Dicas de preenchimento da tela
        $( document ).tooltip({
            position: { my: "left top+15", at: "left top-65", collision: "flipfit" }
        });
	
	$("#voltar").click(function(){        
        window.location = "/Clientes/propostas/index.php/adicionais/adicionais/filtrar_busca/";
    });
    
	$("#novo").click(function(){
		window.location = "/Clientes/propostas/index.php/adicionais/adicionais/novo/";
	});
	
    $("a").click(function(){
            	    	
        var id_acordo = $(this).attr("id_acordo");
                        
        if( $(this).attr("id_acordo") != undefined )
        {   
            var url = "/Clientes/propostas/index.php/adicionais/adicionais/consultar/" + id_acordo;
            NovaJanela(url,"PPA SCOA",800,600,"yes");            
        }        
        else
        {            
            return false;
        }         
        
    });
    
    function NovaJanela(pagina,nome,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        win = window.open(pagina,nome,settings);
    }
	
});
