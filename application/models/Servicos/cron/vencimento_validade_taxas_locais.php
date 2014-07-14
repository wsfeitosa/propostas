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

$data_corte->modify("+7 Days");

$sentido_acordo = "";

/** Bases de exportação **/
$sql_bases = "SHOW DATABASES LIKE '%EXP%'";

$bases_exp = $conn->fetchAll($sql_bases);

/** Bases de Importação **/
$sql_bases = "SHOW DATABASES LIKE '%IMP%'";

$bases_imp = $conn->fetchAll($sql_bases);

/** Seleciona os acordos de taxas locais que vao vencer no periodo **/
try{    
    $sql = $conn->
                select("*")->
                from("CLIENTES.acordos_taxas_locais_globais")->
                where("validade >= ?",date('Y-m-d'))->
                where("validade <= ?",$data_corte->format('Y-m-d'))->
                where("avisar_vencimento = ?","S")->
                order('validade','ASC');
    
    $acordos_encontrados = $conn->fetchAll($sql);

    echo $sql->__toString()."<br/>";    
} catch (Exception $e) {
    echo $e->getMessage();exit(0);
}

if( count($acordos_encontrados) < 1 )
{
	echo "FIM -> Nenhum acordo de taxa local à vencer!";exit;
}	

$acordos_agrupados = array();

foreach( $acordos_encontrados as $acordo )
{   
    /** Seleciona os clientes que estão no acordo **/
    try {
		$sql_cliente_acordo = $conn->
                                    select()->
                                    from("CLIENTES.clientes_x_acordos_taxas_locais_globais")->
                                    join("CLIENTES.clientes","clientes.id_cliente = clientes_x_acordos_taxas_locais_globais.id_cliente",array("*"))->
                                    join("CLIENTES.grupo_comercial","grupo_comercial.idgrupo_comercial = clientes.id_grupo_comercial",array('grupo_chave'))->
                                    where("id_acordos_taxas_locais = ?", $acordo['id']);
        
		$rs_cliente_acordo = $conn->fetchAll($sql_cliente_acordo);
        
	} catch (Exception $e) {
		continue;        
	}
    
    /** Seleciona os portos do acordo **/
    try{
        $sql_porto = $conn->
                        select()->
                        from("CLIENTES.portos_x_acordos_taxas_globais",array("*"))->
                        join("USUARIOS.portos","portos.id_porto = portos_x_acordos_taxas_globais.id_porto",array("*"))->
                        where("id_acordo = ?",$acordo['id']);
        
        $rs_porto = $conn->fetchAll($sql_porto);
    } catch (Exception $e) {
        continue;       
    }
    
    /** Seleciona às taxas do acordo **/
    try {
        $sql_taxas = $conn->
                        select()->
                        from("CLIENTES.taxas_x_acordos_taxas_locais_globais",array("*"))->
                        join("FINANCEIRO.unidades", "unidades.id_unidade = taxas_x_acordos_taxas_locais_globais.id_unidade",array("unidade"))->
                        join("FINANCEIRO.moedas", "moedas.id_moeda = taxas_x_acordos_taxas_locais_globais.id_moeda",array("sigla"))->
                        join("FINANCEIRO.taxas_adicionais","taxas_adicionais.id_txadicional = taxas_x_acordos_taxas_locais_globais.id_taxa_adicional",array("taxa_adicional"))->
                        where("id_acordos_taxas_locais = ?",$acordo['id']);
        $rs_taxas = $conn->fetchAll($sql_taxas);
    } catch (Exception $e) {
        continue;
    }
    
    /**
     * Seleciona os dados sobre o volume embarcado pelos clientes do acordo
     * nos últimos 06 meses
     */   
    $acordos_agrupados[$acordo['id']]['acordo'] = $acordo;
    $acordos_agrupados[$acordo['id']]['clientes'] = $rs_cliente_acordo;    
    $acordos_agrupados[$acordo['id']]['portos'] = $rs_porto;
    $acordos_agrupados[$acordo['id']]['taxas'] = $rs_taxas;
}    

/** Cria mensagem de email que será enviada ao usuário **/
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
            $sentido_acordo = "Exportação";
        }
        else 
        {
            $vendedor = $cliente['customer'];
            $customer = $cliente['customer_importacao'];
            $sentido_acordo = "Importação";
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
         * Verifica se o cliente é da wwa se sim adiciona o email do Lemos no envio
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
     * Calcula o volume de carga que esses clientes tiveram nos últimos 06 meses.
     */          
    $ids_portos = array();
    
    foreach ($acordo['portos'] as $porto) 
    {
        array_push($ids_portos, $porto['id_porto']);
    }
    
    /** Percorre os últimos 06 meses a partir da data atual para buscar os embarques **/
    for( $i=1; $i<=6; $i++ )
    {        
        $data_busca = new DateTime();
        $data_busca->modify("-{$i} Months");
        
        if( $acordo['acordo']['sentido'] == 'IMP' )
        {
            foreach ($bases_imp as $base) 
            {                
                $sql = "SELECT
                            imphouses.numhouse, id_port_delivery as porto_taxa  
                        FROM
                            {$base['Database (%IMP%)']}.imphouses
                            INNER JOIN {$base['Database (%IMP%)']}.impmasters ON impmasters.id_impmaster = imphouses.id_master
                        WHERE
                            (imphouses.id_imp IN (".implode(",", $id_clientes).") OR imphouses.id_forw IN (".implode(",", $id_clientes).")) AND
                            SUBSTRING(impmasters.dtatual,1,10) >= '".$data_busca->format('Y-m')."-01' AND SUBSTRING(impmasters.dtatual,1,10) <= '".$data_busca->format('Y-m')."-31' AND
                            imphouses.id_port_delivery IN (".  implode(",", $ids_portos).")";
                
                $rowSet = $conn->fetchAll($sql);
                
                if( count($rowSet) < 1 )
                {
                    continue;
                }
                
                foreach($rowSet as $dados_porto)
                {                    
                    $totais_volume[$dados_porto['porto_taxa']][$data_busca->format('Y-m')] += 1;
                }    
                                                            
            }           
        }
        else
        {
            foreach ($bases_exp as $base) 
            {                
                $sql = "SELECT
                            bookings.id_booking, tarifarios_pricing.id_place_receipt as porto_taxa
                        FROM
                            {$base['Database (%EXP%)']}.bookings
                            INNER JOIN FINANCEIRO.tarifarios_pricing ON tarifarios_pricing.id_tarifario_pricing = bookings.id_tarifario
                        WHERE
                            bookings.id_cliente IN (".implode(",", $id_clientes).") AND
                            bookings.cancelado = 'N' AND
                            SUBSTRING(bookings.data,1,10) >= '".$data_busca->format('Y-m')."-01' AND SUBSTRING(bookings.data,1,10) <= '".$data_busca->format('Y-m')."-31' AND
                            tarifarios_pricing.id_place_receipt IN (".  implode(",", $ids_portos).")";	
                
                $rowSet = $conn->fetchAll($sql);
                
                if( count($rowSet) < 1 )
                {
                    continue;
                }
                
                foreach($rowSet as $dados_porto)
                {                    
                    $totais_volume[$dados_porto['porto_taxa']][$data_busca->format('Y-m')] += 1;
                }    
                                                            
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
						Comunicamos que o acordo '.$acordo['acordo']['numero'].' de taxas locais de '.$sentido_acordo.', cadastrado em '.$data_cadastro->format('d-m-Y').'
                        pelo usuário '.  retornaNomeUsuario($acordo['acordo']['id_usuario_cadastro']).' da filial '.  retornaFilialUsuario($acordo['acordo']['id_usuario_cadastro']).', vencerá em breve. 
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
							RAZÃO
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
    $portos = "";
    
    foreach ($acordo['portos'] as $porto) 
    {
        $portos .= $porto['porto']."<br />";
    }
    
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
                            <a href="http://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/taxas_locais/taxas_locais/revalidate/'.$acordo['acordo']['id'].'/0">DEIXE EXPIRAR</a> 
                            <br />
                            OU
                            <br />
                            PEDIR REVALIDAÇÃO POR:<br />
                            <a href="https://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/taxas_locais/taxas_locais/revalidate/'.$acordo['acordo']['id'].'/1">1 MÊS</a> | 
                            <a href="https://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/taxas_locais/taxas_locais/revalidate/'.$acordo['acordo']['id'].'/3">3 MESES</a> | 
                            <a href="https://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/taxas_locais/taxas_locais/revalidate/'.$acordo['acordo']['id'].'/6">6 MESES</a> | 
                            <a href="https://'.$_SERVER['SERVER_ADDR'].'/Clientes/propostas/index.php/taxas_locais/taxas_locais/revalidate/'.$acordo['acordo']['id'].'/12">'.date('Y').'</a>
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
                           <td>Portos</td>';                
    
    /** 
     * Obtem todas às datas dos últimos 06 meses para formar
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
    
    /** Retira às datas duplicadas e forma às colunas dos labels **/
    $labels_datas = array_unique($labels_datas);
    
    foreach ($labels_datas as $mes) 
    {
        $tabela_volumes .= '<td>'.$mes.'</td>';
    }
            
    $tabela_volumes .= '</tr>';
    
    /** Aki terminam os cabeçarios e comçam os dados efetivamente **/
    $total_mes = array();    
    
    foreach( $totais_volume as $id_porto => $volumes_porto )
    {
        $tabela_volumes .= '<tr>'
                            . '<td class="texto_pb1" align="center">'.  retornaNomePorto($id_porto).'</td>';
        foreach ($labels_datas as $mes) 
        {           
            $tabela_volumes .= '<td class="texto_pb1" align="center">'.$volumes_porto[$mes].'</td>';
            $total_mes[$mes] += $volumes_porto[$mes];
        }
                        
        $tabela_volumes .= "</tr>";
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
                        <td class="texto_pb1" colspan="7" align="center">Processos dos últimos 06 meses (Exportação => Bookings, Importação => HBL)</td>
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
    
    envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", "wellington.feitosa@allink.com.br;leandro.oliveira@allink.com.br", 
                "joao.neder@allink.com.br;rafael.silverio@allink.com.br", 
				"wellington.feitosa@allink.com.br", "noreply", 
				$assunto, $mensagem,
				$anexo="", $nome_anexo="");
    
    /**
    envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", implode(";", $emails_campo_para), implode(";", $emails_campo_cc), 
				"wellington.feitosa@allink.com.br", "noreply", 
				$assunto, $mensagem,
				$anexo="", $nome_anexo="");
    **/
    //exit(0);
}

echo "FIM !!";