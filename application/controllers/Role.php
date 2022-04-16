<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);					
	}	
	
	public function index() {
		if(!$this->session->userdata('login') || !$this->session->userdata('role')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Level-Role';
		$this->data['judul_utama'] = 'Level-Role';
		$this->data['judul_sub'] = 'List';
		$this->data['title_box'] = 'List of Level';
		$this->data['level'] = $this->access->readtable('level','',array('level.deleted_at'=>null))->result_array();			
		$this->data['isi'] = $this->load->view('level_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function add($id_level=null) {
		if(!$this->session->userdata('login') || !$this->session->userdata('role')){
			$this->no_akses();
			return false;
		}
		$this->data['level'] = '';	
		$this->data['judul_browser'] = 'Level-Role';
		$this->data['judul_utama'] = 'Level-Role';
		$this->data['judul_sub'] = 'List';
		$this->data['title_box'] = 'List of Level';
		if(!empty($id_level)){
			$this->data['level'] = $this->access->readtable('level','',array('deleted_at'=>null,'id'=>$id_level))->row();
		}
		
		$this->data['isi'] = $this->load->view('level_frm', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function save(){		
		if(!$this->session->userdata('login') || !$this->session->userdata('role')){
			$this->no_akses();
			return false;
		}
		
		$save =0;
		$id_level = isset($_POST['id_level']) ? (int)$_POST['id_level'] : '0';		
		$level_name = isset($_POST['level_name']) ? $_POST['level_name'] : '';				
		$customer = isset($_POST['customer']) ? (int)$_POST['customer'] : '0';
		$category = isset($_POST['category']) ? (int)$_POST['category'] : '0';
		$m_coin = isset($_POST['m_coin']) ? (int) $_POST['m_coin'] : '0';
		$m_premium = isset($_POST['m_premium']) ? (int)$_POST['m_premium'] : '0';
		$faq = isset($_POST['faq']) ? (int)$_POST['faq'] : '0';
		$setting = isset($_POST['setting']) ? (int)$_POST['setting'] : '0';
		
		$level = isset($_POST['level']) ? (int)$_POST['level'] : '0';
		$users = isset($_POST['users']) ? (int)$_POST['users'] : '0';
		$data = array(
			'level_name'	=> $level_name,
			'customer'		=> $customer,
			'category'		=> $category,
			'm_coin'		=> $m_coin,
			'm_premium'		=> $m_premium,
			'faq'			=> $faq,
			'setting'		=> $setting,			
			'role'			=> $level,			
			'users'			=> $users
		);
		$where = array();
		$tgl = date('Y-m-d H:i:s');
		$operator_id = $this->session->userdata('operator_id');
		if($id_level > 0){
			
			$where = array('id'=>$id_level);
			$this->access->updatetable('level',$data, $where);
			$save = $id_level;
		}else{
			$data += array('created_by' => $operator_id, 'created_at' => $tgl);
			$save = $this->access->inserttable('level', $data);
		}
		
		echo $save;
	}
	
	public function del(){	
		if(!$this->session->userdata('login') || !$this->session->userdata('role')){
			$this->no_akses();
			return false;
		}		
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id' => $_POST['id']
		);
		$data = array(
			'deleted_at'	=> $tgl,
			'deleted_by'	=> $this->session->userdata('operator_id')
		);		
		echo $this->access->updatetable('level', $data, $where);
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
