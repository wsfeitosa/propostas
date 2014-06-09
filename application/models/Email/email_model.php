<?php
/**
* @package  propostas
* @author Wellington Feitosa <wellington.feitosao@allink.com.br>
* @copyright Allink Transporte Internacionais LTDA. - 04/03/2013
* @version  1.0
* Classe que controla às regras de negócio dos emails no sistema
*/
class Email_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();        
    }
    
    /**
     * buscaEmailPeloIdDaProposta
     * 
     * Encontra os emails pelo id de uma proposta
     * 
     * @name buscaEmailPeloIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return void
     */
    public function buscaEmailPeloIdDaProposta(Proposta $proposta) 
    {
    	$this->load->model("Email/email");
    	
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta inválido para realizar a busca pelos emails");
        }
        
        $this->db->select("emails_propostas.email, emails_propostas.tipo")->
                   from("CLIENTES.emails_propostas")->
                   where("id_proposta",$proposta->getId())->
                   where("ativo","S");
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            return FALSE;
        }    
        
        $result = $rs->result();
        
        foreach ($result as $email_encontrado) 
        {
            
            $email = new Email($email_encontrado->email);
            $email->setTipo($email_encontrado->tipo);
            $proposta->adicionarNovoEmail($email);
        }
        
    }//END FUNCTION
    
    /**
     * salvarEmail
     * 
     * Salva um email relacionado a uma proposta
     * 
     * @name salvarEmail
     * @access public     
     * @param Proposta $proposta 
     * @return void
     */    
    public function salvarEmail(Proposta $proposta) 
    {
        
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            log_message('error',"Não é possivel salva os emails pois o id da proposta não existe!");
            show_error("Não é possivel salva os emails pois o id da proposta não existe!");
        }    
        
        if( $proposta->obterQuantidadeEmails() < 1 )
        {
            return false;
        }    
        
        foreach ($proposta->obterEmails() as $email) 
        {            
            $this->salvar($email, $proposta->getId());            
        }
                        
    }//END FUNCTION
    
    /**
     * salvar
     * 
     * salva um email relacionado a uma proposta
     * 
     * @name salvar
     * @access public
     * @param Email $email
     * @param int $id_proposta 
     * @return Email
     */
    public function salvar( Email $email, $id_proposta = NULL ) 
    {
        if(is_null($id_proposta) )
        {
            show_error("Impossivel salvar os emails de contato da proposta!");
        }    
        
        $dados_para_salvar['email'] = strtolower($email->getEmail());
        $dados_para_salvar['ativo'] = 'S';
        $dados_para_salvar['tipo'] = $email->getTipo();
        $dados_para_salvar['id_proposta'] = $id_proposta;
        
        $rs = $this->db->insert("CLIENTES.emails_propostas",$dados_para_salvar);
        
        $email->setId((int)$this->db->insert_id());
        
        return $rs;
        
    }//END FUNCTION
    
    /**
     * excluirEmailsDaPropostaPeloIdDaProposta
     * 
     * Eclui os emails relacionados a uma proposta, fazendo a busca através do id da proposta
     * 
     * @name excluirEmailsDaPropostaPeloIdDaProposta
     * @access public
     * @param Proposta $proposta
     * @return boolean
     */
    public function excluirEmailsDaPropostaPeloIdDaProposta(Proposta $proposta) 
    {
        
        $id_proposta = $proposta->getId();
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("Id da proposta não foi definido para executar a exclusão dos emails antes da alteração!");
        }    
        
        /** Busca os emails relacionados a esta proposta **/
        $emails_encontrados = $this->buscaEmailsRelacionadosProposta($id_proposta);
        
        if( count($emails_encontrados) < 1 || !is_array($emails_encontrados) )
        {            
            return FALSE;
        }    
        
        foreach ($emails_encontrados as $email) 
        {
            $this->db->where('id_email_proposta', $email);
            $this->db->delete("CLIENTES.emails_propostas");
        }
        
        return TRUE;
        
    }//END FUNCTION
    
    /**
     * buscaEmailsRelacionadosProposta
     * 
     * Busca todos os emails que estão relacionados a uma proposta, fazendo a busca pelo id da proposta
     * 
     * @name buscaEmailsRelacionadosProposta 
     * @access public
     * @param int
     * @return string $emails_proposta
     */
    public function buscaEmailsRelacionadosProposta($id_proposta = NULL) 
    {
        
        if( empty($id_proposta) )
        {
            throw new InvalidArgumentException("Não foi informado o id da proposta para efetuar a busca pelos emails!");
        }    
        
        $emails_encontrados = Array();
        
        $this->db->
                select("emails_propostas.id_email_proposta")->
                from("CLIENTES.emails_propostas")->
                where("emails_propostas.id_proposta",$id_proposta);
        
        $rs = $this->db->get();
        
        if( $rs->num_rows() < 1 )
        {
            return $emails_encontrados;            
        }    
        
        foreach ($rs->result() as $email) 
        {
            array_push($emails_encontrados,$email->id_email_proposta);
        }
        
        return $emails_encontrados;
        
    }//END FUNCTION
    
}//END CLASS


