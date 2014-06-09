<?php 

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
                <tr>
                    <td>                    
                        <br>
                        <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">                         
                            <div id="msg"><?php echo $msg;?></div>
                            
                            <tr>
                                <td width="20%" colspan="1">
                                    Cep:
                                </td>
                                <td width="20%" colspan="1">
                                    Endereço:
                                </td>
                                <td width="20%" colspan="1">
                                    Cidade:
                                </td>
                                <td width="20%" colspan="1">
                                    Estado:
                                </td>
                                <td width="20%" colspan="1">
                                    Bairro:
                                </td>                                                         
                            </tr>
                            <?php 
                            foreach($results as $cep){
                            ?>
                            <tr>
                                <td class="texto_pb" colspan="1">
                                    <a href="#" onclick="window.location = 'http://localhost/ceps/clientes/listClientes/<?php echo $cep['endereco_cep'];?>'">
                                        <?php echo utf8_decode($cep['endereco_cep']);?>
                                    </a>     
                                </td>
                                <td class="texto_pb" colspan="1">
                                    <?php echo utf8_decode($cep['endereco_logradouro']);?>     
                                </td>
                                <td class="texto_pb" colspan="1">
                                    <?php echo utf8_decode($cep['cidade_descricao']);?>     
                                </td> 
                                <td class="texto_pb" colspan="1">
                                    <?php echo utf8_decode($cep['estado']);?>     
                                </td> 
                                <td class="texto_pb" colspan="1">
                                    <?php echo utf8_decode($cep['bairro_descricao']);?>     
                                </td>                              
                            </tr>                            
                            <?php 
                            }                            
                            ?>                                                                                       
                        </table>                        
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
            </table> 
