<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/taxas_locais/taxas_locais/save",Array("id" => "nova", "name" => "nova")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td colspan="2" >
						Número:
					</td>
				</tr>
				<tr>
					<td class="texto_pb" colspan="2">							
						&nbsp;							
					</td>
				</tr>	
				<tr>
					<td>
						Inicio:
					</td>					
					<td>
						Validade:				
					</td>															                                                   
				</tr>
				<tr>					
					<td class="texto_pb">							
						<input type="text" name="inicio" id="inicio" readonly="readonly" value="<?php echo date('d-m-Y');?>" />											
					</td>					
					<td class="texto_pb">							
						<input type="text" name="validade" id="validade" readonly="readonly" value="<?php echo date("d-m-Y",strtotime("+1 Month"));?>" />
					</td>																					      
				</tr>
				<tr>
					<td>
						Sentido:
					</td>					
					<td>
						Cliente:				
					</td>															                                                   
				</tr>							
				<tr>					
					<td class="texto_pb">							
						<?php 
						$opcoes = Array("0" => "Selecione", "IMP" => "Importação", "EXP" => "Exportação");
						echo form_dropdown("sentido",$opcoes,set_value('sentido'),"id = 'sentido'");
						?>												
					</td>					
					<td class="texto_pb">							
						<?php echo form_input(Array( 'name' => 'cliente', 'id' => 'cliente', 'value' => '' )); ?>
					</td>																					      
				</tr>
				<tr>
					<td width="50%">
						Cliente(s) Selecionado(s):
					</td>
					<td>
						Porto(s):
					</td>																			                                                   
				</tr>
				<tr>
					<td class="texto_pb" >
						<?php echo form_dropdown("clientes_selecionados[]", Array(), "", "id='clientes_selecionados' multiple='multiple' size='5'"); ?>
						<input type="button" name="excluir_cliente" id="excluir_cliente" value=" - " />
					</td>
					<td class="texto_pb">
						<?php echo form_multiselect("portos_selecionados[]", $portos, "", "id='portos_selecionados' size='5'" );?>
					</td>					
				</tr>
				<tr>
					<td colspan="1" width="50%">Taxas:</td>
					<td colspan="2">Observações Internas:</td>						
				</tr>
				<tr>
					<td colspan="1" class="texto_pb">
						<?php echo form_multiselect("taxas_selecionadas[]", Array(), "", "id='taxas_selecionadas' size='5'" );?>
						<input type="button" name="incluir_taxa" id="incluir_taxa" value=" + " />						
					</td>
					<td class="texto_pb" colspan="2">
						<?php echo form_textarea(Array('name' => 'observacao_interna', 'id' => 'observacao_interna', 'value' => "", 'rows' => 8)); ?>						
					</td>										
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
