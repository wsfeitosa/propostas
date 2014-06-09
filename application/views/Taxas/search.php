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
					<td>
						Tipo De Busca:
					</td>
					<td>
						Exibir acordos vencidos também:
					</td>
					<td id="label_dado_busca">
						Buscar:
					</td>																				                                                   
				</tr>								
				<tr>
					<td align="left" class="texto_pb">							
						<?php echo form_dropdown("tipo_busca",$tipo_busca,"0","id='tipo_busca'");?>							
					</td>
					<td align="left" class="texto_pb">							
						<?php echo form_dropdown("vencidas",$vencidas,"","id='vencidas'");?>							
					</td>
					<td align="left" class="texto_pb" colspan="2">
						<?php echo form_input("dado_busca","","id='dado_busca'"); ?>						
					</td>																										      
				</tr>										                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
