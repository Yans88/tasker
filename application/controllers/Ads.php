<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ads extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {			
		$this->data['judul_browser'] = 'Ads';
		$this->data['judul_utama'] = 'Ads';
		$this->data['judul_sub'] = 'List';
		$this->data['news'] = $this->access->readtable('ads','',array('deleted_at'=>null))->result_array();
		$this->data['isi'] = $this->load->view('ads/news_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	public function del(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_ads' => $_POST['id']
		);
		$data = array(
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('ads', $data, $where);
	}
	
	public function simpan(){
		$tgl = date('Y-m-d');
		$id_ads = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$judul = isset($_POST['judul']) ? $_POST['judul'] : '';
		$content = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
		$config['upload_path']   = './uploads/ads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']	= '2048';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
		
		$simpan = array(			
			'judul'			=> $judul,
			'description'	=> $content
		);
		if(!$this->upload->do_upload('userfile')){
            $gambar="";
        }else{
            $gambar=$this->upload->file_name;
			$simpan += array('img'	=> $gambar);
        }	
		$where = array();
		$save = 0;	
		if($id_ads > 0){
			$where = array('id_ads'=>$id_ads);
			$save = $this->access->updatetable('ads', $simpan, $where);   
		}else{
			$simpan += array('tgl'	=> $tgl);
			$save = $this->access->inserttable('ads', $simpan);   
		}  
		if($save > 0){
			redirect(site_url('ads'));
		}	 
	}
	
	function add($id=0){
		$this->data['judul_browser'] = 'Ads';
		$this->data['judul_utama'] = 'Ads';
		$this->data['judul_sub'] = 'Add/Edit';
		$this->data['vouchers'] = '';
		if($id > 0)	$this->data['vouchers'] = $this->access->readtable('ads','',array('id_ads'=>$id))->row();
		$this->data['isi'] = $this->load->view('ads/ads_frm', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function add_img($id_ads=0) {	
		$ads = $this->access->readtable('ads','',array('deleted_at'=>null, 'id_ads'=>$id_ads))->row();
		$this->data['judul_browser'] = 'Ads';
		$this->data['judul_utama'] = $ads->judul;
		$this->data['judul_sub'] = 'Image';
		$this->data['id_product'] = $id_ads;
		$this->data['ads'] = $ads;
		$this->data['category'] = $this->access->readtable('ads_images','',array('deleted_at'=>null, 'id_ads'=>$id_ads))->result_array();
		
		$this->data['isi'] = $this->load->view('ads/img_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function simpan_img(){
		$tgl = date('Y-m-d H:i:s');
		$id_img = isset($_POST['id_img']) ? (int)$_POST['id_img'] : 0;		
		$id_product = isset($_POST['id_product']) ? (int)$_POST['id_product'] : 0;		
		$config['upload_path']   = FCPATH.'/uploads/ads/';
        $config['allowed_types'] = 'gif|jpg|png|ico';
		$config['max_size']	= '2048';
		$config['encrypt_name'] = TRUE;
        $this->load->library('upload',$config);
		$gambar="";	
		$simpan = array(			
			'id_ads'		=> $id_product				
		);
		if(!$this->upload->do_upload('userfile')){
            $gambar="";
        }else{
            $gambar=$this->upload->file_name;
			$simpan += array('img'	=> $gambar);
        }	
		$where = array();
		$save = 0;	
		if($id_img > 0){
			$where = array('id'=>$id_img);
			$save = $this->access->updatetable('ads_images', $simpan, $where);   
		}else{
			$simpan += array('created_at'	=> $tgl);
			$save = $this->access->inserttable('ads_images', $simpan);   
		}  
		if($save > 0){
			redirect(site_url('ads/add_img/'.$id_product));
		}
	}
	
	public function del_img(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id' => $_POST['id']
		);
		$data = array(
			'deleted_by'	=> $this->session->userdata('operator_id'),
			'deleted_at'	=> $tgl
		);
		echo $this->access->updatetable('ads_images', $data, $where);
	}
	
	function publish(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_ads' => $_POST['id']
		);
		$data = array(
			'publish_by'	=> $this->session->userdata('operator_id'),
			'is_publish'	=> 1,
			'publish_date'	=> $tgl
		);
		
		$dt = array();
		$ids = array();
		$data_fcm = array();
		$notif_fcm = array();
		$save = 0;	
		$ads = $this->access->readtable('ads','',array('id_ads'=>$_POST['id']))->row();
		
		$get_member = $this->access->readtable('customer',array('fcm_token','id_customer'),array('deleted_at'=>null))->result_array();
		if(!empty($get_member)){
			$this->load->library('send_notif');			
			$send_fcm = '';
			$data_fcm = array(
				'id_notif'		=> $_POST['id'],
				'type' 			=> 12,
				'title'			=> 'Tasker',
				'message'		=> $ads->judul
			);
			$notif_fcm = array(
				'title'		=> 'Tasker',
				'body'		=> $ads->judul,
				'badge'		=> 1,
				'sound'		=> 'Default'
			);
			foreach($get_member as $gm){
				if(!empty($gm['fcm_token'])){
					array_push($ids, $gm['fcm_token']);					
					$dt[] = array(
						'id_member_from'	=> $gm['id_customer'],
						'id_member_to'		=> $gm['id_customer'],
						'pesan'				=> $ads->judul,
						'status_from'		=> 1,
						'status_to'			=> 1,
						'type'				=> 12,
						'_id'				=> $_POST['id'],
						'created_at'		=> $tgl,
					);
				}
						
			}				
		}
		if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);
		if(!empty($dt)) $this->db->insert_batch('master_chat', $dt);	
		echo $this->access->updatetable('ads', $data, $where);
		
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
