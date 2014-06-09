<?php
/**
 * Acordo_Taxas_Bean
 *
 * Esta classe representa a tipo de dados acordo de taxas no sistema 
 *
 * @package models/Taxas_Locais_Acordadas
 * @author Wellington Feitosa <wellington.feitosa@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 16/05/2013
 * @version  versao 1.0
 */
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/Entity.php";

class Acordo_Taxas_Entity implements Entity {
	
	protected $id = NULL;
	protected $numero;
	protected $sentido;
	protected $clientes = Array();
	protected $portos = Array();
	protected $taxas = Array();
	protected $inicio;
	protected $validade;
	protected $observacao;
	protected $usuario_inclusao;
	protected $data_inclusao;
	protected $usuario_alteracao;
	protected $data_alteracao;
	protected $registro_ativo;
	
	public function __construct() 
	{
		
	}
	
	public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function setNumero( $numero )
    {
    	$this->numero = $numero;
    	return $this;
    }
    
    public function getNumero()
    {
    	return $this->numero;
    }
    
    public function getSentido()
    {
        return $this->sentido;
    }

    public function setSentido($sentido)
    {
        $this->sentido = $sentido;
        return $this;
    }

    public function getClientes()
    {
        return $this->clientes;
    }

    public function setClientes(Cliente $cliente)
    {
        $this->clientes[] = $cliente;
        return $this;
    }

    public function getPortos()
    {
        return $this->portos;
    }

    public function setPortos(Porto $portos)
    {
        $this->portos[] = $portos;
        return $this;
    }

    public function getTaxas()
    {
        return $this->taxas;
    }

    public function setTaxas(Taxa $taxas)
    {
        $this->taxas[] = $taxas;
        return $this;
    }

    public function getInicio()
    {
        return $this->inicio;
    }

    public function setInicio(DateTime $inicio)
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getValidade()
    {
        return $this->validade;
    }

    public function setValidade(DateTime $validade)
    {
        $this->validade = $validade;
        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }
    
    public function getUsuarioInclusao()
    {
        return $this->usuario_inclusao;
    }

    public function setUsuarioInclusao(Usuario $usuario_inclusao)
    {
        $this->usuario_inclusao = $usuario_inclusao;
        return $this;
    }

    public function getDataInclusao()
    {
        return $this->data_inclusao;
    }

    public function setDataInclusao(DateTime $data_inclusao)
    {
        $this->data_inclusao = $data_inclusao;
        return $this;
    }

    public function getUsuarioAlteracao()
    {
        return $this->usuario_alteracao;
    }

    public function setUsuarioAlteracao(Usuario $usuario_alteracao)
    {
        $this->usuario_alteracao = $usuario_alteracao;
        return $this;
    }

    public function getDataAlteracao()
    {
        return $this->data_alteracao;
    }

    public function setDataAlteracao(DateTime $data_alteracao)
    {
        $this->data_alteracao = $data_alteracao;
        return $this;
    }

    public function getRegistroAtivo()
    {
        return $this->registro_ativo;
    }

    public function setRegistroAtivo($registro_ativo)
    {
        $this->registro_ativo = $registro_ativo;
        return $this;
    }


	
}//END CLASS