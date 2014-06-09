<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Cliente
 *
 * Classe que manipula os dados da entidade Cliente no sistema
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/01/2013
 * @name Clientes
 * @version 1.0
 */

class Cliente {
	
	private $id_cliente = NULL;
	private $razao = NULL;
	private $cnpj = NULL;
	private $contatos = Array();
	private $endereco = NULL;
	private $numero = NULL;
	private $bairro = NULL;
	private $cidade = NULL;
	private $estado = NULL;
	private $classificacao = NULL;
	private $vendedor_exportacao = NULL;
	private $vendedor_importacao = NULL;
	private $customer_exportacao = NULL;
	private $customer_importacao = NULL;
	private $id_grupo_comercial = NULL;
	private $id_grupo_cnpj = NULL;
	
	public function __construct()
	{
		
	}
		
	/**
	  * Set Id
	  * 
	  * Atribui um id ao cliente
	  * 
	  * @name setId
	  * @access public
	  * @param int 
	  * @return boolean
	  */
	public function setId( $id = NULL )
	{
		
		if( empty($id) || ! is_integer($id) )
		{
			return FALSE;
		}	
		
		$this->id_cliente = $id;
		
		return TRUE;
		
	}
	
	/**
	  * Get Id
	  * 
	  * Obtem o id do cliente
	  * 
	  * @name getId
	  * @access public
	  * @param  
	  * @return int
	  */
	public function getId()
	{
		return (int)$this->id_cliente;
	}
	
	/**
	  * Set Razao
	  * 
	  * Atribui uma razão ao cliente
	  * 
	  * @name setRazao
	  * @access public
	  * @param string
	  * @return boolean
	  */
	public function setRazao( $razao = NULL )
	{
		if( empty($razao) )
	 	{
	 		return FALSE;
	 	}

	 	$this->razao = str_replace("/", "-", $razao);
	 	
	 	return TRUE;
	 	
	}
	 
	/**
	  * Get Razao
	  * 
	  * Obtem uma razão para o usuário
	  * 
	  * @name setRazao
	  * @access public
	  * @param 
	  * @return string
	  */ 
	public function getRazao()
	{
		return (string)$this->razao;
	}
	
	/**
	  * Set CNPJ
	  * 
	  * Atribui um cnpj ao cliente
	  * 
	  * @name setCNPJ
	  * @access public
	  * @param string
	  * @return boolean
	  */
	public function setCNPJ( $cnpj = NULL )
	{
		if( empty($cnpj) )
		{
			return FALSE;
		}
		
		include_once APPPATH."/models/Clientes/cnpj.php";
		
		$objCnpj = new CNPJ($cnpj);
		
		$objCnpj->removerLetrasAcentos();
		
		if( ! $objCnpj->validarCNPJ() )
		{
			return FALSE;
		}	
		
		$this->cnpj = $objCnpj->getCNPJ();
		
		return TRUE;
	}

	/**
	  * getCNPJ
	  * 
	  * Obtem o cnpj do cliente
	  * 
	  * @name getCNPJ
	  * @access public
	  * @param 
	  * @return string
	  */
	public function getCNPJ()
	{
		return (string)$this->cnpj;
	}
	
	/**
	  * Set Contato
	  * 
	  * Atribui um contato para o cliente
	  * 
	  * @name setContato
	  * @access public
	  * @param Contato
	  * @return boolean
	  */
	public function setContatos( Contato $contato )
	{
		$this->contatos[] = $contato;
		return TRUE;
	}
	
	/**
	  * Get Contatos
	  * 
	  * Obtem os contatos do cliente
	  * 
	  * @name getContatos
	  * @access public 
	  * @param  
	  * @return Contato
	  */
	public function getContatos()
	{
		return $this->contatos;
	}
	
	public function setEndereco( $endereco = NULL )
	{
		
		if( empty($endereco) )
		{
			return FALSE;
		}
		
		$this->endereco = $endereco;
		
		return TRUE;
		
	}
	
	public function getEndereco()
	{
		return (string)$this->endereco;
	}
	
	public function setNumero( $numero = NULL )
	{
		
		if( empty($numero) || ! is_integer($numero) )
		{
			return FALSE;
		}	
		
		$this->numero = $numero;
		
		return TRUE;
		
	}
	
	public function getNumero()
	{
		return $this->numero;		
	}
	
	public function setBairro( $bairro = NULL )
	{
		
		if( empty($bairro) )
		{
			return FALSE;
		}	
		
		$this->bairro = $bairro;
		
		return TRUE;
		
	}
	
	public function getBairro()
	{
		return (string)$this->bairro;
	}
	
	public function setCidade( Cidade $cidade )
	{
		
		$this->cidade = $cidade;
		
		return TRUE;
		 
	}
	
	public function getCidade()
	{
		return $this->cidade;	
	}
	
	public function setEstado($estado = NULL)
	{
		
		if( empty($estado) )
		{
			return FALSE;
		}
		
		$this->estado = $estado;
		
		return TRUE;
		
	}

	public function getEstado()
	{
		return $this->estado;
	}
	
	public function setClassificacao( $classificacao = NULL )
	{
		
		if( empty($classificacao) )
		{
			return FALSE;
		}	
		
		$this->classificacao = $classificacao;
		
		return TRUE;
	}
	
	public function getClassificacao()
	{
		return $this->classificacao;
	}
	
	public function getVendedorExportacao()
	{
		return $this->vendedor_exportacao;
	}
		
	public function setVendedorExportacao(Usuario $vendedor_exportacao)
	{
		$this->vendedor_exportacao = $vendedor_exportacao;
		return $this;
	}
		
	public function getVendedorImportacao()
	{
		return $this->vendedor_importacao;
	}
		
	public function setVendedorImportacao(Usuario $vendedor_importacao)
	{
		$this->vendedor_importacao = $vendedor_importacao;
		return $this;
	}
		
	public function getCustomerExportacao()
	{
		return $this->customer_exportacao;
	}
		
	public function setCustomerExportacao(Usuario $customer_exportacao)
	{
		$this->customer_exportacao = $customer_exportacao;
		return $this;
	}
		
	public function getCustomerImportacao()
	{
		return $this->customer_importacao;
	}
		
	public function setCustomerImportacao(Usuario $customer_importacao)
	{
		$this->customer_importacao = $customer_importacao ;
		return $this;
	}
	
	public function setGrupoComercial($id_grupo)
	{
		$this->id_grupo_comercial = $id_grupo;
		return $this;
	}
	
	public function getGrupoComercial()
	{
		return $this->id_grupo_comercial;
	}
	
	public function setGrupoCnpj($id_grupo)
	{
		$this->id_grupo_cnpj = $id_grupo;
		return $this;
	}
	
	public function getGrupoCnpj()
	{
		return $this->id_grupo_cnpj;
	}
	
}//END CLASS