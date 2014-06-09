<?php
/**
 * Exportar_Tarifario
 *
 * Exporta os dados da proposta tarifario 
 *
 * @package models/Relatorios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 28/06/2013
 * @version  versao 1.0
*/
include "relatorio.php";

class Exportar_Tarifario extends CI_Model implements Relatorio {
	
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
	public function adicionarNovoParametro( $parametro )
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
		$this->nome_relatorio = "proposta_tarifario_" . date("YmdHis");
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

		end($this->parametros);
		
		$index = key($this->parametros);
		
		$this->dados_resultado = $this->parametros[$index];
	
		$this->gerarNome();
	
		return TRUE;
	
	}
	
}//END CLASS