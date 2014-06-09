<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open_multipart("index.php/propostas/propostas/importar_planilha/",Array("id" => "upload", "name" => "upload")); ?>
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>	
				<tr>
					<td id="label_tipop" width="33%">
						Selecione o Arquivo Para Importar:
					</td>														                                                   
				</tr>					
				<tr>
					<td class="texto_pb" colspan="1">							
						<?php echo form_upload(Array('name' => 'arquivo', 'id' => 'arquivo'));?>							
					</td>																					      
				</tr>						                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
