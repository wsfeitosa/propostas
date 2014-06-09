<?php
require dirname(dirname(__FILE__)) . "/phpunit_bootstrap.php";

class Unit_Controller extends CI_Controller{
	
	public function __construct(){
		echo "IU";
	}
	
	public function index(){
		echo "IU";
	}
	
}

$controller = new Unit_Controller();

$controller->index();