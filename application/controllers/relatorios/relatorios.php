<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Relatorios extends CI_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->output->enable_profiler(TRUE);
    }

    public function index() 
    {
        
    }

    public function propostas_customer()
    {
    	$this->load->model('Relatorios/Propostas/propostas_x_customer');

    	$relatorio = new Propostas_x_Customer();

    	$relatorio->Gerar();
    }

}
