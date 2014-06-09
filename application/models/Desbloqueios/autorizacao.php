<?php
interface Autorizacao{
	public function verificar_autorizacao_desbloqueio( Usuario $usuario );
	public function autorizar_desbloqueio( Desbloqueio_Entity $entity );	
}
