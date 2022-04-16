<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {			
		if(!$this->session->userdata('login') || !$this->session->userdata('faq')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'FAQ';
		$this->data['judul_utama'] = 'FAQ';
		$this->data['judul_sub'] = 'List';
		$this->data['faq'] = $this->access->readtable('faq','',array('deleted_at'=>null))->result_array();
		$this->data['isi'] = $this->load->view('faq_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_faq' => $_POST['id']
		);
		$data = array(
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('faq', $data, $where);
	}
	
	public function simpan(){
		$tgl = date('Y-m-d');
		$id_faq = isset($_POST['id_faq']) ? (int)$_POST['id_faq'] : 0;
		$answer = isset($_POST['answer']) ? $_POST['answer'] : '';
		$question = isset($_POST['question']) ? $_POST['question'] : '';		
		$simpan = array(			
			'answer'	=> $answer,
			'question'	=> $question
		);
		$where = array();
		$save = 0;	
		if($id_faq > 0){
			$where = array('id_faq'=>$id_faq);
			$save = $this->access->updatetable('faq', $simpan, $where);   
		}else{
			$simpan += array('create_at'	=> $tgl);
			$save = $this->access->inserttable('faq', $simpan);   
		}  
		echo $save;	 
	}
	
	public function no_akses() {
		if ($this->session->userdata('login') == FALSE) {
			redirect('/');
			return false;
		}
		$this->data['judul_browser'] = 'Tidak Ada Akses';
		$this->data['judul_utama'] = 'Tidak Ada Akses';
		$this->data['judul_sub'] = '';
		$this->data['isi'] = '<br/><div class="alert alert-danger" style="margin-left:0;">Anda tidak memiliki Akses.</div><div class="error-page">
        <h3 class="text-red"><i class="fa fa-warning text-yellow"></i> Oops! No Akses.</h3></div>';
		$this->load->view('themes/layout_utama_v', $this->data);
	}


}
