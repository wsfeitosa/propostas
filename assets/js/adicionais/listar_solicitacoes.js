$(function(){
	 
	 // Dicas de preenchimento da tela
	 $( document ).tooltip();
	
	 $( "#accordion" ).accordion({
		 heightStyle: "content"
	 });
	 	 	 
	 $('#simple-menu').sidr();
	 
	 $("input[name*='valor']").priceFormat({
	     prefix: '',
	     thousandsSeparator: ''
	 });
	 
	 /** Calandários JQuery **/
		
	 $( "[name*='inicio']" ).datepicker({ 
		minDate: 0,
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true
		
	 }).attr("readonly","readonly");

	 $( "[name*='validade']" ).datepicker({ 	
		minDate: 0,
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true
		
	 }).attr("readonly","readonly");
	 
	 $("#consultar").click(function(){
		 window.location = '/Clientes/propostas/index.php/adicionais/adicionais/filtrar_busca/';
	 });
	 
	 $("#novo").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/novo/';
	 });
	 
	 $("#desbloqueio").click(function(){
		window.location = '/Clientes/propostas/index.php/adicionais/adicionais/listar_solicitacoes/';
	 });
	 
	 $("#sair").click(function(){
		window.close();
	 });
	 
	 //Tudo que tiver taxa no nome começara escondido
	 $("div[id*='taxa-solicitada-edicao-']").hide();
	
	 $("div[id*='taxa-solicitada-exibicao-']").dblclick(function(){
		 		 
		 $(this).hide();
		 		 		 		 		 		 
		 $(this).next('div').show('explode');	
		 
	 });
	 
	 $("input[id*='taxa-solicitada-salvar-']").click(function(){
		
		 var div_edicao = $(this).parent();
		 
		 var div_exibicao = div_edicao.prev('div');
		 
		 var label_taxa = "";
		 
		 var moeda = div_edicao.children("[name*='moeda']");
		 var unidade = div_edicao.children("[name*='unidade']");
		 var valor = div_edicao.children("[name*='valor']");
		 var valor_minimo = div_edicao.children("[name*='valor_minimo']");
		 var valor_maximo = div_edicao.children("[name*='valor_maximo']");
		 		 			 
		 label_taxa = moeda.find("option:selected").text() + " " + 
					  valor.val() + " " +
			          unidade.find("option:selected").text() + " | MIN." + 
			          valor_minimo.val() + " | MAX." + 
			          valor_maximo.val();
		 
		 div_edicao.hide();
		 
		 //Altera os valores na div de exibição
		 div_exibicao.text(label_taxa);		 
		 div_exibicao.show('highlight');
						 
	 });

	  $("input[id*='taxa-solicitada-cancelar-']").click(function(){

	  	 var div_edicao = $(this).parent();
		 
		 var div_exibicao = div_edicao.prev('div');

		 div_edicao.hide();

		 div_exibicao.show('highlight');

	  });

	  $("input[name='excluir-solicitacao']").click(function(){
	  		
	  		window.location = "/Clientes/propostas/index.php/adicionais/adicionais/excluir_solicitacao/"+$(this).attr("id");

	  		window.reload();

	  });
	 
});