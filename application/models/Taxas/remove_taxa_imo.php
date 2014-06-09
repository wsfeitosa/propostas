<?php
include_once APPPATH . "/models/Taxas/aplica_regras_taxas.php";

class Remove_Taxa_Imo implements Aplica_Regras_Taxas{
	
	const IMO_ID = 14;

	protected $tarifario = NULL;

	public function __construct()
	{
		
	}

	public function removerTaxa( Tarifario $tarifario )
	{
		
		if( $tarifario->solicitacao_imo == "N" )
		{		
			$taxas_tarifario = $tarifario->getTaxa();
	
			foreach( $taxas_tarifario as $index=>$taxa )
			{
				if( $taxa->getId() == self::IMO_ID  )
				{
					$tarifario->removerTaxa($index);
				}	
			}	
		}
		
	}

}