<?php 
$this->load->model("Taxas_Locais_Acordadas/valida_periodo_vencimento");

$validador = new Valida_Periodo_Vencimento();
?>
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
					<td class="titulo_tabela" width="20%">
						Número:
					</td>
					<td class="titulo_tabela" width="30%" >
						Cliente(s):
					</td>
					<td  class="titulo_tabela" width="10%">
						Sentido:
					</td>
					<td  class="titulo_tabela" width="20%">
						Portos:
					</td>
					<td class="titulo_tabela" width="20%" >
						Ação:
					</td>															                                                   
				</tr>
				
				<?php foreach( $acordos as $acordo ): ?>	
				
				<?php												
				$style = $validador->retornaCorDeAcordoComVencimento($acordo);				
				?>
										
				<tr>
					<td align="center" class="texto_pb" <?php echo $style;?> >
						<?php echo $acordo->getNumero(); ?>    
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php
						foreach( $acordo->getClientes() as $cliente ):
							echo $cliente->getCnpj(). " - " . $cliente->getRazao() ."<br />";													
						endforeach;
						?>
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >
						<?php echo $acordo->getSentido(); ?>
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php
						foreach( $acordo->getPortos() as $porto ):
							echo $porto->getNome() . "<br />";													
						endforeach;
						?>							
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php echo anchor("index.php/taxas_locais/taxas_locais/update/".$acordo->getId(),"Alterar","id='link_update'");?> |
						<?php echo anchor_popup("index.php/taxas_locais/taxas_locais/view/".$acordo->getId(),"Visualizar","id='link_view'");?>	
						<?php anchor_popup("index.php/taxas_locais/taxas_locais/view/".$acordo->getId(),"Desativar","id='link_view'");?>											
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
