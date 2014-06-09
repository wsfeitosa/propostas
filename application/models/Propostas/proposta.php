<?php
/**
 * Classe que manipula e compõe a entidade proposta do módulo de propostas
 * @author wsfall
 * @package Propostas
 * @abstract
 */
abstract class Proposta {
	
    protected $id = NULL;
    protected $numero = NULL;
    protected $tipo_proposta = NULL;
    protected $itens = Array();
	protected $clientes = Array();
	protected $emails = Array();
	protected $sentido = "";
	protected $enviada = FALSE;
	protected $nome_nac = NULL;  
	protected $memento = NULL;
	
	public function __contruct(){

	}
	
    /**
     * getId
     * 
     * Obtem o id da proposta
     * 
     * @name getId
     * @access public     
     * @return integer
     */
    public function getId() {
        return (int) $this->id;
    }
    
    /**
     * setId
     * 
     * Atribui um id a proposta
     * 
     * @name setId
     * @access public
     * @param int
     * @return integer
     */
    public function setId($id) {
        $this->id = (int) $id;
        return $this;
    }
        
	/**
	 * Função que adiciona um novo item de proposta da Classe ItemProposta na 
	 * variável de classe da classe Proposta
	 * @name adicionarNovoItem
	 * @access public
	 * @param item ItemProposta
	 * @return int
	 */
	public function adicionarNovoItem( Item_Proposta $item )
	{
		
		if( ! $item instanceof Item_Proposta )
		{
			log_message('error','Tipo de item inválido passado a classe Proposta');
			throw new Exception("Tipo de item inválido passado a classe Proposta");
		}	
		
		$this->itens[] = $item;
		
		end($this->itens);
		
		return key($this->itens);
		
	}
	
    /**
     * getItens
     * 
     * Obtem os itens atribuidos a proposta
     * 
     * @name getItens
     * @access public     
     * @return array
  */
    public function getItens() {
        return $this->itens;
    }
            
	/**
	 * Função que edita um item de proposta da Classe ItemProposta 
	 * @name editarItem
	 * @access public
	 * @param $index int
	 * @return ItemProposta
	 */
	public function editarItem( $index )
	{
		return $this->itens[$index];
	}
	
	/**
	 * Função que remove um item de proposta da Classe ItemProposta
	 * @name removerItem
	 * @access public
	 * @param $index int
	 * @return boolean
	 */
	public function removerItem( $index )
	{
		
		unset($this->itens[$index]);
		
		return TRUE;
		
	}
	
	/**
	 * Retorna a quantidade de itens que estão na proposta
	 * @name obterQuantidadeItens
	 * @access public
	 * @param
	 * @return int
	 */
	public function obterQuantidadeItens()
	{
		return count($this->itens);
	}
	
	/**
	 * Função que adiciona um novo objeto do tipo Cliente na Classe proposta 
	 * @name adicionarNovoCliente
	 * @access public
	 * @param $cliente Cliente
	 * @return int
	 */
	public function adicionarNovoCliente( Cliente $cliente )
	{
		
		if( ! $cliente instanceof Cliente )
		{
			log_message('error','Um objeto de tipo desconhecido foi passado a classe Proposta');
			throw new Exception("Cliente Inválido");
		}	
		
		$this->clientes[] = $cliente;
		
		end($this->clientes);
		
		return key($this->clientes);
		
	}
	
    /**
     * getCliente
     * 
     * Obtém os clientes adicionados a proposta
     * 
     * @name getCliente
     * @access public    
     * @return array
     */
    public function getClientes()
    {
        return $this->clientes;
    }
   
	/**
	 * Função que remove um objeto do tipo Cliente na Classe proposta
	 * @name removerCliente
	 * @access public
	 * @param $index int
	 * @return boolean
	 */
	public function removerCliente( $index )
	{
		
		unset($this->clientes[$index]);
		
		return TRUE;
		
	}
	
	/**
	 * Retorna a quantidade de cliente que estão na proposta
	 * @name obterQuantidadeClientes
	 * @access public
	 * @param 
	 * @return int
	 */
	public function obterQuantidadeClientes()
	{
		return count($this->clientes);
	}
	
	
	/**
	 * Adiciona um email
	 * @name adicionarNovoEmail
	 * @access public
	 * @param $email Email
	 * @return int
	 */
	public function adicionarNovoEmail( Email $email )
	{

		if( ! $email instanceof Email )
		{
			log_message('error','Objeto email invalido passado a classe proposta');
			throw new Exception("Email inválido!");
		}
		
		$this->emails[] = $email;
		
		end($this->emails);
		
		return key($this->emails);
		
	}
	
	/**
	 * Remove um email
	 * @name removerEmail
	 * @access public
	 * @param $index int
	 * @return boolean
	 */
	public function removerEmail( $index )
	{
		
		unset($this->emails[$index]);
		
		return TRUE;
		
	}
	
    /**
     * obterEmails
     * 
     * obtem os emails atribuidos a proposta, se o indice do item for informado
     * retorna somente o item, se nada for informado então retorna todos os emails
     * relacionados a proposta
     * 
     * @name obterEmails
     * @access public
     * @param int
     * @return mixed $email
     */
    public function obterEmails( $index = NULL )
    {        
    
        if(is_null($index) )
        {
            return $this->emails;
        }
        
        return $this->emails[$index];
        
    }
    
	/**
	 * Retorna a quantidade de emails que estão na proposta
	 * @name obterQuantidadeEmails
	 * @access public
	 * @param
	 * @return int
	 */
	public function obterQuantidadeEmails()
	{
		return count($this->emails);
	}
	
	/**
	 * Atribui um valor para o sentido da proposta - IMP ou EXP
	 * @name setSentido
	 * @access public
	 * @param $sentido String
	 * @return void
	 */
	public function setSentido( $sentido )
	{
		
		if( $sentido != "IMP" && $sentido != "EXP" )
		{
			log_message('error','Parametro sentido dirente de IMP ou EXP');
			throw new Exception("Sentido Invalido!");
		}	
		
		$this->sentido = $sentido;
		
		return TRUE;
		
	}
	
	/**
	 * Obtém o valor para o sentido da proposta - IMP ou EXP
	 * @name getSentido
	 * @access public	 
	 * @return String
	 */
	public function getSentido()
	{
		return $this->sentido;		
	}    
	
    /**
	 * Atribui um para a proposta
	 * @name setNumero
	 * @access public
	 * @param $numero String
	 * @return void
	 */
    public function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }
    
    /**
	 * Obtém o número da proposta - IMP ou EXP
	 * @name getNumero
	 * @access public	 
	 * @return String
	 */
    public function getNumero() {
        return $this->numero;
    }       
    
    /**
     * setTipoProposta
     * 
     * atribui um tipo a proposta
     * 
     * @name setTipoProposta
     * @access public
     * @param int
     * @return Proposta
     */
    public function setTipoProposta($tipo_proposta) {
        $this->tipo_proposta = $tipo_proposta;
        return $this;
    }

    /**
     * getTipoProposta
     * 
     * Obtem o tipo de uma proposta
     * 
     * @name getTipoProposta
     * @access public
     * @param int
     * @return string
     */
    public function getTipoProposta() {
        return $this->tipo_proposta;
    }
    
    public function setNomeNac( $nome_nac )
    {
    	$this->nome_nac = strtoupper($nome_nac);
    	return $this;
    }
    
    public function getNomeNac()
    {
    	return strtoupper($this->nome_nac);
    }
    
    /**     
     * @param Memento $memento
     * Restaura a proposta a um estado anterior
     */
    
    public function SetMemento(Memento $memento)
    {    	
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/Memento/memento.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/item_proposta.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/status_item.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Tarifario/tarifario.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Tarifario/tarifario_importacao.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Tarifario/tarifario_exportacao.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Clientes/cliente.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Clientes/agente.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Clientes/cidade.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Email/email.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Tarifario/porto.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Tarifario/rota.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Taxas/taxa_adicional.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Taxas/taxa_local.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Taxas/moeda.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Taxas/unidade.php";
    	    	    	
    	$proposta = unserialize($memento->GetState());
    	
    	$proposta->setTipoProposta(strtolower(get_class($proposta)));
    	
    	return $proposta; 
    }
    
    public function CreateMemento()
    {
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Propostas/Memento/memento.php";
    	include_once "/var/www/html/allink/Clientes/propostas/application/models/Clientes/cliente_model.php";
    	
    	$cliente_model = new Cliente_Model();
    	
    	foreach( $this->getClientes() as $cliente )
    	{
    		$cliente_model->findById($cliente);
    	}	

    	$memento = new Memento(serialize($this),$this->getNumero());
    	
    	return $memento;
    }
            
}//END CLASS