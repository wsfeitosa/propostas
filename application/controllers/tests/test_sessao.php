<?php
session_start();
/**
 * Description of test_sessao
 *
 * @author wsfall
 */
class Test_Sessao extends CI_Controller{
    
    public function __construct()
	{
		parent::__construct();
	
		/** carrega library de testes unitarios **/
		$this->load->library('unit_test');
		$this->unit->active(TRUE);
        $this->output->enable_profiler(TRUE);
	
		/** Carrega o Model a ser testado **/		
		$this->load->model("Adaptadores/sessao");					
	}
    
    /** Função que vai executar todos os testes **/
	public function index()
	{
		try {
				
			/** Testes à serem rodados **/
			foreach (get_class_methods($this) as $method)
			{
				if( strpos($method, "test") !== FALSE )
				{
					$this->$method();
				}
					
			}
				
		} catch (Exception $e) {
			show_error($e->getMessage());
		}
	
		echo $this->unit->report();
	
	}
    
    public function testIncluirUmItemNaSessao() 
    {
        
        $sessao  = new Sessao();
        
        $sessao->setCc("CC")
        ->setPp("PP")
        ->setPeso((float)0.123)
        ->setCubagem((float)1.567)
        ->setVolumes((int)12)
        ->setOrigem("HAMBURG")
        ->setEmbarque("HAMBURG")
        ->setDesembarque("SANTOS")
        ->setDestino("SANTOS")
        ->setUnOrigem("DEHAM")
        ->setUnEmbarque("DEHAM")
        ->setUnDesembarque("BRSSZ")
        ->setUnDestino("BRSSZ")        
        ->setIdTarifario((int)8289)
        ->setMercadoria("PATOS GANSOS E OUTROS ANIMAIS")
        ->setObservacaoCliente("OBS CLI")
        ->setObservacaoInterna("OBS INT")
        ->setLabelsFretesAdicionais("labels_fretes_adicionais")
        ->setLabelsTaxasLocais("labels_taxas_locais")
        ->setFreteAdicionais("frete_adicionais")
        ->setTaxasLocais("taxas_locais")
        ->setValidade(date('Y-m-d'))
        ->setAntiCache(strtotime(date('Y-m-d')));        
        
        $index = $sessao->inserirItemNaSessao();
        
        $this->AlterarUmItemNaSessaoDeveriaPassar($index);
        
        $this->unit->run($index, 'is_int', "Inclui um item na sessão", "Retorno da função: ".$index);
        
    }
    
    public function AlterarUmItemNaSessaoDeveriaPassar($index)
    {       
       
        $sessao = new Sessao();
        
        /** Altera o item atual que está na sessão **/
        $sessao->setIdItem($index)
        ->setCc("CC")
        ->setPp("PP")
        ->setPeso((float)0.123)
        ->setCubagem((float)1.567)
        ->setVolumes((int)12)
        ->setOrigem("HAMBURG")
        ->setEmbarque("HAMBURG")
        ->setDesembarque("SANTOS")
        ->setDestino("SANTOS")
        ->setUnOrigem("DEHAM")
        ->setUnEmbarque("DEHAM")
        ->setUnDesembarque("BRSSZ")
        ->setUnDestino("BRSSZ")        
        ->setIdTarifario((int)78995)
        ->setMercadoria("PATOS GANSOS E OUTROS ANIMAIS E O TECO")
        ->setObservacaoCliente("OBS CLI")
        ->setObservacaoInterna("OBS INT")
        ->setLabelsFretesAdicionais("labels_fretes_adicionais")
        ->setLabelsTaxasLocais("labels_taxas_locais")
        ->setFreteAdicionais("frete_adicionais")
        ->setTaxasLocais("taxas_locais")
        ->setValidade(date('Y-m-d'))
        ->setAntiCache(strtotime(date('Y-m-d')));  
        
        $novo_index = $sessao->inserirItemNaSessao();
        
        $this->unit->run($novo_index, $index, "Testa se o id da sessão foi mantido após a alteração", "Id Anterior: ".$index."  Id Atual: ".$novo_index);
        $this->unit->run($sessao->getIdTarifario(), 78995, "Testa se o campo foi alterado com sucesso", "Id Atual: ".$sessao->getIdTarifario());
                
    }        
    
    public function testRecuperarItemDaSessaoDeveriaPassar()
    {
        
        $sessao = new Sessao();
        
        $item_recuperado = $sessao->recuperarItemDaSessao(0);
        
        $this->unit->run($item_recuperado, is_array($item_recuperado), "Testa se o item foi recuperado com sucesso da sessao" );
        
        /** Verifica so o array retornado esta preenchido **/
        $esta_preenchido = FALSE;
        
        if( count($item_recuperado) > 0  )
        {
            $esta_preenchido = TRUE;
        }    
        
        $this->unit->run($esta_preenchido, TRUE, "Testa se o array contendo o item recuperado esta Preenchido", "Quantidade de itens do Array: ".count($item_recuperado));
        
    }   
    
    public function testExcluiUmItemDaSessaoDeveriaPassar()
    {
        
        $this->testIncluirUmItemNaSessao();
        
        $quantidade_de_itens_na_sessao = count($_SESSION['itens_proposta']);
        
        $sessao = new Sessao();
        $sessao->setIdItem(0);
        $sessao->excluirItemDaSessao();
        
        $msg = "Quantidade de itens antes da exclusão: ".$quantidade_de_itens_na_sessao." Quantidade de itens após a exclusão: ".count($_SESSION['itens_proposta']);
        
        $this->unit->run(($quantidade_de_itens_na_sessao - 1), count($_SESSION['itens_proposta']), "Verifica se o item foi excluído da sessão", $msg);    
    }
    
}//END CLASS

