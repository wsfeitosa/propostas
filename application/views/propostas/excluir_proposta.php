<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/excluir_proposta",Array("id" => "form_excluir", "name" => "form_excluir")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td colspan="4" class="titulo_tabela">
						Numero Da Proposta:
					</td>																		                                                   
				</tr>							
				<tr>
					<td align="center" class="texto_pb">					
						<input type="text" name="numero_proposta" id="numero_proposta" />
						<input type="button" name="enviar" id="enviar" value="Excluir" />							
					</td>																					      
				</tr>						                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
