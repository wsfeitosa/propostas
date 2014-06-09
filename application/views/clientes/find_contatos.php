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
						Contato:
					</td>
					<td class="titulo_tabela">
						Email:
					</td>					
					<td class="titulo_tabela">
						Selecionar Para:
						<?php echo form_checkbox(Array('id' => 'selecionar_todos_para'))?>
					</td>
					<td class="titulo_tabela">
						Selecionar Cc:
						<?php echo form_checkbox(Array('id' => 'selecionar_todos_cc'))?>
					</td>															                                                   
				</tr>
				<?php foreach($contatos as $contato): ?>				
				<tr>
					<td align="center" class="texto_pb">							
						<?php echo $contato->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $contato->getEmail()->getEmail();?>												
					</td>					
					<td align="center" class="texto_pb">							
						<?php echo form_checkbox(Array("name" => "selecionado_para", "id" => $contato->getId(), "value" => $contato->getEmail()->getEmail(), "checked" => FALSE ));?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo form_checkbox(Array("name" => "selecionado_cc", "id" => $contato->getId(), "value" => $contato->getEmail()->getEmail(), "checked" => FALSE ));?>												
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
