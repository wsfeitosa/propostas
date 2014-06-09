$(document).ready(function(){
	
	$("#selecionar_todos_para").click(function(){
		
		if( $(this).is(":checked") )
		{
				
			$("input:checkbox[name='selecionado_para']").each(function(){
				
				$(this).attr("checked","checked");
				
			});			
			
		}
		else		
		{
			
			$("input:checkbox[name='selecionado_para']").each(function(){
				
				$(this).attr("checked",false);
				
			});
									
		}	
		
	});

	$("#selecionar_todos_cc").click(function(){
		
		if( $(this).is(":checked") )
		{
			
			$("input:checkbox[name='selecionado_cc']").each(function(){
				
				$(this).attr("checked","checked");
				
			});			
			
		}
		else		
		{
			
			$("input:checkbox[name='selecionado_cc']").each(function(){
				
				$(this).attr("checked",false);
				
			});
									
		}	
		
	});	
	
	$("#adicionar").click(function(){
		
		var clientes = "";
		
		$("input:checkbox[name='selecionado_para']").each(function(){
			
			if( $(this).attr("id") != "selecionar_todos_para" && $(this).is(":checked") )
			{
				$("#contatos_para_selecionados", window.parent.document).append(new Option($(this).attr("value"), $(this).attr("value")));				
			}
			
		});

		$("input:checkbox[name='selecionado_cc']").each(function(){
			
			if( $(this).attr("id") != "selecionar_todos_cc" && $(this).is(":checked") )
			{
				$("#contatos_cc_selecionados", window.parent.document).append(new Option($(this).attr("value"), $(this).attr("value")));				 
			}
			
		});				
		
		$("#cliente").val("");
		
		window.location = "/Clientes/propostas/index.php/loading/";
				
		$("#pop",window.parent.document).hide();
		
	});//END FUNCTION
	
});//END FILE