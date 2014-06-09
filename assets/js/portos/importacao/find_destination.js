$(document).ready(function(){
	
	$("input:checkbox").click(function(){
		
		/** Validação para listar os tarifários **/
		var error = 0;
		var msg = "";
		/** Verifica se há clientes selecionados **/
		var clientes_selecionados = 0;
		
		$("#clientes_selecionados option", window.parent.document).each(function(){
			clientes_selecionados++;
		});
		
		if( (clientes_selecionados * 1) < 1 )			
		{
			error = 1;
			msg = "Selecione ao menos um clientes antes de consultar o tarifário!\n";
		}
		/** Verifica se a modalidade de embarque foi informada **/
		if( $("#pp", window.parent.document).attr('checked') == undefined && $("#cc", window.parent.document).attr('checked') == undefined )
		{
			error = 1;
			msg = "Selecione uma modalidade de embarque!\n";			
		}			
		/** Verifica se os portos de origem e desembarque foram informados **/
		if( $("#un_origem", window.parent.document).val() == "")
		{
			error = 1;
			msg += "Informe antes o porto de origem!\n";
		}	
		
		var porto_embarque = $("#un_embarque", window.parent.document).val();
		var porto_desembarque = $("#un_desembarque", window.parent.document).val();
		
		
		if( $("#sentido", window.parent.document).val() == "IMP" )
		{	
		
			if( $("#un_desembarque", window.parent.document).val() == "" )
			{
				error = 1;
				msg += "Informe antes o porto de desembarque!\n";
			}
			
			porto_embarque = "NULL";
			
		}
		
		if( $("#sentido", window.parent.document).val() == "EXP" )
		{
		
			if( $("#un_embarque", window.parent.document).val() == "" )
			{
				error = 1;
				msg += "Informe antes o porto de embarque!\n";
			}
			
			porto_desembarque = "NULL";
			
		}
		
		if( $("#inicio", window.parent.document).val() == "" || $("#validade", window.parent.document).val() == "")
		{
			error = 1;
			msg += "Informe corretamente às data de inicio e validade, antes de buscar os tarifários!\n";
		}	
		
		if( error == 1 && $("#tela").val() == "" )
		{
			alert(msg);
			document.getElementById("frame").src = "/Clientes/propostas/index.php/loading/";
			$("#pop",window.parent.document).hide("slow");
		}
		else
		{	
		
			/** Quando selecionado o destino final os campos relacionados tem de estar preenchidos **/		
			$("#un_destino", window.parent.document).val($(this).attr("id"));
			$("#destino", window.parent.document).val($(this).val());
			
			/** obtem e formata os ids dos clientes **/
			var clientes = "";
			
			$("#clientes_selecionados option", window.parent.document).each(function(){
				
				clientes += $(this).val() + "|";
				
			});
			var comprimento_str = (clientes.length - 1)*1;
			clientes = clientes.substring(0,comprimento_str);

			var ppcc = "";
						
			/** Obtem a modalidade do embarque (PP ou CC) **/
			if($("#pp", window.parent.document).attr('checked') == 'checked' && $("#cc", window.parent.document).attr('checked') == "checked" )
			{
				ppcc = "PP|CC";
			}	
			else if( $("#pp", window.parent.document).attr('checked') != 'checked' && $("#cc", window.parent.document).attr('checked') == "checked")
			{
				ppcc = "CC";
			}
			else
			{
				ppcc = "PP";
			}	
			
			var imo = "";

			if( $("#imo",window.parent.document).is(":checked") == true )
			{
				imo = "S";
			}
			else
			{
				imo = "N";
			}	
			
			/** Se for a tela da proposta cotação então não envia a tela para buscar os tarifários **/			
			if( $("#tela").val() == "" )
			{				
				window.location = "/Clientes/propostas/index.php/tarifarios/tarifarios/find/" + clientes + "/" +			
				$("#un_origem", window.parent.document).val() + "/" + porto_embarque + "/" +
				porto_desembarque + "/" + 	$("#un_destino", window.parent.document).val() + 
				"/" + imo + "/" + ppcc +  "/proposta/" + $("#sentido", window.parent.document).val() + "/" +
				$("#inicio", window.parent.document).val() + "/" + $("#validade" ,window.parent.document).val() + "/" +
				$("#tipo_proposta", window.parent.document).val();
			}
			else
			{
				window.location = "/Clientes/propostas/index.php/loading/";
				$("#pop",window.parent.document).hide();
			}	
						
		}	
		
	});
	
	
	
});//END FILE