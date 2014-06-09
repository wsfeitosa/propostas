<?php
$this->load->model("Usuarios/usuario_model");
$this->load->model("Clientes/cliente_model");

$usuario_model = new Usuario_Model();
$cliente_model = new Cliente_Model();
?>
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
					<td class="titulo_tabela">
						Selecionar:
						<?php echo form_checkbox(Array('id' => 'selecionar_todos'))?>
					</td>															                                                   
					<td class="titulo_tabela">
						CNPJ:
					</td>
					<td class="titulo_tabela">
						Razão:
					</td>
					<td  class="titulo_tabela">
						Cidade:
					</td>
					<td  class="titulo_tabela">
						Estado:
					</td>
					<td  class="titulo_tabela">
						Vendedor Exportação:
					</td>
					<td  class="titulo_tabela">
						Vendedor Importação:
					</td>
					<td  class="titulo_tabela">
						Customer Exportação:
					</td>
					<td  class="titulo_tabela">
						Customer Importação:
					</td>
					<td  class="titulo_tabela">
						Grupo Comercial:
					</td>
					<td  class="titulo_tabela">
						Grupo Cnpj:
					</td>
				</tr>
				<?php 
				foreach($clientes as $cliente): 
				
					$usuario_model->findById($cliente->getCustomerExportacao());
					$usuario_model->findById($cliente->getCustomerImportacao());
					$usuario_model->findById($cliente->getVendedorExportacao());
					$usuario_model->findById($cliente->getVendedorImportacao());

					$grupos = $cliente_model->retornaNomeDoGrupo($cliente);	

				?>				
				<tr>
					<td align="center" class="texto_pb">							
						<?php echo form_checkbox(Array("name" => "selecionado", "id" => $cliente->getId(), "value" => $cliente->getCnpj()."-".$cliente->getRazao()." - > ".$cliente->getCidade()->getNome(), "checked" => FALSE ));?>												
					</td>																					      
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCnpj();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getRazao();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCidade()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getEstado();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getVendedorExportacao()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCustomerExportacao()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getVendedorImportacao()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCustomerImportacao()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $grupos['grupo_comercial'];?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $grupos['grupo_cnpj'];?>							
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
