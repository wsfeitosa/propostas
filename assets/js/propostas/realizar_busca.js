$(document).ready(function(){
    
    // Inicializa os elemento da tela
    $("#progressbar").hide();
    $("input[name*='_imp']").hide();
      
    // Dicas de preenchimento da tela
    $( document ).tooltip();
    
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
    
    $("#sentido").change(function(){
        
        $("#id_origem").val("");
        $("#id_embarque").val("");
        $("#id_desembarque").val("");
        $("#id_destino").val("");
        
        if( $(this).val() == "EXP" )
        {
            $("input[name*='_imp']").val("").hide();
            $("input[name*='_exp']").show("drop");            
        }
        
        if( $(this).val() == "IMP" )
        {
            $("input[name*='_imp']").show("drop");
            $("input[name*='_exp']").val("").hide();
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
    
    $( "#usuario_cadastro" ).autocomplete({
		
        source: "/Libs/autocompletar/usuarios.php",
        minLength: 3,
        select: function( event, ui ){

            alert("Usuário(a) "+ ui.item.label +" Selecionado(a)");
            $("#id_usuario_cadastro").val(ui.item.id);				

        }
    });
    
    $( "#vendedor_imp" ).autocomplete({
		
        source: "/Libs/autocompletar/usuarios.php",
        minLength: 3,
        select: function( event, ui ){

            alert("Vendedor(a) "+ ui.item.label +" Selecionado(a)");
            $("#id_vendedor_imp").val(ui.item.id);				

        }
    });

    $( "#customer_imp" ).autocomplete({

        source: "/Libs/autocompletar/usuarios.php",
        minLength: 3,
        select: function( event, ui ){

            alert("Customer "+ ui.item.label +" Selecionado");
            $("#id_customer_imp").val(ui.item.id);				

        }
    });
    
    $( "#vendedor_exp" ).autocomplete({
		
        source: "/Libs/autocompletar/usuarios.php",
        minLength: 3,
        select: function( event, ui ){

            alert("Vendedor(a) "+ ui.item.label +" Selecionado(a)");
            $("#id_vendedor_exp").val(ui.item.id);				

        }
    });

    $( "#customer_exp" ).autocomplete({

        source: "/Libs/autocompletar/usuarios.php",
        minLength: 3,
        select: function( event, ui ){

            alert("Customer "+ ui.item.label +" Selecionado");
            $("#id_customer_exp").val(ui.item.id);				

        }
    });
    
    $( "#origem_exp" ).autocomplete({
						
        source: "/Libs/autocompletar/portos/EXP/origem.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_origem").val(ui.item.id);				

        }
    });

    $( "#origem_imp" ).autocomplete({

        source: "/Libs/autocompletar/portos/IMP/origem.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_origem").val(ui.item.id);				

        }
    });

    $( "#embarque_exp" ).autocomplete({

        source: "/Libs/autocompletar/portos/EXP/embarque.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_embarque").val(ui.item.id);				

        }
    });

    $( "#embarque_imp" ).autocomplete({

        source: "/Libs/autocompletar/portos/IMP/embarque.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_embarque").val(ui.item.id);				

        }
    });

    $( "#desembarque_exp" ).autocomplete({

        source: "/Libs/autocompletar/portos/EXP/desembarque.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_desembarque").val(ui.item.id);				

        }
    });

    $( "#desembarque_imp" ).autocomplete({

        source: "/Libs/autocompletar/portos/IMP/desembarque.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_desembarque").val(ui.item.id);				

        }
    });

    $( "#destino_exp" ).autocomplete({

        source: "/Libs/autocompletar/portos/EXP/destino.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_destino").val(ui.item.id);				

        }
    });

    $( "#destino_imp" ).autocomplete({

        source: "/Libs/autocompletar/portos/IMP/destino.php",
        minLength: 3,
        select: function( event, ui ){

            $("#id_destino").val(ui.item.id);				

        }
    });
    
    $("#localizar").click(function(){
       
       $("#progressbar").show();
       $("form").submit();
       
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
        window.location = "/Clientes/propostas/index.php";
    });
   
});//END FILE