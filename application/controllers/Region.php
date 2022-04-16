<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Region extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {	
		if(!$this->session->userdata('login') || !$this->session->userdata('category')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Region';
		$this->data['judul_utama'] = 'Region';
		$this->data['judul_sub'] = 'List';
		
		$where = array();
		$where = array('deleted_at'=>null);		
		$this->data['region'] = $this->access->readtable('region','',$where)->result_array();
		$this->data['isi'] = $this->load->view('region/region_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function city($region=0) {	
		if(!$this->session->userdata('login') || !$this->session->userdata('category')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'City';
		$this->data['judul_utama'] = 'City';
		$this->data['judul_sub'] = 'List';
			
		$_region = $this->access->readtable('region','',array('id_region'=>$region,'deleted_at'=>null))->row();
		
		$where = array('deleted_at'=>null,'region'=>$region);		
		$this->data['region'] = $this->access->readtable('city','',$where)->result_array();
		$this->data['id_region'] = $region;
		$this->data['region_name'] = $_region->region_name;
		$this->data['isi'] = $this->load->view('region/city_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_region' 	=> $_POST['id']
			
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('region', $data, $where);
	}

	
	public function simpan(){
		$tgl = date('Y-m-d H:i:s');
		$id_region = isset($_POST['id_region']) ? (int)$_POST['id_region'] : 0;		
		$region = isset($_POST['region']) ? ucwords($_POST['region']) : '';		
		$simpan = array(			
			'region_name'		=> $region
		);
		
		$where = array();
		$save = 0;	
		if($id_region > 0){
			$where = array('id_region'=>$id_region);
			// $simpan += array('updated_by'=>$this->session->userdata('operator_id'));
			$this->access->updatetable('region', $simpan, $where);   
			$save = $id_region;   
		}else{
			$simpan += array('created_at' => $tgl,'created_by'	=> $this->session->userdata('operator_id'));
			$save = $this->access->inserttable('region', $simpan);   
		}  
		echo $save;
	}
	
	public function simpan_city(){
		$tgl = date('Y-m-d H:i:s');
		$id_region = isset($_POST['id_region']) ? (int)$_POST['id_region'] : 0;		
		$id_city = isset($_POST['id_city']) ? (int)$_POST['id_city'] : 0;		
		$city = isset($_POST['city']) ? ucwords($_POST['city']) : '';		
		$simpan = array(			
			'nama_city'		=> $city
		);
		
		$where = array();
		$save = 0;	
		if($id_city > 0){
			$where = array('id_city'=>$id_city);
			// $simpan += array('updated_by'=>$this->session->userdata('operator_id'));
			$this->access->updatetable('city', $simpan, $where);   
			$save = $id_city;   
		}else{
			$simpan += array('created_at' => $tgl,'created_by'	=> $this->session->userdata('operator_id'),'region'=>$id_region);
			$save = $this->access->inserttable('city', $simpan);   
		}  
		echo $save;
	}
	
	public function del_city(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_city' 	=> $_POST['id']
			
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('city', $data, $where);
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
