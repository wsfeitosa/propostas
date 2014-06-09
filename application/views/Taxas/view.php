<?php
$changer = new Formata_Taxa();
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>					
			<input type="hidden" value="<?php echo $acordo->getId();?>" name="id_acordo" id="id_acordo" />	
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td>
						Número:
					</td>
					<td>
						Sentido:
					</td>
				</tr>
				<tr>
					<td class="texto_pb">							
						<?php echo $acordo->getNumero(); ?>							
					</td>
					<td class="texto_pb">
						<?php echo $acordo->getSentido(); ?>
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
						<?php echo $acordo->getInicio()->format("d/m/Y"); ?>
					</td>					
					<td class="texto_pb">							
						<?php echo $acordo->getValidade()->format("d/m/Y"); ?>
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
						<?php foreach($acordo->getClientes() as $cliente):?>
								<?php echo $cliente->getCnpj()." - ".$cliente->getRazao()."<br />"; ?>	
						<?php endforeach;?>
					</td>
					<td class="texto_pb">
						<?php foreach($acordo->getPortos() as $porto):?>
								<?php echo $porto->getNome()."<br />"; ?>	
						<?php endforeach;?>
					</td>					
				</tr>
				<tr>
					<td colspan="1" width="50%">Taxas:</td>
					<td colspan="2">Observações Internas:</td>						
				</tr>
				<tr>
					<td colspan="1" class="texto_pb">
						<?php foreach($acordo->getTaxas() as $taxa):?>
								<?php echo $changer->formatarLabelTaxa($taxa)."<br />"; ?>	
						<?php endforeach;?>
					</td>
					<td class="texto_pb" colspan="2">
						<?php echo nl2br($acordo->getObservacao());?>						
					</td>										
				</tr>
				<tr>
					<td width="50%">
						Cadastrado Por:
					</td>
					<td>
						Alterado Última Vez Por:
					</td>																			                                                   
				</tr>
				<tr>
					<td class="texto_pb" >
						<?php echo $acordo->getUsuarioInclusao()->getNome(). " | ".$acordo->getDataInclusao()->format("d-m-Y H:i:s");?>
					</td>
					<td class="texto_pb">
						<?php echo $acordo->getUsuarioAlteracao()->getNome() == '' ? "Não Há" : $acordo->getUsuarioAlteracao()->getNome(). " | ".$acordo->getDataAlteracao()->format("d-m-Y H:i:s");?>
					</td>					
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
