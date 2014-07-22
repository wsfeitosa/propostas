<?php 
//Carrega o serializador de taxas
$this->load->model("Adicionais/serializa_taxa");

$serializador = new Serializa_Taxa();

//Verifica se existe aprovação pendente, se sim, então ,marca o campo de aprovação em vermelho
$styleAProvacaoPendente = "";

if( $acordo->getAprovacaoPendente() == "S" )
{
	$styleAProvacaoPendente = "style = 'color:red;'";
}	
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/adicionais/adicionais/salvar",Array("id" => "consultar", "name" => "consultar")); ?>	
			<input type="hidden" name="id_acordo" id="id_acordo" value="<?php echo $acordo->getId();?>" />		
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td colspan="2" >
						Número:
					</td>
					<td colspan="2" >
						Aprovação Pendente:
					</td>
				</tr>				
				<tr>
					<td class="texto_pb" colspan="2">							
						<?php echo $acordo->getNumeroAcordo(); ?>					
					</td>
					<td class="texto_pb" colspan="2" <?php echo $styleAProvacaoPendente;?> >							
						<?php echo $acordo->getAprovacaoPendente()=="S"?"SIM":"NÃO"; ?>					
					</td>
				</tr>					
				<tr>
					<td colspan="2" width="50%">
						Inicio:
					</td>					
					<td colspan="2" width="50%">
						Validade:				
					</td>															                                                   
				</tr>
				<tr>					
					<td class="texto_pb" colspan="2">							
						<?php echo $acordo->getInicio()->format('d/m/Y'); ?>											
					</td>					
					<td class="texto_pb" colspan="2">							
						<?php echo $acordo->getValidade()->format('d/m/Y'); ?>
					</td>																					      
				</tr>
				<tr>
					<td colspan="1">
						Acordo Cadastrado Por:
					</td>					
					<td colspan="1">
						Acordo Alterado Por:			
					</td>
                    <td colspan="2">
						Última Vez Autorizado Por:			
					</td>
				</tr>							
				<tr>					
					<td class="texto_pb" colspan="1">							
						<?php echo $acordo->getUsuarioCadastro()->getNome() . " em: " . $acordo->getDataCadastro()->format('d/m/Y H:i:s'); ?>										
					</td>					
					<td class="texto_pb" colspan="1">							
						<?php
						if( ! is_null($acordo->getUsuarioAlteracao()) )
						{	 
							echo $acordo->getUsuarioAlteracao()->getNome() . " em: " . $acordo->getDataAlteracao()->format('d/m/Y H:i:s'); 
						}
						else 
						{
							echo "NÃO HOUVE ALTERAÇÃO";
						}	
						?>
					</td>
                    <td class="texto_pb" colspan="2">							
						<?php
						if( ! is_null($acordo->getUsuarioDesbloqueio()) )
						{	 
							echo $acordo->getUsuarioDesbloqueio()->getNome() . " em: " . $acordo->getDataDesbloqueio()->format('d/m/Y H:i:s'); 
						}
						else 
						{
							echo "NÃO HOUVE DESBLOQUEIO";
						}	
						?>
					</td>
				</tr>
				<tr>
					<td colspan='4' width="50%">
						Cliente(s) Selecionado(s):
					</td>																								                                                   
				</tr>
				<tr>
					<td colspan='4' class="texto_pb" >
						<?php foreach ($acordo->getClientes() as $cliente): ?>
							
							<?php echo $cliente->getCNPJ() . " - " . $cliente->getRazao() . " -> " . 
							           $cliente->getCidade()->getNome() . " | " . $cliente->getEstado()."<br />"; 
							?>
							
						<?php endforeach; ?>
					</td>									
				</tr>
				<tr>
					<td colspan="2" width="50%">Taxas:</td>
					<td>Cadastrada por:</td>
					<td>Alterada última vez por:</td>											
				</tr>
				<?php 
				foreach( $acordo->getTaxas() as $taxa )
				{
					$serializador->ConverterTaxaParaString($taxa) . "  |  Cadastrada por:  " . 
					$taxa->getDecorator('usuario_cadastro')->getNome() . "  em  :  " .
					$taxa->getDecorator('data_cadastro')->format('d/m/Y H:i:s') . "  |  Alterada última vez por:  " .
					$taxa->getDecorator('usuario_alteracao')->getNome() . "  em  :  " .
					$taxa->getDecorator('data_alteracao')->format('d/m/Y H:i:s') . "<br />";
				?>
				<tr>
					<td colspan="2" class="texto_pb">
						<?php echo $serializador->ConverterTaxaParaString($taxa);  ?>					
					</td>
					<td class="texto_pb">
						<?php  
						echo $taxa->getDecorator('usuario_cadastro')->getNome() . "  em  :  " .
							 $taxa->getDecorator('data_cadastro')->format('d/m/Y H:i:s');
						?>					
					</td>
					<td class="texto_pb">
						<?php
						echo $taxa->getDecorator('usuario_alteracao')->getNome() . "  em  :  " .
							 $taxa->getDecorator('data_alteracao')->format('d/m/Y H:i:s');
						?>
					</td>														
				</tr>	
				<?php	
				}	
				?>				
				<tr>
					<td colspan="4">Observações Internas:</td>
				</tr>
				<tr>
					<td class="texto_pb" colspan="4">
						<?php echo strtoupper(nl2br($acordo->getObservacao())); ?>						
					</td>	
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
