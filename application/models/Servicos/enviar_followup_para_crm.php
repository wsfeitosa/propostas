<?php
include_once "/var/www/html/allink/Conexao/conecta.inc";
include_once "/var/www/html/allink/sugarScoa/funcoesSugar.php";

$conn = Zend_Conn();

/** 
 * Seleciona todos os emails que foram enviados para os clientes
 * e que ainda não foram enviados para o crm.
 **/

$sql_envios_pendentes = $conn->
							select()->
							from("CLIENTES.envios_propostas")->
							where("envio_sugar = ?","N");

$rsEnviosPendentes = $conn->fetchAll($sql_envios_pendentes);

if( count($rsEnviosPendentes) < 1 )
{
	exit(0);
}	

foreach( $rsEnviosPendentes as $envio )
{
	
	/** Seleciona os dados da proposta para enviar ao CRM **/
	$sql_proposta = $conn->
						select()->
						from("CLIENTES.propostas")->
						where("propostas.id_proposta = ?",$envio['id_proposta']);
	
	$rsProposta = $conn->fetchRow($sql_proposta);
	
	/** Seleciona os clientes relacionados a esta proposta **/
	$sql_clientes_proposta = $conn->
									select()->
									from("CLIENTES.clientes_x_propostas")->
									where("clientes_x_propostas.id_proposta = ?",$envio['id_proposta']);
	
	$rsClientsProposta = $conn->fetchAll($sql_clientes_proposta);
	
	/** Seleciona o vendedor e o customer de cada um dos clientes **/
	foreach( $rsClientsProposta as $clienteProposta )
	{
		
		if( $rsProposta['sentido'] == "EXP" )
		{
			$responsaveis = Array("vendedor" => "responsavel", "customer" => "customer_exportacao", "razao", "id_conta" => "id_sugar");
		}
		else
		{
			$responsaveis = Array("vendedor" => "customer", "customer" => "customer_importacao", "razao", "id_conta" => "id_sugar");
		}		
		
		$sql_responsaveis = $conn->
								select()->
								from("CLIENTES.clientes",$responsaveis)->
								where("clientes.id_cliente = ?", $clienteProposta['id_cliente']);
		
		$rsResponsaveis = $conn->fetchRow($sql_responsaveis);
						
		/** Se o vendedor ou o customer for allink, então não envia para o CRM **/
		$id_sugar_vendedor = retornaUsuarioSugarId($rsResponsaveis['vendedor']);
		$id_sugar_customer = retornaUsuarioSugarId($rsResponsaveis['customer']);
		$id_usuario_scoa = retornaUsuarioSugarId(112);
		
		if( $rsResponsaveis['customer'] == '112' /** || $rsResponsaveis['vendedor'] == 112 **/ )
		{
			continue;
		}	
		
		/** Calcula a data de retorno da tarefa **/
		$data_retorno = date("Y-m-d",strtotime("+5 day",strtotime(date("Y-m-d"))));
		
		$dia_semana = date("w",strtotime($data_retorno));
		
		if($dia_semana == "6" || $dia_semana == "0")
		{
			$data_retorno = date("Y-m-d",strtotime("Monday +1 day",strtotime($data_retorno)));
		}
		
		/** Salva o registro na tabela de tasks do sugar **/		
		$data_tasks = Array(
							'id' => create_guid(),
							'name' => $rsProposta['numero_proposta'] . " - ". $rsResponsaveis['razao'],
							'date_entered' => date('Y-m-d H:i:s'),
							'created_by' => $id_usuario_scoa,
							'description' => "Proposta enviada ao cliente em ".date('d/m/Y H:i:s')."\r\n Este Follow up foi criado automáticamente pelo sistema",
							'deleted' => '0',
							'assigned_user_id' => $id_sugar_customer,
							'status' => 'In Progress',
							'date_due_flag' => '1',
							'date_due' => $data_retorno,
							'date_start_flag' => '1',
							'date_start' => date('Y-m-d H:i:s'),
							'parent_type' => "Accounts",
							'parent_id' => $rsResponsaveis['id_conta'], 
							'priority' => 'High', 
					  );
		
		$conn->insert("sugarcrm.tasks",$data_tasks);
		
		$data_custom_tasks = Array(
									'id_c' => $data_tasks['id'],
									'id_proposta_c' => $envio['id_proposta'],
									'id_oportunidade_c' => $envio['id_proposta'],
						     );

		$conn->insert("sugarcrm.tasks_cstm",$data_custom_tasks);

		/** Retira a proposta da lista de pendencias **/
		$conn->update("CLIENTES.envios_propostas",Array('envio_sugar' => 'S'), 'id_envio_proposta = '.$envio['id_envio_proposta']);
		
	}	
			
}