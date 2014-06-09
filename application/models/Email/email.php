<?php
/**
 * Classe que manipula e comp�e a entidade email do m�dulo de propostas
 * @author wsfall
 * @package Email
 * @name Email
 */
class Email {
	
    private $id = NULL;
	private $email = NULL;
    private $tipo = NULL;
    
	
	public function __construct( $email = NULL )
	{
		
		if( ! is_null($email ) )
		{
			$this->email = $email;
		}
				
	}
	
    /**
     * getId
     * 
     * Obtem o id do email
     * 
     * @name getId
     * @access public     
     * @return int
     */
    public function getId() 
    {
        return (int) $this->id;
    }
    
    /**
     * setId
     * 
     * Atribui um id para o email
     * 
     * @name setId
     * @access public
     * @param int
     * @return Email
     */
    public function setId($id) 
    {
        $this->id = (int) $id;
        return $this;
    }

        
	/**
	 * Atribui um valor para o email
	 * @name setEmail
	 * @access public
	 * @param $email String
	 * @return void
	 */
	public function setEmail( $email )
	{		
		$this->email = $email;				
	}
	
	/**
	 * Obtem o valor de email
	 * @name getEmail
	 * @access public
	 * @param $email String
	 * @return String
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
    /**
     * getTipo
     * 
     * Obt�m o tipo de email (Para ou CC)
     * 
     * @name getTipo
     * @access public     
     * @return string
     */
    public function getTipo() 
    {
        return $this->tipo;
    }
    
    /**
     * setTipo
     * 
     * Atribu� um tipo ao email (Para ou CC)
     * 
     * @name setTipo
     * @access public
     * @param string $tipo
     * @return Email     
     */
    public function setTipo($tipo) 
    {
        
        if( $tipo != "P" && $tipo != "C"  )
        {
            log_message('error',"Tipo inv�lido de email informado: ".$tipo);
            show_error("Tipo inv�lido de email informado: ".$tipo);
        }    
        
        $this->tipo = $tipo;
        return $this;
    }

        
	/**
	 * Verifica se o email informado � um email v�lido
	 * @name emailValido
	 * @access public
	 * @param 
	 * @return Boolean
	 */		
	public function emailValido()
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $this->email)) ? FALSE : TRUE;
	}	
	
}