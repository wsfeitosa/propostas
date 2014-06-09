<?php
/**
 * Define a classifica��o de um cliente
 *
 * Define a classifica��o de um cliente, ou seja, se o cliente � um cliente
 * direto, um forwarder, despachante etc...
 * 
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 10/04/2013
 * @name Define_Classificao
 * @version 1.0
 */
class Define_Classificacao {
        
    public function ObterClassificacao(Cliente $cliente){
        
        if( $cliente->getClassificacao() == 7 )
        {
            return "D";
        }
        else
        {
            return "F";
        }    
        
    }
    
}


