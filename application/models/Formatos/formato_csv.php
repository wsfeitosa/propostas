<?php
include_once "formato.php";

class Formato_CSV implements Formato{
	
	const path = "/var/www/html/allink/relatorios_temp";
	
	public function aplicarFormato(Layout $layout, Relatorio $relatorio){
		
		$relatorio->gerar();
		
		$handle = fopen(self::path."/".$relatorio->obterNome().".csv", "w");
		
		if( ! $handle )
		{
			log_message('error', "Erro ao tentar criar o arquivo do relatório!");
			throw new RuntimeException("Erro ao tentar criar o arquivo do relatório!");
		}
						
		$csv = "";
		
		/** Gera o csv com o resultado dos registros para abrir no excel **/
		
		$invalidos = Array("\\n","\\t","\015","\12",";");
		
		$dadosRelatorio = $relatorio->obterDadosRelatorio();

		/** Gera as colunas de cabeçalho **/
		$class_vars = get_object_vars(($dadosRelatorio[0]));
		
		$colunas = array_keys($class_vars);
		
		$nome_colunas_csv = implode(";",$colunas);
		
		if( ! fwrite($handle, utf8_encode($nome_colunas_csv."\015") ) )
		{
			log_message('error', "Erro ao tentar escrever no arquivo do relatório!");
			throw new RuntimeException("Erro ao tentar escrever no arquivo do relatório!");
		}
		
		foreach( $dadosRelatorio as $row )
		{
				
			$csv = implode(";", str_replace($invalidos, " ", get_object_vars($row)));
				
			if( ! fwrite($handle, utf8_encode($csv."\015") ) )
			{
				log_message('error', "Erro ao tentar escrever no arquivo do relatório!");
				throw new RuntimeException("Erro ao tentar escrever no arquivo do relatório!");
			}
		}	
		
		return "/relatorios_temp/" . $relatorio->obterNome() . ".csv"; 
		
	}
	
}//END CLASS