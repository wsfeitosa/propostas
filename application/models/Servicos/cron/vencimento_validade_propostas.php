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

$data_corte->modify("+6 Days");

echo $data_corte->format('d/m/Y')."\n";

/** Seleciona os itens de proposta que vÃ£o vencer atÃ© este periodo **/
try {
	$sql = $conn->
				select("*")->
				from("CLIENTES.itens_proposta")->
				where("validade >= ?",date('Y-m-d'))->
				where("validade <= ?",$data_corte->format('Y-m-d'))->
				order('validade','ASC');
	
	echo $sql->__toString()."<br/>";			

	$itens_encontrados = $conn->fetchAll($sql);
} catch (Exception $e) {
	echo $e->getMessage();exit(0);
}

if( count($itens_encontrados) < 1 )
{
	echo "FIM -> Nenhum item de proposta à vencer!";exit;
}	

$itens_agrupados = Array();
$propostas = Array();

foreach( $itens_encontrados as $item )
{		
	/** Seleciona os emails que estão nesta proposta **/
	try {
		$sql_emails_proposta = $conn->select()->from("CLIENTES.emails_propostas")->where("id_proposta = ?",$item['id_proposta']);
		
		$emails_item = $conn->fetchAll($sql_emails_proposta);
	} catch (Exception $e) {
		continue;
	}	
	
	/** Obtém os dados da proposta **/
	try {
		$sql_proposta = $conn->select()->from("CLIENTES.propostas")->where("id_proposta = ?", $item['id_proposta']);

		$proposta = $conn->fetchRow($sql_proposta);
	} catch (Exception $e) {
		continue;
	}	

	if( $proposta['tipo_proposta'] == "proposta_spot" )
	{
		continue;
	}

	/** Obtém o cliente do item da proposta **/
	try {
		$sql_cliente_proposta = $conn->select()->from("CLIENTES.clientes_x_propostas")->where("id_proposta = ?", $item['id_proposta']);

		$rs_cliente_proposta = $conn->fetchRow($sql_cliente_proposta);
	} catch (Exception $e) {
		continue;
	}

	try {
		$sql_cliente = $conn->select()->from("CLIENTES.clientes")->where("id_cliente = ?", $rs_cliente_proposta['id_cliente']);

		$cliente = $conn->fetchRow($sql_cliente);
	} catch (Exception $e) {
		continue;
	}

	$propostas[$item['id_proposta']]['proposta'] = $proposta;
	$propostas[$item['id_proposta']]['emails'] = $emails_item;
	$propostas[$item['id_proposta']]['cliente'] = $cliente;
	$propostas[$item['id_proposta']]['itens'][$item['id_item_proposta']] = $item;

	

}

/** Envia um email por proposta copiando o vendedor e o customer do usuário **/
foreach( $propostas as $id_proposta => $proposta )
{

	$email_vendedor = "";
	$email_customer = "";

	$label_vendedor = "";
	$label_customer = "";

	/** Define o email do Vendedor e do customer **/
	if( $proposta['proposta']['sentido'] == "EXP" )
	{		
		$vendedor = $proposta['cliente']['responsavel'];
		$customer = $proposta['cliente']['customer_exportacao'];
	}
	else 
	{
		$vendedor = $proposta['cliente']['customer'];
		$customer = $proposta['cliente']['customer_importacao'];	
	}	

	if( ! in_array($vendedor, $usuarios_invalidos) )
	{
		try {
			$label_vendedor = "Responsável Comercial: " . retornaNomeUsuario($vendedor) . "<br />";
		} catch (Exception $e) {
			continue;
		}	
	}

	if( ! in_array($customer, $usuarios_invalidos) )
	{
		try {
			$label_customer = "Customer Service: " . retornaNomeUsuario($customer) . "<br />";
		} catch (Exception $e) {
			continue;
		}	
	}		

	$email_vendedor = retornaEmailUsuario( $vendedor );
	$email_customer = retornaEmailUsuario( $customer );

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
		font-size: 12px;
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
						Prezado cliente '.strtoupper($proposta["cliente"]["razao"]).',<p /> 
						Informamos que possuímos uma ou mais negociações prestes a vencer dentro de uma semana.<br />
						Caso seja do seu interesse revalidar os valores desta(s) rota(s), queira por gentileza entrar em contato com o departamento comercial da Allink, como segue:<p />

						'.$label_vendedor.'

						'.$label_customer.'
						</td>
					</tr>
					<tr>
						<td class="titulo_tabela" width="10%">
							Número Negociação
						</td>
						<td class="titulo_tabela" width="10%">
							Validade
						</td>
						<td class="titulo_tabela" width="8%">
							Detalhes
						</td>
						<td class="titulo_tabela" width="18%">
							Origem
						</td>
						<td class="titulo_tabela" width="18%">
							Porto Embarque
						</td>
						<td class="titulo_tabela" width="18%">
							Porto Desembarque
						</td>
						<td class="titulo_tabela" width="18%">
							Destino
						</td>												
					</tr>
					<p /> 
					';

	foreach($proposta['itens'] as $item):

	$validade = new DateTime($item["validade"]);	
	
	try {
	
		$portos = reatornaPortosTarifario($item["id_tarifario_pricing"]);
	} catch (Exception $e) {
			continue;
	}

	$link = "http://189.38.56.122/Clientes/tarifario/specific_charges.php?key=".$item['id_item_proposta'];

	$mensagem .= '					
					<tr>
						<td class="texto_pb1" align="center">
							'.$item["numero_proposta"].'
						</td>
						<td class="texto_pb1" align="center">
							'.$validade->format("d/m/Y").'
						</td>
						<td class="texto_pb1" align="center">
							<a href="'.$link.'">Ver Detalhes</a>
						</td>
						<td class="texto_pb1" align="center">
							'.$portos["origem"].'
						</td>
						<td class="texto_pb1" align="center">
							'.$portos["embarque"].'
						</td>
						<td class="texto_pb1" align="center">
							'.$portos["desembarque"].'
						</td>
						<td class="texto_pb1" align="center">
							'.$portos["destino"].'
						</td>						
					</tr>';

	endforeach;

	$mensagem .='
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
		
	/** Serializa os emails do cliente **/
	$emails_serializados = "";

	foreach ($proposta['emails'] as $email) 
	{
		$emails_serializados .= $email['email'].";"; 
	}

	$emails_serializados .= $email_vendedor . ";" . $email_customer;

	/** Remove os emails duplicados **/
	$emails_serializados = explode(";",$emails_serializados);

	$emails_serializados = array_unique($emails_serializados);

	/** Remove os emails inválidos **/
	$zend = new Load_Zend();

	$zend->load("Zend/Validate/EmailAddress");

	$validator = new Zend_Validate_EmailAddress();

	foreach ($emails_serializados as $k => $email_testar) 
	{
		if( ! $validator->isValid($email_testar) )
		{
			unset($emails_serializados[$k]);
		}	
	}

	$emails_serializados = implode(";", $emails_serializados);
	
	$emails_envio = explode(";", $emails_serializados,2);
	
	echo "Emails enviados Para: ".$emails_envio[0]."\r\n";
	echo "Emails enviados Cc: ".$emails_envio[1]."\r\n";
	echo "\r\n";
	
	envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", $email_vendedor, 
				$email_customer, 
				"wellington.feitosa@allink.com.br;leandro.oliveira@allink.com.br", "noreply", 
				"Vencimento de Proposta Comercial Allink - ".strtoupper($proposta["cliente"]["razao"]) . " -> ".$proposta['proposta']['sentido'], $mensagem, 
				$anexo="", $nome_anexo="");
	
	/**
	envia_email("smtp.scoa@allink.com.br", 
				"Allink Transportes Internacionais", $emails_envio[0], $emails_envio[1], 
				"wellington.feitosa@allink.com.br", "noreply", 
				"Vencimento de Proposta Comercial Allink - ".strtoupper($proposta["cliente"]["razao"]) . " -> ".$proposta['proposta']['sentido'], $mensagem,
				$anexo="", $nome_anexo="");
	**/
} 
echo "FIM!\r\n";