<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vouchers extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {			
		if(!$this->session->userdata('login') || !$this->session->userdata('category')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Vouchers';
		$this->data['judul_utama'] = 'Vouchers';
		$this->data['judul_sub'] = 'List';
		
		$where = array();
		$where = array('deleted_at'=>null);		
		$this->data['vouchers'] = $this->access->readtable('voucher','',$where)->result_array();	
		
		$this->data['isi'] = $this->load->view('category/voucher_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function add($id=0){
		$this->data['judul_browser'] = 'Vouchers';
		$this->data['judul_utama'] = 'Vouchers';
		$this->data['judul_sub'] = 'Add/Edit';
		$this->data['vouchers'] = '';
		if($id > 0)	$this->data['vouchers'] = $this->access->readtable('voucher','',array('id'=>$id))->row();
		$this->data['isi'] = $this->load->view('category/voucher_frm', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function chk_voucher(){	
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$kode_voucher = isset($_POST['kode_voucher']) ? strtolower($_POST['kode_voucher']) : '';
		$dt = $this->access->readtable('voucher',array('id'),array('LOWER(kode_voucher)'=>$kode_voucher,'deleted_at'=>null))->row();
		$dt_cnt = !empty($dt) && $id != (int)$dt->id ? (int)$dt->id : 0;
		echo $dt_cnt;
	}
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id' => $_POST['id']
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('voucher', $data, $where);
	}

	
	public function simpan_voucher(){
		$tgl = date('Y-m-d H:i:s');
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;		
		$tipe = isset($_POST['tipe']) ? (int)$_POST['tipe'] : 0;		
		$kd_vc = isset($_POST['kd_vc']) ? $_POST['kd_vc'] : '';
		$exp_date = isset($_POST['exp_date']) ? date('Y-m-d', strtotime($_POST['exp_date'])) : '';
		$potongan = isset($_POST['potongan']) ? str_replace('.','',$_POST['potongan']) : '';
		$maks_potongan = isset($_POST['maks_potongan']) ? str_replace('.','',$_POST['maks_potongan']) : '';		
		$deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
		$config = array();
		$config['upload_path']   = FCPATH.'/uploads/vouchers/';
        $config['allowed_types'] = 'gif|jpg|png|ico|jpeg';
		$config['max_size']	= '2048';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
		$gambar="";	
		if($tipe == 2) $potongan = 100;
		$simpan = array(			
			'kode_voucher'		=> $kd_vc,								
			'nilai_potongan'	=> $potongan,							
			'maks_potongan'		=> $maks_potongan,				
			'expired_date'		=> $exp_date,				
			'tipe'				=> $tipe,				
			'deskripsi'			=> $deskripsi			
		);
		if(!$this->upload->do_upload('userfile')){
            $gambar="";
        }else{
            $gambar=$this->upload->file_name;
			$simpan += array('img'	=> $gambar);
        }
		
		$where = array();
		$save = 0;	
		if($id > 0){
			$where = array('id'=>$id);
			$simpan += array('updated_by'	=> $this->session->userdata('operator_id'));
			$save = $this->access->updatetable('voucher', $simpan, $where);   
		}else{
			$simpan += array('created_at' => $tgl,'created_by' => $this->session->userdata('operator_id'));
			$save = $this->access->inserttable('voucher', $simpan);   
		}  
		
		redirect(site_url('vouchers'));
	}
	
	
	public function publish(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id' => $_POST['id']
		);
		$data = array(
			'publish_by'	=> $this->session->userdata('operator_id'),
			'is_publish'	=> $tgl
		);
		echo $this->access->updatetable('voucher', $data, $where);
	}
	
	public function no_akses() {
		if ($this->session->userdata('login') == FALSE) {
			redirect('/');
			return false;
		}
		$this->data['judul_browser'] = 'Tidak Ada Akses';
		$this->data['judul_utama'] = 'Tidak Ada Akses';
		$this->data['judul_sub'] = '';
		$this->data['isi'] = '<div class="alert alert-danger">Anda tidak memiliki Akses.</div><div class="error-page">
        <h3 class="text-red"><i class="fa fa-warning text-yellow"></i> Oops! No Akses.</h3></div>';
		$this->load->view('themes/layout_utama_v', $this->data);
	}


}
