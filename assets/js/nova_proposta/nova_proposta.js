$(document).ready(function(){
	
	$("#criar").click(function(){
		
		var erro = 0;
		var msg = "";
				
		if( $("#tipo_proposta").val() == "0" )
		{
			erro = 1;
			msg += "Selecione o tipo da Proposta!\n";
		}	
		
		if( $("#sentido").val() == "0" )
		{
			erro = 1;
			msg += "Selecione o Sentido\n";			
		}	
		
		if( erro == 1 )
		{
			alert(msg);
		}	
		else
		{
			$("#nova").submit();
		}	
			
		
	});
	
});//END FILE
