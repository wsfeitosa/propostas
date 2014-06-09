<?php
interface Database_Operations {
	public function save( Entity $bean );
	public function findById( Entity $bean );
	public function update( Entity $bean );
	public function delete( Entity $bean );
}