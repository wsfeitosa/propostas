<?php 
$this->load->model("Propostas/valida_periodo_vencimento");

$validador = new Valida_Periodo_Vencimento();
?>
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
						Ver Todos:
					</td>
					<td class="titulo_tabela">
						Cliente(s):
					</td>
					<td  class="titulo_tabela">
						Sentido:
					</td>
					<td class="titulo_tabela">
						Tipo De Proposta:
					</td>
					<td class="titulo_tabela">
						Nome Do Nac:
					</td>															                                                   
				</tr>
				<?php foreach($propostas as $proposta): ?>
				
				<?php												
				$style = $validador->retornaCorDeAcordoComVencimento($proposta);				
				?>																				
				<tr>
					<td align="center"class="texto_pb" <?php echo $style;?> >
						<p />						
                        <a href="#" class="tooltip" id_proposta="<?php echo $proposta->getId();?>" tipo_proposta="<?php echo $proposta->getTipoProposta();?>" >
						<span>
							<img class="callout" src="/Imagens/callout.gif"></img>
							<strong>Atenção!</strong>
							<br>
							Este item exibe inicialmente os 10 primeiros itens da proposta, 
							com possibilidade de carregar os demais itens na tela, 05 de cada vez. (Mais rápido).							
						</span>
						<?php echo $proposta->getNumero();?>
                        </a>                        
					</td>
					<td align="center"class="texto_pb" <?php echo $style;?> >		
						<p />				
						<a href="#" class="tooltip" completo="<?php echo $proposta->getId();?>" tipo_proposta="<?php echo $proposta->getTipoProposta();?>" >
						<span>
							<strong>Atenção!</strong>
							<br>
							Este item ira exibir na tela todos os itens da proposta. (Pode Demorar dependendo da quantidade de itens).							
						</span>
						Ver Todos os Itens
                        </a> 
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php                            
                            foreach ($proposta->getClientes() as $cliente ):
                                echo $cliente->getCnpj(). " - " .$cliente->getRazao()."<br />";
                            endforeach;
                        ?>
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php echo $proposta->getSentido();?>							
					</td>
					<td align="center" class="texto_pb" <?php echo $style;?> >							
						<?php echo ucwords(str_replace("_"," ",$proposta->getTipoProposta())); ?>												
					</td>	
					<td align="center" class="texto_pb" <?php echo $style;?> >
						<?php echo $proposta->getNomeNac(); ?>
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
