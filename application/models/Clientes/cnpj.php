<?php
class CNPJ {
	
	private $cnpj = NULL;
	private $path = NULL;
	
	public function __construct($cnpj = NULL)
	{
		$this->cnpj = $cnpj;
		$this->path = "/var/www/html/allink/Libs";
	}
	
	public function setCNPJ( $cnpj = NULL )
	{
		
		if( empty($cnpj) )
		{
			return FALSE;
		}	
		
		$this->cnpj = $cnpj;
		
		return TRUE;
		
	}
	
	public function getCNPJ()
	{
		return (string)$this->cnpj;
	}
	
	public function removerLetrasAcentos()
	{
		
		/** revome todos os caracteres que não são números **/
		$this->cnpj = preg_replace('/[^0-9]/', '', $this->cnpj);
		
		if( strlen($this->cnpj) != 11 && strlen($this->cnpj) != 14 )
		{
			return FALSE;
		}
		
		return TRUE;
		
	}
	
	public function validarCNPJ()
	{
		
		if( empty($this->cnpj) )
		{
			throw New Exception("O cnpj Não foi Preenchido");
		}	
		
		switch(strlen($this->cnpj))
		{
			case "11":
				
				include_once $this->path."/verifica_cpf.php";
				
				return CalculaCPF($this->cnpj);
				
			break;

			case "14":
				
				include_once $this->path."/verifica_cnpj.php";

				return CalculaCNPJ($this->cnpj);
				
			break;
			
			case "6":
				return TRUE;
			break;

			default:
				throw new Exception("Numero de caracteres do cnpj invalido! -> ".$this->cnpj." quantidade de caracteres : ".strlen($this->cnpj));
		
		}	
		
	}
	
}//END CLASS