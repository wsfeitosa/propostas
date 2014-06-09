<?php
interface Relatorio{
		
	public function adicionarNovoParametro($parametro);
	public function obterParametros();
	public function gerar();
	public function obterNome();
		
}