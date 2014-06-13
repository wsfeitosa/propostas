<?php
class Email_Model extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function formatarMensagemDeSolicitacao(Solicitacao_Desbloqueio $solicitacao)
	{		
		$this->load->model("Email/email");
		$this->load->model("Email/envio");
						
		/** Assinatura **/
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Adicionais/serializa_taxa");
		
		$usuario = new Usuario();
		$usuario->setId((int)$_SESSION['matriz'][7]);
		
		$usuario_model = new Usuario_Model();
		$usuario_model->findById($usuario);
		$assinatura = $usuario_model->gerarAssinatura($usuario);

		$dataInicioAprovada = $solicitacao->getAcordo()->getInicio();
		$validadeAprovada = $solicitacao->getAcordo()->getValidade();
						
		(string) $labelClientes = "";
		(string) $labelTaxas = "";
		(string) $corpo_email = "";
				
		foreach( $solicitacao->getAcordo()->getClientes() as $clienteAcordo )
		{	
			$this->cliente_model->findById($clienteAcordo);
			if( $clienteAcordo->getRazao() != "" )
			{			
				$labelClientes .= $clienteAcordo->getCNPJ() . " -> " . $clienteAcordo->getRazao() . " | " . $clienteAcordo->getEstado() . "<br/>";
			}
		}
		
		foreach( $solicitacao->getAcordo()->getTaxas() as $taxa )
		{
			//As taxas adicionadas para o desbloqueio não tem o nome setado
			if( $taxa->getNome() != "" || $taxa->getNome() == null )
			{
				$this->load->model("Taxas/taxa_model");
				$this->load->model("Taxas/unidade_model");
				$this->load->model("Taxas/moeda_model");
				
				$this->taxa_model->obterNomeTaxaAdicional($taxa);
				$this->unidade_model->findById($taxa->getUnidade());
				$this->moeda_model->findById($taxa->getMoeda());
			}
				
			$labelTaxas .= $this->serializa_taxa->ConverterTaxaParaString($taxa) . "<br />";
		}	
		
		(string) $mensagem = '<div class="uma_coluna">				            
						      	<label class="conteudo">
						        	Prezado Aprovador,<br/>
						           	O acordo de taxas adicionais sobre o frete número '.$solicitacao->getAcordo()->getNumeroAcordo().', dos seguintes clientes abaca de ser aprovado:<p />
									'.$labelClientes.' <p />
									Data de inicio: '.$dataInicioAprovada->format('d/m/Y').'<br />
									Data de validade: '.$validadeAprovada->format('d/m/Y').'	<p />

									Taxas Aprovadas:<br />
									'.$labelTaxas.'<p />

									Observações:<br />
									'.nl2br(strtoupper($solicitacao->getAcordo()->getObservacao())).'<p />	

									Aprovador:<br />
									'.$usuario->getNome().'				
						        </label>
						      </div>
						      <div class="uma_coluna">				            
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
		$corpo_email .= "<p class='titulo'> Resposta de Solicitação de aprovação de valores e condições do acordo de adicionais {$solicitacao->getAcordo()->getNumeroAcordo()} </p>";
		$corpo_email .= $mensagem;
		$corpo_email .="</div>";
		$corpo_email .="<div class='botoes'>Email enviado em: ".date('d/m/Y H:i:s')."</div>";
		$corpo_email .= "</body></html>";
		
		return $corpo_email;
		
	}
	
	public function formatarMensagemDeAprovacao(Solicitacao_Desbloqueio $solicitacao, $exclusao = FALSE)
	{
		$this->load->model("Email/email");
		$this->load->model("Email/envio");
		
		/** Assinatura **/
		$this->load->model("Usuarios/usuario");
		$this->load->model("Usuarios/usuario_model");
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Adicionais/acordo_adicionais_model");
		$this->load->model("Adicionais/serializa_taxa");
		
		$usuario = new Usuario();
		$usuario->setId((int)$_SESSION['matriz'][7]);
		
		$usuario_model = new Usuario_Model();
		$usuario_model->findById($usuario);
		$assinatura = $usuario_model->gerarAssinatura($usuario);
		
        //FIXME às data do acordo estão incorretas pois não é possivel serializar objetos do tipo DateTime
		$dataInicioSolicitada = $solicitacao->getAcordo()->getInicio();
		$validadeSolicitada = $solicitacao->getAcordo()->getValidade();
                						
		(string) $labelClientes = "";
		(string) $labelTaxas = "";
		(string) $corpo_email = "";
		
		foreach( $solicitacao->getAcordo()->getClientes() as $clienteAcordo )
		{
			$this->cliente_model->findById($clienteAcordo);
			if( $clienteAcordo->getRazao() != "" )
			{
				$labelClientes .= $clienteAcordo->getCNPJ() . " -> " . $clienteAcordo->getRazao() . " | " . $clienteAcordo->getEstado() . "<br/>";
			}
		}
				
		foreach( $solicitacao->getAcordo()->getTaxas() as $taxa )
		{
			//As taxas adicionadas para o desbloqueio não tem o nome setado
			if( $taxa->getNome() != "" || $taxa->getNome() == null )
			{
				$this->load->model("Taxas/taxa_model");
				$this->load->model("Taxas/unidade_model");
				$this->load->model("Taxas/moeda_model");
		
				$this->taxa_model->obterNomeTaxaAdicional($taxa);
				$this->unidade_model->findById($taxa->getUnidade());
				$this->moeda_model->findById($taxa->getMoeda());
			}
		
			$labelTaxas .= $this->serializa_taxa->ConverterTaxaParaString($taxa) . "<br />";
		}
		
        if( $exclusao == FALSE )
        {
            $acao = "foi aprovado";
        }
        else
        {
            $acao = "teve a solicitação excluída";
        }    
        
		(string) $mensagem = '<div class="uma_coluna">
						      	<label class="conteudo">
						        	Prezado Solicitante,<br/>
						           	O acordo de taxas adicionais sobre o frete número '.$solicitacao->getAcordo()->getNumeroAcordo().', dos seguintes clientes '.$acao.':<p />
									'.$labelClientes.' <p />
									Data de inicio: '.$dataInicioSolicitada->format('d/m/Y').'<br />
									Data de validade: '.$validadeSolicitada->format('d/m/Y').'	<p />
		
									Taxas:<br />
									'.$labelTaxas.'<p />
		
									Observações:<br />
									'.nl2br(strtoupper($solicitacao->getAcordo()->getObservacao())).'<p />
		
									Aprovador:<br />
									'.$usuario->getNome().'
						        </label>
						      </div>
						      <div class="uma_coluna">
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
		$corpo_email .= "<p class='titulo'> Resposta de solicitação de desbloqueio de valores e condições do acordo de adicionais {$solicitacao->getAcordo()->getNumeroAcordo()} </p>";
		$corpo_email .= $mensagem;
		$corpo_email .="</div>";
		$corpo_email .="<div class='botoes'>Email enviado em: ".date('d/m/Y H:i:s')."</div>";
		$corpo_email .= "</body></html>";
		
		return $corpo_email;
	}
			
}
