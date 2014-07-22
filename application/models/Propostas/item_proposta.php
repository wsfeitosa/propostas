<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! isset($_SESSION) )
{
	session_start();
}

/**
 * Classe responsavel para manipulação dos itens da proposta
 * 
 * @package Proposta 
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 14/01/2013
 * @version  versao 1.0 
*/

class Item_Proposta{
	
    protected $id = NULL;
    protected $inicio = NULL;
	protected $validade = NULL;
    protected $numero = NULL;
    protected $tarifario = NULL;
	protected $status = NULL;
    protected $pp = FALSE; 
    protected $cc = FALSE; 
    protected $imo = NULL;
    protected $mercadoria = null;
    protected $peso = null; 
    protected $cubagem = null;
    protected $volumes = null;
    protected $observacao_interna = null; 
    protected $observacao_cliente = null; 
    protected $usuario_desbloqueio = null;
    protected $data_desbloqueio = null;


    public function __construct(Tarifario $tarifario = NULL)
	{
		if(!is_null($tarifario))
        {
            $this->tarifario = $tarifario; 
        }  	

		$this->inicio = date('Y-m-d H:i:s');
		
	}
	
    /**
     * setId
     * 
     * Atribui um id ao item
     * 
     * @name setId
     * @access public
     * @param int
     * @return Item_Proposta
     */
    public function setId($id) 
    {
        $this->id = (int) $id;
        return $this;
    }
    
    /**
     * getId
     * 
     * Obtém o id do item
     * 
     * @name getId
     * @access public     
     * @return int
     */
    public function getId() 
    {
        return (int) $this->id;
    }
    
    /**
     * setNumero
     * 
     * Atribui um numero a um item de proposta
     * 
     * @name setNumero
     * @access public
     * @param string $numero
     * @return Itens_Propostas
     */
    public function setNumero($numero) 
    {
        $this->numero = $numero;
        return $this;
    }
        
    /**
     * getNumero
     * 
     * Obtém o número de um item de proposta
     * 
     * @name getNumero
     * @access public     
     * @return string $numero
     */
    public function getNumero() 
    {
        return $this->numero;
    }
    
    /**
     * getTarifario
     * 
     * Obtem o objeto tarifario que está relacionado ao item
     * 
     * @name getTarifario
     * @access public     
     * @return Object Tarifario
     */
    public function getTarifario() {
        return $this->tarifario;
    }
        
	/**
	  * Atribui um valor para a data inicial
	  * 
	  * @name setInicio
	  * @access public
	  * @param $inicio Date
	  * @return boolean
	  */
	public function setInicio( $inicio = NULL )
	{
		
		if( is_null($inicio) )
		{
			error_log("Data Invalida atribuida ao item da proposta");
			throw new Exception("Data Invalida atribuida ao item da proposta");
		}

		$this->inicio = $inicio;
		
		return TRUE;
		
	}
	
	/**
	 * Obtem um valor para a data inicial
	 * 
	 * @name getInicio
	 * @access public
	 * @param 
	 * @return DateTime
	 */
	public function getInicio()
	{
		return $this->inicio;
	}	
	
	/**
	 * Atribui um valor para a data de Validade
	 * 
	 * @name getInicio
	 * @access public
	 * @param $date Date
	 * @return boolean
	 */
	public function setValidade( $validade = NULL )
	{
		if( is_null($validade) )
		{
			error_log("Data Invalida atribuida ao item validade da proposta");
			throw new Exception("Data Invalida atribuida ao item validade da proposta");
		}
		
		$this->validade = $validade;
		
		return $this;
		
	}
	
	/**
	 * Obtem o valor de status da proposta
	 *
	 * A função retorna o valor da data de validade
	 *
	 * @name getValidade
	 * @access public
	 * @param
	 * @return DateTime
	 */
	public function getValidade()
	{
		return $this->validade;
	}
	
	/**
	 * Atribui um valor para o status do item
	 * 
	 * A função só aceita valores se os mesmos forem do tipo Email.
	 *
	 * @name setStatus
	 * @access public
	 * @param  StatusItem
	 * @return boolean
	 */
	public function setStatus( Status_Item $status )
	{
		
		$this->status = $status;
		
		return TRUE;
		
	}
	
	/**
	 * Obtem o valor de status da proposta
	 *
	 * A função retorna um objeto do tipo StatusItem que contem o status real da proposta,
	 * isto é utilizado para fazer os follow Up das propostas
	 *
	 * @name getStatus
	 * @access public
	 * @param  
	 * @return StatusItem
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
     * @name getPp
     * @access public
     * @return string
     */    
    public function getPp()
    { 
        return (bool)$this->pp;
    }
    
    /**
     * @access public
     * @name setPp
     * @param string $pp
     * @return string
     */
    public function setPp($pp)
    { 
       $this->pp = (bool)$pp ;
    } 
    
    /**
     * @access public
     * @name getCc     
     * @return string
     */
    public function getCc()
    { 
       return (bool)$this->cc;
    }
    
    /**
     * @access public
     * @name setCc
     * @param string $cc
     * @return string
     */
    public function setCc($cc)
    { 
       $this->cc = (bool)$cc ;
    } 
    
    /**
     * @access public
     * @name setImo
     * @param string $imo
     * @return string
     */
    public function setImo($imo)
    {   
        $this->imo = $imo;
        return $this;
    }

    /**
     * @access public
     * @name getImo
     * @return string
     */
    public function getImo()
    {
        return $this->imo;
    }

    /**
     * @access public
     * @name getMercadoria
     * @return string
     */
    public function getMercadoria()
    { 
       return $this->mercadoria;
    }
    
    /**
     * @access public
     * @name setMercadoria
     * @param string $mercadoria
     * @return string
     */
    public function setMercadoria($mercadoria)
    { 
       $this->mercadoria = $mercadoria ;
    } 
    
    /**
     * @access public
     * @name getPeso     
     * @return float
     */
    public function getPeso()
    { 
       return (float)$this->peso;
    }
    
    /**
     * @access public
     * @name setPeso
     * @param float $peso
     * @return float
     */
    public function setPeso($peso)
    { 
       $this->peso = $peso ;
    } 

    /**
     * @access public
     * @name getCubagem 
     * @return float
     */
    public function getCubagem()
    {    
        return (float)$this->cubagem;
    }
    
    /**
     * @access public
     * @name setCubagem
     * @param float $cubagem
     * @return float
     */
    public function setCubagem($cubagem)
    { 
       $this->cubagem = $cubagem ;
    } 
    
    /**
     * @access public
     * @name getVolumes     
     * @return integer
     */
    public function getVolumes()
    { 
       return $this->volumes;
    }
    
    /**
     * @access public
     * @name setVolumes
     * @param integer $volumes
     * @return integer
     */
    public function setVolumes($volumes)
    { 
       $this->volumes = $volumes ;
    } 
    
    /**
     * @access public
     * @name getObservacaoInterna     
     * @return string
     */
    public function getObservacaoInterna()
    { 
       return $this->observacao_interna;
    }
    
    /**
     * @access public
     * @name setObservacaoInterna
     * @param string $observacao_interna
     * @return string
     */
    public function setObservacaoInterna($observacao_interna)
    { 
       $this->observacao_interna = $observacao_interna ;
    } 
    
    /**
     * @access public
     * @name getObservacaoCliente     
     * @return string
     */
    public function getObservacaoCliente()
    { 
       return $this->observacao_cliente;
    }
    
    /**
     * @access public
     * @name setObservacaoCliente
     * @param string $observacao_cliente
     * @return string
     */
    public function setObservacaoCliente($observacao_cliente)
    { 
       $this->observacao_cliente = $observacao_cliente ;
    } 
    
    public function getUsuarioDesbloqueio() 
    {
        return $this->usuario_desbloqueio;
    }

    public function getDataDesbloqueio() 
    {
        return $this->data_desbloqueio;
    }

    public function setUsuarioDesbloqueio(Usuario $usuario_desbloqueio) 
    {
        $this->usuario_desbloqueio = $usuario_desbloqueio;
        return $this;
    }

    public function setDataDesbloqueio(DateTime $data_desbloqueio) 
    {
        $this->data_desbloqueio = $data_desbloqueio;
        return $this;
    }


	
}//END CLASS