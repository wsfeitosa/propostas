<?php
$this->load->helper(array("html","url","form"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="EXPIRES" CONTENT="0">     
        <title><?php echo $form_title;?></title>
        <?php echo link_tag('assets/css/geral.css');?>
        <script language="javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>
        <script language="javascript" src="<?php echo base_url().'assets/js/ceps.js';?>"></script>                         
    </head>
<body>
<?php echo form_open('ceps/find');?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="left">
    <tr>        
        <td class="titulo_tabela" width="10">
            <img alt="img1" src="<?php echo base_url().'assets/img';?>/pixel.gif" width="10" height="1" border="0">
        </td>
        <td class="header1" nowrap>
            <?php echo $form_name;?>
            <img alt="img2" src="<?php echo base_url().'assets/img';?>/pixel.gif" width="10" height="1" border="0">
        </td>
        <td>
            <img alt="img3" src="<?php echo base_url().'assets/img';?>/formtab_r.gif" width="10" height="21" border="0">
        </td>
        <td class="linha_t" width="100%">&nbsp;</td>
        <td class="linha_t">
            <img alt="img4" src="<?php echo base_url().'assets/img';?>/pixel.gif" width="10" height="8" border="0">
        </td>                   
    </tr>   
    <tr>
        <td colspan="5">
        
            <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
                <tr>
                    <td>                    
                        <br>
                        <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
                            <div id="msg"><?php echo $msg;?></div>                         
                            <tr>
                                <td colspan="5" id="label_cep">
                                    Cep:
                                </td>                                                         
                            </tr>
                            <tr>
                                <td class="texto_pb" colspan="5">
                                    <?php 
                                    $data = array(
                                                  'name'        => 'cep',
                                                  'id'          => 'cep',
                                                  'value'       => '',
                                                  'maxlength'   => '9',
                                                  'size'        => '10',
                                                  'style'       => 'width:10%',
                                    );
                                    echo form_input($data);
                                    ?>
                                </td>                               
                            </tr>                                                                                       
                        </table>                        
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
            </table>
            
        </td>
    </tr>
    <tr>
        <td width="10" class="formtab_b">&nbsp;</td>
        <td class="barra_menu" colspan="5" align="right">           
            <a href="#" title="CLIQUE AQUI PARA LOCALIZAR UMA PROPOSTA !" name="salvar" id="salvar"><img src="<?php echo base_url().'assets/img';?>/system_localizar.jpg" border="0" /></a>
            <a href="#" title="CLIQUE AQUI PARA VOLTAR !"><img src="<?php echo base_url().'assets/img';?>/system_voltar.jpg" border="0" id="voltar" /></a>  
        </td>   
</table>
</form>
</body>
</html>
