<?php
/**
 * Desbloqueio_Taxa
 *
 * Classe que representa os dados do desbloqueio de taxas da proposta no sistema  
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 05/06/2013
 * @version  versao 1.0
*/
include_once APPPATH . "models/Desbloqueios/solicitacao_entity.php";

class Solicitacao_Taxa_Entity implements Solicitacao_Entity {
	
	protected $id_desbloqueio, $taxa, $usuario_solicitacao, $data_solicitacao, $usuario_desbloqueio, 
			   $data_desbloqueio, $status, $nota_exportacao, $nota_importacao, $observacao, $modulo;
	
    public function __construct()
    {
    	
    }

	public function getIdDesbloqueio()
    {
        return $this->id_desbloqueio;
    }

    public function setIdDesbloqueio($id_desbloqueio)
    {
        $this->id_desbloqueio = $id_desbloqueio;
        return $this;
    }

    public function getTaxa()
    {
        return $this->taxa;
    }

    public function setTaxa(Taxa $taxa)
    {
        $this->taxa = $taxa;
        return $this;
    }

    public function getUsuarioSolicitacao()
    {
        return $this->usuario_solicitacao;
    }

    public function setUsuarioSolicitacao(Usuario $usuario_solicitacao)
    {
        $this->usuario_solicitacao = $usuario_solicitacao;
        return $this;
    }

    public function getDataSolicitacao()
    {
        return $this->data_solicitacao;
    }

    public function setDataSolicitacao(DateTime $data_solicitacao)
    {
        $this->data_solicitacao = $data_solicitacao;
        return $this;
    }

    public function getIdUsuarioDesbloqueio()
    {
        return $this->usuario_desbloqueio;
    }

    public function setUsuarioDesbloqueio(Usuario $usuario_desbloqueio)
    {
        $this->usuario_desbloqueio = $usuario_desbloqueio;
        return $this;
    }

    public function getDataDesbloqueio()
    {
        return $this->data_desbloqueio;
    }

    public function setDataDesbloqueio(DateTime $data_desbloqueio)
    {
        $this->data_desbloqueio = $data_desbloqueio;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getNotaExportacao()
    {
        return $this->nota_exportacao;
    }

    public function setNotaExportacao(Nota $nota_exportacao)
    {
        $this->nota_exportacao = $nota_exportacao;
        return $this;
    }

    public function getNotaImportacao()
    {
        return $this->nota_importacao;
    }

    public function setNotaImportacao(Nota $nota_importacao)
    {
        $this->nota_importacao = $nota_importacao;
        return $this;
    }

    public function setModulo($modulo)
    {
    	$this->modulo = $modulo;
    	return $this;
    }
    
    public function getModulo()
    {
    	return $this->modulo;
    }
    
    public function setObservacao($observacao)
    {
    	$this->observacao = $observacao;
    	return $this;
    }
    
    public function getObservacao()
    {
    	return $this->observacao;
    }
    
}