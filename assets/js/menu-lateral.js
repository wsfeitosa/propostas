$(function(){
	
	$('#simple-menu').sidr();

    $("#booking").click(function(){
        var url = "/Exportacao/booking.php";
        NovaJanela(url, "Booking", 800, 600, "yes");
    });

    $("#consultar-booking").click(function(){
        var url = "/Exportacao/booking.php?bookings=bookings";
        NovaJanela(url, "Booking", 800, 600, "yes");
    });

    $("#routing-order").click(function(){
        var url = "/routing_order/formulario_ro.php";
        NovaJanela(url, "Routing Order", 800, 600, "yes");
    });
    
    $("#consultar-routing-order").click(function(){
        var url = "/routing_order/pesquisa_ro.php";
        NovaJanela(url, "Routing Order", 800, 600, "yes");
    });

    $("#crm").click(function(){
        var url = "/groupware/conectar_sugar.php";
        NovaJanela(url, "CRM", 800, 600, "yes");
    });

    $("#sair").click(function(){
        window.close(this);
    });

    function NovaJanela(pagina,nome,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        win = window.open(pagina,nome,settings);
    }

});