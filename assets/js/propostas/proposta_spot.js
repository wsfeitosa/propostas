$(document).ready(function() {
	
	$("#progressbar").hide();
	
    /** Bloqueia o porto de embarque se for imp ou de descarga se for exp **/
    if ($("#sentido").val() == "IMP")
    {
        $("#embarque").attr("readonly", "readonly");
        $("#desembarque").attr("readonly", false);
    }
    else
    {
        $("#embarque").attr("readonly", false);
        $("#desembarque").attr("readonly", "readonly");
    }
    
    $( "#inicio" ).datepicker({ 
		minDate: 'today',
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "http://" + location.host + "/Clientes/propostas/assets/img/calendar.gif",
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
			buttonImage: "http://" + location.host + "/Clientes/propostas/assets/img/calendar.gif",
			buttonImageOnly: true,
            /**
			onClose: function( selectedDate ) {
				 $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
			}**/
	}).attr("readonly","readonly");
    
    /** Formata os campos com , e . **/
     $('#peso').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '',
        centsLimit: 3
    });

    $('#cubagem').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '',
        centsLimit: 3
    });
    
    /** Se os campos PP ou CC forem alterados zera os valores **/
    $("#pp").click(function(){    	
    	$("#destino").val("");
    	$("#un_destino").val("");
    	$("#frete_adicionais").empty();
    	$("#taxas_locais").empty();    	
    });
    
    $("#cc").click(function(){    	
    	$("#destino").val("");
    	$("#un_destino").val("");
    	$("#frete_adicionais").empty();
    	$("#taxas_locais").empty();    	
    });
    
    $("#imo").click(function(){    	
    	$("#destino").val("");
    	$("#un_destino").val("");
    	$("#frete_adicionais").empty();
    	$("#taxas_locais").empty();    	
    });

    /** Adiciona o contato cc no campo de contatos selecionados **/
    $("#adicionar_contato_cc").click(function() {

        if ($("#contato_cc").val() == "")
        {
            return false;
        }

        if (!IsEmail($("#contato_cc").val()))
        {
            alert("Email Inválido!");
            $("#contato_cc").val("");
            $("#contato_cc").focus();
            return false;
        }
        
        $("#contatos_cc_selecionados").append(new Option($("#contato_cc").val(), $("#contato_cc").val()));

        $("#contato_cc").val("");

    });

    /** Faz a busca por um cliente **/
    $("#cliente").blur(function() {

        if ($(this).val() != "")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/clientes/clientes/find/" + $("#cliente").val() + "/find.js";

            $("#pop").show("slow");
        }

    });

    /** Faz a busca por contatos avulsos **/
    $("#adicionar_email_avulso").click(function(){

        var clientes = "";

        $("#clientes_selecionados option").each(function(){
            clientes += $(this).val() + ":";
        });
        
        document.getElementById("frame").src = "/Clientes/propostas/index.php/clientes/contatos/find/" + clientes;

        $("#pop").show("slow");

    });
    
    /** Adiciona uma taxa na proposta **/
    $("#incluir_taxa").click(function(){
    	document.getElementById("frame").src = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/add/propostas_taxas_add.js";

        $("#pop").show("slow");
    });
    
    $("#incluir_taxa_local").click(function(){
    	document.getElementById("frame").src = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/add/propostas_taxas_locais_add.js";

        $("#pop").show("slow");
    });
    
    /** Remove uma taxa selecionada **/
    $("#excluir_taxa").click(function() {

        //$("#frete_adicionais option:selected").remove();
        $("#frete_adicionais").empty();

    });
    
    /** Remove uma taxa selecionada **/
    $("#excluir_taxa_local").click(function() {

        $("#taxas_locais option:selected").remove();

    });
    
    /** Faz a busca pelo porto de origem **/
    $("#origem").blur(function() {

        /** Se o campo estiver vazio então não toma nenhuma ação **/
        if ($(this).val() == "")
        {
            return false;
        }

        /** Verifica se a proposta é de importação ou exportação **/
        if ($("#sentido").val() == "IMP")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_origin/" + $("#origem").val() + "/Importacao" ;

            $("#pop").show("slow");
        }
        else
        {
        	 document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_origin/" + $("#origem").val() + "/Exportacao/";

             $("#pop").show("slow");

        }

    });

    /** Faz a busca pelo porto de embarque **/
    $("#embarque").blur(function() {

        /** Se o campo estiver vazio então não toma nenhuma ação **/
        if ($(this).val() == "")
        {
            return false;
        }

        /** Verifica se a proposta é de importação ou exportação **/
        if ($("#sentido").val() == "IMP")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_loading/" + $("#embarque").val() + "/Importacao";

            $("#pop").show("slow");
        }
        else
        {
        	document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_loading/" + $("#embarque").val() + "/Exportacao";

            $("#pop").show("slow");
        }

    });

    /** Faz a busca pelo porto de desembarque **/
    $("#desembarque").blur(function() {

        /** Se o campo estiver vazio então não toma nenhuma ação **/
        if ($(this).val() == "")
        {
            return false;
        }

        /** Verifica se a proposta é de importação ou exportação **/
        if ($("#sentido").val() == "IMP")
        {
        	 document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_discharge/" + $("#desembarque").val() + "/Importacao";

             $("#pop").show("slow");
        }
        else
        {
        	document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_discharge/" + $("#desembarque").val() + "/Exportacao";

            $("#pop").show("slow");
        }

    });

    /** Faz a busca pelo porto de destino **/
    $("#destino").blur(function() {

        /** Se o campo estiver vazio então não toma nenhuma ação **/
        if ($(this).val() == "")
        {
            return false;
        }

        /** Verifica se a proposta é de importação ou exportação **/
        if ($("#sentido").val() == "IMP")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_destination/" + $("#destino").val() + "/Importacao";

            $("#pop").show("slow");
        }
        else
        {
        	document.getElementById("frame").src = "/Clientes/propostas/index.php/portos/portos/find_destination/" + $("#destino").val() + "/Exportacao";

            $("#pop").show("slow");
        }

    });

    /** Remove um contato cc selecionado **/
    $("#remover_contato_cc").click(function() {

        $("#contatos_cc_selecionados option:selected").remove();

    });

    /** Remove um cliente selecionado **/
    $("#remover_cliente").click(function() {

        $("#clientes_selecionados option:selected").remove();

    });

    /** Remove um email para selecionado **/
    $("#remover_contato_para").click(function() {

        $("#contatos_para_selecionados option:selected").remove();

    });

    /** Remove uma rota selecionada **/
    $("#remover_rota").click(function() {

        var id_item = $("#rotas_adicionadas option:selected").val();

        $.ajax({
            type: "POST",
            url: "/Clientes/propostas/index.php/propostas/itens_propostas/excluirItemSessao/" + id_item,
            data: {itemIndex: id_item}
        }).done(function() {
            alert("Item Excluido!");
            $("#rotas_adicionadas option:selected").remove();
        });

    });

    /** Volta para a tela inicial **/
    $("#voltar").click(function() {
        window.location = "/Clientes/propostas/index.php/";
    });

    /** Solicita alteração de uma taxa adicional ou frete **/
    $("#frete_adicionais").dblclick(function() {

        var id = $(this).val();
        var text = $(this).text();
        var index = $(this).children(":selected").index();
                                
        if( $("#id_proposta").val() != undefined  )
        {           
            document.getElementById("frame").src = "/Clientes/propostas/index.php/desbloqueios/desbloqueios/taxa/" + $("#sentido").val() + "/" + id + "/Taxa_Adicional/" + $("#posicao_combo").val() + "/" + index + "/proposta";

            $("#pop").show("slow");
        }
        
    });

    /** Solicita a alteração de uma taxa local **/
    $("#taxas_locais").dblclick(function() {
        
        var id = $(this).val();
        var text = $(this).text();
        var index = $(this).children(":selected").index();
                        
        if( $("#id_proposta").val() != undefined  )
        {         
            document.getElementById("frame").src = "/Clientes/propostas/index.php/desbloqueios/desbloqueios/taxa/" + $("#sentido").val() + "/" + id + "/Taxa_Local/" + $("#posicao_combo").val() + "/" + index + "/proposta";

            $("#pop").show("slow");
        }
        
    });

    $("#rotas_adicionadas").dblclick(function() {

        var id = $(this).find("option").filter(":selected").val();
        var text = $(this).find("option").filter(":selected").text();
        var index = $(this).attr("selectedIndex");

        //INICIO
        $.ajax({
            type: "POST",
            url: "/Clientes/propostas/index.php/propostas/itens_propostas/recuperarItemSessao/" + id + "/" + "/" + (new Date()).getTime(),
            beforeSend: function() {
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml) {

                $(xml).each(function() {

                    var erro = $(this).find("erro").text();

                    if (erro == 1)
                    {
                        alert("Aconteceu um problema e não foi possivel recuperar o item\nContate o deptarmento de TI.");
                        return false;
                    }
                    
                    $("#id_tarifario").val($(this).find("id_tarifario").text());
                    $("#posicao_combo").val($(this).find("id").text());
                    $("#mercadoria").val($(this).find("mercadoria").text());
                    $("#inicio").val($(this).find("inicio").text());
                    $("#validade").val($(this).find("validade").text());
                    $("#peso").val($(this).find("peso").text());
                    $("#cubagem").val($(this).find("cubagem").text());
                    $("#volumes").val($(this).find("volumes").text());
                    $("#origem").val($(this).find("origem").text());
                    $("#embarque").val($(this).find("embarque").text());
                    $("#desembarque").val($(this).find("desembarque").text());
                    $("#destino").val($(this).find("destino").text());
                    $("#un_origem").val($(this).find("un_origem").text());
                    $("#un_embarque").val($(this).find("un_embarque").text());
                    $("#un_desembarque").val($(this).find("un_desembarque").text());
                    $("#un_destino").val($(this).find("un_destino").text());
                    $("#observacao_interna").val($(this).find("observacao_interna").text());
                    $("#observacao_cliente").val($(this).find("observacao_cliente").text());

                    /** 
                     * Verifica se o PP ou CC foram selecionados e marca ou não o campo
                     */
                    document.getElementById("pp").checked = false;
                    document.getElementById("cc").checked = false;
                    
                    if ($(this).find("pp").text() == "PP")
                    {
                        document.getElementById("pp").checked = true;
                    }

                    if ($(this).find("cc").text() == "CC")
                    {
                        document.getElementById("cc").checked = true;
                    }

                    /** Preenche o campo imo **/
                    if( $(this).find("imo").text() == "S" )
                    {
                        $("#imo").attr("checked","checked");
                    }
                    else
                    {
                        $("#imo").attr("checked",false);        
                    } 

                    // preenche os campos de taxas
                    var frete_taxas = $(this).find("frete_adicionais").text().split("---");
                    var label_frete_taxas = $(this).find("labels_frete_adicionais").text().split("---");

                    // Antes de adicionar a taxas zera o combo de taxas
                    $("#frete_adicionais").empty();

                    for (i = 0; i < frete_taxas.length; i++)
                    {                        
                        // Não sei por que estão retornando algumas linhas vazias,
                        // então estou validando estas linhas
                        if (frete_taxas[i] != "")
                        {
                            $("#frete_adicionais").append(new Option(label_frete_taxas[i].replace(/PORC/gi,"%"), frete_taxas[i]));
                        }
                    }

                    var taxas_locais = $(this).find("taxas_locais").text().split("---");
                    var label_taxas_locais = $(this).find("labels_taxas_locais").text().split("---");

                    // Antes de adicionar a taxas zera o combo de taxas
                    $("#taxas_locais").empty();

                    for (x = 0; x < taxas_locais.length; x++)
                    {                        
                        // Não sei por que estão retornando algumas linhas vazias,
                        // então estou validando estas linhas
                        if (taxas_locais[x] != "")
                        {
                            $("#taxas_locais").append(new Option(label_taxas_locais[x].replace(/PORC/gi,"%"), taxas_locais[x]));
                        }
                    }

                    $("#msg").html("");

                });

            }//END SUCCESS

        });
        //FIM

    });

    /** Adiciona a rota ao campo de rotas selecionadas **/

    //primeiro verifica se já existem mais de 05 itens
    $("#incluir_rota").click(function() {

        /**
         * Esta variával armazena a posição do item combo de rotas incluidas,
         * através dela eu determino se o item é novo ou então é uma alteração
         * de um item já existênte.
         */
        var posicaoNoCombo = $("#posicao_combo").val();
        
        if (posicaoNoCombo !== "")
        {
            $('#rotas_adicionadas option[value="' + posicaoNoCombo + '"]').remove();
        }
        else
        {
            posicaoNoCombo = null;
        }
        
        var qtd_itens = 0;

        $("#rotas_adicionadas option").each(function() {
            qtd_itens++;
        });

        if (qtd_itens >= 5)
        {
            alert("O maximo possível para esta modalidade de propsta são 05 itens por envio!");
            return false;
        }

        //Validação para incluir o item
        var error = 0;
        var msg = "";

        if ($("#clientes_selecionados option").length == 0)
        {
            error = 1;
            msg += "Informe ao menos um cliente antes inserir o item\n";
        }

        if ($("#un_origem").val() == "")
        {
            error = 1;
            msg += "Infome o porto de origem\n";
        }

        if ($("#un_embarque").val() == "")
        {
            error = 1;
            msg += "Informe o porto de embarque\n";
        }

        if ($("#un_desembarque").val() == "")
        {
            error = 1;
            msg += "Informe o porto de desembarque\n";
        }

        if ($("#un_destino").val() == "")
        {
            error = 1;
            msg += "Informe o porto de destino\n";
        }
        
        if( $("#peso").val() == "" )
        {
        	error = 1;
        	msg += "Informe o peso da carga\n";
        }	
        
        if( $("#cubagem").val() == "" )
        {
        	error = 1;
        	msg += "Informe a cubagem da carga\n";
        }	
        
        if( $("#volumes").val() == "" )
        {
        	error = 1;
        	msg += "Informe a quantidade de volumes\n";
        }	
        
        if( $("#inicio").val() == "" )
        {
        	error = 1;
        	msg += "Informe a Data de Inicio da proposta\n";
        }	
        
        if( $("#validade").val() == "" )
        {
        	error = 1;
        	msg += "Informe a Validade da proposta\n";
        }	
        
        if( $("#mercadoria").val() == "" )
        {
        	error = 1;
        	msg += "Informe a Mercadoria antes de incluir o item\n";
        }	

        if (error == 1)
        {
            alert(msg);
            return false;
        }

        values_taxas_frete = "";
        labels_taxas_frete = "";
        
        /** valida às observações **/
        if( $("#observacao_cliente").val() == "" )
        {
            $("#observacao_cliente").val(" ");
        }
        
        if( $("#observacao_interna").val() == "" )
        {
            $("#observacao_interna").val(" ");
        }    

        /** Formata o frete e taxas sobre o frete **/
        $("#frete_adicionais option").each(function() {

            values_taxas_frete += $(this).val() + "---";
            labels_taxas_frete += $(this).text() + "---";

        });

        values_taxas_frete = values_taxas_frete.split(";");
        values_taxas_frete = values_taxas_frete.join(":");

        labels_taxas_frete = labels_taxas_frete.split(";");
        labels_taxas_frete = labels_taxas_frete.join(":");

        /** serializa as taxas locais para enviar via ajax **/
        values_taxas_locais = "";
        labels_taxas_locais = "";

        $("#taxas_locais option").each(function() {

            values_taxas_locais += $(this).val() + "---";
            labels_taxas_locais += $(this).text() + "---";

        });

        values_taxas_locais = values_taxas_locais.split(";");
        values_taxas_locais = values_taxas_locais.join(":");

        labels_taxas_locais = labels_taxas_locais.split(";");
        labels_taxas_locais = labels_taxas_locais.join(":");
        
        /** remove os caracteres % das taxas pois o mesmo não passa na url **/
        labels_taxas_locais = labels_taxas_locais.replace(/%/gi,"PORC");
        labels_taxas_frete = labels_taxas_frete.replace(/%/gi,"PORC");
        
        values_taxas_frete = values_taxas_frete.replace(/%/gi,"PORC");
        values_taxas_locais = values_taxas_locais.replace(/%/gi,"PORC");

        /** Verifica se os combos de taxa foram preenchidos e às taxas formatadas (caso da proposta só de taxas locais) **/
        if( labels_taxas_locais == "" )
        {
            labels_taxas_locais = "NULL";
        }  

        if( values_taxas_locais == "" )
        {
            values_taxas_locais = "NULL";
        }

        if( labels_taxas_frete == "" )
        {
            labels_taxas_frete = "NULL";
        }    

        if( values_taxas_frete == "" )
        {
            values_taxas_frete = "NULL";
        } 
                
        /** Verifica se o pp ou cc está selecionado antes de formar a url **/
        var pp = null;
        var cc = null;

        if ($("#pp").attr("checked") == "checked")
        {
            pp = "PP";
        }

        if ($("#cc").attr("checked") == "checked")
        {
            cc = "CC";
        }

        var imo = "";

        if( $("#imo").is(":checked") == true )
        {
            imo = "S";
        }
        else
        {
            imo = "N";
        }

        // INCICIO AJAX
        $.ajax({
            type: "POST",
            url: "/Clientes/propostas/index.php/propostas/itens_propostas/incluirItemSessao/" + $("#id_tarifario").val() + "/" + $("#mercadoria").val() + "/" +
                    pp + "/" + cc + "/" + $("#inicio").val() + "/" + $("#validade").val() + "/" +
                    $("#peso").val().replace(",", ".") + "/" + $("#cubagem").val().replace(",", ".") + "/" + $("#volumes").val().replace(",", ".") + "/" +
                    imo + "/" + $("#origem").val() + "/" + $("#embarque").val() + "/" + $("#desembarque").val() + "/" +
                    $("#destino").val() + "/" + $("#un_origem").val() + "/" + $("#un_embarque").val() + "/" +
                    $("#un_desembarque").val() + "/" + $("#un_destino").val() + "/" + encodeURI(values_taxas_frete) + "/" +
                    encodeURI(labels_taxas_frete.replace("/", " ")) + "/" + encodeURI(values_taxas_locais) + "/" + encodeURI(labels_taxas_locais.replace("/", " ")) + "/" +
                    encodeURI($("#observacao_interna").val().replaceAll("/","-")) + "/" + encodeURI($("#observacao_cliente").val().replaceAll("/","-")) + "/" + posicaoNoCombo + "/" +
                    (new Date()).getTime(),
            beforeSend: function() {
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml) {

                $(xml).each(function() {

                    var label = ""; //Label do option do combo de rotas adicionadas
                    var value = $(this).find("id_item").text(); //Valor do option do combo de rotas adicionadas

                    label += $("#origem").val() + "-";
                    label += $("#embarque").val() + "-";
                    label += $("#desembarque").val() + "-";
                    label += $("#destino").val();

                    $("#rotas_adicionadas").append(new Option(label, value));

                    // Zera os campos para seja inserido um novo item
                    $("#id_tarifario").val("");
                    $("#posicao_combo").val("");
                    $("#mercadoria").val("");                    
                    $("#peso").val("");
                    $("#cubagem").val("");
                    $("#volumes").val("");
                    $("#origem").val("");
                    $("#embarque").val("");
                    $("#desembarque").val("");
                    $("#destino").val("");
                    $("#un_origem").val("");
                    $("#un_embarque").val("");
                    $("#un_desembarque").val("");
                    $("#un_destno").val("");
                    $("#observacao_interna").val("");
                    $("#observacao_cliente").val("");
                    $("#taxas_locais").empty();
                    $("#frete_adicionais").empty();

                    $("input:checkbox").each(function() {

                        $(this).attr("checked", false);

                    });

                    $("#mercadoria").focus();

                    $("#msg").html("");

                });

            }//END SUCCESS

        });

    });

    /** Valida o formulário e envia para ser salvo **/
    $("#salvar").click(function() {

        var erro = 0;
        var msg = "";

        // Varifica se foi seleciona pelo menos uma rota
        var quantidade_de_itens = 0;

        $("#rotas_adicionadas option").each(function() {
            quantidade_de_itens++;
        });

        if (quantidade_de_itens < 1)
        {
            erro = 1;
            msg += "Informe ao menos uma rota antes de salvar!\n";
        }

        // Verifica se pelo um cliente foi informado
        var quantidade_de_clientes_selecionados = 0;

        $("#clientes_selecionados option").each(function() {
            quantidade_de_clientes_selecionados++;
        });

        if (quantidade_de_clientes_selecionados < 1)
        {
            erro = 1;
            msg += "Informe ao menos um cliente antes de salvar\n";
        }

        if (erro == 1)
        {
            alert(msg);
            return false;
        }
        else
        {
            // Seleciona todas as taxas dos combos antes de submeter o formulario
            $("select option").each(function() {
                $(this).attr("selected", "selected");
            });
            
            $("#progressbar").show();
            $("#corpo").hide();
            
            $('form').submit().delay(4000);
            
            //$("form").submit();
        }


    });//END SALVAR

    $("#localizar").click(function(){
        window.location = "/Clientes/propostas/index.php/propostas/propostas/realizar_busca/";
    });
    
    $("#novo").click(function(){
        window.location = "/Clientes/propostas/index.php";
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

});// END FILE

function IsEmail(email) {
    var exclude = /[^@\-\.\w]|^[_@\.\-]|[\._\-]{2}|[@\.]{2}|(@)[^@]*\1/;
    var check = /@[\w\-]+\./;
    var checkend = /\.[a-zA-Z]{2,3}$/;
    if (((email.search(exclude) != -1) || (email.search(check)) == -1) || (email.search(checkend) == -1)) {
        return false;
    }
    else {
        return true;
    }
}