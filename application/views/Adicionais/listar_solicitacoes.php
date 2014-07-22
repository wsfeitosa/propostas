<?php 
//pr($solicitacoes_pendentes[2]->getAcordo());
$this->load->model("Adicionais/acordo_adicionais");
$this->load->model("Adicionais/acordo_adicionais_model");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Scoa</title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <meta name="description" content="Scoa Sistema de controle Allink" />
    <meta name="author" content="Allink Transportes Internacionais Ltda" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="robots" content="noarchive" />        
    <link rel="stylesheet" href="/Libs/jquery-ui-1.10.4/css/redmond/jquery-ui-1.10.4.custom.css" type="text/css" />
    <link rel="stylesheet" href="/Clientes/propostas/assets/js/sidr/stylesheets/jquery.sidr.light.css">    
    <link rel="stylesheet" href="/Estilos/scoa.css" type="text/css" />
    <style>
    	body{
    		font-size:10px;
    	}	

    	.tabela_scoa, .tabela_scoa th, .tabela_scoa_sombra th, .tabela_scoa td, .tabela_scoa_sombra td{    		
		    font-size:10px;
		}
		#progressbar .ui-progressbar-value {
			background-color: #ccc;
		}
		.ui-tooltip	{
		    font-size:8pt;		    
		}		
    </style>  
    <script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script language="javascript" src="/Libs/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.js"></script>
	<script language="javascript" src="/Libs/jquery/jquery-block-ui/jquery-block-ui.js"></script>		
	<script language="javascript" src="/Libs/JavaScript/replaceAll.js"></script>
	<script type="text/javascript" src="/Libs/jquery/jquery.price_format.1.7.js"></script>
	<script type="text/javascript" src="/Libs/jquery/jquery.price_format.1.7.min.js"></script>
	<?php echo $js;?>
</head>
<body>
		 
	<div id="sidr">		
		<ul>
			<li><a href="#" id="novo">Novo Acordo de Adicionais</a></li>
			<li><a href="#" id="consultar">Consultar Acordo de Adicionais</a></li>
			<li><a href="#" id="desbloqueio">Desbloqueios</a></li>
			<li><a href="#" id="sair">Sair</a></li>
		</ul>
	</div>
	
    <div class="principal">
        <p class="titulo">Desbloqueio de Acordos de adicionais sobre o frete</p>    
        
        <div id="accordion">
        	<?php
        	if( count($solicitacoes_pendentes) > 0 )
        	{
        		foreach( $solicitacoes_pendentes as $solicitacao )
        		{
        			$acordo = $solicitacao->getAcordo();        			
        			$clientes = $acordo->getClientes(); 
									
        			$acordoSalvo = new Acordo_Adicionais();
        			$acordoSalvo->setId((int)$acordo->getId());
        			$this->acordo_adicionais_model->consultarAcordoAdicionaisPorId($acordoSalvo);
                    $this->acordo_adicionais_model->localizaUltimoDesbloqueio($acordoSalvo);
        		?>
        		        		        		
        		<h3><?php echo $acordo->getNumeroAcordo() . " - " . $clientes[0]->getRazao(); ?></h3>
        		        		
        		<?php echo form_open("index.php/adicionais/adicionais/autorizar_desbloqueio/",Array('id' => "form-".$solicitacao->getId()));?>
				<input type="hidden" name="id_acordo" id="id_acordo" value="<?php echo $acordo->getId(); ?>" />
				<input type="hidden" name="id_solicitacao" id="id_solicitacao" value="<?php echo $solicitacao->getId(); ?>" />
				<div>
					
					<div class="uma_coluna" >
						<label class="label">Clientes:</label>
						<?php foreach($clientes as $cliente): ?>					
							<?php echo $cliente->getCNPJ() . " - " . $cliente->getRazao() . " -> " . $cliente->getEstado() . "<br />"; ?>
						<?php endforeach; ?>
					</div>
					
					<div class="coluna">
						<label class="label">Data Inicio:</label>
						<?php
						$dataInicio = new DateTime($acordo->data_inicio);						
						?>
						<input type="text" title="Data em que o acordo começará a valer efetivamente" name="inicio-<?php echo $acordo->getId();?>" id="inicio-<?php echo $acordo->getId();?>" readonly="readonly" value="<?php echo $dataInicio->format('d-m-Y');?>" />
					</div>
					
					<div class="coluna">
						<label class="label">Data Validade:</label>
						<?php
						$dataValidade = new DateTime($acordo->data_final);						 
						?>	
						<input type="text" title="Data limite em que o acordo será válido" name="validade-<?php echo $acordo->getId();?>" id="validade-<?php echo $acordo->getId();?>" readonly="readonly" value="<?php echo $dataValidade->format('d-m-Y');?>" />					
					</div>
					
					<div class="coluna">
						<label class="label">Alterar Propostas Retroativamente:</label>
						<?php echo form_dropdown('alterar_retroativos-'.$acordo->getId(),Array("S" => "Sim", "N" => "Não"),$acordo->alterar_retroativos,"id = 'alterar_retroativos-{$acordo->getId()}'"); ?>
					</div>
					
					<div class="coluna">
						<label class="label">Solicitado Por:</label>
						<?php echo $solicitacao->getSolicitante()->getNome(); ?>
					</div>
					
                    <div class="coluna">
						<label class="label">Acordo Cadastrado Por:</label>
						<?php echo $acordoSalvo->getUsuarioCadastro()->getNome() . " EM " . $acordoSalvo->getDataCadastro()->format('d/m/Y H:i:s'); ?>
					</div>
                    
                    <div class="coluna">
						<label class="label">Acordo Alterado Última Vez  Por:</label>
						<?php
                        if( $acordoSalvo->getUsuarioAlteracao() instanceof Usuario)
                        {    
                            echo $acordoSalvo->getUsuarioAlteracao()->getNome() . " EM " . $acordoSalvo->getDataAlteracao()->format('d/m/Y H:i:s'); 
                        }
                        else
                        {
                            echo "NÃO HOUVE ALTERAÇÃO"; 
                        }    
                        ?>
					</div>
                    
                    <div class="coluna">
						<label class="label">Acordo Aprovado Última Vez  Por:</label>                                               
						<?php 
                        if( $acordoSalvo->getUsuarioDesbloqueio() instanceof Usuario )
                        {    
                            echo $acordoSalvo->getUsuarioDesbloqueio()->getNome() . " EM " . $acordoSalvo->getDataDesbloqueio()->format('d/m/Y H:i:s'); 
                        }
                        else
                        {
                            echo "NÃO HOUVE DESBLOQUEIO";
                        }    
                        ?>					
                    </div>
                    
					<div class="uma_coluna">
					
						<table class="tabela_scoa">
					    <thead>
					        <tr>
					            <th>Taxa</th>
					            <th>Como está agora</th>
					            <th>Para / Contra Proposta</th>	            
					        </tr>
					    </thead>
					    <tbody> 
					    	<?php foreach($acordo->getTaxas() as $taxaSolicitada): ?>   	
					        <tr>	            
					            <td><?php echo $taxaSolicitada->getNome(); ?></td>	
					            <td>
					            <?php
					            $taxaEncontrada = null;
					             
					            foreach( $acordoSalvo->getTaxas() as $taxaSalva )
					            {					            	
					            	if( $taxaSalva->getId() == $taxaSolicitada->getId() )
					            	{
										$taxaEncontrada = $taxaSalva;
					            	}	
					            }

					            //Se não encontrou a taxa no acordo salvo é por que ela foi adicionada e não alterada
					            if( is_null($taxaEncontrada) )
					            {
					            	echo "Não há acordo vigente. Aplica-se padrão.";			
					            }
					            else 
					            {
					            	echo $taxaEncontrada->getMoeda()->getSigla() . " " .
					            		 number_format($taxaEncontrada->getValor(),2) . " ".
					            		 $taxaEncontrada->getUnidade()->getUnidade() . " | MIN. " .
					            		 number_format($taxaEncontrada->getValorMinimo(),2) . " | MAX. " .
					            		 number_format($taxaEncontrada->getValorMaximo(),2);
					            }	
					            ?>
					            </td>
					            <td>
					            
					            	<div id="taxa-solicitada-exibicao-<?php echo $taxaSolicitada->getId(); ?>" title="Clique duas vezes em cima da taxa para altera-la" >
						            <?php 
						            echo $taxaSolicitada->getMoeda()->getSigla() . " " .
						            	 number_format($taxaSolicitada->getValor(),2) . " ".
			                             $taxaSolicitada->getUnidade()->getUnidade() . " | MIN. " .
						                 number_format($taxaSolicitada->getValorMinimo(),2) . " | MAX. " .
						                 number_format($taxaSolicitada->getValorMaximo(),2);					            
						            ?>
						            </div>
						            <div id="taxa-solicitada-edicao-<?php echo $taxaSolicitada->getId(); ?>" >
						            							            	
						            	<?php  			            	
						            	echo form_dropdown('taxa-solicitada-moeda-'.$taxaSolicitada->getId(),$moedas,$taxaSolicitada->getMoeda()->getId(),"id = 'taxa-solicitada-moeda-{$taxaSolicitada->getId()}'");
					            		echo ' - ';
					            		echo form_input(Array('id' => 'taxa-solicitada-valor-'.$taxaSolicitada->getId(), 'name' => 'taxa-solicitada-valor-'.$taxaSolicitada->getId(), 'value' => number_format($taxaSolicitada->getValor(),2), 'size' => '6' ));
					            		echo ' - ';
					            		echo form_dropdown('taxa-solicitada-unidade-'.$taxaSolicitada->getId(), $unidades, $taxaSolicitada->getUnidade()->getId(), "id = 'taxa-solicitada-unidade-{$taxaSolicitada->getId()}'");
					            		echo ' | MIN.';
					            		echo form_input(Array('id' => 'taxa-solicitada-valor_minimo-'.$taxaSolicitada->getId(), 'name' => 'taxa-solicitada-valor_minimo-'.$taxaSolicitada->getId(), 'value' => number_format($taxaSolicitada->getValorMinimo(),2), 'size' => '6' ));
					            		echo ' | MAX.';
					            		echo form_input(Array('id' => 'taxa-solicitada-valor_maximo-'.$taxaSolicitada->getId(), 'name' => 'taxa-solicitada-valor_maximo-'.$taxaSolicitada->getId(), 'value' => number_format($taxaSolicitada->getValorMaximo(),2), 'size' => '6' ));
						            	?>
						            	
						            	<input type="button" id="taxa-solicitada-salvar-<?php echo $taxaSolicitada->getId();?>" value="Salvar Edição" />
						            	<input type="button" id="taxa-solicitada-cancelar-<?php echo $taxaSolicitada->getId();?>" value="Cancelar" />
						            </div>
						            					            
					            </td>	            
					        </tr>
				            <?php endforeach; ?>
					    </tbody>
				
						</table> 
						
						<div class="botoes">						
							<input type="submit" id="salvar" name="salvar" value="Aprovar"/>
							<input type="button" id="<?php echo $acordo->getId(); ?>" name="excluir-solicitacao" value="Excluir Solicitação"/>
						</div>
						
					</div>	
												
				</div>	
				
				</form>
				
        		<?php 	
        		}	
        	}	
        	?>
        							
        </div>
        
        <div class="botoes">            
        	<input type="button" name="simple-menu" id="simple-menu" value="Opções" />	           
        </div>   	            
        		
    </div>

</body>
</html>