<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coin extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {		
		if(!$this->session->userdata('login') || !$this->session->userdata('m_coin')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Master Data Coin';
		$this->data['judul_utama'] = 'Master Data';
		$this->data['judul_sub'] = 'Coin';
		
		$where = array();
		$where = array('deleted_at'=>null);		
		$this->data['coin'] = $this->access->readtable('master_coin','',$where)->result_array();
		$this->data['isi'] = $this->load->view('category/coin_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_coin' 	=> $_POST['id']
			
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('master_coin', $data, $where);
	}

	
	public function simpan_cat(){
		$tgl = date('Y-m-d H:i:s');
		$id_coin = isset($_POST['id_coin']) ? (int)$_POST['id_coin'] : 0;		
		$jml_coin = isset($_POST['jml_coin']) ? str_replace(',','',$_POST['jml_coin']) : '';
		$nominal = isset($_POST['nominal']) ? str_replace(',','',$_POST['nominal']) : '';
		$deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
		$simpan = array();
		$simpan = array(
			'jml_coin'	=> str_replace('.','',$jml_coin),
			'nominal'	=> str_replace('.','',$nominal),
			'deskripsi'	=> $deskripsi
		);	
		
		$where = array();
		$save = 0;	
		if($id_coin > 0){
			$where = array('id_coin'=>$id_coin);
			$simpan += array('update_by'=>$this->session->userdata('operator_id'));
			$this->access->updatetable('master_coin', $simpan, $where);   
			$save = $id_coin;   
		}else{
			$simpan += array('created_at'	=> $tgl,'created_by'=>$this->session->userdata('operator_id'));
			$save = $this->access->inserttable('master_coin', $simpan);   
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
