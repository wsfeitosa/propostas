<?php 
$this->load->model("Usuarios/usuario");
$this->load->model("Usuarios/usuario_model");

$usuario_model = new Usuario_Model();
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
						Proposta:
					</td>
					<td class="titulo_tabela">
						Usuário:
					</td>
					<td class="titulo_tabela">
						Data do Log:
					</td>																				                                                   
				</tr>
				<?php foreach($logs as $log): ?>					
				<tr>
					<td align="center" class="texto_pb">							
						<?php echo anchor_popup('index.php/propostas/propostas/exibir_historico/'.$log->id_log, $log->numero_proposta, Array( 'width' => '1024','height' => '768')); ?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php 
						$usuario = new Usuario();
						$usuario->setId((int)$log->id_usuario);
						
						$usuario_model->findById($usuario);
						
						echo $usuario->getnome();
						?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php
						$data = new DateTime($log->data_log); 
						echo $data->format('d/m/Y H:i:s'); 
						?>												
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
