<link rel="stylesheet" type="text/css" href="/Estilos/tooltip.css">
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/realizar_busca",Array("id" => "listar", "name" => "listar")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td class="titulo_tabela">
						Número:
					</td>
					<td class="titulo_tabela">
						Cliente(s):
					</td>	
                    <td class="titulo_tabela">
                        Taxas:
                    </td>
                    <td class="titulo_tabela">
                        Aprovação Pendente:
                    </td>
					<td class="titulo_tabela">
						Data de inicio:
					</td>
					<td class="titulo_tabela">
						Validade:
					</td>															                                                   
				</tr>
				<?php foreach($acordos_encontrados as $acordo): ?>							
				<tr>
					<td align="center"class="texto_pb">											
                        <a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['numero_acordo'];?>
                        </a>                        
					</td>
					<td align="center"class="texto_pb">		
						<a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['clientes'];?>
                        </a> 
					</td>
                    <td align="center" class="texto_pb">							
						<a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['taxas_acordo'];?>
                        </a> 
					</td>
                    <td align="center" class="texto_pb">							
						<a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['aprovacao_pendente'];?>
                        </a> 
					</td>
					<td align="center" class="texto_pb">							
						<a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['data_inicial']->format('d/m/Y');?>
                        </a> 
					</td>
					<td align="center" class="texto_pb">							
						<a href="#" id_acordo="<?php echo $acordo['id_acordo'];?>">						
						<?php echo $acordo['data_final']->format('d/m/Y');?>
                        </a> 		
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
