<?php
include_once "formato.php";

class Formato_Excel implements Formato{
	
	const path = "/var/www/html/allink/relatorios_temp";
		
	public function aplicarFormato( Layout $layout, Relatorio $relatorio ){
		
		$layout_formatado = $layout->aplicarLayout($relatorio);
		
		$handler = $this->criarArquivo($relatorio->obterNome());
				
		if ( ! fwrite($handler,$layout_formatado) )
		{
			log_message('error', "Erro ao tentar escrever no arquivo do relatório!");
			throw new RuntimeException("Erro ao tentar escrever no arquivo do relatório!");
		}
		
		fclose($handler);
		
		return "/relatorios_temp/" . $relatorio->obterNome() . ".xls";
		
	}
	
	protected function criarArquivo( $nome_arquivo = NULL )
	{
		if( empty($nome_arquivo) )
		{
			log_message('error', "Nome do arquivo inválido!");
			throw new RuntimeException("Nome do arquivo inválido!");	
		}	
		
		$handler = fopen(self::path."/".$nome_arquivo.".xls", "w");
		
		if( ! $handler )
		{
			log_message('error', "Erro ao tentar criar o arquivo do relatório!");
			throw new RuntimeException("Erro ao tentar criar o arquivo do relatório!");
		}	

		return $handler;
		
	}

}//END CLASS