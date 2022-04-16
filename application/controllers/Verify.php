<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Verify extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('Access', 'access', true);
		
	}


	public function index(){
		$id	= $this->input->get('id');
		$id = urldecode($id);
		$data	= array();
		$res['dt'] = 'Failed';
		if(!empty($id)){			
			$login = $this->access->readtable('customer','',array('_id'=>$id))->row();
			if($login->verify_email == 1){
				$res['dt'] = 'Failed';
			}else{
				$data = array('verify_email' => 1);				
				$this->access->updatetable('customer',$data, array("_id" => $id));				
				$res['dt'] = 'Congratulation';
			}
		}
		
		$this->load->view('themes/verify', $res);
	}
	
	
	
}

?>