$(document).ready(function(){
	
	/** Cria o alerta para todas às rotas bloqueadas **/
	$('.mensagem').click(function(){
				
		alert($(this).attr('title'));			
						
	});
	
	$("#selecionar_todos").click(function(){
		
		if( $(this).is(":checked") )
		{
			
			$("input:checkbox").each(function(){
				
				if( $(this).attr('disabled') != 'disabled' )
				{
					$(this).attr("checked","checked");
				}	
				
				
			});			
			
		}
		else		
		{
			
			$("input:checkbox").each(function(){
				
				$(this).attr("checked",false);
				
			});
									
		}	
		
	});
	
	$("#adicionar").click(function(){
		
        var acordo_adicionais = "";
        
		$("input:checked").each(function(){
									
			if( $(this).attr("id") != "selecionar_todos" )
			{												
				/** Busca os tarifários **/
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
		                                        
				$.ajax({
		            type: "POST",  
		            async: false,
		            url:  "/Clientes/propostas/index.php/tarifarios/tarifarios/fillAndPutOnSession/" + $(this).attr('id') + "/" + $(this).val() + "/" + clientes +  "/" + $("#inicio", window.parent.document).val() + "/" + $("#validade", window.parent.document).val() + "/" + imo + "/" + $(this).attr('id_item'),            
		            beforeSend: function(){		            	
		            	$("#msg", window.parent.document).html("<img src='/Clientes/propostas/assets/img/busy.gif' />Aguarde Processando...");
		            	$("#msg").html("<img src='/Clientes/propostas/assets/img/busy.gif' />Aguarde Processando...");
		            },
		            dataType: "xml",
		            success: function(xml){        
		                		            			            	
		            	$(xml).each(function() {

		                    var label = ""; //Label do option do combo de rotas adicionadas
		                    var value = $(this).find("id_item").text(); //Valor do option do combo de rotas adicionadas
		                    var origem = $(this).find("origem").text();
		                    var embarque = $(this).find("embarque").text();
		                    var desembarque = $(this).find("desembarque").text();
		                    var destino = $(this).find("destino").text();
		                    acordo_adicionais = $(this).find("adicional_negociado").text();
                            console.log(acordo_adicionais);
		                    label += origem + " - " + embarque + " - " + desembarque + " - " + destino;
		                    		                    
		                    $("#rotas_adicionadas", window.parent.document).append(new Option(label, value));

		                    // Zera os campos para seja inserido um novo item
		                    $("#id_tarifario", window.parent.document).val("");
		                    $("#posicao_combo", window.parent.document).val("");
		                    $("#mercadoria", window.parent.document).val("");                    
		                    $("#peso", window.parent.document).val("");
		                    $("#cubagem", window.parent.document).val("");
		                    $("#volumes", window.parent.document).val("");
		                    $("#origem", window.parent.document).val("");
		                    $("#embarque", window.parent.document).val("");
		                    $("#desembarque", window.parent.document).val("");
		                    $("#destino", window.parent.document).val("");
		                    $("#un_origem", window.parent.document).val("");
		                    $("#un_embarque", window.parent.document).val("");
		                    $("#un_desembarque", window.parent.document).val("");
		                    $("#un_destno", window.parent.document).val("");
		                    $("#observacao_interna", window.parent.document).val("");
		                    $("#observacao_cliente", window.parent.document).val("");
		                    $("#taxas_locais", window.parent.document).empty();
		                    $("#frete_adicionais", window.parent.document).empty();

		                    $("input:checkbox", window.parent.document).each(function() {

		                        $(this).attr("checked", false);

		                    });

		                    $("#mercadoria", window.parent.document).focus();
		                    
		                });
		            	                  		                		                
		            }//END SUCCESS
		            
				});
				
			}
	
		});
		
        /** Emite um alerta ao cliente que ele possui adiconais negociados **/
        if( acordo_adicionais != "" )
        {
            alert("Este cliente possui taxas adicionais negociadas:\n"+acordo_adicionais);
        }
        
		$("#msg", window.parent.document).html("");				
		window.location = "/Clientes/propostas/index.php/loading/";		
		
		$("#pop",window.parent.document).hide();		  		          
		
	});//END FUNCTION
	
});