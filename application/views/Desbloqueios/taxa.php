<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open($form_action,Array("id" => "desbloqueio", "name" => "desbloqueio")); ?>	
			<input type="hidden" name="modulo" id="modulo" value="<?php echo $modulo;?>" />
			<input type="hidden" name="id_item" id="id_item" value="<?php echo $taxa->getIdItem();?>" />		
			<input type="hidden" name="tipo_taxa" id="tipo_taxa" value="<?php echo get_class($taxa);?>" />
			<input type="hidden" name="index_taxa" id="index_taxa" value="<?php echo $index_taxa;?>" />
			<input type="hidden" name="taxa_serializada" id="taxa_serializada" value="<?php echo $taxa_serializada;?>" />
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
						<?php echo $taxa->getNome(); ?>
						<input type="hidden" name="id_taxa" id="id_taxa" value="<?php echo $taxa->getId();?>" />							
					</td>																										      
				</tr>
				<tr>
					<td>Unidade:</td>
					<td>Moeda:</td>
					<td>Volume Esperado:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<?php echo form_dropdown("unidade",$unidades,$taxa->getUnidade()->getId(),"id='unidade'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_dropdown("moeda",$moedas,$taxa->getMoeda()->getId(),"id='moeda'");?>
					</td>
					<td class="texto_pb">
						<?php echo form_dropdown("nota",$notas,"","id='nota'");?>
					</td>					
				</tr>
				<tr>
					<td>Valor:</td>
					<td>Valor Minimo:</td>
					<td>Valor Maximo:</td>
				</tr>
				<tr>
					<td class="texto_pb">
						<input type="text" name="valor" id="valor" value="<?php echo sprintf( "%02.2f",$taxa->getValor() ); ?>" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_minimo" id="valor_minimo" value="<?php echo sprintf( "%02.2f",$taxa->getValorMinimo() ); ?>" />
					</td>
					<td class="texto_pb">
						<input type="text" name="valor_maximo" id="valor_maximo" value="<?php echo sprintf( "%02.2f",$taxa->getValorMaximo() ); ?>" />
					</td>
				</tr>	
				<tr>
					<td colspan="3">Justificativa:</td>
				</tr>
				<tr>
					<td colspan="3" class="texto_pb">
						<?php echo form_textarea(Array('id'=>'justificativa','name'=>'justificativa', 'cols' => '115'));?>
					</td>
				</tr>								                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
