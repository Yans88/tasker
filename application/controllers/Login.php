<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	// public $data = array ('pesan' => '');
	
	public function __construct () {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Login_m','login', TRUE);
	}
	
	public function index() {
		// status user login = BENAR, pindah ke halaman home
		$this->data = '';
		if ($this->session->userdata('login') == TRUE) {
			// redirect('product');
			redirect(base_url('user'));
		} else {
			// status login salah, tampilkan form login
			// validasi sukses
			if($this->login->validasi()) {
				// cek di database sukses
				if($this->login->cek_user()) {
					redirect('user');
				} else {
					// cek database gagal
					$this->data['pesan'] = 'Username atau Password salah.';
				}
			} else {
				// validasi gagal
         }
        
         $this->load->view('themes/login_form_v', $this->data);
		}
	}
	
	// function test(){
		// redirect('home');
	// }

	public function logout() {
		// $this->login->logout();
		$this->session->sess_destroy();
		$this->session->unset_userdata(array('u_name' => '', 'login' => FALSE));
		
		redirect('/');
	}
}