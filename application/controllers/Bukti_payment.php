<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bukti_payment extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
	}	
	
	public function index() {
		if(!$this->session->userdata('login')){
			$this->no_akses();
			return false;
		}
		ini_set('memory_limit', '-1');
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Complain';
		$this->data['judul_sub'] = 'Payment';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 2;
		$this->data['isi'] = $this->load->view('bukti_payment/task_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function tbt() {
		if(!$this->session->userdata('login')){
			$this->no_akses();
			return false;
		}
		ini_set('memory_limit', '-1');
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'List';
		$this->data['judul_sub'] = 'Payment';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 1;
		$this->data['isi'] = $this->load->view('bukti_payment/task_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function completed() {
		if(!$this->session->userdata('login')){
			$this->no_akses();
			return false;
		}
		ini_set('memory_limit', '-1');
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Completed';
		$this->data['judul_sub'] = 'Payment';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 3;
		$this->data['isi'] = $this->load->view('bukti_payment/task_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function failed() {
		if(!$this->session->userdata('login')){
			$this->no_akses();
			return false;
		}
		ini_set('memory_limit', '-1');
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Failed';
		$this->data['judul_sub'] = 'Payment';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 4;
		$this->data['isi'] = $this->load->view('bukti_payment/task_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function load_data(){
		ini_set('memory_limit', '-1');
        $requestData= $_REQUEST;
		$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
		$order = $requestData['order'];
		
		$col = 0;
        $dir = "";
        $sort = array();
        if(!empty($order)) {
            foreach($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }
		if($dir != "asc" && $dir != "desc") {
            $dir = "asc";
        }
		$columns_valid = array(
			"task.id_task",
            "task.no_task",
            "task.title_task",            
            "task.duration",            
            "task.kategori",
            "task.need_applicant",
            
        );
		if(!isset($columns_valid[$col])) {
            $sort = array('task.id_task','ASC');
        } else {
            $sort = array($columns_valid[$col],$dir);
        }
        $member = array();
		$where = array('list_apply.status_bukti_pembayaran'=> $status);
		
		$select = array('list_apply.id_employer','customer.nama','customer.last_name');
		$dt_apply = $this->access->readtable('list_apply',$select,$where,array('customer'=>'customer.id_customer = list_apply.id_employer'),'','','LEFT')->result_array();
		
		$list_apply = array();
		if(!empty($dt_apply)){
			foreach($dt_apply as $da){
				$list_apply[$da['id_employer']] = $da['nama'].' '.$da['last_name'];
			}
		}
		
		$select = array('list_apply.*','task.title_task','task.no_task','customer.nama','customer.last_name');
        if(!empty($requestData['search']['value'])) {
			$search = $this->db->escape_str($requestData['search']['value']);
			$member = $this->access->readtable('list_apply',$select,$where,array('customer' => 'customer.id_customer = list_apply.id_member','task' => 'task.id_task = list_apply.id_task'),'',$sort,'LEFT','',array('no_task'=>$search), array('title_task'=>$search,'nama'=>$search))->result_array();
			
		    $totalFiltered=count($member);
		    $totalData=count($member);
        }else{
			$member = $this->access->readtable('list_apply',$select,$where,array('customer' => 'customer.id_customer = list_apply.id_member','task' => 'task.id_task = list_apply.id_task'),array($requestData['length'],$requestData['start']),$sort,'LEFT')->result_array();			
			$members = $this->access->readtable('list_apply','',$where)->result_array();
			
			$totalData = count($members);
			$totalFiltered=count($members);
        }
		
        $data = array();
        $nestedData=array();
				
		$i=1;
		if($requestData['start'] > 0){
			$i = (int)$i + (int)$requestData['start'];
		}
        if(!empty($member)){            		
            foreach($member as $row) {
				$nestedData=array();
				$info = '';
				if($row['deleted_at'] == '' || empty($row['deleted_at'])){	
					$dt_sp = '';
					$list_sp = '';
					$path = !empty($row['bukti_pembayaran']) ? $row['bukti_pembayaran'] : base_url('uploads/no_photo.jpg');
					$nestedData[] = $i++.'.';				
					$nestedData[] = '<a title="View Detail" href='.site_url('bukti_payment/detail/'.$row['id']).'>'.$row['no_task'].'</a>';			 	
					$nestedData[] = $row['title_task'];					
					$nestedData[] = $list_apply[$row['id_employer']];					
					$nestedData[] = $row['nama'].' '.$row['last_name'];
					$nestedData[] = date('d-m-Y H:i', strtotime($row['upload_date_bukti_pembayaran']));
								
					$data[] = $nestedData;
				}					
			}            
        }
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
            );

        echo json_encode($json_data);  // send data as json format
    }
	
	
	function detail($id_apply=0){
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Detail';
		$this->data['judul_sub'] = 'Task Detail';
		$this->data['title_box'] = 'List Task';
		$select = array();
		$where = array('list_apply.id'=>$id_apply);
		$select = array('list_apply.*','customer.nama','customer.last_name');
		$list_apply = $this->access->readtable('list_apply',$select,$where,array('customer' => 'customer.id_customer = list_apply.id_member'),'','','LEFT')->row();
		$id_task = $list_apply->id_task;
		$select = array();		
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$this->data['task'] = $this->access->readtable('task',$select,array('id_task'=>$id_task),array('customer'=>'customer.id_customer = task.id_member'),'','','LEFT')->row();
		$select = array();
		$sort = array('abs(id_bp)','ASC');
		$this->data['issue_bp'] = $this->access->readtable('issue_bp','',array('id_apply'=>$id_apply),'','',$sort)->result_array();
		$this->data['dt'] = '';
		$this->data['list_apply'] = $list_apply;
		
		$this->data['task_img'] = $this->access->readtable('task_img','',array('id_task'=>$id_task,'deleted_at'=>null))->result_array();	
		$this->data['isi'] = $this->load->view('bukti_payment/detail_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function appr_reject(){
		$tgl = date('Y-m-d H:i:s');
		$status = (int)$_POST['status'];
		$where = array(
			'id' => $_POST['id']
		);
		$data = array(
			'deleted_at'	=> $tgl
		);
		$remark = '';
		$simpan_issue = array();
		if($status == 4){
			$remark = 'Close by admin, Failed';
			$where = array('list_apply.id'=>$_POST['id']);
			$list_apply = $this->access->readtable('list_apply','',$where)->row();
			$id_task = (int)$list_apply->id_task;
			$id_member = (int)$list_apply->id_member;
			$id_employer = (int)$list_apply->id_employer;
			$task = '';
			$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
			$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
			$coin_member = (int)$login->coin;
			$coin_task = (int)$task->fee_task;
			$coin_member = $coin_member + $coin_task;
			$this->access->updatetable('customer', array('coin'=>(int)$coin_member), array('id_customer'=>$id_member));
			$this->access->updatetable('customer', array('status'=>2), array('id_customer'=>$id_employer));
			$dt_history = array();
			$dt_history = array(
				'id_ta' 		=> $_POST['id'],
				'id_act' 		=> $id_task,
				'id_member' 	=> $id_member,
				'coin' 			=> $coin_task,
				'ttl_coin' 		=> $coin_member,
				'type' 			=> 3,				// refund apply task
				'created_at' 	=> $tgl,
				'ket' 			=> 'Refund apply task #'.$id_task
			);
			$this->access->inserttable('history_coin', $dt_history);
		}
		if($status == 3){
			$remark = 'Close by admin, Succcess';
		}
		$simpan_issue = array();
		$simpan_issue = array(
			'id_apply'		=> $_POST['id'],
			'keterangan'	=> $remark,
			'id_member'		=> -1,
			'member'		=> 'Admin',
			'created_at'	=> $tgl
		);
		$this->access->inserttable('issue_bp', $simpan_issue);
		$simpan = array();
		$simpan = array(
			'status_date'	=> $tgl,
			'status_bukti_pembayaran'	=> $status,
			'ket_stts_bukti_pembayaran'	=> $remark
		);
		$this->access->updatetable('list_apply', $simpan, array('id'=>$_POST['id']));
		echo $_POST['id'];
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
