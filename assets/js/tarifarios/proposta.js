$(document).ready(function(){
	
	/** Cria o alerta para todas às rotas bloqueadas **/
	$('.mensagem').click(function(){
				
		alert($(this).attr('title'));			
						
	});
	
	/** Envia a requisição a pagina para carrega o tariário **/	
	$("input:checkbox").click(function(){
		
		/** obtem e formata os ids dos clientes **/
		var clientes = "";
		
		$("#clientes_selecionados option", window.parent.document).each(function(){
			
			clientes += $(this).val() + "|";
			
		});

        var comprimento_str = (clientes.length - 1)*1;
        clientes = clientes.substring(0,comprimento_str);

        var imo = "";
       
        if( $("#imo", window.parent.document).is(":checked") == true )
        {
            imo = "S";
        }
        else
        {
            imo = "N";
        } 
		
        var pp = "NULL";
        var cc = "NULL";
        
        if( $("#pp",window.parent.document).is(":checked") == true )
        {
        	pp = "PP";
        }
        
        if( $("#cc",window.parent.document).is(":checked") == true )
        {
        	cc = "CC";
        }
        
		$.ajax({
            type: "POST",            
            url:  "/Clientes/propostas/index.php/tarifarios/tarifarios/fill/" + $(this).attr('id') + "/" + $(this).val() + "/" + clientes +  "/" + $("#inicio", window.parent.document).val() + "/" + $("#validade", window.parent.document).val() + "/" + imo + "/" + pp + "/" + cc + "/" + $(this).attr('id_item'),            
            beforeSend: function(){                     
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml){        
                
                $(xml).find("tarifario").each(function(){
                	
                	var id_tarifario = $(this).find("id_tarifario").text();
                	var origem = $(this).find("data_inicio").text();
                	var validade = $(this).find("validade").text();
                	var sentido = $(this).find("sentido").text();
                	var observacao_tarifario = $(this).find("observacao").text();
                	var rotas = $(this).find("rota");
                	var taxas_adicionais = $(this).find("taxas_adicionais");
                	var taxas_locais = $(this).find("taxas_locais");
                	                	                	                	
                	/** Carrega os portos do tarifario **/
                	$("#origem", window.parent.document).val($(rotas).find("origem").find("nome").text());
                	$("#un_origem", window.parent.document).val($(rotas).find("origem").find("uncode").text());
                	
                	$("#embarque", window.parent.document).val($(rotas).find("embarque").find("nome").text());
                	$("#un_embarque", window.parent.document).val($(rotas).find("embarque").find("uncode").text());
                	
                	$("#desembarque", window.parent.document).val($(rotas).find("desembarque").find("nome").text());
                	$("#un_desembarque", window.parent.document).val($(rotas).find("desembarque").find("uncode").text());
                	
                	$("#destino", window.parent.document).val($(rotas).find("destino").find("nome").text());
                	$("#un_destino", window.parent.document).val($(rotas).find("destino").find("uncode").text());
                	
                	/** Carrega às observações do tariário **/
                	//$("#observacao_cliente", window.parent.document).val(observacao_tarifario);
                	
                	/** Zera o frete e os adicionais **/
                	$("#frete_adicionais", window.parent.document).empty();

                    var ppcc_taxas_locais = null;

                    if( sentido == "IMP" )
                    {
                        ppcc_taxas_locais = "CC";
                    }
                    else
                    {
                        ppcc_taxas_locais = "PP";
                    }    
                	
                	/** carrega o frete e taxas adicionais **/
                	$(taxas_adicionais).find("taxa").each(function(){
                		
                		label =  $(this).find("nome").text() + " | " + 
                				 $(this).find("moeda").text() + " " +
                				 $(this).find("valor").text() + " " +
                				 $(this).find("unidade").text() + " | " +
                				 $(this).find("valor_minimo").text() + " | " + 
                				 $(this).find("valor_maximo").text() + " " + 
                                 $(this).find("ppcc").text();
                		
                		value =  $(this).find("id_taxa").text() + ";" +
                				 $(this).find("id_moeda").text() + ";" +
                				 $(this).find("id_unidade").text() + ";" +
                				 $(this).find("valor").text() + ";" +
                				 $(this).find("valor_minimo").text() + ";" +
                				 $(this).find("valor_maximo").text() + ";" +
                                 $(this).find("ppcc").text();
                		
                		$("#frete_adicionais", window.parent.document).append(new Option(label, value));
                		
                	});
                	
                	/** Zera as taxas locais **/
                	$("#taxas_locais", window.parent.document).empty();
                	
                	/** carrega o frete e taxas locais **/
                	$(taxas_locais).find("taxa").each(function(){
                		
                		label =  $(this).find("nome").text() + " | " + 
                				 $(this).find("moeda").text() + " " +
                				 $(this).find("valor").text() + " " +
                				 $(this).find("unidade").text() + " | " +
                				 $(this).find("valor_minimo").text() + " | " + 
                				 $(this).find("valor_maximo").text() + " " +
                                 ppcc_taxas_locais;
                		
                		value =  $(this).find("id_taxa").text() + ";" +
                				 $(this).find("id_moeda").text() + ";" +
                				 $(this).find("id_unidade").text() + ";" +
                				 $(this).find("valor").text() + ";" +
                				 $(this).find("valor_minimo").text() + ";" +
                				 $(this).find("valor_maximo").text() + ";" + 
                                 ppcc_taxas_locais;
                		
                		$("#taxas_locais", window.parent.document).append(new Option(label, value));
                		
                	});
                	                	        	
                	$("#id_tarifario", window.parent.document).val(id_tarifario);
                	        	
                });
                
                document.getElementById("frame").src = "/Clientes/propostas/index.php/loading/";
                
                $("#pop",window.parent.document).hide("slow");
                
            }//END SUCCESS
            
		});
		
	});//END 
	
});