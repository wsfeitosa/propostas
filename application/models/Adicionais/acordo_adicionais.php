<?php
class Acordo_Adicionais extends CI_Model {
	
	protected $id = 0;
	
	protected $numero_acordo = null;
	
	protected $sentido = null;
	
	protected $clientes = Array();
	
	protected $usuario_cadastro = null;
	
	protected $usuario_alteracao = null;
	
	protected $data_cadastro = null;
	
	protected $data_alteracao = null;
	
	protected $inicio = null;
	
	protected $validade = null;
	
	protected $observacao = null;
	
	protected $ativo = null;
	
	protected $taxas = Array();
	
	protected $aprovacao_pendente = null;
	
	public function __construct()
	{
		
	}
	
	public function getId()
	{
		return $this->id;
	}
		
	public function setId($id)
	{
		$this->id = $id ;
		return $this;
	}
		
	public function getNumeroAcordo()
	{
		return $this->numero_acordo;
	}
		
	public function setNumeroAcordo($numero_acordo)
	{
		$this->numero_acordo = $numero_acordo;
		return $this;
	}

	public function setSentido($sentido)
	{
		$permitidos = array("IMP","EXP");
		
		if( ! in_array(strtoupper($sentido), $permitidos) )
		{
			log_message('error',"Sentido do embarque informado desconhecido, precisa ser IMP ou EXP");
			throw new InvalidArgumentException("Sentido do embarque informado desconhecido, precisa ser IMP ou EXP");
		}	
		
		$this->sentido = strtoupper($sentido);
		return $this;		
	}
	
	public function getSentido()
	{
		return $this->sentido;
	}
	
	public function getClientes()
	{
		return $this->clientes;
	}
		
	public function setCliente(Cliente $cliente)
	{
		$this->clientes[] = $cliente;
		return $this;
	}

	public function retirarCliente($index)
	{
		unset($this->clientes[$index]);
		return $this;
	}
	
	public function limparClientes()
	{
		$this->clientes = array();
		return $this;
	}
	
	public function getUsuarioCadastro()
	{
		return $this->usuario_cadastro;
	}
		
	public function setUsuarioCadastro(Usuario $usuario_cadastro)
	{
		$this->usuario_cadastro = $usuario_cadastro;
		return $this;
	}
		
	public function getUsuarioAlteracao()
	{
		return $this->usuario_alteracao;
	}
		
	public function setUsuarioAlteracao(Usuario $usuario_alteracao)
	{
		$this->usuario_alteracao = $usuario_alteracao;
		return $this;
	}
	
	public function getDataCadastro()
	{
		return $this->data_cadastro;
	}
		
	public function setDataCadastro(DateTime $data_cadastro)
	{
		$this->data_cadastro = $data_cadastro;
		return $this;
	}
		
	public function getDataAlteracao()
	{
		return $this->data_alteracao;
	}
		
	public function setDataAlteracao(DateTime $data_alteracao)
	{
		$this->data_alteracao = $data_alteracao;
		return $this;
	}
		
	public function getInicio()
	{
		return $this->inicio;
	}
		
	public function setInicio(DateTime $inicio)
	{
		$this->inicio = $inicio ;
		return $this;
	}
		
	public function getValidade()
	{
		return $this->validade;
	}
		
	public function setValidade(DateTime $validade)
	{
		$this->validade = $validade ;
		return $this;
	}
		
	public function getObservacao()
	{
		return $this->observacao;
	}
		
	public function setObservacao($observacao)
	{
		$this->observacao = $observacao ;
		return $this;
	}
		
	public function getAtivo()
	{
		return $this->ativo;
	}
		
	public function setAtivo($ativo)
	{
		$this->ativo = $ativo ;
		return $this;
	}
	
	public function getTaxas()
	{
		return $this->taxas;
	}
		
	public function setTaxas(Taxa $taxa)
	{
		$this->taxas[] = $taxa;
		return $this;
	}
	
	public function retirarTaxa($index)
	{
		unset($this->taxas[$index]);
		return $this;
	}
	
	public function limparTaxas()
	{
		$this->taxas = array();
		return $this;
	}
	
	public function contarTaxas()
	{
		return (int) count($this->taxas);
	}
	
	public function setAprovacaoPendente($pendente)
	{
		$this->aprovacao_pendente = $pendente;
		return $this;
	}
	
	public function getAprovacaoPendente()
	{
		return $this->aprovacao_pendente;
	}
	
	public function serializar()
	{			
		return serialize($this);		 
	}
	
	public function deserializar($acordoSerializado)
	{
		if( empty($acordoSerializado) )
		{
			throw new InvalidArgumentException("Nenhum acordo informado para deserializar!");
		}
	
		$this->load->model("Clientes/cliente");
		$this->load->model("Clientes/cidade");
		$this->load->model("Taxas/taxa_adicional");
		$this->load->model("Taxas/unidade");
		$this->load->model("Taxas/moeda");
		$this->load->model("Usuarios/usuario");
		$this->load->model("Adicionais/acordo_adicionais");
	
		return unserialize($acordoSerializado);			
	}
	
	public function removerClientesDuplicados()
	{		
		$clientes_unicos = array();
		
		foreach($this->clientes as $index => $cliente)
		{
			if( ! in_array($cliente->getId(), $clientes_unicos) )
			{
				$clientes_unicos[] = $cliente->getId();
			}
			else 
			{
				$this->retirarCliente($index);
			}			
		}
	}
		
}