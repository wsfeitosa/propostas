<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/scoa-sdk/autoload.php";
include_once "entity/intervalo_datas.php";

class Validador extends CI_Model {
	
	public function __construct() 
	{
		parent::__construct();		
		$this->output->enable_profiler(false);
	}
	
	/**
	 * valida o acordo antes de salvar 
	 * @name validarAcordo
	 * @param Acordo_Adicionais $acordo
	 * @return void
	 */
	public function validarAcordo( Acordo_Adicionais $acordo )
	{	
		$mensagensDeErro = array();
		$clientesParaRemover = array();
		$intervaloDatas = new IntervaloDatas();			
		
		$this->load->model("Clientes/cliente_model");
		$this->load->model("Taxas/taxa_model");
		
		$clienteModel = new Cliente_Model();
		
		$taxaModel = new Taxa_Model();
		
		//Primeiro verifica se existe algum outro acordo ativo no mesmo periodo de validade		
		foreach( $acordo->getClientes() as $index=>$cliente )
		{
			
			$this->db->
				select("clientes_x_acordo_adicionais.*, acordo_adicionais.*, acordo_adicionais.id as id_acordo")->
				from("CLIENTES.clientes_x_acordo_adicionais")->
				join("CLIENTES.acordo_adicionais","acordo_adicionais.id = clientes_x_acordo_adicionais.id_acordo_adicionais")->
				where("id_cliente",$cliente->getId())->
				where("acordo_adicionais.sentido",$acordo->getSentido())->
				where("acordo_adicionais.id !=",$acordo->getId())->
			    where("acordo_adicionais.ativo",'S');
		
			$rowSetAcordos = $this->db->get();
			
			if( $rowSetAcordos->num_rows() < 1 )
			{
				continue;
			}	
			
			foreach( $rowSetAcordos->result() as $acordoConflito )
			{
				$dataInicioAcordoRegistrado = new DateTime($acordoConflito->inicio);
				$dataValidadeAcordoRegistrado = new DateTime($acordoConflito->validade);
				
				$intervaloDatas->setPrimeiroIntervalo(array($acordo->getInicio(),$acordo->getValidade()));
				$intervaloDatas->setSegundoIntervalo(array($dataInicioAcordoRegistrado,$dataValidadeAcordoRegistrado));
				
				if( $intervaloDatas->verificarSeExisteConflito() === true )
				{																
					// Verifica se neste acordo existe a mesma sendo cadastrada
					foreach( $acordo->getTaxas() as $taxa )
					{
						//Verifica se a taxa já existe no acordo em conflito
						$this->db->
								select("taxas_acordo_adicionais.id_taxa")->
								from("CLIENTES.taxas_acordo_adicionais")->
								where("taxas_acordo_adicionais.id_taxa",$taxa->getId())->
								where("taxas_acordo_adicionais.id_acordo_adicional",$acordoConflito->id_acordo);
						
						$resultSetTaxa = $this->db->get();
						
						if( $resultSetTaxa->num_rows() > 0 )
						{
							$clienteModel->findById($cliente);
							$taxaModel->obterNomeTaxaAdicional($taxa);
							
							$msgException = "O cliente " . $cliente->getCNPJ() . " - " . $cliente->getRazao() . " já possui um acordo válido para a taxa: " . $taxa->getNome() . ". Este acordo não foi salvo!";
							
							log_message('error',$msgException);
							throw new RuntimeException($msgException,$acordoConflito->id);														
						}							
						
					}						
					
				}	
				
			}	
									
		}	
				
	}
	
}

