<?php
/**
 * BreakDown
 *
 * Formata o breakdown do tarifario 
 *
 * @package Tarifario/breakdown
 * @author Wellington Feitosa <wellington.feitosa@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 19/07/2013
 * @version  versao 1.0
*/
class Breakdown extends CI_Model {
	
	protected $tarifario = NULL;
	protected $breakdown = NULL;
	protected $frequencias = Array(
									 1 => "SEMANAL", 2 => "QUINZENAL", 3 => "DEZENAL", 4 => "MENSAL",
									 5 => "2 / SEMANA", 6 => "3 / SEMANA", 7 => "4 / SEMANA", 8 => "5 / SEMANA",			
				);
	
	public function __construct(Tarifario $tarifario = NULL) 
	{
		parent::__construct();		
		$this->tarifario = $tarifario;
	}
	
	public function setTarifario(Tarifario $tarifario)
	{
		$this->tarifario = $tarifario;
		return $this;
	}
	
	public function getTarifario()
	{
		return $this->tarifario;
	}
	
	public function setBreakDown( $breakdown )
	{
		$this->breakdown = $breakdown;
		return $this;
	}
	
	public function getBreakDown()
	{
		return $this->breakdown;
	}
	
	public function formatarBreakDown()
	{
		
		if( is_null($this->tarifario) )
		{
			throw new InvalidArgumentException("O id do tarifario não foi definido para realizar a busca pelo breakdown");
		}	
		
		$this->db->
				select("transit_receipt_x_loading, id_frequencia_receipt_x_loading,
						transit_loading_x_via, id_frequencia_loading_x_via, 
						transit_via_x_place_delivery, id_frequencia_via_x_place_delivery, 
						transit_via_x_via_adicional, id_frequencia_via_x_via_adicional, 
						transit_via_adicional_x_place_delivery, id_frequencia_via_adicional_x_place_delivery")->
				from("FINANCEIRO.tarifarios_pricing")->
				where("tarifarios_pricing.id_tarifario_pricing",$this->tarifario->getId());
		
		$rs = $this->db->get();
		
		if( $rs->num_rows() < 1 )
		{
			throw new RuntimeException("Impossivel recuperar o breakdown do tarifário pois o tarifarios não foi encontrado!");
		}	
		
		$row = $rs->row();
		
		/** Origem -> Embarque **/
		$transit_origem_x_loading = "";
		
		if( $row->transit_receipt_x_loading > 0 )
		{	
			$transit_origem_x_loading = $this->tarifario->getRota()->getPortoOrigem()->getNome() . " / " .
										$this->tarifario->getRota()->getPortoEmbarque()->getNome() . ": ". 
										$row->transit_receipt_x_loading . " DIAS APROX. | FREQUENCIA: " . 
										$this->frequencias[$row->id_frequencia_receipt_x_loading];
		
		}
		
		/** Embarque -> Desembarque **/
		$transit_loading_x_discharge = "";
		
		if( $row->transit_loading_x_via > 0 )
		{
			$transit_loading_x_discharge = $this->tarifario->getRota()->getPortoEmbarque()->getNome() . " / " .
										   $this->tarifario->getRota()->getPortoDesembarque()->getNome() . ": " .
										   $row->transit_loading_x_via . " DIAS APROX. | FREQUENCIA: " .
										   $this->frequencias[$row->id_frequencia_loading_x_via] . " | ON BOARD";
		}	
		
		/** via ->via adicional **/
		$transit_via_x_via_adicional = "";
				
		/** Desembarque -> Destino **/
		$transit_discharge_x_destination = "";
		
		if( $row->transit_via_x_place_delivery > 0 )
		{
			$transit_dicharge_x_destination = $this->tarifario->getRota()->getPortoDesembarque()->getNome() . " / " .
										      $this->tarifario->getRota()->getPortoFinal()->getNome() . ": " .
										      $row->transit_via_x_place_delivery . " DIAS APROX. | FREQUENCIA: " .
										      $this->frequencias[$row->id_frequencia_via_x_place_delivery];
		}	
		
		$this->breakdown = $transit_origem_x_loading . "\r\n" . $transit_loading_x_discharge . "\r\n" . $transit_dicharge_x_destination;
						
		return $this->breakdown;
		
	}
	
}//END CLASS