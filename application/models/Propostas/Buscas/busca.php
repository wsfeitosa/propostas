<?php
/**
 * Interface Busca
 *
 * Classe abstrata que define os métodos de busca que serão aplicados às propostas
 * 
 * @package Propostas/buscas
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 21/03/2013
 * @name Busca
 * @version 1.0
 */

interface Busca {
    public function buscar($parametro, $sentido, $vencidas);
}
