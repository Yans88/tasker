<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		
	}	
	
	public function index() {
		
		if ($this->session->userdata('login') == FALSE) {
			redirect('/');
			return false;
		}		
		$this->data['judul_browser'] = 'Beranda';
		$this->data['judul_utama'] = 'Beranda';
		$this->data['judul_sub'] = 'Recharge';			
		$this->data['isi'] = $this->load->view('home_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function set_token(){
		$token = isset($_POST['token']) ? $_POST['token'] : '';	
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$id_admin = (int)$this->session->userdata('operator_id');
		$level = (int)$this->session->userdata('mylevel');
		$cek_token = $this->access->readtable('token_fcm_cms','',array('token' => $token))->row();
		$data = array(
			'id_admin'		=> $id_admin,
			'browser'		=> $browser,
			'mylevel'		=> $level,
			'token'			=> $token			
		);
		if(!empty($cek_token)){
			$this->access->updatetable('token_fcm_cms', $data, array('id'=>$cek_token->id));
			$save = $cek_token->id;
		}else{
			$data += array('created_at'	=> date('Y-m-d H:i:s'));
			$save = $this->access->inserttable('token_fcm_cms', $data);
		}
		$this->session->set_userdata('id_token', $save);
	}

	public function no_akses() {
		if ($this->session->userdata('login') == FALSE) {
			redirect('/');
			return false;
		}
		$this->data['judul_browser'] = 'Tidak Ada Akses';
		$this->data['judul_utama'] = 'Tidak Ada Akses';
		$this->data['judul_sub'] = '';
		$this->data['isi'] = '<div class="alert alert-danger">Anda tidak memiliki Akses.</div>';
		$this->load->view('themes/layout_utama_v', $this->data);
	}


}
