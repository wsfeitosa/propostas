<?php
/**
 * Classe abstrata que representa a entidade Taxa
 *
 * Esta é uma classe abstrata que representa as taxas e de onde as
 * classes concretas Taxa_Adiciona, Taxa_Portuaria e Frete herdam
 * o seus métodos e caracteristicas
 *
 * @package Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @name Taxas
 * @version 1.0
 * @abstract
 */
abstract class Taxa{
	
    private $id_item = NULL; // id da tabela do banco de dados
	private $id = NULL;
	private $valor = 0.00;
	private $nome = NULL;
	private $valor_minimo = 0.00;
	private $valor_maximo = 0.00;
	private $moeda = NULL;
	private $unidade = NULL;
	private $bloqueada = NULL;
	private $ppcc = NULL;
	private $decorador = array();
	
	public function __construct()
	{
		
	}
    
    /**
	  * getIdItem
	  * 
	  * @name getIdItem
	  * @access public	  
	  * @return integer
	  */
    public function getIdItem() {
        return (int)$this->id_item;
    }
    
    /**
	  * setIdItem
	  * 
	  * @name setIdItem
	  * @access public
	  * @param int
	  * @return object $this
	  */
    public function setIdItem($id_item) {
        $this->id_item = (int)$id_item;
        return $this;
    }
        
	/**
	  * setId
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
		
		$this->id = $id;
		
		return TRUE;	
		
	}
	
	/**
	  * getId
	  * 
	  * @name getId
	  * @access public
	  * @param 
	  * @return int
	  */
	public function getId()
	{
		return (int)$this->id;
	}
	
	/**	 
	  * Set Valor
	  * 
	  * Atribui Valor a taxa
	  * 
	  * @name setValor
	  * @access public
	  * @param float
	  * @return boolean
	  */
	public function setValor( $valor )
	{
		
		if( ! is_float($valor) )
		{
			return FALSE;
		}	
		
		$this->valor = $valor;
		
		return TRUE;
		
	}
	
	/**
	  * Get Valor
	  * 
	  * Obtem o valor da taxa
	  * 
	  * @name getValor
	  * @access public
	  * @param 
	  * @return float
	  */
	public function getValor()
	{
		//return (float)number_format($this->valor,2,".",",");
		return $this->valor;
	}
	
	/**
	 * Set Valor Minimo
	 *
	 * Atribui Valor minimo a taxa
	 *
	 * @name setValorMinimo
	 * @access public
	 * @param float
	 * @return boolean
	 */
	public function setValorMinimo( $valor )
	{
	
		if( ! is_float($valor) )
		{
			return FALSE;
		}
	
		$this->valor_minimo = $valor;
	
		return TRUE;
	
	}
	
	/**
	 * Get Valor Minimo
	 *
	 * Obtem o valor minimo da taxa
	 *
	 * @name getValorMinimo
	 * @access public
	 * @param
	 * @return float
	 */
	public function getValorMinimo()
	{
		//return (float)number_format($this->valor_minimo,2,".",",");
		return $this->valor_minimo;
	}
	
	/**
	 * Set Valor Maximo
	 *
	 * Atribui Valor maximo a taxa
	 *
	 * @name setValorMaximo
	 * @access public
	 * @param float
	 * @return boolean
	 */
	public function setValorMaximo( $valor )
	{
	
		if( ! is_float($valor) )
		{
			return FALSE;
		}
	
		$this->valor_maximo = $valor;
	
		return TRUE;
	
	}
	
	/**
	 * Get Valor Maximo
	 *
	 * Obtem o valor maximo da taxa
	 *
	 * @name getValorMaximo
	 * @access public
	 * @param
	 * @return float
	 */
	public function getValorMaximo()
	{
		//return (float)number_format($this->valor_maximo,2,".",",");
		return $this->valor_maximo;
	}
	
	/**
	 * Set Moeda
	 *
	 * Atribui uma moeda a taxa
	 *
	 * @name setMoeda
	 * @access public
	 * @param Moeda
	 * @return boolean
	 */
	public function setMoeda( $moeda )
	{
	
		if( ! $moeda instanceof Moeda  )
		{
			log_message('error','Instancia invalida da classe Moeda!');
			throw new Exception("Instancia invalida da classe Moeda!");
		}
	
		$this->moeda = $moeda;
	
		return TRUE;
	
	}
	
	/**
	 * Get Moeda
	 *
	 * Obtem Unidade da moeda da taxa
	 *
	 * @name getMoeda
	 * @access public
	 * @param
	 * @return Moeda
	 */
	public function getMoeda()
	{
		return $this->moeda;
	}
	
	/**
	 * Set Unidade
	 *
	 * Atribui uma unidade a taxa
	 *
	 * @name setUnidade
	 * @access public
	 * @param Unidade
	 * @return boolean
	 */
	public function setUnidade( $unidade )
	{
		if( ! $unidade instanceof Unidade  )
		{
			log_message('error','Instancia invalida da classe Unidade!');
			throw new Exception("Instancia invalida da classe Unidade!");
		}
		
		$this->unidade = $unidade;
		
		return TRUE;
	}
	
	/**
	 * Get Unidade
	 *
	 * Obtem a Unidade da taxa
	 *
	 * @name getUnidade
	 * @access public
	 * @param
	 * @return Unidade
	 */
	public function getUnidade()
	{
		return $this->unidade;
	}
	
	/**
	 * Set Nome
	 *
	 * Atribui um nome a taxa
	 *
	 * @name setNome
	 * @access public
	 * @param strig
	 * @return boolean
	 */
	public function setNome( $nome = NULL )
	{
		
		if( empty($nome) )
		{
			return FALSE;
		}	
		
		$this->nome = $nome;
		
		return TRUE;
		
	}
	
	/**
	 * Get Nome
	 *
	 * Obtem o nome da taxa
	 *
	 * @name getNome
	 * @access public
	 * @param
	 * @return string
	 */
	public function getNome()
	{
		return $this->nome;
	}

	public function setBloqueada( $bloqueada )
	{
		$this->bloqueada = $bloqueada;
		return $this;
	}

	public function getBloqueada()
	{
		return $this->bloqueada;
	}

	public function setPPCC($ppcc)
	{
		// Se estiver vazio, então acompnha a modalidade do frete(AF)
		if( empty($ppcc) )
		{
			$this->ppcc = "AF";
		}
		else
		{	
			$this->ppcc = $ppcc;
		}	
	}

	public function getPPCC()
	{
		return $this->ppcc;
	}
	
	public function addDecorator($nome,$decorador)
	{
		$this->decorador[$nome] = $decorador;
	}
	
	public function getDecorator($nome)
	{
		return $this->decorador[$nome];
	}
}//END CLASS