<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/nova_proposta",Array("id" => "nova", "name" => "nova")); ?>	
			<input type="hidden" name="index_combo" id="index_combo" value="<?php echo $index_combo;?>" />		
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
						<input type="text" name="taxa" id="taxa" value="<?php echo $nome_taxa;?>" size="50" />
						<input type="hidden" name="id_taxa" id="id_taxa" value="<?php echo $id_taxa;?>" />	
						<img border="0" style="vertical-align: middle;" name="taxas_exportacao" id="taxas_exportacao" src="/icones/help.png" title="Clique para exibir quais são os padrões para às principais taxas de Exportação" />						
					</td>																															      
				</tr>
				<tr>
					<td>Unidade:</td>
					<td>Moeda:</td>
					<td>Modalidade:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<?php echo form_dropdown("unidade",$unidades,$unidade_selecionada,"id='unidade'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_dropdown("moeda",$moedas,$moeda_selecionada,"id='moeda'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_radio(Array("name" => 'modalidade',"id"=>"modalidade","value"=>"PP","checked" => $ppcc=="PP"?true:false));?>PP
						<?php echo form_radio(Array("name" => 'modalidade',"id"=>"modalidade","value"=>"CC","checked" => $ppcc=="CC"?true:false));?>CC
						<?php echo form_radio(Array("name" => 'modalidade',"id"=>"modalidade","value"=>"AF","checked" => $ppcc=="AF"?true:false));?>AF
					</td>
				</tr>
				<tr>
					<td>Valor:</td>
					<td>Valor Minimo:</td>
					<td>Valor Maximo:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<input type="text" name="valor" id="valor" value="<?php echo $valor;?>" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_minimo" id="valor_minimo" value="<?php echo $valor_minimo;?>" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_maximo" id="valor_maximo" value="<?php echo $valor_maximo;?>" />
					</td>
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
