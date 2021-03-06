<?php
ini_set('display__errors', 'On');
define(SERVER_ROOT,"/var/www/html/allink"); 

include_once SERVER_ROOT.'/Conexao/conecta.inc';
include_once SERVER_ROOT.'/Libs/envia_msg.php';
include 'funcoes.php';

$conn = Zend_Conn();
db_conecta();

$usuarios_invalidos = Array(112,387,341,491,492);

/** Calcula a data de vencimento para seis dias a contar da data de hoje **/
$data_corte = new DateTime();
$data_volumes = new DateTime();

$data_corte->modify("+6 Days");

$sentido_acordo = "";

/** Bases de exporta��o **/
$sql_bases = "SHOW DATABASES LIKE '%EXP%'";

$bases = $conn->fetchAll($sql_bases);

//echo $data_corte->format('d-m-Y')."<br />";

/** Seleciona os acordos adicionais que vao vencer no periodo **/
try{    
    $sql = $conn->
                select("*")->
                from("CLIENTES.acordo_adicionais")->
                where("validade >= ?",date('Y-m-d'))->
                where("validade <= ?",$data_corte->format('Y-m-d'))->
                where("avisar_vencimento = ?","S")->
                order('validade','ASC');
    
    $acordos_encontrados = $conn->fetchAll($sql);
} catch (Exception $e) {
    echo $e->getMessage();exit(0);
}

if( count($acordos_encontrados) < 1 )
{
	echo "FIM -> Nenhum acordo de incentivos � vencer!";exit;
}	

$acordos_agrupados = array();

foreach( $acordos_encontrados as $acordo )
{       
    /** Seleciona os clientes que est�o no acordo **/
    try {
		$sql_cliente_acordo = $conn->
                                    select()->
                                    from("CLIENTES.clientes_x_acordo_adicionais")->
                                    join("CLIENTES.clientes","clientes.id_cliente = clientes_x_acordo_adicionais.id_cliente",array("*"))->
                                    joinLeft("CLIENTES.grupo_comercial","grupo_comercial.idgrupo_comercial = clientes.id_grupo_comercial",array('grupo_chave'))->
                                    where("id_acordo_adicionais = ?", $acordo['id']);
        
		$rs_cliente_acordo = $conn->fetchAll($sql_cliente_acordo);
        
	} catch (Exception $e) {
		echo $e->getMessage();exit(0);        
	}
            
    /** Seleciona �s taxas do acordo **/
    try {
        $sql_taxas = $conn->
                        select()->
                        from("CLIENTES.taxas_acordo_adicionais",array("*"))->
                        join("FINANCEIRO.unidades", "unidades.id_unidade = taxas_acordo_adicionais.id_unidade",array("unidade"))->
                        join("FINANCEIRO.moedas", "moedas.id_moeda = taxas_acordo_adicionais.id_moeda",array("sigla"))->
                        join("FINANCEIRO.taxas_adicionais","taxas_adicionais.id_txadicional = taxas_acordo_adicionais.id_taxa",array("taxa_adicional"))->
                        where("id_acordo_adicional = ?",$acordo['id']);
        $rs_taxas = $conn->fetchAll($sql_taxas);
    } catch (Exception $e) {
        continue;
    }
    
    /**
     * Seleciona os dados sobre o volume embarcado pelos clientes do acordo
     * nos �ltimos 06 meses
     */   
    $acordos_agrupados[$acordo['id']]['acordo'] = $acordo;
    $acordos_agrupados[$acordo['id']]['clientes'] = $rs_cliente_acordo;   
    $acordos_agrupados[$acordo['id']]['taxas'] = $rs_taxas;
}    

/** Cria mensagem de email que ser� enviada ao usu�rio **/
foreach ($acordos_agrupados as $id_acordo => $acordo) 
{    
    $vendedores = array();
    $customers = array();   
    $emails_campo_para = array();
    $id_clientes = array();
    $totais_volume = array();
    $enviar_wwa = false;
    
	/** Define o email do Vendedor e do customer **/
    foreach ($acordo['clientes'] as $cliente) 
    {               
        $email_vendedor = "";
        $email_customer = "";

        $label_vendedor = "";
        $label_customer = "";
                        
        if( $acordo['acordo']['sentido'] == "EXP" )
        {		
            $vendedor = $cliente['responsavel'];
            $customer = $cliente['customer_exportacao'];
            $sentido_acordo = "Exporta��o";
        }
        else 
        {
            $vendedor = $cliente['customer'];
            $customer = $cliente['customer_importacao'];
            $sentido_acordo = "Importa��o";
        }	

        if( ! in_array($vendedor, $usuarios_invalidos) )
        {
            try {
                $email_vendedor = retornaEmailUsuario( $vendedor );
                array_push($vendedores, $email_vendedor);   
                
                if(retornaFilialUsuario($vendedor) == retornaFilialUsuario($acordo['acordo']['id_usuario_cadastro']) )
                {
                    array_push($emails_campo_para, $email_vendedor);
                }    
            } catch (Exception $e) {
                continue;
            }	
        }

        if( ! in_array($customer, $usuarios_invalidos) )
        {
            try {
                $email_customer = retornaEmailUsuario( $customer );        
                array_push($customers, $email_customer);               
            } catch (Exception $e) {
                continue;
            }	
        }		
        
        /**
         * Verifica se o cliente � da wwa se sim adiciona o email do Lemos no envio
         */
        if( $cliente['grupo_chave'] == "S" )
        {
            $enviar_wwa = true;
            array_push($vendedores, "alexandre.lemos@allink.com.br");
        }  
        
        array_push($id_clientes, $cliente['id_cliente']);
        
    }
	
    $vendedores = array_unique($vendedores);
    $customers = array_unique($customers);
    $emails_campo_para = array_unique($emails_campo_para);
    
    /**
     * Calcula o volume de carga que esses clientes tiveram nos �ltimos 06 meses.
     */          
    
    /** Percorre os �ltimos 06 meses a partir da data atual para buscar os embarques **/
    for( $i=1; $i<=6; $i++ )
    {        
        $data_busca = new DateTime();
        $data_busca->modify("-{$i} Months");
               
        foreach ($bases as $base) 
        {                
            $sql = "SELECT
                        bookings.id_booking, 
                        IF(bookings.cubagemsd > 0, bookings.cubagemsd, bookings.cubagem) AS 'cubagem'
                    FROM
                        {$base['Database (%EXP%)']}.bookings
                        INNER JOIN FINANCEIRO.tarifarios_pricing ON tarifarios_pricing.id_tarifario_pricing = bookings.id_tarifario
                    WHERE
                        bookings.id_cliente IN (".implode(",", $id_clientes).") AND
                        bookings.cancelado = 'N' AND
                        SUBSTRING(bookings.data,1,10) >= '".$data_busca->format('Y-m')."-01' AND SUBSTRING(bookings.data,1,10) <= '".$data_busca->format('Y-m')."-31'";	
            
            try{                       
                $rowSet = $conn->fetchAll($sql);
            } catch (Exception $e) {
                echo $e->getMessage()."<br /><pre>".$sql;
                exit(0);
            }
            
            if( count($rowSet) < 1 )
            {
                continue;
            }

            foreach($rowSet as $dados_porto)
            {                    
                $totais_volume[$dados_porto['porto_taxa']][$data_busca->format('Y-m')] += $dados_porto['cubagem'];
            }    

        }                   
        
    }    
            
    $data_cadastro = new DateTime($acordo['acordo']['data_cadastro']);
    $validade = new DateTime($acordo['acordo']['validade']);
    
    /**Cria o html do corpo da mensagem*/
	$mensagem = '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	<title>SCOA - ENVIO DE E-MAILS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<style type="text/css">
	.titulo_tabela
	{
		background-color:#4682B4;
		color:#FFFFFF;
		font-family:Verdana;
		font-size:11px;
		text-align:center;
	}
	.texto_pb1
	{
		font-family:Verdana;
		font-size: 10px;
		color: #000000;
		background: #FFFFFF;
	}
	.texto_pb2
	{
		font-family:Verdana;
		font-size: 9px;
		color: #000000;
	}
	.padrao
	{
		background: #DBEAF5;
	}
	.tabela_azul
	{
		background: #FFFFFF;
		border:1px solid #4682B4;
	}
	.alerta
	{
		color: red;
	}
	</style>
	</head>
	<body>
	<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>
			<br>
				<table border="0" cellpadding="1" cellspacing="1" width="97%" class="padrao" align="center">
					<tr>
						<td colspan = "7" class="texto_pb1">
						Comunicamos que o acordo de incentivo '.$acordo['acordo']['numero'].' de '.$sentido_acordo.', cadastrado em '.$data_cadastro->format('d-m-Y').'
                        pelo usu�rio '.  retornaNomeUsuario($acordo['acordo']['id_usuario_cadastro']).' da filial '.  retornaFilialUsuario($acordo['acordo']['id_usuario_cadastro']).', vencer� em breve. 
						</td>
					</tr>
					<tr>
						<td class="titulo_tabela" style="color:red;">                            
							<b>EXPIRA EM: '.$validade->format('d-m-Y').'</b>
						</td>
						<td class="titulo_tabela">
							CNPJ
						</td>
						<td class="titulo_tabela">
							RAZ�O
						</td>
						<td class="titulo_tabela">
							CIDADE
						</td>
						<td class="titulo_tabela">
							UF
						</td>
						<td class="titulo_tabela">
							VENDEDOR
						</td>
						<td class="titulo_tabela">
							CUSTOMER
						</td>												
					</tr>
					<p /> 
					';
    
    /**
     * Formata a coluna com os dados do acordo
     */   
    $taxas_acordo = "";
    
    foreach ($acordo['taxas'] as $taxa) 
    {
        $taxas .= $taxa['taxa_adicional'] . " | " . $taxa['sigla'] . " " . number_format($taxa['valor'], 2, ".", ",") .
                  " " . $taxa['unidade'] . " | " . number_format($taxa['valor_minimo'], 2, ".", ",") . " | " .
                  number_format($taxa['valor_maximo'], 2, ".", ",") . "<br />";
    }
       
    $contRowsPan = 1;
    
    foreach($acordo['clientes'] as $cliente)
    {                  
        
        if( $acordo['acordo']['sentido'] == "EXP" )
        {
            $vendedor = $cliente['responsavel'];
            $customer = $cliente['customer_exportacao'];
        }
        else
        {
            $vendedor = $cliente['customer'];
            $customer = $cliente['customer_importacao'];
        }             
        
        if( $contRowsPan === 1  )
        {
            $rowspan = "rowspan='".count($acordo['clientes'])."'";
            
            $coluna = '<td class="texto_pb1" align="center" '.$rowspan.'>
							'.$portos.'<p />
                            '.$taxas.'<p />  
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/adicionais/adicionais/revalidar/'.$acordo['acordo']['id'].'/0">DEIXE EXPIRAR</a> 
                            <br />
                            OU
                            <br />
                            PEDIR REVALIDA��O POR:<br />
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/adicionais/adicionais/revalidar/'.$acordo['acordo']['id'].'/1">1 M�S</a> | 
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/adicionais/adicionais/revalidar/'.$acordo['acordo']['id'].'/3">3 MESES</a> | 
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/adicionais/adicionais/revalidar/'.$acordo['acordo']['id'].'/6">6 MESES</a> | 
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/adicionais/adicionais/revalidar/'.$acordo['acordo']['id'].'/12">'.date('Y').'</a>
					   </td>';
        }
        else
        {
            $coluna = "";            
        }    
        
        $contRowsPan++;
        
        $mensagem .= '<tr>
                        '.$coluna.'
						<td class="texto_pb1" align="center">
							'.$cliente['cnpj'].'
						</td>
						<td class="texto_pb1" align="center">
							'.$cliente['razao'].'
						</td>
						<td class="texto_pb1" align="center">
							'.retornaCidade($cliente['cidade']).'
						</td>
						<td class="texto_pb1" align="center">
							'.$cliente['estado'].'
						</td>
						<td class="texto_pb1" align="center">
							'.  retornaNomeUsuario($vendedor).'
						</td>
						<td class="texto_pb1" align="center">
							'.  retornaNomeUsuario($customer).'
						</td>											
					</tr>';
    }    
    
    $tabela_volumes = '<tr class="titulo_tabela">
                           <td>Meses</td>';                
    
    /** 
     * Obtem todas �s datas dos �ltimos 06 meses para formar
     * os labels da tabela.
     */
    $labels_datas = array();
    foreach( $totais_volume as $id_porto => $volumes_porto )
    {        
        foreach ($volumes_porto as $mes => $volume_mes) 
        {           
            array_push($labels_datas, $mes);
        }
    } 
    
    /** Retira �s datas duplicadas e forma �s colunas dos labels **/
    $labels_datas = array_unique($labels_datas);
    
    foreach ($labels_datas as $mes) 
    {
        $tabela_volumes .= '<td>'.$mes.'</td>';
    }
            
    $tabela_volumes .= '</tr>';
    
    /** Aki terminam os cabe�arios e com�am os dados efetivamente **/
    
    $total_mes = array();    
    
    foreach( $totais_volume as $id_porto => $volumes_porto )
    {       
        foreach ($labels_datas as $mes) 
        {            
            $total_mes[$mes] += $volumes_porto[$mes];
        }        
    } 
    
    $tabela_volumes .= '<tr>'
                        . '<td class="texto_pb1" align="center">TOTAL</td>';
    foreach( $labels_datas as $mes )
    {
        $tabela_volumes .= '<td class="texto_pb1" align="center">'.$total_mes[$mes].'</td>';
    }
    
    $tabela_volumes .= "</tr>";
    
	$mensagem .='
                    <tr>
                        <td class="texto_pb1" colspan="7">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="texto_pb1" colspan="7" align="center">Cubagem dos �ltimos 06 meses (M�)</td>
                    </tr>
                    <tr>
                        <td class="texto_pb1" colspan="7">&nbsp;</td>
                    </tr>
				</table>
                
                <table border="0" cellpadding="1" cellspacing="1" width="97%" class="padrao" align="center">
                    '.$tabela_volumes.'
                </table>
			  <br>	
			</td>
		</tr>		
	</table>
	<div class="texto_pb1">
	Atenciosamente,<br />
	Allink Transportes Internacionais.
	</div>				
	</body>
	</html>';
    
    print $mensagem;  
            
    $emails = array_merge($vendedores,$customers);
    $emails_campo_cc = array_diff($emails, $emails_campo_para);    
    
    var_dump(implode(";", $emails_campo_para), implode(";", $emails_campo_cc));
    
    $assunto = "AVISO DE VENCIMENTO 1 SEMANA - {$acordo['acordo']['numero']} - ".$acordo['clientes'][0]['razao'];
    /**
    envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", "wellington.feitosa@allink.com.br;fabio.shimada@allink.com.br", 
                "joao.neder@allink.com.br;rafael.silverio@allink.com.br", 
				"wellington.feitosa@allink.com.br", "noreply", 
				$assunto, $mensagem,
				$anexo="", $nome_anexo="");
    
    
    envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", implode(";", $emails_campo_para), implode(";", $emails_campo_cc), 
				"wellington.feitosa@allink.com.br", "noreply", 
				$assunto, $mensagem,
				$anexo="", $nome_anexo="");
    **/
    //exit(0);
}

echo "FIM !!";
