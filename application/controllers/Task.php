<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends MY_Controller {

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
		$this->data['judul_utama'] = 'List';
		$this->data['judul_sub'] = 'Task';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 2;
		$this->data['isi'] = $this->load->view('task/task_v', $this->data, TRUE);
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
		$this->data['judul_sub'] = 'Task';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 1;
		$this->data['isi'] = $this->load->view('task/task_v', $this->data, TRUE);
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
		$this->data['judul_sub'] = 'Task';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 3;
		$this->data['isi'] = $this->load->view('task/task_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function cancelled() {
		if(!$this->session->userdata('login')){
			$this->no_akses();
			return false;
		}
		ini_set('memory_limit', '-1');
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Completed';
		$this->data['judul_sub'] = 'Task';
		$this->data['title_box'] = 'List Task';
		$this->data['status'] = 4;
		$this->data['isi'] = $this->load->view('task/task_v', $this->data, TRUE);
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
		$where = array('task.deleted_at'=>null);
		if($status > 0 && $status != 4) $where += array('task.status'=>$status);
		if($status == 4) $where += array('task.status >='=>4);
		$select = array('list_apply.id_task','customer.nama','customer.last_name');
		$dt_apply = $this->access->readtable('list_apply',$select,'',array('customer'=>'customer.id_customer = list_apply.id_member'),'','','LEFT')->result_array();
		
		$list_apply = array();
		if(!empty($dt_apply)){
			foreach($dt_apply as $da){
				$list_apply[$da['id_task']][] = '- '.$da['nama'].' '.$da['last_name'];
			}
		}
		
		$select = array('task.*','customer.nama','customer.last_name');
        if(!empty($requestData['search']['value'])) {
			$search = $this->db->escape_str($requestData['search']['value']);
			$member = $this->access->readtable('task',$select,$where,array('customer' => 'customer.id_customer = task.id_member'),'',$sort,'LEFT','',array('no_task'=>$search), array('title_task'=>$search,'kategori'=>$search))->result_array();
			
		    $totalFiltered=count($member);
		    $totalData=count($member);
        }else{
			$member = $this->access->readtable('task',$select,$where,array('customer' => 'customer.id_customer = task.id_member'),array($requestData['length'],$requestData['start']),$sort,'LEFT')->result_array();			
			$members = $this->access->readtable('task','',$where)->result_array();
			
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
					$dt_sp = $list_apply[$row['id_task']];					
					$list_sp = !empty($dt_sp) ? implode('<br/>',$dt_sp) : '-';
					$nestedData[] = $i++.'.';				
					$nestedData[] = '<a title="View Detail" href='.site_url('task/detail/'.$row['id_task']).'>'.$row['no_task'].'</a>';			 	
					$nestedData[] = $row['title_task'];
					$nestedData[] = $row['kategori'];
					$nestedData[] = date('d/m/Y', strtotime($row['start_date'])).' - '.date('d/-m/Y', strtotime($row['end_date'])). ' ('.$row['duration'].' hari)';
					$nestedData[] = $row['nama'].' '.$row['last_name'];
					$nestedData[] = $list_sp;
					$nestedData[] = $row['need_applicant'];
					
					
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
	
	
	function detail($id_task=0){
		$this->data['judul_browser'] = 'Task';
		$this->data['judul_utama'] = 'Detail';
		$this->data['judul_sub'] = 'Task Detail';
		$this->data['title_box'] = 'List Task';
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$this->data['task'] = $this->access->readtable('task',$select,array('id_task'=>$id_task),array('customer'=>'customer.id_customer = task.id_member'),'','','LEFT')->row();
		$select = array();
		$select = array('customer.id_customer','customer.nama','customer.last_name','customer.accomp_task','customer.rating_applicant','customer.rating_employee','customer.photo','list_apply.status','list_apply.id_task','list_apply.completed_at','list_apply.status_applicant','list_apply.id','list_apply.complete_applicant_at','list_apply.appr_date','list_apply.rating_by_appl','list_apply.rating_by_emp','list_apply.ket_by_appl','list_apply.ket_by_emp','list_apply.ongoing_date','list_apply.created_at');
		$sort = array('abs(list_apply.id)','ASC');
		$this->data['dt'] = $this->access->readtable('list_apply',$select,array('list_apply.id_task'=>$id_task),array('customer'=>'customer.id_customer = list_apply.id_member'),'',$sort,'LEFT')->result_array();
		$this->data['task_img'] = $this->access->readtable('task_img','',array('id_task'=>$id_task,'deleted_at'=>null))->result_array();	
		$this->data['isi'] = $this->load->view('task/detail_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
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
