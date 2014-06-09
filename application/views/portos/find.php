<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/nova_proposta",Array("id" => "nova", "name" => "nova")); ?>			
			<input type="hidden" name="tela" id="tela" value="<?php echo $tela;?>" />
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td class="titulo_tabela">
						Porto:
					</td>
					<td class="titulo_tabela">
						Pais:
					</td>
					<td class="titulo_tabela">
						UnCode:
					</td>										
					<td class="titulo_tabela">
						Selecionar:						
					</td>															                                                   
				</tr>
				<?php foreach($portos as $porto): ?>				
				<tr>
					<td align="center" class="texto_pb">							
						<?php echo $porto->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $porto->getPais();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $porto->getUnCode();?>												
					</td>										
					<td align="center" class="texto_pb">							
						<?php echo form_checkbox(Array("name" => "selecionado", "id" => $porto->getUnCode(), "value" => $porto->getNome(), "checked" => FALSE ));?>												
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
