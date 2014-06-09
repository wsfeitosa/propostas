<?php
if( ! $_SESSION['matriz'] )
{
	session_start();
}	
/**
 * Classe que manipula e compõe a entidade envio do módulo de propostas
 * esta classe envia os emails do módulo de propostas
 * @author wsfall
 * @package Email
 * @name Envio
 */
class Envio {

	private $emails = Array();
	private $status = FALSE;
	
	public function __construct()
	{
		
	}
	
	/**
	 * Adiciona um novo email para ser enviado
	 * @name adicionarNovoEmail
	 * @access public
	 * @param Email $email
	 * @return Boolean
	 */
	public function adicionarNovoEmail( Email $email )
	{
		
		$this->emails[] = $email;
		
		end($this->emails);
		
		return key($this->emails);
		
	}
	
	/**
	  * Remove um email da classe
	  * @name removerEmail
	  * @access public
	  * @param index int
	  * @return Boolean
	  */
	public function removerEmail( $index )
	{
		
		unset($this->emails[$index]);
		
		return TRUE;
		
	}
	
	/**
	  * Retorna a quantidade de emails estão atualmente atribuidos a classe de envio
	  * @name obterQuantidadeEmails
	  * @access public
	  * @param 
	  * @return int
	  */
	public function obterQuantidadeEmails()
	{
		return count($this->emails);
	}
	
	/**
	  * Envia a mensagem contendo os emails que foram atribuidos a classe
	  * @name enviarmensagem
	  * @access public
	  * @param $corpoMensagem String
	  * @return boolean
	  */
	public function enviarMensagem( $corpoMensagem, $assunto ,$anexo = "", $nome_anexo = "")
	{

		include_once $_SERVER['DOCUMENT_ROOT']."/Libs/envia_msg.php";
		include_once APPPATH."/models/Usuarios/usuario.php";
		include_once APPPATH."/models/Usuarios/usuario_model.php";
		
		$usuario_envio = new Usuario();
		$usuario_model = new Usuario_Model();
		
		$usuario_envio->setId((int)$_SESSION['matriz'][7]);
		$usuario_model->findById($usuario_envio);
				
		$emails_serializados = $this->serializaEmails();
		
		$emails = explode(";", $emails_serializados,2);
		
		$email_enviado = envia_email(
										$usuario_envio->getEmail()->getEmail(),
										$usuario_envio->getnome(), 
										$emails[0], $emails[1], "",
										//"wellington.feitosa@allink.com.br", $emails[1], "",
										$usuario_envio->getEmail()->getEmail(),
										$assunto, $corpoMensagem, 
										$anexo, $nome_anexo 
		);
		
		return $email_enviado;
		
	}
	
	/**
	  * Serializa os email no formato correto para o envio
	  * @name serializarEmails
	  * @access protected
	  * @param  
	  * @return $emails_serializados String
	  */
	protected function serializaEmails()
	{
		
		if( ! is_array($this->emails) || count($this->emails) < 1 )
		{
			return FALSE;
		}	
		
		/** Primeiro remove todos os emails duplicados **/
		$emails = array();
		
		foreach( $this->emails as $email )
		{
			$emails[] = $email->getEmail();
		}	
		
		$emails = array_unique($emails);
		
		$emails_serializados = "";
		
		foreach( $emails as $email )
		{
			$emails_serializados .= $email.";";
		}	
		
		/** Remove o ultimo caractere de ; do fim da String **/
		return substr($emails_serializados, 0, -1);
				
	}
	
	public function incluir_estilo_email ()
	{
		$estilo_email = "
				<style type='text/css'>
				body
				{
					background:#ffffff;
					color:#4682B4;
					width:99%;
					margin-top:2%;
					margin-left:1%;
					margin-right:1%;
					font-size:14px;
					font-family:arial;
				}
				
				a, a:visited
				{
					text-decoration:none;
					color:#4682B4;
					font-weight: bold;
				}
				
				a:hover
				{
					color:red;
				}
				
				.principal
				{
					width:96%;
					margin:1%;
					display:table;
					padding:4px;
					background:#FFFFFF;
					box-shadow:0px 0px 20px #555;
				}
				
				.titulo
				{
					text-align:left;
					padding:4px;
					background:#4B6C9E;
					color:#FFFFFF;
					width:99%;
					height:40px;
					font-size:18px;
					margin:0.1%;
					margin-bottom:20px;
				}
				
				.sub_titulo
				{
					text-align:left;
					padding:4px;
					background:#4B6C9E;
					color:#FFFFFF;
					width:96%;
					font-size:14px;
					margin:0.3%;
					margin-bottom:20px;
				}
				
				.container_elemento
				{
					float:left;
					/*display:block;*/
					margin-left:0.3%;
				}
				
				.label
				{
					display:block;    /** inputs abaixo dos labels */
					/*float:left;*/   /** inputs a direita dos labels */
					width:230px;
					padding:0px;
				}
				
				.conteudo
				{
					display:block;    /** inputs abaixo dos labels */
					color:#555555;
					margin:0.4%;
					margin-bottom:2%;
				}
				
				.botoes
				{
					float:left;
					padding-left:6px;
					padding-right:6px;
					height:50px;
					width:98%;
				}
				
				.coluna
				{
					width:31%;
					float:left;
					margin-left:0.3%;
				}
				
				.coluna2
				{
					width:48%;
					float:left;
					margin-left:0.3%;
				}
				
				.uma_coluna
				{
					width:90%;
					float:left;
					margin-left:0.3%;
				}
				
				.quatro_colunas
				{
					float:left;
					margin-left:0.3%;
					height:50px;
					width:20%;
				}
				
				.cinco_colunas
				{
					float:left;
					margin-left:0.3%;
					height:50px;
					width:18%;
				}
				
				
				input[type=text], textarea, .coluna select
				{
					padding:2px;
					margin:2px 0 12px 0;
					background:#fff;
					font-size:11px;
					color:#4682B4;
					border:1px #ddd solid;
					-webkit-box-shadow:0px 0px 4px #aaa;
					-moz-box-shadow:0px 0px 4px #aaa;
					box-shadow:0px 0px 4px #aaa;
					-webkit-transition:background 0.3s linear;
				}
				
				select
				{
					padding:2px;
					margin:2px 0 6px 0;
					background:#fff;
					font-size:11px;
					color:#4682B4;
					border:1px #ddd solid;
					-webkit-box-shadow:0px 0px 4px #aaa;
					-moz-box-shadow:0px 0px 4px #aaa;
					box-shadow:0px 0px 4px #aaa;
					-webkit-transition:background 0.3s linear;
					heigth:10px;
				}
				
				input[type=button], input[type=submit], button
				{
					border:1px #ddd solid;
					padding:3px;
					margin:3px 0 12px 0;
					background:#4B6C9E;
					color:#FFFFFF;
					font-size:12px;
					font-family:arial;
				}
				
				input[type=button]:hover, input[type=submit]:hover, button:hover
				{
					background:#eee;
					color:#555555;
				}
				
				input[type=text]:hover, textarea:hover
				{
					background:#eee;
				}
				
				.tabela_scoa_sombra
				{
					width:96%;
					margin-left:0.7%;
					text-align:left;
					border-collapse:collapse;
					box-shadow:0px 0px 20px #555;
				}
				
				.tabela_scoa
				{
					width:96%;
					margin-left:0.7%;
					text-align:left;
					border-collapse:collapse;
					border:1px solid #BECBDD;
				}
				
				.tabela_scoa th, .tabela_scoa_sombra th
				{
					padding:8px 12px 8px 12px;
					font-weight:bold;
					font-size:14px;
					border-bottom:2px solid #4682B4;
					background:#E8EEF7;
				}
				
				.tabela_scoa td, .tabela_scoa_sombra td
				{
					padding:8px 12px 8px 12px;
					font-size:12px;
					color:#555555;
					border-bottom:1px solid #4682B4;
				}
				
				.tabela_scoa tbody tr:hover td, .tabela_scoa_sombra tbody tr:hover td
				{
					color:#111111;
					background:#98AECE;
				}
				</style>
				";
		return $estilo_email;
	}
	
		
}//END CLASS