<?php
class Nota {
	
	protected $id_nota = NULL, $nota = NULL, $valor_minimo = NULL, $valor_maximo = NULL;

	public function setId( $id )
	{
		$this->id_nota = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id_nota;
	}
	
	public function setNota( $nota )
	{
		$this->nota = $nota;
		return $this;	
	}
	
	public function getNota()
	{
		return $this->nota;
	}
	
	public function setValorMinimo( $valor_minimo )
	{
		$this->valor_minimo = $valor_minimo;
		return $this;
	}
	
	public function getValorMimimo()
	{
		return $this->valor_minimo;
	}
	
	public function setValorMaximo( $valor_maximo )
	{
		$this->valor_maximo = $valor_maximo;
		return $this;
	}
	
	public function getValorMaximo()
	{
		return $this->valor_maximo;
	}
	
}
