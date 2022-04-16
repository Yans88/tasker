<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {			
		if(!$this->session->userdata('login') || !$this->session->userdata('category')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Category';
		$this->data['judul_utama'] = 'Category';
		$this->data['judul_sub'] = 'List';
		
		$where = array();
		$where = array('deleted_at'=>null);
		$this->data['tipe'] = 1;
		$this->data['category'] = $this->access->readtable('kategori','',$where)->result_array();
		
		$this->data['isi'] = $this->load->view('category/category_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_kategori' => $_POST['id']
		);
		$data = array(
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('kategori', $data, $where);
	}
	
	public function simpan_cat(){
		$tgl = date('Y-m-d H:i:s');
		$id_category = isset($_POST['id_category']) ? (int)$this->converter->decode($_POST['id_category']) : 0;		
		$category = isset($_POST['category']) ? $_POST['category'] : '';
		
		$config['upload_path']   = FCPATH.'/uploads/kategori/';
        $config['allowed_types'] = 'gif|jpg|png|ico|jpeg';
		$config['max_size']	= '2048';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
		$gambar="";	
		$simpan = array(			
			'nama_kategori'		=> $category			
					
		);
		if(!$this->upload->do_upload('userfile')){
            $gambar="";
        }else{
            $gambar=$this->upload->file_name;
			$simpan += array('img'	=> $gambar);
        }	
		$where = array();
		$save = 0;	
		if($id_category > 0){
			$where = array('id_kategori'=>$id_category);
			$save = $this->access->updatetable('kategori', $simpan, $where);   
		}else{
			$simpan += array('created_at' => $tgl);
			$save = $this->access->inserttable('kategori', $simpan);   
		} 
		redirect(site_url('category'));
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
