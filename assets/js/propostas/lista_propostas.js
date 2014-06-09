$(document).ready(function(){
    
    $("#voltar").click(function(){
        window.location = "/Clientes/propostas/index.php/propostas/propostas/realizar_busca";
    });
    
    $("a").click(function(){
        
        var id_proposta = $(this).attr("id_proposta");
        var tipo_proposta = $(this).attr("tipo_proposta");

        if( $(this).attr("id_proposta") != undefined )
        {   
            var url = "/Clientes/propostas/index.php/propostas/propostas/consultar/" + id_proposta + "/" + tipo_proposta;
            NovaJanela(url,"PPA SCOA",800,600,"yes");            
        }
        else if( $(this).attr("completo") != undefined )
        {            
            var url = "/Clientes/propostas/index.php/propostas/propostas/consultar/" + $(this).attr("completo") + "/" + tipo_proposta + "/0" + "/10000/";
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