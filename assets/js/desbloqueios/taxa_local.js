$(document).ready(function(){
	
	$("#valor").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#valor_minimo").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#valor_maximo").priceFormat({
	    prefix: '',
	    thousandsSeparator: ''
	});
	
	$("#salvar").click(function(){
						
		$("#desbloqueio").submit();		
			 
	});
	
});