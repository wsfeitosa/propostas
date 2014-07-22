<?php
$clientes_selecionados = Array();

foreach( $acordo->getClientes() as $cliente )
{
	$clientes_selecionados[$cliente->getId()] = $cliente->getCnpj()." - ".$cliente->getRazao();
}	

$portos_selecionados = Array();

foreach( $acordo->getPortos() as $porto )
{
	$portos_selecionados[] = $porto->getId();
}	

$taxas_selecionadas = Array();

foreach( $acordo->getTaxas() as $taxa )
{
	$changer = new Formata_Taxa();
	
	$taxas_selecionadas[$changer->formatarValueTaxa($taxa, ";")] = $changer->formatarLabelTaxa($taxa);
}

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/taxas_locais/taxas_locais/save",Array("id" => "nova", "name" => "nova")); ?>	
			<input type="hidden" name="id_acordo" id="id_acordo" value="<?php echo $acordo->getId(); ?>" />	
			<input type="hidden" name="sentido" id="sentido" value="<?php echo $acordo->getSentido();?>" />	
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
						Cadastrado Por:
					</td>
				</tr>
				<tr>
					<td class="texto_pb">							
						<?php echo $acordo->getNumero();?>						
					</td>
					<td class="texto_pb">
						<?php echo $acordo->getUsuarioInclusao()->getNome(). " | ".$acordo->getDataInclusao()->format("d-m-Y H:i:s");?>
					</td>
				</tr>
                <tr>
					<td>
						Alterado Última Vez Por:
					</td>
					<td>
						Desbloqueado Última Vez Por:
					</td>
				</tr>
                <tr>
					<td class="texto_pb" >
						<?php echo $acordo->getUsuarioAlteracao() == NULL ? "Não Há" : $acordo->getUsuarioAlteracao()->getNome(). " EM ".$acordo->getDataAlteracao()->format("d/m/Y H:i:s");?>
					</td>
					<td class="texto_pb">
						<?php echo $acordo->getUsuarioDesbloqueio() == NULL ? "Não Houve Debloqueio" : $acordo->getUsuarioDesbloqueio()->getNome(). " EM ".$acordo->getDataDesbloqueio()->format("d/m/Y H:i:s");?>
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
						<input type="text" name="inicio" id="inicio" readonly="readonly" value="<?php echo $acordo->getInicio()->format('d-m-Y');?>" />											
					</td>					
					<td class="texto_pb">							
						<input type="text" name="validade" id="validade" readonly="readonly" value="<?php echo $acordo->getValidade()->format('d-m-Y');?>" />
					</td>																					      
				</tr>
				<tr>
					<td>
						Sentido:
					</td>					
					<td>
						Cliente:				
					</td>															                                                   
				</tr>							
				<tr>					
					<td class="texto_pb">							
						<?php echo $acordo->getSentido() == "IMP" ? "IMPORTAÇÃO" : "EXPORTAÇÃO"; ?>												
					</td>					
					<td class="texto_pb">							
						<?php echo form_input(Array( 'name' => 'cliente', 'id' => 'cliente', 'value' => '' )); ?>
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
						<?php echo form_dropdown("clientes_selecionados[]", $clientes_selecionados, "", "id='clientes_selecionados' multiple='multiple' size='5'"); ?>
						<input type="button" name="excluir_cliente" id="excluir_cliente" value=" - " />
					</td>
					<td class="texto_pb">
						<?php echo form_multiselect("portos_selecionados[]", $portos, $portos_selecionados, "id='portos_selecionados' size='5'" );?>
					</td>					
				</tr>
				<tr>
					<td colspan="1" width="50%">Taxas:</td>
					<td colspan="2">Observações Internas:</td>						
				</tr>
				<tr>
					<td colspan="1" class="texto_pb">
						<?php echo form_multiselect("taxas_selecionadas[]", $taxas_selecionadas, "", "id='taxas_selecionadas' size='5'" );?>
						<input type="button" name="incluir_taxa" id="incluir_taxa" value=" + " />						
					</td>
					<td class="texto_pb" colspan="2">
						<?php echo form_textarea(Array('name' => 'observacao_interna', 'id' => 'observacao_interna', 'value' => nl2br($acordo->getObservacao()), 'rows' => 8)); ?>						
					</td>										
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
