$(document).ready(function(){
			
	$("#voltar").click(function(){
		window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/";
	});
	
	$("#tipo_busca").change(function(){
		
		switch( $(this).val() )
		{
			
			case "0":
				$("#label_dado_busca").html("Dado Busca:");
				$( "#dado_busca" ).attr("readonly",false);
			break;
			
			case "numero":
				$("#label_dado_busca").html("Número Acordo:");
				$( "#dado_busca" ).attr("readonly",false);
			break;
			
			case "cliente":
				$("#label_dado_busca").html("Cliente:");
				$( "#dado_busca" ).attr("readonly",false);
			break;
			
			case "vencimento":
				$("#label_dado_busca").html("Vencimento:");
				$( "#dado_busca" ).datepicker({ 					
					dateFormat: 'dd-mm-yy' , 
					changeYear: true , 
					changeMonth: true, 
					showOn: "button",
					buttonImage: "/Imagens/cal.gif",
					buttonImageOnly: true,		
				}).attr("readonly","readonly");
			break;
			
			case "porto":
				$("#label_dado_busca").html("Porto:");
			break;
			
			default:
				$("#label_dado_busca").html("Dados Busca:");
			
		}
		
	});
	
	$("#localizar").click(function(){
		
		var erro = false;
		var msg = "";
		
		if( $("#tipo_busca").val() == "0" )
		{
			erro = true;
			msg += "Selecione um Tipo de Busca!\n";
		}
		
		if( $("#dado_busca").val() == "" )
		{
			erro = true;
			msg += "Informe o Dado para realizar a busca!";
		}	
		
		if( erro == true )
		{
			alert(msg);
			return false;
		}
		else
		{
			window.location = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/listView/" + $("#tipo_busca").val() + "/" + $("#dado_busca").val() + "/" + $("#vencidas").val();
		}	
		
	});
	
});