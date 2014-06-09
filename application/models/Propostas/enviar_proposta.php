<?php
if( ! isset($_SESSION['matriz']) )
{
	session_start();
}	
/**
 * Enviar Proposta
 *
 * Envia o email da proposta juntamente com os anexos das proposta 
 *
 * @package models/Propostas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 30/07/2013
 * @version  versao 1.0
*/
class Enviar_Proposta extends CI_Model {
	
	protected $proposta = NULL;
		
	public function __construct( Proposta $proposta )
	{
		parent::__construct();
		$this->proposta = $proposta;
		
		$this->load->model("Email/email_model");
		$this->load->model("Email/envio");
		$this->load->model("Email/email");
		$this->output->enable_profiler(TRUE);
	}
	
	public function Enviar()
	{
				
		/** Adiciona os emails a mensagem **/
		$envio = new Envio();
		
		$emails_proposta = $this->proposta->obterEmails();
		
		$email_para_log = "";
		$email_cc_log = "";

		foreach( $emails_proposta as $email )
		{
			$envio->adicionarNovoEmail($email);
			$email_para_log .= $email->getEmail()."|";
		}	
		
		/** Inclui os emails do vendedor e do customer do cliente **/
		$responsaveis = $this->buscarEmailDoVendedorECustomer();
		
		foreach( $responsaveis as $responsavel )
		{
			$email_responsavel = new Email();
			$email_responsavel->setEmail($responsavel['email']);			
			$envio->adicionarNovoEmail($email_responsavel);
			$email_cc_log .= $responsavel['email']."|";
		}	
		
		(string)$anexos = "";
		(string)$nome_anexos = "";
		
		/** Cria os anexos **/
		include_once 'exportar_pdf_proposta.php';
		
		$exportar_pdf = new Exportar_Pdf_Proposta($this->proposta,TRUE);
		
		$anexos = $exportar_pdf->gerarPdf();
		
		$nome_anexos = "proposta.pdf";
		
		/** Se for uma proposta cotação então anexa também o excel **/
		if( $this->proposta instanceof Proposta_Tarifario )
		{
			include_once 'exportar_proposta_tarifario.php';
			
			$exportar_excel = new Exportar_Proposta_Tarifario($this->proposta,TRUE);
			
			$anexos .=  ";".$exportar_excel->exportar();
			
			$nome_anexos .= ";proposta.xls";
		}	
	
		$corpo_email = $this->formatarMensagem();
		
		$envio->enviarMensagem($corpo_email, "ALLINK: COTAÇÃO DE SERVIÇOS DE TRANSPORTES MARÍTIMOS - ".$this->proposta->getNumero(),$anexos,$nome_anexos);
		
		/** Atualiza o status da proposta **/
		$this->db->where("id_proposta",$this->proposta->getId());
		$this->db->update("CLIENTES.itens_proposta",Array('id_status_item' => 3));
		
		/** Salva o log de envio da proposta **/
		$log_envio_email = Array(
								  'id_proposta' => $this->proposta->getId(),
								  'id_usuario_envio' => $_SESSION['matriz'][7],
								  'data_envio' => date('Y-m-d H:i:s'),
								  'emails_para' => $email_para_log,
								  'emails_cc' => $email_cc_log,
								  'envio_sugar' => 'N',
						   );
		
		$this->db->insert("CLIENTES.envios_propostas",$log_envio_email);
		
		$PID = shell_exec("nohup nice php -f ".APPPATH."/models/Servicos/enviar_followup_para_crm.php 1>/dev/null & echo $!");
						
		echo "<script language='javascript'>
				alert('Mensagem enviada com sucesso!');				
				window.close();
			   </script>";
		
	}
	
	protected function formatarMensagem()
	{
		
		if( is_null($this->proposta) )
		{
			show_error("A proposta não foi corretamente informada para realizar o envio do email!");exit;
		}	
		
		if( ! isset($_SESSION['matriz']) )
		{
			show_error("Sua sessão não foi iniciada ou expirou enquanto você enviava a proposta!");exit;
		}	
		
		/** Assinatura **/
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		
		$usuario = new Usuario();
		$usuario->setId((int)$_SESSION['matriz'][7]);
		
		$usuario_model = new Usuario_Model();
		$usuario_model->findById($usuario);
		$assinatura = $usuario_model->gerarAssinatura($usuario);
		
		$cliente = $this->proposta->getClientes();
		
		$mensagem ='<div class="container_elemento">				            
			            <label class="conteudo">
			            	Prezado Cliente '.$cliente[0]->getRazao().',<br/>
			            	Agradecemos seu interesse em cotar com a Allink Transportes Internacionais Ltda. e apresentamos anexa a nossa oferta e condições. 
			            </label>
			        </div>
			        <div class="container_elemento">				            
			            <label class="conteudo">
			            	'.$assinatura.'
			            </label>
			        </div>';
		$corpo_email .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							    <title>Scoa</title>
							    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
							    <meta name="description" content="Scoa Sistema de controle Allink" />
							    <meta name="author" content="Allink Transportes Internacionais Ltda" />
							    <meta name="robots" content="noindex,nofollow" />
							    <meta name="robots" content="noarchive" />
							    '.$this->envio->incluir_estilo_email().'
							</head>
							<body>';		
		$corpo_email .= "<div class='principal'>";
		$corpo_email .= "<p class='titulo'>COTAÇÃO DE TRANSPORTES MARÍTIMOS </p>";
		$corpo_email .= $mensagem;
		$corpo_email .="</div>";
		$corpo_email .="<div class='botoes'>Email enviado em: ".date('d/m/Y H:i:s')."</div>";
		$corpo_email .= "</body></html>";
		return $corpo_email;		
	}

	protected function buscarEmailDoVendedorECustomer()
	{

		$clientes_proposta = $this->proposta->getClientes();
		
		$primeiro_cliente = $clientes_proposta[0];
		
		$this->db->
					select("responsavel as vendedor_exp, customer as vendedor_imp,
							customer_exportacao, customer_importacao")->
					from("CLIENTES.clientes")->
					where("id_cliente",$primeiro_cliente->getId());
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{ 
			show_error("Não foi possivel encontrar o vendedor eo customer do cliente para enviar a proposta");
		}	
		
		$cliente = $rs->row();
		
		if( $this->proposta->getSentido() == "EXP" )
		{
			$this->db->select("email")->from("USUARIOS.usuarios")->where_in("id_user",Array($cliente->vendedor_exp,$cliente->customer_exportacao));				
		}
		else
		{
			$this->db->select("email")->from("USUARIOS.usuarios")->where_in("id_user",Array($cliente->vendedor_imp,$cliente->customer_importacao));						
		}
		
		$rs_responsavel = $this->db->get();
						
		return $rs_responsavel->result_array();
		
	}
	
} //END CLASS