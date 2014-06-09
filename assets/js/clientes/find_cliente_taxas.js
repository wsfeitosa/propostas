$(document).ready(function(){
	
	$("#selecionar_todos").click(function(){
		
		if( $(this).is(":checked") )
		{
			
			$("input:checkbox").each(function(){
				
				$(this).attr("checked","checked");
				
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
		
		var clientes = "";
		
		$("input:checked").each(function(){
			
			if( $(this).attr("id") != "selecionar_todos" )
			{
				$("#clientes_selecionados", window.parent.document).append(new Option($(this).attr("value"), $(this).attr("id")));
				clientes +=  $(this).attr("id") + ":"; 
			}
			
		});
		
		var comprimento_str = (clientes.length - 1)*1;
		clientes = clientes.substring(0,comprimento_str);

		$("#cliente").val("");
		
		document.getElementById("frame").src = "/Clientes/propostas/index.php/loading/";
		
		$("#pop",window.parent.document).hide();
						
	});//END FUNCTION
	
});//END FILE