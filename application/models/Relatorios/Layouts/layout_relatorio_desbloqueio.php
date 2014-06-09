<?php
include APPPATH."/models/Relatorios/Layouts/layout.php";

class Layout_Relatorio_Desbloqueio implements Layout {
		
	public function aplicarLayout( Relatorio $relatorio )
	{
		$relatorio->gerar();
		 		
		$layout = "
						<table border='1'>
							<tr>
								<td colspan='4' align='center'>".$relatorio->obterNome()."</td>
							<tr>
							<tr>
								<td>Nome:</td>
								<td>Login:</td>
								<td>Password:</td>
								<td>Cargo:</td>						
							</tr>";

		foreach( $relatorio->obterDadosRelatorio() as $row )
		{
			$layout .="<tr>
					   		<td>".$row->nome."</td>
					   		<td>".$row->usuario."</td>
					   		<td>".$row->senha."</td>
					   		<td>".$row->cargo."</td>						
					   </tr>";
		}	
					
		$layout .= "</table>";
			
		return $layout;		
		
	}
	
}//END CLASS