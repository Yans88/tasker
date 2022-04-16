<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Premium extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}		
		$this->data['judul_browser'] = 'Master Data Premium';
		$this->data['judul_utama'] = 'Master Data';
		$this->data['judul_sub'] = 'Premium';
		
		$where = array();
		$where = array('deleted_at'=>null);		
		$this->data['coin'] = $this->access->readtable('master_premium','',$where)->result_array();
		$this->data['isi'] = $this->load->view('category/premium_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_premium' 	=> $_POST['id']
			
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('master_premium', $data, $where);
	}

	
	public function simpan_cat(){
		$tgl = date('Y-m-d H:i:s');
		$id_premium = isset($_POST['id_premium']) ? (int)$_POST['id_premium'] : 0;		
		$jml_hari = isset($_POST['jml_hari']) ? str_replace(',','',$_POST['jml_hari']) : '';
		$nominal = isset($_POST['nominal']) ? str_replace(',','',$_POST['nominal']) : '';
		$simpan = array();
		$simpan = array(
			'jml_hari'	=> str_replace('.','',$jml_hari),
			'nominal'	=> str_replace('.','',$nominal)
		);	
		
		$where = array();
		$save = 0;	
		if($id_premium > 0){
			$where = array('id_premium'=>$id_premium);
			$simpan += array('update_by'=>$this->session->userdata('operator_id'));
			$this->access->updatetable('master_premium', $simpan, $where);   
			$save = $id_premium;   
		}else{
			$simpan += array('created_at'	=> $tgl,'created_by'=>$this->session->userdata('operator_id'));
			$save = $this->access->inserttable('master_premium', $simpan);   
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
