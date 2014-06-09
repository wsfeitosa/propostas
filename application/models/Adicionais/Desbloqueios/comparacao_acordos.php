<?php
class Comparacao_Acordos extends CI_Model {
			
	public function __construct() 
	{
		parent::__construct();	
	}
	
	public function verificaSeDoisAcordosSaoIguais( Acordo_Adicionais $acordoSalvo, Acordo_Adicionais $acordoAlterado )
	{
		$houveAlteracao = false;		
		
		if( $acordoSalvo->getInicio()->format('Y-m-d') != $acordoAlterado->getInicio()->format('Y-m-d') )
		{
			$houveAlteracao = true;
		}	
		
		if( $acordoSalvo->getValidade()->format('Y-m-d') != $acordoAlterado->getValidade()->format('Y-m-d') )
		{
			$houveAlteracao = true;
		}	
		
		/** Verifica se alguma das taxas foi alterada **/
		if( $acordoSalvo->contarTaxas() != $acordoAlterado->contarTaxas() )
		{
			$houveAlteracao = true;
		}		
						
		foreach( $acordoSalvo->getTaxas() as $taxaSalva )
		{
			$taxaEncontrada = false;			
			
			//Procura a taxa do primeiro acordo no segundo
			foreach ($acordoAlterado->getTaxas() as $taxaAlterada)
			{
				if($taxaSalva->getId() == $taxaAlterada->getId())
				{
					$taxaEncontrada = true;
					
					// Compara os valores e demais dados das taxas
					if( $taxaSalva->getUnidade()->getId() != $taxaAlterada->getUnidade()->getId() )
					{
						$houveAlteracao = true;
					}	
					
					if( $taxaSalva->getMoeda()->getId() != $taxaAlterada->getMoeda()->getId() )
					{
						$houveAlteracao = true;
					}	
					
					if( $taxaSalva->getValor() != $taxaAlterada->getValor() )
					{
						$houveAlteracao = true;
					}

					if( $taxaSalva->getValorMinimo() != $taxaAlterada->getValorMinimo() )
					{
						$houveAlteracao = true;
					}
					
					if( $taxaSalva->getValorMaximo() != $taxaAlterada->getValorMaximo() )
					{
						$houveAlteracao = true;
					}
					
				}	
			}
			
			/**
			 * Se a taxa do primeiro acordo não foi encontrada no segundo,
			 * então isso quer dizer que às taxas foram alteradas.
			 */
			if( $taxaEncontrada === false )
			{
				$houveAlteracao = true;
				break;
			}	
			
		}	
		
		return $houveAlteracao;
		
	}
	
}
