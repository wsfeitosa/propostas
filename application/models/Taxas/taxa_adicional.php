<?php
/**
 * Classe que representa a entidade Taxa Adicional
 *
 * Esta é uma classe que representa as taxas adicionais do sistema. 
 *
 * @package Taxas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 15/01/2013
 * @name Taxa_Adicional
 * @version 1.0
 * @abstract
 */
include_once 'taxa.php';
include_once APPPATH."models/Taxas_Locais_Acordadas/Interfaces/Entity.php";

class Taxa_Adicional extends Taxa implements Entity{
	
}