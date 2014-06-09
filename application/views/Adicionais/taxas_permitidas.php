<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Scoa</title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <meta name="description" content="Scoa Sistema de controle Allink" />
    <meta name="author" content="Allink Transportes Internacionais Ltda" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="robots" content="noarchive" />        
    <link rel="stylesheet" href="/Libs/jquery-ui-1.10.4/css/redmond/jquery-ui-1.10.4.custom.css" type="text/css" />
    <link rel="stylesheet" href="/Clientes/propostas/assets/js/sidr/stylesheets/jquery.sidr.light.css">    
    <link rel="stylesheet" href="/Estilos/scoa.css" type="text/css" />    
    <script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.js"></script>
	<script language="javascript" src="/Libs/jquery/jquery-block-ui/jquery-block-ui.js"></script>		
	<script language="javascript" src="/Libs/JavaScript/replaceAll.js"></script>
	<script type="text/javascript" src="/Libs/jquery/jquery.price_format.1.7.js"></script>
	<script type="text/javascript" src="/Libs/jquery/jquery.price_format.1.7.min.js"></script>
	<?php echo $js;?>
</head>
<body>

<div class="principal">

	<p class="titulo">Principais Taxas de Exportação</p>
	<table class="tabela_scoa">
	    <thead>
	        <tr>
	            <th>Taxa</th>
	            <th>Moeda</th>
	            <th>Unidade</th>
	            <th>Modalidade</th>            
	        </tr>
	    </thead>
	    <tbody>
	    	<?php foreach($taxas as $taxa): ?>
	        <tr>
	            <td><?php echo $taxa->getNome();?></td>
	            <td><?php echo $taxa->getMoeda()->getSigla();?></td>
	            <td><?php echo $taxa->getUnidade()->getUnidade();?></td>
	            <td><?php echo $taxa->getPPCC();?></td>	            
	        </tr>       
	    <?php endforeach; ?>
	    </tbody>
	</table>

</div>

</body>
</html>