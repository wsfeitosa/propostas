<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/adicionais/adicionais/listar_resultados_busca",Array("id" => "realizar_busca", "name" => "realizar_busca")); ?>
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td colspan="1" id="label_numero" width="25%">
						Número:
					</td>
					<td colspan="1" id="label_apenas_meus" width="25%">
						Apenas Meus Acordos (Cadastrados por mim):
					</td>
					<td colspan="1" id="label_inicial" width="25%">
						Data Inicial:
					</td>
					<td colspan="1" id="label_validade" width="25%">
						Validade:
					</td>										                                                   
				</tr>					
				<tr>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(array('name' => 'numero', 'id' => 'numero', 'title' => 'Número do acordo que você quer pesquisar'));?>							
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_checkbox(array('name' => 'apenas_meus', 'id' => 'apenas_meus','checked' => false, 'title' => 'Busca Apenas os acordos cadastrados por mim'));?>							
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "data_inicial", "id" => "data_inicial", "title" => 'Data em que o acordo começará a valer efetivamente')); ?>				
					</td>	
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "data_final", "id" => "data_final", "title" => "Data limite em que o acordo estará válido")); ?>				
					</td>															      
				</tr>
				<tr>
					<td colspan="2" id="label_opcao_tipo_cliente">
						Buscar Por: 
					</td>					
					<td colspan="2">
						Filial: 
					</td>
				</tr>
				<tr>
					<td class="texto_pb" colspan="2" id="label_opcao_tipo_cliente">
						<?php echo form_dropdown("tipo_cliente_busca",$tipo_cliente_busca,"0","id='tipo_cliente_busca'"); ?> 
					</td>
					<td class="texto_pb" colspan="2" id="label_filial">
						<?php echo form_dropdown("filial",$filiais,"0","id='filial'"); ?>
					</td>
				</tr>								
				<tr>
					<td class="texto_pb" colspan="4">
						<div id="pesquisa_cliente" >							
							<?php echo form_input(Array("name" => "cliente", "id" => "cliente", "size" => 50, "title" => "Busca pelos acordos do cliente informado")); ?>
							<input type="hidden" name="id_cliente" id="id_cliente" value="0" />	
						</div>
						<div id="pesquisa_grupo_comercial">
							<?php echo form_input(Array("name" => "grupo_comercial", "id" => "grupo_comercial", "size" => 50, "title" => "Busca pelos acordos de qualquer componente que estiver no grupo")); ?>
							<input type="hidden" name="id_grupo_comercial" id="id_grupo_comercial" value="0" />	
						</div>
						<div id="pesquisa_grupo_cnpj">
							<?php echo form_input(Array("name" => "grupo_cnpj", "id" => "grupo_cnpj", "size" => 50, "title" => "Busca pelos acordos de qualquer componente que estiver no grupo")); ?>
							<input type="hidden" name="id_grupo_cnpj" id="id_grupo_cnpj" value="0" />				
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Vendedor:
					</td>
					<td colspan="2">
						Customer:
					</td>
				</tr>
				<tr>
					<td class="texto_pb" colspan="2">
						<?php echo form_input(Array("name" => "vendedor", "id" => "vendedor", "size" => 50, "title" => "Busca pelos acordos dos clientes do vendedor informado")); ?>
						<input type="hidden" name="id_vendedor" id="id_vendedor" value="0" />	
					</td>
					<td class="texto_pb" colspan="2">
						<?php echo form_input(Array("name" => "customer", "id" => "customer", "size" => 50, "title" => "Busca pelos acordos dos clientes do customer informado")); ?>
						<input type="hidden" name="id_customer" id="id_customer" value="0" />	
					</td>
				</tr>	
				<tr>
					<td colspan="2">
						Cadastrada por:
					</td>
					<td colspan="2">
						Taxa cadastrada no acordo:
					</td>
				</tr>	
				<tr>
					<td class="texto_pb" colspan="2">
						<?php echo form_input(Array("name" => "usuario_cadastro", "id" => "usuario_cadastro", "size" => 50, "title" => "Busca pelos acordos cadastrados pela pessoa informada")); ?>
						<input type="hidden" name="id_usuario_cadastro" id="id_usuario_cadastro" value="0" />	
					</td>
					<td class="texto_pb" colspan="2">						
						<?php echo form_input(Array("name" => "nome_taxa", "id" => "nome_taxa", "size" => 50, "title" => "Busca pelos acordos que contém a taxa informada"));  ?>
						<input type="hidden" name="id_taxa" id="id_taxa" value="0" /> 
					</td>
				</tr>
				<tr>
					<td colspan="4">
						Status do Acordo:
					</td>
				</tr>
				<tr>
					<td class="texto_pb" colspan="4">						
						<?php echo form_dropdown("status",$status,"0","id='status'"); ?> 
					</td>
				</tr>								                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>

