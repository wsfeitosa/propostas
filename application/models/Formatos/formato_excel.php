<?php
include_once "formato.php";

class Formato_Excel implements Formato{
	
	const path = "/var/www/html/allink/relatorios_temp";
		
	public function aplicarFormato( Layout $layout, Relatorio $relatorio ){
		
		$layout_formatado = $layout->aplicarLayout($relatorio);
		
		$handler = $this->criarArquivo($relatorio->obterNome());
				
		if ( ! fwrite($handler,$layout_formatado) )
		{
			log_message('error', "Erro ao tentar escrever no arquivo do relat�rio!");
			throw new RuntimeException("Erro ao tentar escrever no arquivo do relat�rio!");
		}
		
		fclose($handler);
		
		return "/relatorios_temp/" . $relatorio->obterNome() . ".xls";
		
	}
	
	protected function criarArquivo( $nome_arquivo = NULL )
	{
		if( empty($nome_arquivo) )
		{
			log_message('error', "Nome do arquivo inv�lido!");
			throw new RuntimeException("Nome do arquivo inv�lido!");	
		}	
		
		$handler = fopen(self::path."/".$nome_arquivo.".xls", "w");
		
		if( ! $handler )
		{
			log_message('error', "Erro ao tentar criar o arquivo do relat�rio!");
			throw new RuntimeException("Erro ao tentar criar o arquivo do relat�rio!");
		}	

		return $handler;
		
	}

}//END CLASS