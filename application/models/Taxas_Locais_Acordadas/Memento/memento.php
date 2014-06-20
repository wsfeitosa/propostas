<?php
/**
 * Description of memento
 *
 * @author wsfall
 */
class Memento {
    
    private $state = NULL;
	private $numero_acordo = NULL;
    
    public function __construct($state = NULL, $numero = NULL)
	{		
		if( ! is_null($state) )
		{
			$this->state = $state;
		}			
		
		if( ! is_null($numero) )
		{
			$this->numero_acordo = $numero;
		}		
	}
    
    public function getState() 
    {
        return $this->state;
    }

    public function getNumeroAcordo() 
    {
        return $this->numero_acordo;
    }

    public function setState($state) 
    {
        $this->state = $state;
        return $this;
    }

    public function setNumeroAcordo($numero_acordo)
    {
        $this->numero_acordo = $numero_acordo;
        return $this;
    }


    
}
