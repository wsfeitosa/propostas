<?php
include_once APPPATH . "/models/Taxas/aplica_regras_taxas.php";

class Remove_Taxas_Exportacao implements Aplica_Regras_Taxas{
	
	protected $regras = Array(
								array('id_taxa' => 1038, 'ppcc' => 'PP'), //Só puxa a taxa se for CC, se for PP remove a taxa
	);
	
	public function __construct()
	{
		
	}	
	
	public function removerTaxa(Tarifario $tarifario)
	{
		
		if( $tarifario->getSentido() == "EXP" )
		{
			$regras = $this->regras;
			
			foreach( $regras as $regra )
			{			
				foreach( $tarifario->getTaxa() as $index => $taxa )
				{
					if( $taxa->getId() == $regra['id_taxa'] && $tarifario->solicitacao_ppcc == $regra['ppcc'] )
					{
						$tarifario->removerTaxa($index);
					}		
				}			
			}
		}		
		
	}
	
}

