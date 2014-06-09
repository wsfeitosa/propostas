<?php
class Relatorio_Adapter{
	
	const path = "/var/www/html/allink/relatorios_temp";
	
	private $relatorio = NULL;
	private $formato = NULL;
	private $path = NULL;
	
	public function __construct()
	{
		
	}
	
	public function gerarRelatorio( Relatorio $relatorio, Formato $formato, Layout $layout )
	{
		
		try{
						
			$this->path = $formato->aplicarFormato($layout, $relatorio);							
			
		} catch (RuntimeException $re) {
			
			log_message('error',$re->getMessage());
			show_error($re->getMessage());
			
		} catch (Exception $e){
			
			log_message('error',$e->getMessage());
			show_error($e->getMessage());
			
		}
		
		
				
	}
	
	public function exportar()
	{
				
		echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
              <html>
                <head>
                    <title>Allink</title>
                </head>
                <body>
                    <script type='text/JavaScript'>
						window.open('".$this->path."','','height=300,width=200,left='+(screen.width-200)/2+',top='+(screen.height-300)/2+',scrollbars=yes,location=no,toolbar=no,menubar=no,resizeable=yes');
						window.close(self);
					</script>
                </body>
              </html>";
		
	}
	
}