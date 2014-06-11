<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/listar_resultados_busca",Array("id" => "realizar_busca", "name" => "realizar_busca")); ?>
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
                    <td colspan="1">
						Sentido:
					</td>
					<td colspan="1">
						Apenas Meus Acordos (Cadastrados por mim):
					</td>
                    <td colspan="1">						
                        Status:
					</td>
                    <td colspan="1">						
                        Filial:
					</td>
				</tr>					
				<tr>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_dropdown("sentido", $sentidos, "EXP", "id='sentido'")?>							
					</td>
					<td class="texto_pb" colspan="1">												
                        <?php echo form_checkbox(array('name' => 'apenas_meus', 'id' => 'apenas_meus','checked' => false, 'value' => 'S', 'title' => 'Busca Apenas os acordos cadastrados por mim'));?>
                    </td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_dropdown("status", $status, "0", "id='status'");?>							
					</td>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_dropdown("filial", $filiais, "0", "id='filial'");?>							
					</td>
				</tr>
                <tr>
					<td colspan="2" id="label_apenas_meus">						
                        Número Proposta Master:
					</td>
                    <td colspan="2" id="label_apenas_meus">
						Número Item Proposta:
					</td>												                                                   
				</tr>
                <tr>
					<td class="texto_pb" colspan="2">							
                        <?php echo form_input(array('name' => 'numero_proposta_master', 'id' => 'numero_proposta_master', 'title' => 'Número do acordo que você quer pesquisar'));?>
					</td>
                    <td class="texto_pb" colspan="2">					
                        <?php echo form_input(array('name' => 'numero_item_proposta', 'id' => 'numero_item_proposta', 'title' => 'Número do acordo que você quer pesquisar'));?>
					</td>																				      
				</tr>
                <tr>
                    <td colspan="1" id="label_inicial">
						Data Inicial (De):
					</td>
                    <td colspan="1" id="label_inicial">
						Data Inicial (Até):
					</td>
					<td colspan="1" id="label_validade">
						Validade (De):
					</td>
                    <td colspan="1" id="label_validade">
						Validade (Até):
					</td>
                </tr>
                <tr>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "data_inicial_inicio", "id" => "data_inicial_inicio", "title" => '')); ?>				
					</td>	
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "data_inicial_final", "id" => "data_inicial_final", "title" => '')); ?>				
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "validade_inicio", "id" => "validade_inicio", "title" => "")); ?>				
					</td>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "validade_final", "id" => "validade_final", "title" => '')); ?>				
					</td>
                </tr>
                <tr>
					<td colspan="1" id="label_apenas_meus">
						Cadastrada Por:
					</td>
					<td colspan="1">
						Cliente:
					</td>
					<td colspan="1">
						Grupo Comercial:
					</td>	
                    <td colspan="1">
						Grupo Cnpj:
					</td>
				</tr>
                <tr>
					<td class="texto_pb" colspan="1">					
                        <?php echo form_input(Array("name" => "usuario_cadastro", "id" => "usuario_cadastro", "title" => 'Nome do usuário que cadastrou o acordo no sistema')); ?>				
                        <input type="hidden" name="id_usuario_cadastro" id="id_usuario_cadastro" />	
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "cliente", "id" => "cliente", "title" => 'Nome do cliente que está resgistrado no acordo')); ?>				
                        <input type="hidden" name="id_cliente" id="id_cliente" />
                    </td>	
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "grupo_comercial", "id" => "grupo_comercial", "title" => "Grupo do qual o cliente que está registrado no acordo faz parte")); ?>				
                        <input type="hidden" name="id_grupo_comercial" id="id_grupo_comercial" />
                    </td>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "grupo_cnpj", "id" => "grupo_cnpj", "title" => "Grupo do qual o cliente que está registrado no acordo faz parte")); ?>				
                        <input type="hidden" name="id_grupo_cnpj" id="id_grupo_cnpj" />
                    </td>
				</tr>
                <tr>
					<td colspan="1">
						Vendedor Exportação:
					</td>
					<td colspan="1">
						Customer Exportação:
					</td>
					<td colspan="1">
						Vendedor Importação:
					</td>
                    <td colspan="1">
						Customer Importação:
					</td>
				</tr>
                <tr>					
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "vendedor_exp", "id" => "vendedor_exp", "title" => 'Vendedor de exportação do cliente que está resgistrado no acordo')); ?>				
                        <input type="hidden" name="id_vendedor_exp" id="id_vendedor_exp" />
                    </td>	
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "customer_exp", "id" => "customer_exp", "title" => "Customer de exportação do cliente que está resgitrado no acordo")); ?>				
                        <input type="hidden" name="id_customer_exp" id="id_customer_exp" />
                    </td>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "vendedor_imp", "id" => "vendedor_imp", "title" => 'Vendedor de importação do cliente que está resgistrado no acordo')); ?>				
                        <input type="hidden" name="id_vendedor_imp" id="id_vendedor_imp" />
                    </td>	
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "customer_imp", "id" => "customer_imp", "title" => "Customer do cliente que está resgitrado no acordo")); ?>				
                        <input type="hidden" name="id_customer_imp" id="id_customer_imp" />
                    </td>
				</tr>
                <tr>
					<td colspan="1" width="25%">
						Origem:
					</td>
					<td colspan="1" width="25%">
						Embarque:
					</td>
					<td colspan="1" width="25%">
						Desembarque:
					</td>
                    <td colspan="1" width="25%">
						Destino:
					</td>
				</tr>
                <tr>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "origem_exp", "id" => "origem_exp", "title" => 'Local de recebimento da carga')); ?>				
                        <?php echo form_input(Array("name" => "origem_imp", "id" => "origem_imp", "title" => 'Local de recebimento da carga')); ?>
                        <input type="hidden" name="id_origem" id="id_origem" />							
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "embarque_exp", "id" => "embarque_exp", "title" => 'Porto de embarque da carga')); ?>
                        <?php echo form_input(Array("name" => "embarque_imp", "id" => "embarque_imp", "title" => 'Porto de embarque da carga')); ?>
                        <input type="hidden" name="id_embarque" id="id_embarque" />
                    </td>	
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "desembarque_exp", "id" => "desembarque_exp", "title" => "Porto de desembarque da carga")); ?>
                        <?php echo form_input(Array("name" => "desembarque_imp", "id" => "desembarque_imp", "title" => "Porto de desembarque da carga")); ?>
                        <input type="hidden" name="id_desembarque" id="id_desembarque" />
                    </td>
                    <td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "destino_exp", "id" => "destino_exp", "title" => "Local de entrega da carga")); ?>	
                        <?php echo form_input(Array("name" => "destino_imp", "id" => "destino_imp", "title" => "Local de entrega da carga")); ?>                        
                        <input type="hidden" name="id_destino" id="id_destino" />
                    </td>
				</tr>
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
