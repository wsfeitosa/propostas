<?php //print"<pre>";print_r($tarifarios);print"</pre>";exit;?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/nova_proposta",Array("id" => "nova", "name" => "nova")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td class="titulo_tabela">
						Origem:
					</td>
					<td class="titulo_tabela">
						Embarque:
					</td>
					<td class="titulo_tabela">
						Descarga:
					</td>										
					<td class="titulo_tabela">
						Destino:						
					</td>
					<td class="titulo_tabela">
						TT (Dias):						
					</td>
					<td class="titulo_tabela">
						Frete:						
					</td>
					<td class="titulo_tabela">
						Selecionar:						
					</td>															                                                   
				</tr>
				<?php foreach($tarifarios as $tarifario): ?>
					<?php						
						$bloqueado = "false";
						$msg = "";
						$class = "";
					
						if( ! empty($tarifario->mensagem_imo) )
						{
							$bloqueado = "disabled";
							$msg .= $tarifario->mensagem_imo . "\n"; 
							$class = "class='mensagem'";
						}

						if( ! empty($tarifario->mensagem_collect) )
						{
							$bloqueado = "disabled";
							$msg .= $tarifario->mensagem_collect;
							$class = "class='mensagem'";
						}	
					?>														
				<tr title='<?php echo $msg; ?>' <?php echo $class; ?> >
					<td align="center" class="texto_pb">							
						<?php echo $tarifario->getRota()->getPortoOrigem()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $tarifario->getRota()->getPortoEmbarque()->getNome();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $tarifario->getRota()->getPortoDesembarque()->getNome();?>												
					</td>										
					<td align="center" class="texto_pb">							
						<?php echo $tarifario->getRota()->getPortoFinal()->getNome();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $tarifario->getTransitTime();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php
                            foreach( $tarifario->getTaxa() as $taxa ):                             	 
                                if( $taxa->getId() == 10 )
                                {
                                    echo $taxa->getMoeda()->getSigla() . " | ";
                                    echo number_format($taxa->getValor(),2)." | ";
                                    echo $taxa->getUnidade()->getUnidade();
                                }    
                                
                            endforeach;							
						?>												
					</td>					
					<td align="center" class="texto_pb">						
						<input type="checkbox" name='' id='<?php echo $tarifario->getId(); ?>' id_item='<?php echo $tarifario->id_item_proposta; ?>' value='<?php echo get_class($tarifario); ?>' <?php echo $bloqueado; ?> />
					</td>																				      
				</tr>				
				<?php endforeach; ?>					                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
