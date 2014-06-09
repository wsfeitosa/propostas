<?php
class Agrupa_Valores extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function converterParaValores( Array $valores )
	{
		$itens = Array();
		
		if( count($valores) > 0 )
		{
			foreach($valores as $nomeItem => $item)
			{
				if( strstr($nomeItem,"-") !== false )
				{
					$taxa = explode("-",$nomeItem);
					$itens[$taxa[3]][$taxa[2]] = $item;
				}	
			}	
		}	
		
		return $itens;				
	}
	
}
