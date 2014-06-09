<?php
include "relatorio.php";

class Relatorio_Desbloqueios extends CI_Model implements Relatorio{
	
	protected $parametros = Array();
	protected $nome_relatorio = NULL;
	protected $dados_resultado = NULL;
		
	const path = "/var/www/html/allink/relatorios_temp";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	  * Adicionar Novo Parametro
	  * 
	  * Adiciona um novo parametro ao relatorio
	  * 
	  * @name adicionarNovoParametro
	  * @access public
	  * @param string
	  * @return boolean
	  */
	public function adicionarNovoParametro( Array $parametro )
	{
		
		if( empty($parametro) )
		{
			return FALSE;
		}	
		
		$this->parametros[] = $parametro;
		
		end($this->parametros);
		
		return key($this->parametros);
		
	}
	
	/**
	  * Obter Parametros
	  * 
	  * Obtem os parametros passados ao relatorio
	  * 
	  * @name obterParametros
	  * @access public
	  * @param 
	  * @return Array
	  */
	public function obterParametros()
	{
		return $this->parametros;
	}
	
	/**
	  * Obter Nome
	  * 
	  * Obtem o nome do relatorio
	  * 
	  * @name obterNome
	  * @access public
	  * @param 
	  * @return string
	  */
	public function obterNome()
	{
		return $this->nome_relatorio;
	}
	
	/**
	  * Gerar Nome
	  * 
	  * gerar o nome do relatorio para salva-lo
	  * 
	  * @name gerarNome
	  * @access public
	  * @param 
	  * @return void
	  */
	protected function gerarNome()
	{
		$this->nome_relatorio = "relatorio_desbloqueios_" . date("YmdHis");
	}
	
	/**
	  * Obter Dados Relatorio
	  * @name obterDadosRelatorio
	  * @access public
	  * @param 
	  * @return resource
	  */
	public function obterDadosRelatorio()
	{
		return $this->dados_resultado;
	}
	
	/**
	  * Gerar
	  * 
	  * gera o relatorio em um diretorio do sistema
	  * 
	  * @name gerar
	  * @access public
	  * @param 
	  * @return boolean
	  */
	public function gerar()
	{
				
		$this->db->select("usuarios.*")->from("USUARIOS.usuarios");		
		
		foreach( $this->parametros as $parametro )
		{	
			$this->db->like(key($parametro),$parametro[key($parametro)]);
		}

		$rs = $this->db->get();
		
		$this->dados_resultado = $rs->result();

		$this->gerarNome();
		
		return TRUE;
		
	}
	
}//END CLASS