<?php
/**
 * Usuario_Model
 *
 * Aplica às operações de banco de dados a entidade usuário 
 *
 * @package models/Usuarios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 27/05/2013
 * @version  versao 1.0
*/

class Usuario_Model extends CI_Model{
	
	public function __construct() 
	{
		parent::__construct();
	}
			
	/**
	 * findById
	 *
	 * Pesquisa um cliente pelo ID, baseado no usuário
	 *
	 * @name findById
	 * @access public
	 * @param Usuario $usuario
	 * @return void
	 * @throws InvalidArgumentException
	 */ 	
	public function findById( Usuario $usuario )
	{
		
		$id_usuario = $usuario->getId();
		
		if( empty($id_usuario) )
		{
			throw new InvalidArgumentException("Nenhum id de usuário definido para realizar a pesquisa!");
		}	
		
		$this->db->select("nome, cargo, id_filial, email, ddd, ramal, telefone, fax");
		$this->db->from("USUARIOS.usuarios");
		//$this->db->where("cancelado","N");
		$this->db->where("id_user",$usuario->getId());
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{			
			throw new RuntimeException("Nenhum Usuário Encontrado!");
		}	
		
		$usuario_encontrado = $rs->row();
		
		$this->load->model("Email/email");
		$this->load->model("Usuarios/filial");
		$this->load->model("Usuarios/filial_model");
		
		$usuario->setNome($usuario_encontrado->nome);
		$usuario->setCargo($usuario_encontrado->cargo);
		$usuario->setDdd($usuario_encontrado->ddd);
		$usuario->setFone($usuario_encontrado->telefone);
		$usuario->setRamal($usuario_encontrado->ramal);
		$usuario->setFax($usuario_encontrado->fax);
		
		$filial = new Filial();
		$filial->setId((int)$usuario_encontrado->id_filial);
		
		$filial_model = new Filial_Model();
		$filial_model->findById($filial);
		
		$usuario->setFilial($filial);
		
		$email = new Email();
		$email->setEmail($usuario_encontrado->email);
		$usuario->setEmail($email);
		
	}
	
	/**
	 * gerarAssinatura
	 *
	 * gera a assinatura do usuário no mesmo padrão do email
	 *
	 * @name gerarAssinatura
	 * @access public
	 * @param Usuario $usuario
	 * @return string $assinatura
	 */ 	
	public function gerarAssinatura(Usuario $usuario)
	{
		
		/** Filiais **/
		$imagens = Array(
							1 => 'itj', 2 => 'poa', 3 => 'cwb', 4 => 'spo',
							5 => 'rjo', 6 => '', 7 => 'ssz', 8 => '',
							9 => '', 10 => '', 11 => '', 12 => '',
							13 => '', 14 => '',
		);
		
		$assinatura = '     <style type="text/css"> body {font: 10pt arial;color:#145088;} </style>
						    <p style="color:#145088; font-size:14pt;"><font face="Arial">
						        <strong>'.$usuario->getnome().'</strong><br>
						        <span style="font-size:11pt;">'.$usuario->getCargo().' - Allink '.$usuario->getFilial()->getNomeFilial().'<br>
						        <span style="color:#Ed3237; font-size:10pt;">
						        <strong>Allink Transportes Internacionais</strong></font></span></p>
						
						    <p style="color:#145088; font-size:10pt;"><font face="Arial">
						        Phone: +55 '.$usuario->getDdd().' '.$usuario->getFone().'<br />'; 

		$ramal = $usuario->getRamal();
		
		if( ! empty( $ramal ) )
		{
			$assinatura .= "Direct: +55 ".$usuario->getDdd()." ".$usuario->getFone()."<br />";
		}					

		$fax = $usuario->getFax();
		
		if( ! empty( $fax ) )
		{			
			$assinatura .= 'Fax: +55 '.$usuario->getDdd().' '.$usuario->getFax().'<br />';
		}		
						        
						        
		$assinatura .=        	$usuario->getEmail()->getEmail().'<br>
						        <span style="color:#145088; font-size:14pt;">
						        <strong>www.allink.com.br</strong></font></span></p>
						        <img src="http://www.allink.com.br/marketing/imagem_1'.$imagens[$usuario->getFilial()->getId()].'.jpg"/>
						        <p style="color:#145088; font-size:12pt;"><font face="Arial">
						        <strong>E MARITIMO, E NOSSO!</strong><br>
						        <span style="font-size:10pt; text-decoration:none;">
						        Allink is a member of the WorldWide Alliance - www.wwalliance.com</span></font></p>';
		
		return $assinatura;
		
	}
	
}//END CLASS