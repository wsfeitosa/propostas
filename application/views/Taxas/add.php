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
					<td colspan="3">
						Taxa:
					</td>																				                                                   
				</tr>								
				<tr>
					<td align="left" class="texto_pb" colspan="3">							
						<?php echo form_dropdown("taxa",$taxas,"","id='taxa'");?>							
					</td>																										      
				</tr>
				<tr>
					<td>Unidade:</td>
					<td>Moeda:</td>
					<td>PP | CC | AF:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<?php echo form_dropdown("unidade",$unidades,"","id='unidade'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_dropdown("moeda",$moedas,"","id='moeda'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_radio("modalidade","PP","id='modalidade'");?>
						<?php echo form_radio("modalidade","CC","id='modalidade'");?>
						<?php echo form_radio("modalidade","AF","id='modalidade'");?>
					</td>
				</tr>
				<tr>
					<td>Valor:</td>
					<td>Valor Minimo:</td>
					<td>Valor Maximo:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<input type="text" name="valor" id="valor" value="0.00" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_minimo" id="valor_minimo" value="0.00" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_maximo" id="valor_maximo" value="0.00" />
					</td>
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
