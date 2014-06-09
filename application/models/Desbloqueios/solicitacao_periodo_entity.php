<?php
/**
 * Desbloqueio_Periodo
 *
 * Classe que representa os dados do desbloqueio dos periodos das propostas no sistema   
 *
 * @package models/Desbloqueios
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 05/06/2013
 * @version  versao 1.0
*/
include_once APPPATH . "models/Desbloqueios/solicitacao_entity.php";

class Solicitacao_Periodo_Entity implements Solicitacao_Entity {
	
	protected  $id_desbloqueio, $id_item, $inicio, $validade, $status,
			   $usuario_solicitacao, $data_solicitacao, $usuario_desbloqueio,
			   $data_desbloqueio, $nota_exportacao, $nota_importacao, $modulo;
	
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

    public function getIdItem()
    {
        return $this->id_item;
    }

    public function setIdItem($id_item)
    {
        $this->id_item = $id_item;
        return $this;
    }

    public function getInicio()
    {
        return $this->inicio;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getValidade()
    {
        return $this->validade;
    }

    public function setValidade($validade)
    {
        $this->validade = $validade;
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

    public function getUsuarioSolicitacao()
    {
        return $this->usuario_solicitacao;
    }

    public function setUsuarioSolicitacao($usuario_solicitacao)
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

    public function getUsuarioDesbloqueio()
    {
        return $this->usuario_desbloqueio;
    }

    public function setUsuarioDesbloqueio($usuario_desbloqueio)
    {
        $this->usuario_desbloqueio = $usuario_desbloqueio;
        return $this;
    }

    public function getDataDesbloqueio()
    {
        return $this->data_desbloqueio;
    }

    public function setDataDesbloqueio($data_desbloqueio)
    {
        $this->data_desbloqueio = $data_desbloqueio;
        return $this;
    }

    public function getNotaExportacao()
    {
        return $this->nota_exportacao;
    }

    public function setNotaExportacao($nota_exportacao)
    {
        $this->nota_exportacao = $nota_exportacao;
        return $this;
    }

    public function getNotaImportacao()
    {
        return $this->nota_importacao;
    }

    public function setNotaImportacao($nota_importacao)
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
    
} 