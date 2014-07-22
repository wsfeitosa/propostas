<style>
.ui-button-text{
	font-size: 11px;
}
</style>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/adicionais/adicionais/salvar",Array("id" => "nova", "name" => "nova")); ?>
			<input type="hidden" name="id_acordo" id="id_acordo" value="<?php echo $acordo->getId();?>"/>		
			<input type="hidden" name="sentido" id="sentido" value="EXP"/>	
			<input type="hidden" name="alterar_retroativos" id="alterar_retroativos" value="N"/>
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>  
				<div id="dialog-confirm" title="Influenciar Retroativos?">
					<p style="font-size: 12px; color: black;"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Deseja validar esse acordo para todas as propostas existentes ou apenas para as novas ?</p>
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
						<?php echo $acordo->getNumeroAcordo(); ?>						
					</td>
                    <td class="texto_pb">							
						<?php echo $acordo->getUsuariocadastro()->getNome() . " EM " . $acordo->getDataCadastro()->format('d/m/Y H:i:s'); ?>						
					</td>
				</tr>
                <tr>
					<td>
						Alterado Última Vez Por:
					</td>
                    <td>
                        Desbloqueado Última Pez Por:
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
						<input type="text" title="Data em que o acordo começará a valer efetivamente" name="inicio" id="inicio" readonly="readonly" value="<?php echo $acordo->getInicio()->format('d-m-Y');?>" />											
					</td>					
					<td class="texto_pb">							
						<input type="text" title="Data limite em que o acordo será válido" name="validade" id="validade" readonly="readonly" value="<?php echo $acordo->getValidade()->format('d-m-Y');?>" />
					</td>																					      
				</tr>
				<tr>										
					<td colspan="4">
						Cliente:				
					</td>															                                                   
				</tr>							
				<tr>									
					<td colspan="4" class="texto_pb">							
						<?php echo form_input(Array( 'name' => 'cliente', 'id' => 'cliente', 'value' => '', 'size' => '50', "title" => "Informe o nome do cliente, ou parte dele para buscar pelo cliente.")); ?>
						<input type="button" name="pesquisar_cliente" id="pesquisar_cliente" value="Pesquisar" />
					</td>																					      
				</tr>
				<tr>
					<td colspan='2' width="50%">
						Cliente(s) Selecionado(s):
					</td>																								                                                   
				</tr>
				<tr>
					<td colspan='2' class="texto_pb" >
						<?php echo form_dropdown("clientes_selecionados[]", $combo_clientes, "", "id='clientes_selecionados' multiple='multiple' size='5'"); ?>
						<input type="button" title="excluir o cliente marcado da lista de clientes selecionados" name="excluir_cliente" id="excluir_cliente" value=" - " />
					</td>									
				</tr>
				<tr>
					<td colspan="1" width="50%">Taxas:</td>
					<td colspan="2">Observações Internas:</td>						
				</tr>
				<tr>
					<td colspan="1" class="texto_pb">
						<?php echo form_multiselect("taxas_selecionadas[]", $combo_taxas, "", "id='taxas_selecionadas' size='5'" );?>
						<input type="button" title="inclui uma nova taxa no acordo do cliente." name="incluir_taxa" id="incluir_taxa" value=" + " />
						<input type="button" title="exclui a taxa marcada do acordo." name="excluir_taxa" id="excluir_taxa" value=" - " />						
					</td>
					<td class="texto_pb" colspan="2">
						<?php echo form_textarea(Array('name' => 'observacao_interna', 'id' => 'observacao_interna', 'value' => $acordo->getObservacao(), 'rows' => 8, 'title' => "Permite escrever uma observação interna, não será visualizada pelo cliente.")); ?>						
					</td>										
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
