<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
    <tr>
        <td>                    
            <br>				
            <?php echo form_open("index.php/propostas/propostas/salvar", Array("id" => "form_consulta", "name" => "form_consulta")); ?>
            <input type="hidden" name="sentido" id="sentido" value="<?php echo $sentido; ?>" />			
            <input type="hidden" name="tipo_proposta" id="tipo_proposta" value="proposta_especial" />
            <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
                <div id="pop">
                    <a href="#" onclick="document.getElementById('frame').src = '/Clientes/propostas/index.php/loading/'; document.getElementById('pop').style.display='none';">[Fechar]</a>											
                    <iframe id="frame" name="frame" src="/Clientes/propostas/index.php/loading/" frameborder="0" width="100%" height="95%" src="#"></iframe>
                </div>       
                <div id="msg"></div>                  
                <tr>
                    <td colspan="2" id="label_tipop" width="50%">
                        Cliente:
                    </td>
                    <td colspan="2" id="label_sentido" width="50%">
                        Contatos CC:
                    </td>															                                                   
                </tr>					
                <tr>
                    <td class="texto_pb" colspan="2">							
                        <?php echo form_input(Array("name" => "cliente", "id" => "cliente")); ?>							
                    </td>
                    <td class="texto_pb" colspan="2">							
                        <?php echo form_input(Array("name" => "contato_cc", "id" => "contato_cc")); ?>	
                        <input type="button" name="adicionar_contato_cc" id="adicionar_contato_cc" value="+" />						
                    </td>																					      
                </tr>	
                <tr>
                    <td colspan="2" id="label_tipop" width="50%">
                        Clientes Selecionados:
                    </td>
                    <td colspan="2" id="label_sentido" width="50%">
                        Contatos CC Selecionados:
                    </td>															                                                   
                </tr>					
                <tr>
                    <td class="texto_pb" colspan="2">							
                        <?php echo form_dropdown("clientes_selecionados[]", Array(), "", "id='clientes_selecionados' multiple='multiple' size='5'"); ?>
                        <input type="button" name="remover_cliente" id="remover_cliente" value="-" />							
                    </td>
                    <td class="texto_pb" colspan="2">							
                        <?php echo form_dropdown("contatos_cc_selecionados[]", Array(), "", "id='contatos_cc_selecionados' multiple='multiple' size='5'"); ?>	
                        <input type="button" name="remover_contato_cc" id="remover_contato_cc" value="-" />						
                    </td>																					      
                </tr>
                <tr>
                    <td colspan="4">
                        Contatos Emails Para:
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="texto_pb">
                        <?php echo form_dropdown("contatos_para_selecionados[]", Array(), "", "id='contatos_para_selecionados' multiple='multiple' size='5'"); ?>	
                        <input type="button" name="adicionar_email_avulso" id="adicionar_email_avulso" value=" ? " />
                        <input type="button" name="remover_contato_para" id="remover_contato_para" value="-" />
                    </td>
                </tr>	
                <tr>
                    <td colspan="4" class="titulo_tabela">
                        Dados Da Rota
                        <input type="hidden" name="id_tarifario" id="id_tarifario" value="" />
                        <input type="hidden" name="posicao_combo" id="posicao_combo" value="" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Inicio:</td>                    
                    <td colspan="2">Validade(Embarque):</td>					
                </tr>
                <td class="texto_pb" colspan="2">
                        <?php echo form_input(Array('name' => 'inicio', 'id' => 'inicio', 'size' => '14', 'value' => date('d-m-Y'), 'readonly' => 'readonly')); ?>
                </td>
                <td class="texto_pb" colspan="2">
                        <?php echo form_input(Array('name' => 'validade', 'id' => 'validade', 'size' => '14', 'value' => date('t-m-Y'), 'readonly' => 'readonly')); ?>
                </td>				
                <tr>
                    <td>Mercadoria:</td>
                    <td>Modalidade:</td>
                    <td>Peso(Kg) - Cubagem(M3) - Volumes:</td>
                    <td>Carga Imo</td>					
                </tr>		
                <tr>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "mercadoria", "id" => "mercadoria")); ?>
                    </td>
                    <td class="texto_pb">
                        <?php echo form_checkbox(Array('name' => 'pp', 'id' => 'pp', 'value' => 'PP')); ?>PP
                        <?php echo form_checkbox(Array('name' => 'cc', 'id' => 'cc', 'value' => 'CC')); ?>CC
                    </td>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "peso", "id" => "peso", "size" => "10")); ?> -
                        <?php echo form_input(Array("name" => "cubagem", "id" => "cubagem", "size" => "10")); ?> -
                        <?php echo form_input(Array("name" => "volumes", "id" => "volumes", "size" => "5")); ?>
                    </td>
                    <td class="texto_pb">
                        <?php echo form_checkbox(Array("name" => "imo", "id" => "imo")); ?>
                    </td>
                </tr>
                <tr>
                    <td>Origem:</td>
                    <td>Embarque:</td>
                    <td>Desembarque:</td>
                    <td>Destino:</td>
                </tr>	
                <tr>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "origem", "id" => "origem")); ?>
                        <input type="hidden" name="un_origem" id="un_origem" value="" />
                    </td>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "embarque", "id" => "embarque")); ?>
                        <input type="hidden" name="un_embarque" id="un_embarque" value="" />
                    </td>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "desembarque", "id" => "desembarque")); ?>
                        <input type="hidden" name="un_desembarque" id="un_desembarque" value="" />
                    </td>
                    <td class="texto_pb">
                        <?php echo form_input(Array("name" => "destino", "id" => "destino")); ?>
                        <input type="hidden" name="un_destino" id="un_destino" value="" />
                    </td>
                </tr>	
                <tr>
                    <td colspan="2">Frete e Adicionais:</td>
                    <td colspan="1">Taxas Locais:</td>
                </tr>
                <tr>
                    <td colspan="2" class="texto_pb">
                        <?php echo form_dropdown("frete_adicionais[]", Array(), "", "id='frete_adicionais' multiple='multiple' size='13'"); ?>
                        <input type="button" name="incluir_taxa" id="incluir_taxa" value=" + " />	
                        <input type="button" name="excluir_taxa" id="excluir_taxa" value=" Somente Taxas Locais " />					
                    </td>
                    <td colspan="1" class="texto_pb">
                        <?php echo form_dropdown("taxas_locais[]", Array(), "", "id='taxas_locais' multiple='multiple' size='13'"); ?>
                        <input type="button" name="incluir_taxa_local" id="incluir_taxa_local" value=" + " />						
					<td class="texto_pb">	
                        <input type="button" name="incluir_rota" id="incluir_rota" value="Adicionar Item" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Observação Interna</td>
                    <td colspan="2">Observação Para o Cliente</td>
                </tr>
                <tr>
                    <td colspan="2" class="texto_pb">
                        <?php echo form_textarea(Array('name' => 'observacao_interna', 'id' => 'observacao_interna', 'value' => "", 'rows' => 5)); ?>
                    </td>
                    <td colspan="2" class="texto_pb">
                        <?php echo form_textarea(Array('name' => 'observacao_cliente', 'id' => 'observacao_cliente', 'value' => "", 'rows' => 5)); ?>
                    </td>
                </tr>	
                <tr>
                    <td colspan="4" class="titulo_tabela">
                        &nbsp;
                    </td>
                </tr>	
                <tr>
                    <td colspan="4">Rotas Adicionadas:</td>
                </tr>
                <tr>
                    <td colspan="4" class="texto_pb">
                        <?php echo form_dropdown("rotas_adicionadas[]", Array(), "", "id='rotas_adicionadas' multiple='multiple' size='5'"); ?>
                        <input type="button" name="remover_rota" id="remover_rota" value="-" />
                    </td>
                </tr>		                                                                                     
            </table>                        
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF">&nbsp;</td>
    </tr>			    
</table>
