<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {
		if(!$this->session->userdata('login') || !$this->session->userdata('customer')){
			$this->no_akses();
			return false;
		}
		$this->data['judul_browser'] = 'Customer';
		$this->data['judul_utama'] = 'Customer';
		$this->data['judul_sub'] = 'List';	
		$selects = array('customer.*','admin.fullname');
		$this->data['customer'] = $this->access->readtable('customer',$selects,array('customer.deleted_at'=>null),array('admin'=>'admin.operator_id = customer.appr_acc_by'),'','','LEFT')->result_array();
		$this->data['isi'] = $this->load->view('customer/customer_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function appr_reject(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_customer' 		=> $_POST['id_member']			
		);
		$data = array(
			'verify_acc'	=> $_POST['status'],
			'appr_acc_date'	=> $tgl,
			'appr_acc_by'	=> $this->session->userdata('operator_id')
		);
		$ttl_coin = 0;
		$dt_upd2 = array();
		if((int)$_POST['status'] == 1){
			$login = $this->access->readtable('customer','',array('id_customer'=>$_POST['id_member']))->row();
			$my_coin = (int)$login->coin;
			$ttl_coin = $my_coin + 1;
			$id_member_share = (int)$login->invite_by;
			$referal_code = strtoupper($login->invite_code);
			$pesan = 'Verifikasi KTP sudah disetujui';
			$this->insert_inbox((int)$_POST['id_member'], $pesan,10, (int)$_POST['id_member']);
			$dt_history = array();
			$dt_history[] = array(
				'id_ta' 		=> (int)$id_member_share,
				'id_act' 		=> (int)$id_member_share,
				'id_member' 	=> $_POST['id_member'],
				'coin' 			=> 1,
				'ttl_coin' 		=> $ttl_coin,
				'type' 			=> 5,				// Bonus coin referal
				'created_at' 	=> $tgl,
				'ket' 			=> 'Bonus coin referal #'.$referal_code
			);
			
			$customer = $this->access->readtable('customer','',array('id_customer'=>$id_member_share,'abs(verify_acc)'=>1))->row();
			$id_member_share = 0;
			if(!empty($customer)){
				$id_member_share = (int)$customer->id_customer;
				$my_coin = (int)$customer->coin;
				$ttl_coin2 = 0;
				$ttl_coin2 = $my_coin + 1;
				$dt_history[] = array(
					'id_ta' 		=> (int)$_POST['id_member'],
					'id_act' 		=> (int)$_POST['id_member'],
					'id_member' 	=> $id_member_share,
					'coin' 			=> 1,
					'ttl_coin' 		=> $ttl_coin2,
					'type' 			=> 5,				// Bonus coin referal
					'created_at' 	=> $tgl,
					'ket' 			=> 'Bonus coin referal #'.$_POST['id_member']
				);				
				$dt_upd2 = array('coin'=>$ttl_coin2);
			}
			
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$_POST['id_member'],'type' => 1,'status' =>-1));
		}
		if((int)$ttl_coin > 0){
			$data +=array('coin'=>$ttl_coin);
		}
		if(!empty($dt_upd2)){
			$this->access->updatetable('customer', $dt_upd2, array('id_customer'=>$id_member_share));
		}
		$this->access->updatetable('customer', $data, $where);
		$this->db->insert_batch('history_coin', $dt_history);
		echo (int)$_POST['id_member'];
	}
	
	public function simpan(){
		$tgl = date('Y-m-d H:i:s');
		$id_customer = isset($_POST['id_customer']) ? (int)$_POST['id_customer'] : 0;		
		$nama = isset($_POST['nama']) ? $_POST['nama'] : '';		
		$phone = isset($_POST['phone']) ? $_POST['phone'] : '';		
		$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';		
		$region = isset($_POST['region']) ? (int)$_POST['region'] : 0;		
		$city = isset($_POST['kota']) ? $_POST['kota'] : 'Palembang';		
		$simpan = array(			
			'nama'		=> $nama,
			'phone'		=> $phone,
			'alamat'	=> $alamat,
			'region'	=> $region,
			'city'		=> $city
		);
		
		$where = array();
		$save = 0;	
		if($id_customer > 0){
			$where = array('id_customer'=>$id_customer);
			$simpan += array('update_by'=>$this->session->userdata('operator_id'));
			$this->access->updatetable('customer', $simpan, $where); 		
			$save = $id_customer;
		}else{
			$simpan += array('created_at'	=> $tgl,'created_by'=>$this->session->userdata('operator_id'));
			$save = $this->access->inserttable('customer', $simpan);			
		} 
		
		echo $save;
	}
	
	function detail($id=0){
		$this->data['judul_browser'] = 'Customer';
		$this->data['judul_utama'] = 'Customer';
		$this->data['judul_sub'] = 'Detail';	
		$selects = array('customer.*','admin.fullname');
		$this->data['customer'] = $this->access->readtable('customer',$selects,array('customer.id_customer'=>$id),array('admin'=>'admin.operator_id = customer.appr_acc_by'),'','','LEFT')->row();
		$this->data['trans_coin'] = $this->access->readtable('transaksi','',array('id_member'=>$id,'type'=>1))->result_array();	
		$this->data['trans_premium'] = $this->access->readtable('transaksi','',array('id_member'=>$id,'type'=>2))->result_array();	
		$this->data['isi'] = $this->load->view('customer/customer_detail', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function my_task(){
		$sort = array('abs(task.id_task)','DESC');
		$where = array('task.deleted_at'=>null);
		$id_member = $_POST['id_member'];
		if($id_member > 0){			
			$where += array('task.id_member'=>$id_member);
		}
		$task = '';
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$task = $this->access->readtable('task',$select,$where,array('customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT','',$_like)->result_array();
		$res = '';
		if(!empty($task)){
			foreach($task as $t){
				$_status = '';
				if($t['status'] == 1) $_status = '<span class="label label-success pull-right">Available</span>';
				if($t['status'] == 2) $_status = '<span class="label label-warning pull-right">On Progress</span>';
				if($t['status'] == 3) $_status = '<span class="label label-info pull-right">Completed</span>';
				if($t['status'] > 3) $_status = '<span class="label label-danger pull-right">Not Available</span>';
				$res .= '<li class="item">
                  <div class="product-img">
                    <strong>'.$t['title_task'].'</strong>
                  </div>
                  <div class="product-info">
                    
						<p style="margin-top:5px;margin-bottom:5px;">Kategori : '.$t['katogori'].'</p>
						<p style="margin-bottom:2px;">Need Applicant : '.$t['need_applicant'].'</p>
						<p style="margin-bottom:2px;">Payrate : '.number_format($t['pay_rate'],0,'',',').'</p>'.$_status.'
                        <p style="margin-bottom:2px;">Start Date : '.date('d M Y', strtotime($t['start_date'])).' - '.date('d M Y', strtotime($t['end_date'])).'</p>                    
						<p>'.$t['deskripsi'].'</p>
						
						 <ul class="list-inline">
                    <li style="font-size:11px; color:#585858;">Posting Date : '.date('d M Y', strtotime($t['created_at'])).'</li>
                    
                    
                  </ul>
                  </div>
                </li>';
			}
		}
		echo $res;
	}
	
	function my_apply(){
		$id_member = $_POST['id_member'];
		$where = array();
		$where = array('list_apply.id_member'=>$id_member);
		$field_in = '';
		$where_in = '';
		$select = array('customer.id_customer','customer.nama','customer.last_name','customer.accomp_task','customer.rating_applicant','customer.rating_employee','customer.photo','list_apply.status','list_apply.id_task','list_apply.refund_date','list_apply.status_applicant','list_apply.id','list_apply.appr_date','task.title_task','task.deskripsi','task.duration','task.region_name','task.start_date','task.end_date','list_apply.appr_date','list_apply.rating_by_appl','list_apply.rating_by_emp','list_apply.ket_by_appl','list_apply.ket_by_emp','list_apply.completed_at','list_apply.complete_applicant_at');
		$sort = array('abs(list_apply.id)','DESC');
		$dt = $this->access->readtable('list_apply',$select,$where,array('task'=>'task.id_task = list_apply.id_task','customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT','','','', $field_in, $where_in)->result_array();
		$res = '';
		if(!empty($dt)){
			foreach($dt as $d){
				$_status = '';
				if($d['status'] == 1) $_status = '<span class="label label-info pull-right">Applied</span>';
				if($d['status'] == 2) $_status = '<span class="label label-warning pull-right">Pending</span>';
				if($d['status'] == 3 || $d['status'] == 6) $_status = '<span class="label label-danger pull-right">Rejected</span>';
				if($d['status'] == 5 && $d['status_applicant'] == 5) $_status = '<span class="label label-info pull-right">Ongoing</span>';
				if($d['status'] == 4 && $d['status_applicant'] == 4) $_status = '<span class="label label-success pull-right">Completed</span>';
				if($d['status'] == 5 && $d['status_applicant'] == 4) $_status = '<span class="label label-warning pull-right">Waiting Completed Employer</span>';
				
				$res .= '<li class="item">
                  <div class="product-img">
                    <strong>'.$d['title_task'].'</strong>
                  </div>
                  <div class="product-info">
                    
						<p style="margin-top:5px;margin-bottom:2px;">Nama : '.$d['nama'].'</p>'.$_status.'
						
                        <p style="margin-bottom:2px;">Start Date : '.date('d M Y', strtotime($d['start_date'])).' - '.date('d M Y', strtotime($d['end_date'])).'</p>                    
						<p>'.$d['deskripsi'].'</p>
						
						 
                  </div>
                </li>';
			}
		}
		echo $res;
	}
	
	function insert_inbox($id_member=0, $pesan = '', $type=0, $_id=0){
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$this->load->library('send_notif');			
		$send_fcm = '';	
		$ids = array();
		$ids = array($login->fcm_token);
		$data_fcm = array(
			'id'					=> $_id,
			'id_member_to'			=> $id_member,
			'nama_pengirim'			=> '',
			'last_name_pengirim'	=> '',
			'title'					=> 'Tasker',
			'type'					=> $type				
		);
		$notif_fcm = array(
			'title'		=> 'Tasker',
			'body'		=> $pesan,
			'badge'		=> $type,
			'sound'		=> 'Default'
		);
		if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);
		$tgl = date('Y-m-d H:i:s');
		$dt = array(
			'id_member_from'	=> $id_member,
			'id_member_to'		=> $id_member,
			'pesan'				=> $pesan,
			'status_from'		=> 1,
			'status_to'			=> 1,
			'type'				=> $type,
			'_id'				=> $_id,
			'created_at'		=> $tgl,
		);
		$this->access->inserttable('master_chat', $dt);		
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
