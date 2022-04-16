<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_task extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('Access', 'access', true);		
			
	}	
	
	public function index() {
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}		
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';	
		$this->data['judul_browser'] = 'Report task';
		$this->data['judul_utama'] = 'Pending';
		$this->data['judul_sub'] = 'Report';
			
		$sql = 'SELECT COUNT(task_report.id_task) as cnt, task_report.id_task,task_report.status,task_report.created_at, task.title_task from task_report LEFT JOIN task on task.id_task = task_report.id_task WHERE task_report.status = 1 and task.status in(1,2) GROUP by task_report.id_task order by abs(task_report.id) asc';
		$this->data['report'] = $this->db->query($sql)->result_array();
		
		
		$this->data['isi'] = $this->load->view('report_task/report_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function completed() {
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}		
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';	
		$this->data['judul_browser'] = 'Report task';
		$this->data['judul_utama'] = 'Pending';
		$this->data['judul_sub'] = 'Report';
			
		$sql = 'SELECT COUNT(task_report.id_task) as cnt, task_report.id_task,task_report.status,task_report.created_at, task.title_task from task_report LEFT JOIN task on task.id_task = task_report.id_task WHERE task_report.status != 1 and task.status not in(4,3) GROUP by task_report.id_task order by abs(task_report.id) asc';
		$this->data['report'] = $this->db->query($sql)->result_array();
		
		
		$this->data['isi'] = $this->load->view('report_task/report_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	function view($id=0){
		$this->data['judul_browser'] = 'Report';
		$this->data['judul_utama'] = 'Report';
		$this->data['judul_sub'] = 'Detail';
		$task = $this->access->readtable('task','',array('id_task'=>$id))->row();	
		$id_member = $task->id_member;
		$selects = array('customer.*','admin.fullname');
		$this->data['customer'] = $this->access->readtable('customer',$selects,array('customer.id_customer'=>$id_member),array('admin'=>'admin.operator_id = customer.appr_acc_by'),'','','LEFT')->row();
		$this->data['task'] = $task;	
		$_selects = array('task_report.*','customer.nama');
		$this->data['member_report'] = $this->access->readtable('task_report',$_selects,array('id_task'=>$id),array('customer'=>'customer.id_customer = task_report.id_member_report'),'','','LEFT')->result_array();	
		$this->data['task_img'] = $this->access->readtable('task_img',array('image'),array('id_task'=>$id,'deleted_at'=>null))->result_array();
		$this->data['isi'] = $this->load->view('report_task/report_detail', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function appr_rej(){		
		$tgl = date('Y-m-d H:i:s');
		$id_task = $_POST['id_task'];
		$status = (int)$_POST['status'];
		if($status == 2){
			$where = array(
				'id_task' 	=> $id_task,
				'status'	=> 1
			);
			$where2 = array(
				'id_task' 	=> $id_task,
				'status <='	=> 2
			);
			$this->access->updatetable('task', array('status'=>6), $where2);
			//$this->access->updatetable('list_apply', array('status'=>6,'status_applicant'=>6), $where2);
			$this->access->updatetable('list_apply', array('status'=>6,'status_applicant'=>6), array('id_task' => $id_task,'refund_date'=>null));
		}
		if($status == 3){
			$where = array(
				'id' 		=> $_POST['id'],
				'status'	=> 1,
			);
		}
		
		$data = array(
			'status'		=> $status,
			'status_by'		=> $this->session->userdata('operator_id'),
			'status_date'	=> $tgl,
		);
		if($status == 3){
			$data += array('_id'=>$_POST['id']);
		}
		
		echo $this->access->updatetable('task_report', $data, $where);
		
	}
	
	public function reject(){
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';		
		$this->data['judul_browser'] = 'Transaksi Coin';
		$this->data['judul_utama'] = 'Cancelled';
		$this->data['judul_sub'] = 'Coin';
		$this->data['status'] = 3;
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone','admin.fullname');
		$where = array();
		$where = array('transaksi.status'=>3,'transaksi.type'=>1);
		$from = '';
		$to = '';
		if(!empty($tgl)){
			$_tgl = !empty($tgl) ? explode('-', $tgl) : '';
			$start_date = !empty($tgl) ? str_replace('/','-',$_tgl[0]) : '';
			$end_date = !empty($tgl) ? str_replace('/','-',$_tgl[1]) : '';	
			$from = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : $start_date;
			$to = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : $end_date;	
		}
		if(!empty($from)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") >= '=>$from);
		}
		if(!empty($to)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") <= '=>$to);
		}		
		$this->data['transaksi'] = $this->access->readtable('transaksi',$selects,$where,array('customer'=>'customer.id_customer = transaksi.id_member','admin'=>'admin.operator_id = transaksi.status_by'),'','','LEFT')->result_array();
		$this->data['tgl'] = $tgl;		
		$this->data['isi'] = $this->load->view('trans_coin/trans_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	public function appr_reject(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_trans' 		=> (int)$_POST['id_trans']			
		);
		$data = array(
			'status'	=> (int)$_POST['status'],
			'status_date'	=> $tgl,
			'status_by'	=> $this->session->userdata('operator_id')
		);
		if((int)$_POST['status'] == 4){
			$trans = $this->access->readtable('transaksi','',$where)->row();
			$id_member = (int)$trans->id_member;
			$buy_coin = (int)$trans->jml;
			$customer = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
			$my_coin = (int)$customer->coin;
			$ttl_coin = $my_coin + $buy_coin;
			$this->access->updatetable('customer', array('coin'=>$ttl_coin), array('id_customer'=>$id_member));
			$dt_history = array();
			$dt_history = array(
				'id_ta' 		=> (int)$_POST['id_trans'],
				'id_act' 		=> (int)$_POST['id_trans'],
				'id_member' 	=> $id_member,
				'coin' 			=> $buy_coin,
				'ttl_coin' 		=> $ttl_coin,
				'type' 			=> 1,				// add coin by transaksi
				'created_at' 	=> $tgl,
				'ket' 			=> 'Add coin transaksi #'.(int)$_POST['id_trans']
			);
			$this->access->inserttable('history_coin', $dt_history);
		}			
		$this->access->updatetable('transaksi', $data, $where);
	}
	
	


}
