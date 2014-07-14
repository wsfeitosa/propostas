<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Cliente Model
 *
 * Classe que contém as regras de negócio da entidade cliente
 *
 * @package Clientes
 * @author Wellington Feitosa <wellington.feitosao@allink.com.br>
 * @copyright Allink Transporte Internacionais LTDA. - 29/01/2013
 * @name Cliente_Model
 * @version 1.0
 */
class Cliente_Model extends CI_Model{
			
	public function __construct()
	{
		parent::__construct();
	}
	
	public function findByName($name = NULL)
	{

		include_once "cliente.php";
		include_once "cidade.php";	
		$this->load->model("Usuarios/usuario");
				
		if( empty($name) )
		{
			throw new Exception("O Nome do cliente deve ser uma String Válida!");
		}	
						
        $this->db->
        		   select("id_cliente, razao, cnpj, bairro, endereco, numero, estado,
        			       id_classificacao, cidade, responsavel, customer, customer_exportacao, customer_importacao,
        				   id_grupo_comercial, id_grupo_cnpj")->
                   from("CLIENTES.clientes")->
                   like("razao", utf8_decode(urldecode($name)))->
                   where("ativo", "S");
        
        $rs = $this->db->get();
        
		$clientes = Array();
		
		foreach( $rs->result() as $cliente )
		{
			try{
				$objCliente = New Cliente();
				
				$objCliente->setId((int)$cliente->id_cliente);			
				$objCliente->setRazao($cliente->razao);			
				$objCliente->setCNPJ($cliente->cnpj);
				$objCliente->setBairro($cliente->bairro);
				$objCliente->setEndereco($cliente->endereco);
				$objCliente->setNumero((int)$cliente->numero);
				$objCliente->setEstado($cliente->estado);
				
				$cidade = new Cidade();
				
				$cidade->setId((int)$cliente->cidade);
				
				try{
									
					$cidade_encontrada = $cidade->findById();
					
				} catch (Exception $e) {
					continue;
				}
					
				if( ! $cidade_encontrada )
				{	
					throw new RuntimeException("A cidade procurada não existe!");
				}
					
				$objCliente->setCidade($cidade);
				
				$vendedor_exportacao = new Usuario();
				$vendedor_importacao = new Usuario();
				$customer_exportacao = new Usuario();
				$customer_importacao = new Usuario();
				
				$vendedor_exportacao->setId((int)$cliente->responsavel);
				$vendedor_importacao->setId((int)$cliente->customer);
				$customer_exportacao->setId((int)$cliente->customer_exportacao);
				$customer_importacao->setId((int)$cliente->customer_importacao);
				
				$objCliente->setVendedorExportacao($vendedor_exportacao);
				$objCliente->setVendedorImportacao($vendedor_importacao);
				$objCliente->setCustomerExportacao($customer_exportacao);
				$objCliente->setCustomerImportacao($customer_importacao);
				
				$objCliente->setGrupoComercial($cliente->id_grupo_comercial);
				$objCliente->setGrupoCnpj($cliente->id_grupo_cnpj);
				
				$clientes[] = $objCliente;	
								
			} catch (Exception $e) {				
				log_message('error',$e->getMessage());
				show_error($e->getMessage());
			}			
		}	
		
		return $clientes;
				
	}//END FUNCTION
    
    /**
     * findById
     * 
     * Busca os cliente baseado no id do cliente
     * 
     * @name findById
     * @access public
     * @param Cliente $cliente
     * @return void
     */
    public function findById(Cliente $cliente) 
    {            	
    	include_once "cidade.php";
    	$this->load->model("Usuarios/usuario");
    	    	
        $id_cliente = $cliente->getId();
        
        if( empty($id_cliente) )
        {
            throw new InvalidArgumentException("Id do cliente informado é invalido para realizar a consulta!");
        }    
        
        $this->db->
        		select("id_cliente, razao, cnpj, bairro, endereco, numero, estado,
        			    id_classificacao, cidade, responsavel, customer, customer_exportacao, customer_importacao,
        				id_grupo_comercial, id_grupo_cnpj")->
                from("CLIENTES.clientes")->
                where("id_cliente", $cliente->getId())->
                order_by("id_cliente","ASC");
                //where("ativo", "S");
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            throw new RuntimeException("Id do cliente não encontrado na base de dados");
        }    
        
        $cliente_encontrado = $rs->row();
        
        $cliente->setId((int) $cliente_encontrado->id_cliente);			
        $cliente->setRazao($cliente_encontrado->razao);			
        $cliente->setCNPJ($cliente_encontrado->cnpj);
        $cliente->setBairro($cliente_encontrado->bairro);
        $cliente->setEndereco($cliente_encontrado->endereco);
        $cliente->setNumero((int) $cliente_encontrado->numero);
        $cliente->setEstado($cliente_encontrado->estado);
        $cliente->setClassificacao($cliente_encontrado->id_classificacao);

        $cidade = new Cidade();

        $cidade->setId((int)$cliente_encontrado->cidade);

        if( ! $cidade->findById() )
        {	
            throw new RuntimeException("A cidade procurada não existe!");
        }

        $cliente->setCidade($cidade);   

        $vendedor_exportacao = new Usuario();
        $vendedor_importacao = new Usuario();
        $customer_exportacao = new Usuario();
        $customer_importacao = new Usuario();
        
        $vendedor_exportacao->setId((int)$cliente_encontrado->responsavel);
        $vendedor_importacao->setId((int)$cliente_encontrado->customer);
        $customer_exportacao->setId((int)$cliente_encontrado->customer_exportacao);
        $customer_importacao->setId((int)$cliente_encontrado->customer_importacao);
        
        $cliente->setVendedorExportacao($vendedor_exportacao);
        $cliente->setVendedorImportacao($vendedor_importacao);
        $cliente->setCustomerExportacao($customer_exportacao);
        $cliente->setCustomerImportacao($customer_importacao);
        
        $cliente->setGrupoComercial($cliente_encontrado->id_grupo_comercial);
        $cliente->setGrupoCnpj($cliente_encontrado->id_grupo_cnpj);
        
    }//END FUNCTION
    
    /**
     * findByIdDaProposta
     * 
     * Busca os clientes que estão vinculados a uma proposta
     * 
     * @name findByIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return void
     */
    public function findByIdDaProposta(Proposta $proposta) 
    {
        
    	include_once "cliente.php";
    	    	
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta inválido para efetuar a busca pelos clientes!");
        }    
        
        $this->db->select("clientes_x_propostas.id_cliente")->
                   from("CLIENTES.clientes_x_propostas")->
                   where("clientes_x_propostas.id_proposta",$proposta->getId());
        
        $rs = $this->db->get();
        
        $linhas = $rs->num_rows();
        
        if( $linhas < 1 )
        {
            throw new RuntimeException("Nenhum cliente encontrado para a Proposta :" . $proposta->getNumero());
        }    
        
        $result = $rs->result();
        
        foreach ($result as $cliente_encontrado) 
        {            
            $cliente = new Cliente();            
            $cliente->setId((int) $cliente_encontrado->id_cliente);
            $this->findById($cliente);
            
            $proposta->adicionarNovoCliente($cliente);            
        }
        
        
    }//END FUNCTION
    
    /**
     * salvarCLienteProposta
     * 
     * Salva todos os cliente relacionados a uma proposta
     * 
     * @name salvaClienteProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
  */
    public function salvarClienteProposta( Proposta $proposta )
    {
        
        /** verifica se existem clientes para salvar **/
        if( count($proposta->getClientes()) < 1 )
        {
            log_message('error','Não existe cliente para ser salvo para está proposta!');
            show_error('Não existe cliente para ser salvo para está proposta!');
        }    
        
        /** verifica se o id da proposta está setado, ou seja se a proposta já foi salva **/
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error','A proposta ainda não foi salva, então não é possível relacionar os clientes!');
            show_error('A proposta ainda não foi salva, então não é possível relacionar os clientes!');
        }    
        
        $clientesDaProposta = $proposta->getClientes();
        
        foreach($clientesDaProposta as $key => $cliente) 
        {
            $this->salvar($cliente, $id_proposta);
        }
        
    }        
	
    public function salvar( Cliente $cliente, $id_proposta = NULL )
    {
        
        $dados_para_salvar = Array(
                                   'id_cliente' => $cliente->getId(),
                                   'id_proposta' => $id_proposta            
        );
        
        $rs = $this->db->insert("CLIENTES.clientes_x_propostas",$dados_para_salvar);
        
        return $rs;
        
    }        
    
    
    /**
     * excluirClientesPeloIdDaProposta
     * 
     * Exclui todos clientes que estão vinculados a uma proposta, fazendo a busca
     * pelo id da proposta
     * 
     * @name excluiClientesPeloIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
     */
    public function excluiClientesPeloIdDaProposta(Proposta $proposta) 
    {
       
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("O id da proposta não foi definido para realizar a exclusão dos clientes relacionados!");
        }    
        
        /** Seleciona todos os clientes relacionados a aquela proposta **/
        $this->db->
                select("clientes_x_propostas.id")->
                from("CLIENTES.clientes_x_propostas")->
                where("clientes_x_propostas.id_proposta",$proposta->getId());
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            return FALSE;
        } 
        
        $clientes_relacionados = $rs->result();
        
        /** Exclui todos os cliente relacionados **/
        foreach ($clientes_relacionados as $cliente_relacionado) 
        {
            $this->db->delete("CLIENTES.clientes_x_propostas", Array("id" => $cliente_relacionado->id));
        }
        
        return TRUE;
        
    }//END FUNCTION
    
    /** 
      * verificarModalidadeDosClientes
      * 
      * Verifica se todos os clientes informados tem a mesma modalidade (Direto ou Forwarder)
      * 
      * @name verificarModalidadeDosClientes
      * @access public
      * @param string $ids_dos_clientes
      * @return boolean
      * @throws InvalidArgumentException
      * @throws UnexpectedValueException
      */
    public function verificarModalidadeDosClientes( $ids_clientes_selecionados = "" )
    {
    	
    	include_once "cliente.php";
      include_once "define_classificacao.php";
    	    	
    	if( empty( $ids_clientes_selecionados ) )
    	{
    		throw new InvalidArgumentException("Nenhum cliente foi informado para realizar a validação das modalidades");
    	}	
    	
    	/** explode os clientes que devem estar separados pelo caractere : **/
    	$pilha_clientes_informados = explode(":", $ids_clientes_selecionados);
		   	
    	if( ! is_array($pilha_clientes_informados) || count($pilha_clientes_informados) < 1 )
    	{
    		throw new UnexpectedValueException("Não foi possivel realizar a comparação entre às modalidades dos clientes!");
    	}	
    	    	
    	$clientes_para_comparacao = Array();
    	
      $verificadorDeClassificacao = new Define_Classificacao();

    	foreach( $pilha_clientes_informados as $id_cliente )
    	{
    		
    		if( $id_cliente == "" )
    		{
    		    continue;
    		}	
    		
    		$cliente = new Cliente();
    		
    		$cliente->setId((int)$id_cliente);
    		
    		$this->findById($cliente);
        
        $classificacao_cliente_corrente = $verificadorDeClassificacao->ObterClassificacao($cliente);
        
        $clientes_para_comparacao[] = $classificacao_cliente_corrente;
    		 		    		   		
    	}	
    	
      $modalidades_encontradas = array_unique($clientes_para_comparacao);

      if( count($modalidades_encontradas) > 1 )
      {
          return FALSE;
      }  
      else
      {  
    	    return TRUE;
    	}
    }

    public function retornaNomeDoGrupo(Cliente $cliente)
    {
        $grupo_comercial = "";
        $grupo_cnpj = "";

        if( $cliente->getGrupoComercial() != null AND $cliente->getGrupoComercial() != 0 )
        {
            $this->db->
                    select("nome_grupo")->
                    from("CLIENTES.grupo_comercial")->
                    where("idgrupo_comercial",$cliente->getGrupoComercial());

            $rowSet = $this->db->get();

            if( $rowSet->num_rows > 0 )
            {
                $grupo_comercial = $rowSet->row()->nome_grupo;
            }  
                    
        }  

        if( $cliente->getGrupoCnpj() != null AND $cliente->getGrupoCnpj() != 0 )
        {
             $this->db->
                    select("nome_grupo")->
                    from("CLIENTES.grupo_cnpj")->
                    where("idgrupo_cnpj",$cliente->getGrupoCnpj());

            $rowSet = $this->db->get();

            if( $rowSet->num_rows > 0 )
            {
                $grupo_cnpj = $rowSet->row()->nome_grupo;
            }  
        } 

        return array(
                      "grupo_comercial" => $grupo_comercial,
                      "grupo_cnpj" => $grupo_cnpj
        );

    }

}//END CLASS