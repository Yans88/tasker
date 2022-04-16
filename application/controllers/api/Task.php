<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Task extends CI_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model('Access','access',true);
		$this->load->model('Setting_m','sm', true);
		$this->load->library('converter');
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
    }	
	
    function submit_task(){	
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$title = isset($param['title']) ? $param['title'] : '';
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;	
		$is_remote = isset($param['is_remote']) ? (int)$param['is_remote'] : 0;	
		$description = isset($param['description']) ? $param['description'] : '';			
		$pay_rate = isset($param['pay_rate']) ? str_replace(',','',$param['pay_rate']) : '';		
		$need_applicant = isset($param['need_applicant']) ? str_replace(',','',$param['need_applicant']) : '';		
		$pay_rate = str_replace('.','',$pay_rate);
		$need_applicant = str_replace('.','',$need_applicant);
		$category = isset($param['category']) ? (int)$param['category'] : 0;
		$kategori = $this->access->readtable('kategori','',array('id_kategori'=>$category))->row();
		$region = isset($param['region']) ? (int)$param['region'] : 0;	
		$_region = $this->access->readtable('region','',array('id_region'=>$region))->row();
		$city = isset($param['city']) ? (int)$param['city'] : 0;	
		$_city = $this->access->readtable('city','',array('id_city'=>$city))->row();
		$start_date = isset($param['start_date']) && !empty($param['start_date']) ? new DateTime($param['start_date']) : '';
		$end_date = isset($param['end_date']) && !empty($param['end_date']) ? new DateTime($param['end_date']) : '';
		$diff = $start_date->diff($end_date)->days;
		$start_date = !empty($start_date) ? $start_date->format('Y-m-d') : '';
		$end_date = !empty($end_date) ? $end_date->format('Y-m-d') : '';
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$premium_start_date = !empty($login->premium_start_date) ? $login->premium_start_date : '';
		$premium_end_date = !empty($login->premium_end_date) ? $login->premium_end_date : '';
		$is_premium = 7;
		$max_task = 5;
		$_tgl = date('Y-m-d');
		if(!empty($premium_start_date) && !empty($premium_end_date)){			
			if ($_tgl >= $premium_start_date && $_tgl <= $premium_end_date){
				$is_premium = 14;
				$max_task = 10;
			}		
		}
		$cek_active_task = $this->access->readtable('task','',array('task.deleted_at'=>null,'id_member'=>$id_member,'status <=' => 2))->result_array();
		$cnt_active_task = count($cek_active_task);
		$dt_maxjob = array();
		if($max_task <= (int)$cnt_active_task){			
			$dt_maxjob = array(
				'id_member'				=> $id_member,
				'active_posted_task'	=> $cnt_active_task,
				'max_posted_task'		=> $max_task
			);
			$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'active_posted_task over limit',
					'data'		=> $dt_maxjob
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$exp_date = date('Y-m-d', strtotime($_tgl. ' + '.$is_premium.' days'));
		$result = array();
		$_fee_task = $pay_rate / 50000;
		$fee_task = round($_fee_task,0,PHP_ROUND_HALF_UP);
		$fee_task = (int)$fee_task > 0 ? (int)$fee_task : 1;
		$dt_simpan = array(
			'id_member'			=> $id_member,
			'title_task'		=> $title,
			'deskripsi'			=> $description,
			'pay_rate'			=> $pay_rate,
			'need_applicant'	=> (int)$need_applicant,
			'id_cat'			=> (int)$category,
			'id_region'			=> (int)$region,
			'id_city'			=> (int)$city,
			'kategori'			=> $kategori->nama_kategori,
			'region_name'		=> $_region->region_name,
			'city_name'			=> $_city->nama_city,
			'start_date'		=> $start_date,
			'end_date'			=> $end_date,
			'duration'			=> (int)$diff,
			'fee_task'			=> (int)$fee_task,
			'created_at'		=> $tgl,
			'expired_date'		=> $exp_date,
			'status'			=> 1,
			'is_remote'			=> $is_remote
		);
		$save = $this->access->inserttable('task', $dt_simpan);
		if($save > 0){
			$no_task = 'TSK'.date('y').''.date('m').'000000'.$save;
			$dt_simpan += array('no_task'=>$no_task);
			$this->access->updatetable('task', array('no_task'=>$no_task), array('id_task'=>$save));	
			$config = array();
			$config['upload_path'] = "./uploads/task/";
			$config['allowed_types'] = "jpg|png|jpeg|";
			$config['max_size']	= '4096';		
			$config['encrypt_name'] = TRUE;
			$this->load->library('upload');
			$files = $_FILES;
			$jml = count($_FILES['img']['name']);
			$upl = '';
			$dt_img = array();
			for($i=0;$i<(int)$jml;$i++){
				$upl = '';
				$_FILES['img']['name']= $files['img']['name'][$i];
				$_FILES['img']['type']= $files['img']['type'][$i];
				$_FILES['img']['tmp_name']= $files['img']['tmp_name'][$i];
				$_FILES['img']['error']= $files['img']['error'][$i];
				$_FILES['img']['size']= $files['img']['size'][$i];  
				$this->upload->initialize($config);
				$this->upload->do_upload('img');
				$upl = $this->upload->data();
				$dt_img[] = array('id_task'=>$save,'image'=>$upl['file_name'],'created_at'=>$tgl);
			}
			if(!empty($dt_img)) $this->db->insert_batch('task_img', $dt_img);
			$this->notify_me($id_member, $save);
			$dt_simpan += array('image'=>$dt_img,'id_task'=>$save);
		}
		$result = [
					'err_code'	=> '00',
					'err_msg_id'	=> 'Task Baru berhasil di tambahkan',
					'err_msg'	=> 'New Task Successfully Posted',
					'data'		=> $dt_simpan
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function top_task(){
		$result = array();
		$dt = array();
		$param = $this->input->post();		
		$city = isset($param['city']) ? (int)$param['city'] : 0;
		$id_category = isset($param['id_category']) ? (int)$param['id_category'] : 0;		
		$id_member_wishlist = isset($param['id_member_wishlist']) ? (int)$param['id_member_wishlist'] : 0;
		$pay_rate = isset($param['pay_rate']) ? $param['pay_rate'] :'';
		$duration = isset($param['duration']) ? $param['duration'] :'';		
		$_sort = isset($param['sort']) ? (int)$param['sort'] : 1;
		$keyword = isset($param['keyword']) ? $param['keyword'] :'';
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$_like = array();
		if(!empty($keyword)){
			$keyword = $this->db->escape_str($keyword);
			$_like = array('task.title_task'=> $keyword);
		}
		$tgl = date('Y-m-d H:i:s');	
		$dt_block = $this->get_all_block($id_member_wishlist);
		$field_notin = '';
		if(!empty($dt_block)){
			$field_notin = 'id_customer';
		}
		$_sort = array('ABS(rating_applicant)','DESC');
		$_login = $this->access->readtable('customer',array('id_customer'),array('verify_acc'=>1),'','',$_sort,'','','', '', '', '', '', '', $field_notin,$dt_block)->result_array();
		
		$i = 0;
		$task = array();
		$favorite = array();
		$dt_fav = $this->access->readtable('wishlist_task','',array('id_member'=>$id_member_wishlist))->result_array();
		if(!empty($dt_fav)){
			foreach($dt_fav as $df){
				array_push($favorite, $df['id_task']);
			}
		}
		$_max = (int)count($_login) < 20 ? (int)count($_login) : 20;
		if(!empty($_login)){
			foreach($_login as $_l){
				$_task = '';
				$id_customer = 0;
				$where = array();
				
				$where = array('task.deleted_at'=>null);
				$where += array('date_format(task.start_date, "%Y-%m-%d") >= '=>$tgl);
				$where += array('date_format(task.expired_date, "%Y-%m-%d") >= '=>$tgl);
				$where += array('task.status'=>1);
				if($id_category > 0){
					$where += array('task.id_cat'=>$id_category);
				}
				if($city > 0){
					$where += array('task.id_city'=>$city);
				}
				if($status > 0){
					$where += array('task.status'=>$status);
				}
				if(!empty($pay_rate)){
					$pay_rate = explode('-',$pay_rate);
					$start_rate = str_replace(',','',$pay_rate[0]);
					$end_rate = str_replace(',','',$pay_rate[1]);
					$start_rate = str_replace('.','',$start_rate);
					$end_rate = str_replace('.','',$end_rate);
					$where += array('task.pay_rate >='=>$start_rate,'task.pay_rate <=' => $end_rate);
				}
				if(!empty($duration)){
					$duration = explode('-',$duration);
					$start_dr = str_replace(',','',$duration[0]);
					$end_dr = str_replace(',','',$duration[1]);
					$start_dr = str_replace('.','',$start_dr);
					$end_dr = str_replace('.','',$end_dr);
					$where += array('task.duration >='=>$start_dr,'task.duration <=' => $end_dr);
				}
				if($i < (int)$_max){				
					$id_customer = (int)$_l['id_customer'];
					$sort = array('abs(task.id_task)','RANDOM');
					$where += array('task.id_member'=>$id_customer);
					
					$select = array('task.*','customer.nama','customer.last_name','customer.photo');
					$_task = $this->access->readtable('task',$select,$where,array('customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT','',$_like)->row();
					error_log($this->db->last_query());
				}
				if(!empty($_task)){
					$is_wishlist = 0;
					if (in_array($_task->id_task, $favorite)){
						$is_wishlist = 1;
					}
					$photo = !empty($_task->photo) ? base_url('uploads/photo_cv/'.$_task->photo) : '';
					$task_img = '';
					$dt_img = array();
					$task_img = $this->access->readtable('task_img','',array('id_task'=>$_task->id_task,'deleted_at'=>null))->result_array();
					if(!empty($task_img)){
						foreach($task_img as $ti){
							$dt_img[] = array(
								'id_img'	=> $ti['id_img'],
								'image'		=> !empty($ti['image']) ? base_url('uploads/task/'.$ti['image']) : '',
							);
						}
					}
					$dt[] = array(
						'id_task'			=> $_task->id_task,
						'no_task'			=> $_task->no_task,
						'id_member'			=> $_task->id_member,
						'nama'				=> $_task->nama,
						'last_name'			=> $_task->last_name,
						'title_task'		=> $_task->title_task,
						'deskripsi'			=> $_task->deskripsi,
						'pay_rate'			=> $_task->pay_rate,
						'need_applicant'	=> (int)$_task->need_applicant,
						'jml_applicant'		=> (int)$_task->jml_applicant,
						'appr_applicant'	=> (int)$_task->appr_applicant,
						'completed_applicant'	=> (int)$_task->completed_applicant,
						'status'			=> (int)$_task->status,
						'id_cat'			=> (int)$_task->id_cat,
						'id_region'			=> (int)$_task->id_region,
						'id_city'			=> (int)$_task->id_city,
						'kategori'			=> $_task->kategori,
						'region_name'		=> $_task->region_name,
						'city_name'			=> $_task->city_name,
						'start_date'		=> $_task->start_date,
						'end_date'			=> $_task->end_date,
						'expired_date'		=> $_task->expired_date,					
						'duration'			=> (int)$_task->duration,
						'fee_task'			=> (int)$_task->fee_task,
						'photo_member'		=> $photo,
						'first_appr_date'	=> $_task->first_appr_date,
						'completed_at'		=> $_task->completed_at,
						'is_wishlist'		=> $is_wishlist,
						'image'				=> $dt_img
					);
					$i++;
				}
			}
		}
		
		if(!empty($dt)){
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=>  null
			];
		}
		
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function get_task(){
		$result = array();
		$dt = array();
		$param = $this->input->post();
		$city = isset($param['city']) ? (int)$param['city'] : 0;
		$id_category = isset($param['id_category']) ? (int)$param['id_category'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id_member_wishlist = isset($param['id_member_wishlist']) ? (int)$param['id_member_wishlist'] : 0;
		$pay_rate = isset($param['pay_rate']) ? $param['pay_rate'] :'';
		$duration = isset($param['duration']) ? $param['duration'] :'';		
		$_sort = isset($param['sort']) ? (int)$param['sort'] : 1;
		$keyword = isset($param['keyword']) ? $param['keyword'] :'';
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$_like = array();
		if(!empty($keyword)){
			$keyword = $this->db->escape_str($keyword);
			$_like = array('task.title_task'=> $keyword);
		}
		
		$sort = array('abs(task.id_task)','DESC');
		if($_sort == 2) $sort = array('abs(task.id_task)','ASC');
		$where = array('task.deleted_at'=>null);
		if($id_category > 0){
			$where += array('task.id_cat'=>$id_category);
		}
		if($id_member > 0){
			$id_member_wishlist = $id_member;
			$where += array('task.id_member'=>$id_member);
		}
		if($id_member == 0){
			$tgl = date('Y-m-d H:i:s');
			$where += array('date_format(task.start_date, "%Y-%m-%d") >= '=>$tgl);
			$where += array('date_format(task.expired_date, "%Y-%m-%d") >= '=>$tgl);
			$where += array('task.status'=>1);
		}
		if($city > 0){
			$where += array('task.id_city'=>$city);
		}
		if($status > 0){
			$where += array('task.status'=>$status);
		}
		if(!empty($pay_rate)){
			$pay_rate = explode('-',$pay_rate);
			$start_rate = str_replace(',','',$pay_rate[0]);
			$end_rate = str_replace(',','',$pay_rate[1]);
			$start_rate = str_replace('.','',$start_rate);
			$end_rate = str_replace('.','',$end_rate);
			$where += array('task.pay_rate >='=>$start_rate,'task.pay_rate <=' => $end_rate);
		}
		if(!empty($duration)){
			$duration = explode('-',$duration);
			$start_dr = str_replace(',','',$duration[0]);
			$end_dr = str_replace(',','',$duration[1]);
			$start_dr = str_replace('.','',$start_dr);
			$end_dr = str_replace('.','',$end_dr);
			$where += array('task.duration >='=>$start_dr,'task.duration <=' => $end_dr);
		}
		$dt_block = $this->get_all_block($id_member_wishlist);
		$field_notin = '';
		if(!empty($dt_block)){
			$field_notin = 'task.id_member';
		}
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$task = $this->access->readtable('task',$select,$where,array('customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT','',$_like, '', '', '', '', '', $field_notin,$dt_block)->result_array();
		
		$favorite = array();
		$dt_fav = $this->access->readtable('wishlist_task','',array('id_member'=>$id_member_wishlist))->result_array();
		if(!empty($dt_fav)){
			foreach($dt_fav as $df){
				array_push($favorite, $df['id_task']);
			}
		}
		
		if(!empty($task)){
			foreach($task as $t){
				$is_wishlist = 0;
				if (in_array($t['id_task'], $favorite)){
					$is_wishlist = 1;
				}
				$task_img = '';
				$dt_img = array();
				$task_img = $this->access->readtable('task_img','',array('id_task'=>$t['id_task'],'deleted_at'=>null))->result_array();
				if(!empty($task_img)){
					foreach($task_img as $ti){
						$dt_img[] = array(
							'id_img'	=> $ti['id_img'],
							'image'		=> !empty($ti['image']) ? base_url('uploads/task/'.$ti['image']) : '',
						);
					}
				}
				$photo = !empty($t['photo']) ? base_url('uploads/photo_cv/'.$t['photo']) : '';
				$dt[] = array(
					'id_task'			=> $t['id_task'],
					'no_task'			=> $t['no_task'],
					'id_member'			=> $t['id_member'],
					'nama'				=> $t['nama'],
					'last_name'			=> $t['last_name'],
					'title_task'		=> $t['title_task'],
					'deskripsi'			=> $t['deskripsi'],
					'pay_rate'			=> $t['pay_rate'],
					'need_applicant'	=> (int)$t['need_applicant'],
					'jml_applicant'		=> (int)$t['jml_applicant'],
					'appr_applicant'	=> (int)$t['appr_applicant'],
					'completed_applicant'	=> (int)$t['completed_applicant'],
					'status'			=> (int)$t['status'],
					'id_cat'			=> (int)$t['id_cat'],
					'id_region'			=> (int)$t['id_region'],
					'id_city'			=> (int)$t['id_city'],
					'kategori'			=> $t['kategori'],
					'region_name'		=> $t['region_name'],
					'city_name'			=> $t['city_name'],
					'start_date'		=> $t['start_date'],
					'end_date'			=> $t['end_date'],
					'expired_date'		=> $t['expired_date'],					
					'duration'			=> (int)$t['duration'],
					'fee_task'			=> (int)$t['fee_task'],
					'photo_member'		=> $photo,
					'first_appr_date'	=> $t['first_appr_date'],
					'completed_at'		=> $t['completed_at'],
					'is_remote'			=> $t['is_remote'],
					'is_wishlist'		=> $is_wishlist,
					'image'				=> $dt_img
				);
			}
		}
		if(!empty($dt)){
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=>  null
			];
		}
		
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function detail_task(){
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member_wishlist = isset($param['id_member_wishlist']) ? (int)$param['id_member_wishlist'] : 0;
		$id_applicant = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$task = $this->access->readtable('task',$select,array('id_task'=>$id_task),array('customer'=>'customer.id_customer = task.id_member'),'','','LEFT')->row();
		$dt = array();
		$dt_img = array();
		$task_img = $this->access->readtable('task_img','',array('id_task'=>$id_task,'deleted_at'=>null))->result_array();	
		$favorite = array();
		$dt_fav = $this->access->readtable('wishlist_task','',array('id_member'=>$id_member_wishlist))->result_array();
		if(!empty($dt_fav)){
			foreach($dt_fav as $df){
				array_push($favorite, $df['id_task']);
			}
		}
		if(!empty($task_img)){
			foreach($task_img as $ti){
				$dt_img[] = array(
					'id_img'	=> $ti['id_img'],
					'image'		=> !empty($ti['image']) ? base_url('uploads/task/'.$ti['image']) : '',
				);
			}
		}
		$is_wishlist = 0;
		if (in_array($id_task, $favorite)){
			$is_wishlist = 1;
		}
		$id_chat = 0;
		$id_member_task = (int)$task->id_member;
		$dt_apply = '';
		$status_applicant = '';
		$status_employer = '';
		if($id_applicant > 0 && $id_applicant != $id_member_task){
			$sql = 'SELECT master_chat.id_chat FROM `master_chat` WHERE (id_member_to = '.$id_member_task.' or id_member_to = '.$id_applicant.') AND (id_member_from = '.$id_applicant.' or id_member_from = '.$id_member_task.') and type=1 order by master_chat.updated_at DESC';			
			$dt = $this->db->query($sql)->row();
			$id_chat = (int)$dt->id_chat > 0 ? (int)$dt->id_chat : 0;
			$where = array();
			$where = array('list_apply.id_member'=>$id_applicant,'list_apply.id_task' => $id_task);		
			$select = array('list_apply.status','list_apply.refund_date','list_apply.status_applicant','list_apply.id','list_apply.appr_date','list_apply.appr_date','list_apply.rating_by_appl','list_apply.rating_by_emp','list_apply.ket_by_appl','list_apply.ket_by_emp','list_apply.completed_at','list_apply.complete_applicant_at');		
			$dt_apply = $this->access->readtable('list_apply',$select,$where)->row();
			if(!empty($dt_apply)){
				$rating_by_emp = $dt_apply->rating_by_emp;
				$rating_by_appl = $dt_apply->rating_by_appl;
				$ket_by_emp = $dt_apply->ket_by_emp;
				$ket_by_appl = $dt_apply->ket_by_appl;
				$status_employer = (int)$dt_apply->status == 7 ? 3 : (int)$dt_apply->status; 
				$status_applicant = (int)$dt_apply->status_applicant == 7 ? 3 : (int)$dt_apply->status_applicant;
				$reject_date = $dt_apply->reject_date;
				$completed_at = $dt_apply->completed_at;
				$complete_applicant_at = $dt_apply->complete_applicant_at;
				$refund_date = $dt_apply->refund_date;
			}
			$dt_block = $this->cek_block($id_applicant, $id_member_task);			
			$id_block = $dt_block['id_block'];
			$id_user_yg_memblock = $dt_block['id_user_yg_memblock'];
		}
		
		
		
		if(!empty($task)){
			$photo = !empty($task->photo) ? base_url('uploads/photo_cv/'.$task->photo) : '';
			$dt = array(
				'id_task'			=> $task->id_task,
				'id_apply'			=> $dt_apply->id,
				'no_task'			=> $task->no_task,
				'id_member'			=> $task->id_member,
				'nama'				=> $task->nama,
				'last_name'			=> $task->last_name,
				'title_task'		=> $task->title_task,
				'deskripsi'			=> $task->deskripsi,
				'pay_rate'			=> $task->pay_rate,
				'need_applicant'	=> (int)$task->need_applicant,
				'jml_applicant'		=> (int)$task->jml_applicant,
				'appr_applicant'	=> (int)$task->appr_applicant,
				'completed_applicant'	=> (int)$task->completed_applicant,
				'status'			=> (int)$task->status,
				'id_cat'			=> (int)$task->id_cat,				
				'id_region'			=> (int)$task->id_region,
				'id_city'			=> (int)$task->id_city,
				'kategori'			=> $task->kategori,
				'region_name'		=> $task->region_name,
				'city_name'			=> $task->city_name,
				'start_date'		=> $task->start_date,
				'end_date'			=> $task->end_date,
				'expired_date'		=> $task->expired_date,
				'duration'			=> (int)$task->duration,
				'fee_task'			=> (int)$task->fee_task,
				'is_remote'			=> $task->is_remote,
				'photo_member'		=> $photo,
				'created_at'		=> $task->created_at,
				'first_appr_date'	=> $task->first_appr_date,
				'completed_at'		=> $task->completed_at,
				'is_wishlist'		=> $is_wishlist,
				'id_chat'			=> $id_chat,
				'id_block'			=> (int)$id_block,
				'id_user_yg_memblock'	=> (int)$id_user_yg_memblock,
				'id_applicant'		=> $id_applicant,
				'status_employer'		=> $status_employer,
				'status_applicant'		=> $status_applicant,				
				'image'				=> $dt_img
			);
		}
		$result = array();
		if(!empty($dt)){
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=> null
			];
		}
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function edit_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$title = isset($param['title']) ? $param['title'] : '';
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;	
		$description = isset($param['description']) ? $param['description'] : '';			
		$pay_rate = isset($param['pay_rate']) ? str_replace(',','',$param['pay_rate']) : '';		
		$need_applicant = isset($param['need_applicant']) ? str_replace(',','',$param['need_applicant']) : '';		
		$pay_rate = str_replace('.','',$pay_rate);
		$need_applicant = str_replace('.','',$need_applicant);
		$category = isset($param['category']) ? (int)$param['category'] : 0;
		$kategori = $this->access->readtable('kategori','',array('id_kategori'=>$category))->row();
		$region = isset($param['region']) ? (int)$param['region'] : 0;	
		$_region = $this->access->readtable('region','',array('id_region'=>$region))->row();
		$city = isset($param['city']) ? (int)$param['city'] : 0;		
		$_city = $this->access->readtable('city','',array('id_city'=>$city))->row();
		$start_date = isset($param['start_date']) && !empty($param['start_date']) ? new DateTime($param['start_date']) : '';
		$end_date = isset($param['end_date']) && !empty($param['end_date']) ? new DateTime($param['end_date']) : '';
		$diff = $start_date->diff($end_date)->days;
		$start_date = !empty($start_date) ? $start_date->format('Y-m-d') : '';
		$end_date = !empty($end_date) ? $end_date->format('Y-m-d') : '';
		$is_remote = isset($param['is_remote']) ? (int)$param['is_remote'] : 0;	
		$result = array();
		$_fee_task = $pay_rate / 50000;
		$fee_task = round($_fee_task,0,PHP_ROUND_HALF_UP);
		$dt_simpan = array();
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
		$_need_applicant = (int)$task->need_applicant;
		$appr_applicant = (int)$task->appr_applicant;
		if($_need_applicant == $appr_applicant){
			$result = [
					'err_code'	=> '06',
					'err_msg'	=> 'need applicant sudah cukup',
					'data'		=> null
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$dt_simpan = array(
			'id_member'			=> $id_member,
			'title_task'		=> $title,
			'deskripsi'			=> $description,
			'pay_rate'			=> $pay_rate,
			'need_applicant'	=> (int)$need_applicant,
			'id_cat'			=> (int)$category,
			'id_region'			=> (int)$region,
			'id_city'			=> (int)$city,
			'kategori'			=> $kategori->nama_kategori,
			'region_name'		=> $_region->region_name,
			'city_name'			=> $_city->nama_city,
			'start_date'		=> $start_date,
			'end_date'			=> $end_date,
			'duration'			=> (int)$diff,
			'fee_task'			=> (int)$fee_task,
			'is_remote'			=> (int)$is_remote,
			'created_at'		=> $tgl,
		);
		if($id_task > 0){
			$this->access->updatetable('task', $dt_simpan, array('id_task'=>$id_task));	
			$dt_simpan += array('id_task'	=> $id_task);			
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'Task updated',
					'err_msg_id'	=> 'Task berhasil di perbaharui',
					'data'		=> $dt_simpan,
				];
			$task = '';
			$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
			$need_applicant = 0;
			$need_applicant = (int)$task->need_applicant;
			$appr_applicant = (int)$task->appr_applicant;			
			error_log($_need_applicant);
	    	error_log($appr_applicant);			
			
			if($need_applicant == $appr_applicant){
				$this->access->updatetable('list_apply', array('status'=>3,'status_applicant'=>3), array('id_task'=>$id_task,'status'=>1));
				$task_upd =array('status'=>2);
				$dt_ok = $this->access->readtable('list_apply',array('id'),array('status'=>3,'status_applicant'=>3,'id_task'=>$id_task))->result_array();
				$dt_live = array();
				if(!empty($dt_ok)){
					foreach($dt_ok as $d){
						$dt_live[] = array(
							'id_apply'		=> $d['id'],
							'type'			=> 2,
							'ket'			=> 'Reject task by employer',
							'created_at'	=> $tgl
						);						
					}
					$this->db->insert_batch('live_update', $dt_live);		
				}
				$this->access->updatetable('task', $task_upd, array('id_task'=>$id_task));
			}
		}else{
			$result = [
					'err_code'	=> '03',
					'err_msg'	=> 'insert has problem',
					'data'		=> $dt_simpan,
				];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function del_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$this->access->updatetable('task', array('deleted_at'=>$tgl), array('id_task'=>$id_task));
		$cek_apply = 'SELECT id FROM list_apply WHERE id_task ='.$id_task.' and status IN (1,2,5,7)';
		$dt_cek_apply = $this->db->query($cek_apply)->result_array();
		$dt_upd  = array();
		$id_apply  = array();
		if(!empty($dt_cek_apply)){
			foreach($dt_cek_apply as $dc){
				$id_apply[] = '"'.$dc['id'].'"';
				$_id_apply = implode(',',$id_apply);	
				$dt_upd[] = array(
					'id'		=> $dc['id'],
					'status'	=> 9
				);
			}
			$sql = 'SELECT id_myvoucher FROM history_coin WHERE type = 2 and id_myvoucher is not null and id_ta IN ('.$_id_ta.')';
			error_log($sql);
			$_dt = $this->db->query($sql)->result_array();
			$dt_v = array();
			if(!empty($_dt)){
				foreach($_dt as $dd){
					$dt_v[] = array(
						'id'		=> $dd['id_myvoucher'],
						'is_used'	=> 0,
						'id_apply'	=> 0
					);
				}
			}
			$this->db->update_batch('my_voucher', $dt_v, 'id');
			$this->db->update_batch('list_apply', $dt_upd, 'id');
		}
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'Task successfully deleted.',				
					'err_msg_id'	=> 'Task berhasil di hapus.'					
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function del_img_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_img = isset($param['id_img']) ? (int)$param['id_img'] : 0;
		$this->access->updatetable('task_img', array('deleted_at'=>$tgl), array('id_img'=>$id_img));
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok'					
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function upl_img_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$config = array();
		$config['upload_path'] = "./uploads/task/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$simpan = array();
		$save = 0;
		if(!empty($_FILES['img'])){
			$upl = '';
			if($this->upload->do_upload('img')){
				$upl = $this->upload->data();
				$simpan = array("id_task"=>$id_task,"image" => $upl['file_name'],'created_at'=>$tgl);
				$save = $this->access->inserttable('task_img', $simpan);
				unset($simpan['image']);
				$simpan += array('image'=> base_url('uploads/task/'. $upl['file_name']),'id_img'=>$save);
			}
		}
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'id_img'	=> $save,
					'data'		=> $simpan
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function apply_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id_myvoucher = isset($param['id_myvoucher']) ? (int)$param['id_myvoucher'] : 0;
		$vouchers = $this->access->readtable('my_voucher','',array('id'=>$id_myvoucher,'id_member'=>$id_member,'date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d')))->row();
		if($id_task <= 0){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_task require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($id_member <= 0){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_member require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(!empty($vouchers)){
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'voucher not found or expired'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$is_used = (int)$vouchers->is_used;
		if($is_used > 0){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'voucher sudah digunakan sebelumnya'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$nilai_potongan = (int)$vouchers->nilai_potongan;
		$maks_potongan = (int)$vouchers->maks_potongan;
		$task = $this->access->readtable('task','',array('id_task'=>$id_task,'status'=>1,'id_member !=' => $id_member))->row();	
		$id_employer = $task->id_member;
		$dt_block = $this->cek_block($id_member, $id_employer);
		$id_block = $dt_block['id_block'];
		$id_user_yg_memblock = $dt_block['id_user_yg_memblock'];
		if($id_block > 0){
			$result = [
					'err_code'	=> '02',
					'err_msg'	=> 'Tidak bisa apply task ini, karena sedang diblock',
					'data'		=> $id_block
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($task)){
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'task not found or invalid'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'verify_acc'=>1))->row();
		$cek_task = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task))->row();
		if(!empty($cek_task)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'task already apply'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($login)){
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'applicant not found or verify_acc invalid'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$premium_start_date = !empty($login->premium_start_date) ? $login->premium_start_date : '';
		$premium_end_date = !empty($login->premium_end_date) ? $login->premium_end_date : '';
		$max_active_job = 1;		
		if(!empty($premium_start_date) && !empty($premium_end_date)){
			$_tgl = date('Y-m-d');
			if ($_tgl >= $premium_start_date && $_tgl <= $premium_end_date){
				$max_active_job = 2;
			}		
		}
		$cek_active_job = $this->access->readtable('list_apply','',array('id_member'=>$id_member),'','','','','','','', 'status',array(2,5))->result_array();
		$cnt_active_job = count($cek_active_job);
		$dt_maxjob = array();
		if($max_active_job <= (int)$cnt_active_job){			
			$dt_maxjob = array(
				'id_member'			=> $id_member,
				'active_job'		=> $cnt_active_job,
				'max_active_job'	=> $max_active_job
			);
			$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'active job over limit',
					'data'		=> $dt_maxjob
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$coin_member = (int)$login->coin;
		$coin_task = (int)$task->fee_task;
		$pot_voucher = 0;
		$kode_voucher = '';
		if($id_myvoucher > 0){
			$kode_voucher = $vouchers->kode_voucher;
			$tipe = $vouchers->tipe;
			if($tipe == 1){
				$pot_voucher = ($nilai_potongan/100) * $coin_task;
				$pot_voucher = (int)$pot_voucher > (int)$maks_potongan ? $maks_potongan : round($pot_voucher, 0);
			}
			if($tipe == 2){				
				$pot_voucher = $coin_task;
			}
		}		 
		$coin_digunakan = (int)$coin_task - (int)$pot_voucher;
		if($coin_member < $coin_digunakan){
			$result = [
					'err_code'		=> '05',
					'err_msg'		=> 'coin applicant not enough'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$jml_applicant = (int)$task->jml_applicant;
		$this->access->updatetable('task', array('jml_applicant'=>$jml_applicant + 1), array('id_task'=>$id_task));
		$coin_member = $coin_member - $coin_task;
		$this->access->updatetable('customer', array('coin'=>(int)$coin_member), array('id_customer'=>$id_member));	
		$dt_simpan = array();
		$dt_history = array();		
		$dt_simpan = array(
			'id_task' 			=> $id_task,
			'id_employer' 		=> $id_employer,
			'id_member' 		=> $id_member,
			'status' 			=> 1,
			'status_applicant' 	=> 1,
			'created_at' 		=> $tgl
		);
		$save = 0;
		$save = $this->access->inserttable('list_apply', $dt_simpan);
		$this->set_live_upd($save,1,'Apply Task');
		$dt_history = array(
			'id_ta' 		=> $save,
			'id_act' 		=> $id_task,
			'id_member' 	=> $id_member,
			'coin_task' 	=> $coin_task,
			'pot_voucher' 	=> $pot_voucher,
			'id_myvoucher' 	=> $id_myvoucher,
			'kode_voucher' 	=> $kode_voucher,
			'coin' 			=> $coin_digunakan,
			'ttl_coin' 		=> $coin_member,
			'type' 			=> 2,				// pemotongan apply task
			'created_at' 	=> $tgl,
			'ket' 			=> 'Apply task #'.$id_task
		);
		if($id_myvoucher > 0) $this->access->updatetable('my_voucher',array('is_used'=>$tgl_now,'id_apply'=>$save),array('id'=>$id_myvoucher,'id_member'=>$id_member));
		$this->access->inserttable('history_coin', $dt_history);
		
		$pesan = 'Task diapply oleh applicant';		
		$id_member_task = (int)$task->id_member;
		$this->insert_inbox($id_member_task, $pesan,3, $id_task);		
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'Task applied',				
					'err_msg_id'	=> 'Task diambil /  berhasil dipilih, mohon dicek secara berkala di halaman my task.'				
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function upd_apply(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$status = isset($param['status']) ? (int)$param['status'] : 3;
		$result = array();
		if($id_task <= 0){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_task require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($id_member <= 0){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_member require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task))->row();
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_apply = (int)$cek_apply->status;
		$id_apply = (int)$cek_apply->id;
		if($status_apply > 1){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant already update'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$task = '';
		$task = $this->access->readtable('task','',array('id_task'=>$id_task,'id_member !=' => $id_member))->row();
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		if($status == 2){			
			$need_applicant = (int)$task->need_applicant;
			$appr_applicant = (int)$task->appr_applicant;
			if($need_applicant == $appr_applicant){
				$result = [
					'err_code'		=> '08',
					'err_msg'		=> 'applicant already complete'
				];
				http_response_code(200);
				echo json_encode($result);
				return false;
			}
			$premium_start_date = !empty($login->premium_start_date) ? $login->premium_start_date : '';
			$premium_end_date = !empty($login->premium_end_date) ? $login->premium_end_date : '';
			$max_active_job = 1;		
			if(!empty($premium_start_date) && !empty($premium_end_date)){
				$_tgl = date('Y-m-d');
				if ($_tgl >= $premium_start_date && $_tgl <= $premium_end_date){
					$max_active_job = 2;
				}		
			}
			$cek_active_job = $this->access->readtable('list_apply','',array('id_member'=>$id_member),'','','','','','','', 'status',array(2,5))->result_array();
			$cnt_active_job = count($cek_active_job);
			$dt_maxjob = array();
			if($max_active_job <= (int)$cnt_active_job){			
				$dt_maxjob = array(
					'id_member'			=> $id_member,
					'active_job'		=> $cnt_active_job,
					'max_active_job'	=> $max_active_job
				);
				$result = [
						'err_code'	=> '04',
						'err_msg'	=> 'active job over limit',
						'data'		=> $dt_maxjob
					];
				http_response_code(200);
				echo json_encode($result);
				return false;
			}
			$_appr = $appr_applicant + 1;
			$task_upd = array();
			$task_upd = array('appr_applicant'=>(int)$_appr);
			if($_appr == 1){
				$task_upd += array('first_appr_date'=>$tgl);
			}			
			$fcm_token = '';
			$fcm_token = $login->fcm_token;
			$this->access->updatetable('list_apply', array('status'=>$status,'status_applicant'=>2,'appr_date'=>$tgl,'token_reminder'=>$fcm_token), array('id'=>$id_apply,'status'=>1));
			$this->set_live_upd($id_apply,3,'Approve Task by Employer');
			$pesan = 'Task diapprove oleh employer';		
			$this->insert_inbox($id_member, $pesan,2, $id_task);
			if((int)$_appr == $need_applicant){
				$this->access->updatetable('list_apply', array('status'=>3,'status_applicant'=>3), array('id_task'=>$id_task,'status'=>1));
				$task_upd +=array('status'=>2);
				$dt_ok = $this->access->readtable('list_apply',array('id'),array('status'=>3,'status_applicant'=>3,'id_task'=>$id_task))->result_array();
				$dt_live = array();
				if(!empty($dt_ok)){
					foreach($dt_ok as $d){
						$dt_live[] = array(
							'id_apply'		=> $d['id_apply'],
							'type'			=> 2,
							'ket'			=> 'Reject task by employer',
							'created_at'	=> $tgl
						);						
					}
					$this->db->insert_batch('live_update', $dt_live);		
				}
			}
			$this->access->updatetable('task', $task_upd, array('id_task'=>$id_task));
			$result = [
				'err_code'			=> '00',
				'err_msg'			=> 'Applicant accepted',
				'err_msg_id'			=> 'Aplikan berhasil diterima',
				'need_applicant'	=> $need_applicant,
				'appr_applicant'	=> $_appr,
			];
		}
		if($status == 3){			
			$coin_member = (int)$login->coin;
			$coin_task = (int)$task->fee_task;
			$coin_member = $coin_member + $coin_task;
			$this->access->updatetable('customer', array('coin'=>(int)$coin_member), array('id_customer'=>$id_member));
			$dt_history = array();
			$dt_history = array(
				'id_ta' 		=> $id_apply,
				'id_act' 		=> $id_task,
				'id_member' 	=> $id_member,
				'coin' 			=> $coin_task,
				'ttl_coin' 		=> $coin_member,
				'type' 			=> 3,				// refund apply task
				'created_at' 	=> $tgl,
				'ket' 			=> 'Refund apply task #'.$id_task
			);
			$this->access->inserttable('history_coin', $dt_history);
			$this->set_live_upd($id_apply,2,'Reject task by employer');
			$this->access->updatetable('list_apply', array('status'=>3,'status_applicant'=>3,'refund_date'=>$tgl), array('id'=>$id_apply,'status'=>1));
			$result = [
				'err_code'			=> '00',
				'err_msg'			=> 'Applicant rejected',
				'err_msg_id'			=> 'Aplikan berhasil ditolak',
				'need_applicant'	=> $need_applicant,
				'appr_applicant'	=> $_appr,
			];
		}
		
		http_response_code(200);
		echo json_encode($result);
	}
	
	function list_applicant(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();	
		$id_employer = $task->id_member;
		$dt_block = $this->get_all_block($id_employer);
		$field_notin = '';
		if(!empty($dt_block)){
			$field_notin = 'list_apply.id_member';
		}
		$select = array('customer.id_customer','customer.nama','customer.last_name','customer.accomp_task','customer.completed_task','customer.cnt_rating_applicant','customer.cnt_rating_employee','customer.rating_applicant','customer.rating_employee','customer.photo','list_apply.status','list_apply.id_task','list_apply.refund_date','list_apply.status_applicant','list_apply.id','list_apply.appr_date');
		$sort = array('abs(list_apply.id)','ASC');
		$dt = $this->access->readtable('list_apply',$select,array('list_apply.id_task'=>$id_task),array('customer'=>'customer.id_customer = list_apply.id_member'),'',$sort,'LEFT','', '', '', '', '', '', '', $field_notin,$dt_block)->result_array();
		$result = array();
		$dt_cust = array();
		if(!empty($dt)){
			foreach($dt as $members){
				$dt_cust[] = array(
					'id_apply'			=> $members['id'],
					'id_task'			=> $members['id_task'],
					'id_member'			=> $members['id_customer'],
					'nama'				=> $members['nama'],
					'last_name'			=> $members['last_name'],									
					'accomp_task'		=> $members['accomp_task'],			
					'completed_task'		=> (int)$members['completed_task'],			
					'rating_dari_applicant'	=> $members['rating_applicant'],			
					'rating_dari_employee'	=> $members['rating_employee'],							
					'cnt_rating_dari_applicant'	=> (int)$members['cnt_rating_applicant'],							
					'cnt_rating_dari_employee'	=> (int)$members['cnt_rating_employee'],							
					'status_employer'	=> (int)$members['status'] == 7 ? 3 : (int)$members['status'],							
					'status_applicant'	=> (int)$members['status_applicant'] == 7 ? 3 : (int)$members['status_applicant'],		
					'reject_date'		=> (int)$members['appr_date'],							
					'refund_date'		=> $members['refund_date'],							
					'photo'				=> !empty($members['photo']) ? base_url('uploads/photo_cv/'.$members['photo']) : ''		
					
				);
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt_cust
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found'
			];
		}
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function list_applicant_approve(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$select = array('customer.id_customer','customer.nama','customer.last_name','customer.accomp_task','customer.rating_applicant','customer.rating_employee','customer.photo','list_apply.status','list_apply.id_task','list_apply.completed_at','list_apply.status_applicant','list_apply.id','list_apply.complete_applicant_at','list_apply.appr_date','list_apply.rating_by_appl','list_apply.rating_by_emp','list_apply.ket_by_appl','list_apply.ket_by_emp','list_apply.ongoing_date','list_apply.status_bukti_pembayaran');
		$sort = array('abs(list_apply.id)','ASC');
		$dt = $this->access->readtable('list_apply',$select,array('list_apply.id_task'=>$id_task),array('customer'=>'customer.id_customer = list_apply.id_member'),'',$sort,'LEFT')->result_array();
		$result = array();
		$dt_cust = array();
		if(!empty($dt)){
			foreach($dt as $members){
				if((int)$members['status'] == 2 || (int)$members['status'] == 4 || $members['status'] == 5){
					$dt_cust[] = array(
						'id_apply'			=> $members['id'],
						'id_task'			=> $members['id_task'],
						'id_member'			=> $members['id_customer'],
						'nama'				=> $members['nama'],
						'last_name'			=> $members['last_name'],									
						'accomp_task'		=> $members['accomp_task'],			
						'rating_applicant'	=> $members['rating_applicant'],			
						'rating_employee'	=> $members['rating_employee'],							
						'status_employer'	=> (int)$members['status'] == 7 ? 3 : (int)$members['status'],							
						'status_applicant'	=> (int)$members['status_applicant'] == 7 ? 3 : (int)$members['status_applicant'],					
						'approve_date'		=> $members['appr_date'],
						'ongoing_date'		=> $members['ongoing_date'],
						'rating_by_emp'		=> $members['rating_by_emp'],			
						'rating_by_appl'	=> $members['rating_by_appl'],							
						'ket_by_emp'		=> $members['ket_by_emp'],							
						'ket_by_appl'		=> $members['ket_by_appl'],												
						'completed_at'		=> $members['completed_at'],							
						'complete_applicant_at'		=> $members['complete_applicant_at'],
						'status_bp'		=> (int)$members['status_bukti_pembayaran'] > 0 ? $members['status_bukti_pembayaran'] : 1,	
						'photo'				=> !empty($members['photo']) ? base_url('uploads/photo_cv/'.$members['photo']) : '',			
						
					);
				}
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt_cust
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found'
			];
		}
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function complete_task_applicant(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task,'status'=>5))->row();
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_apply = (int)$cek_apply->status_applicant;
		$id_apply = (int)$cek_apply->id;
		if($status_apply == 4){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant already update'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($status_apply == 3 || $status_apply == 7){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant rejected'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($status_apply == 2){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant pending'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$this->access->updatetable('list_apply', array('status_applicant'=>4,'complete_applicant_at'=>$tgl), array('id'=>$id_apply,'status'=>5));
		$this->set_live_upd($id_apply,5,'Task complete by applicant');
		$pesan = 'Task complete by applicant';
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();		
		$id_member_task = (int)$task->id_member;
		$this->insert_inbox($id_member_task, $pesan,4, $id_task);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function complete_task_employer(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task,'status'=>5))->row();
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$completed_task = (int)$login->completed_task;
		$completed_task = $completed_task + 1;
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_applicant = (int)$cek_apply->status_applicant;
		$status_employer = (int)$cek_apply->status;
		$id_apply = (int)$cek_apply->id;
		if($status_applicant <= 3){
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'task applicant belum selesai atau pending'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($status_employer == 4){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant already update'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$this->access->updatetable('list_apply', array('status'=>4,'completed_at'=>$tgl), array('id'=>$id_apply,'status'=>5));
		// $cek_apply = $this->access->readtable('list_apply','',array('id_task'=>$id_task,'status'=>5))->row();
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
		$appr_applicant = (int)$task->appr_applicant;
		$completed_applicant = (int)$task->completed_applicant;
		$_completed_applicant = 0;
		$_completed_applicant = $completed_applicant + 1;
		$task_upd = array();
		$task_upd = array('completed_applicant'=>(int)$_completed_applicant);
		if($_completed_applicant == $appr_applicant){
			$task_upd += array('completed_at'=>$tgl,'status'=>3);
		}
		$this->access->updatetable('customer', array('completed_task'=>(int)$completed_task), array('id_customer'=>$id_member));	
		$this->access->updatetable('task', $task_upd, array('id_task'=>$id_task));
		$this->set_live_upd($id_apply,5,'Task complete by employer');
		$pesan = 'Task complete by employer';		
		$this->insert_inbox($id_member, $pesan,5, $id_task,$id_apply);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function update_progress_appl(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task))->row();
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_applicant = (int)$cek_apply->status_applicant;
		$status_employer = (int)$cek_apply->status;
		$id_apply = (int)$cek_apply->id;
		if($status_employer == 4){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'task completed'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($status_applicant == 3 || $status_applicant == 7){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant rejected'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($status_employer == 5){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status applicant already update'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_applicant = (int)$cek_apply->status_applicant;
		$status_employer = (int)$cek_apply->status;
		$id_apply = (int)$cek_apply->id;
		$this->access->updatetable('list_apply', array('status'=>5,'status_applicant'=>5,'ongoing_date'=>$tgl), array('id'=>$id_apply,'status'=>2));
		$this->set_live_upd($id_apply,4,'Task on progress by applicant');
		$pesan = 'Task diprogress oleh applicant';
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
		$id_member_task = (int)$task->id_member;
		$this->insert_inbox($id_member_task, $pesan,2, $id_task);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function top_applicant(){
		$tgl = date('Y-m-d H:i:s');		
		$sort = array('abs(completed_task)','DESC');
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? $param['keyword'] :'';
		
		$_like = array();
		if(!empty($keyword)){
			$keyword = $this->db->escape_str($keyword);
			$_like = array('customer.nama'=> $keyword);
		}
		$login = $this->access->readtable('customer','',array('deleted_at'=>null),'','',$sort,'','',$_like)->result_array();
		
		if(!empty($login)){
			foreach($login as $l){
				$dt_cust[] = array(
					'id_member'		=> $l['id_customer'],
					'nama'			=> $l['nama'],
					'last_name'		=> $l['last_name'],
					'dob'			=> $l['dob'],				
					'completed_task'		=> $l['completed_task'],			
					'rating_applicant'		=> $l['rating_applicant'],			
					'rating_employee'		=> $l['rating_employee'],							
					'cnt_rating_applicant'	=> $l['cnt_rating_applicant'],							
					'cnt_rating_employee'	=> $l['cnt_rating_employee'],							
					'photo'			=> !empty($l['photo']) ? base_url('uploads/photo_cv/'.$l['photo']) : ''					
				);
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt_cust
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found'
			];
		}
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function rating_applicant(){ //rating by appl
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$rating = isset($param['rating']) ? (int)$param['rating'] : 0;
		$comment = isset($param['comment']) ? $param['comment'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task))->row();
		if($rating > 5){
			$result = [
					'err_code'		=> '08',
					'err_msg'		=> 'rating maksimal 5'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_applicant = (int)$cek_apply->status_applicant;
		$status_employer = (int)$cek_apply->status;
		$rating_by_appl = (int)$cek_apply->rating_by_appl;
		$id_apply = (int)$cek_apply->id;
		if($status_applicant == 2){
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'task belum diapprove oleh employer'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($rating_by_appl > 0){
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'task sudah di rating sebelumnya'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
		$id_member_task = (int)$task->id_member;
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member_task))->row();
		$jml_rating_applicant = (int)$login->jml_rating_applicant + $rating;
		$cnt_rating_applicant = (int)$login->cnt_rating_applicant + 1;
		$rating_applicant = $jml_rating_applicant / $cnt_rating_applicant;
		$_rating_applicant = round($rating_applicant,0,PHP_ROUND_HALF_UP);
		$this->access->updatetable('customer', array('rating_applicant'=>$rating_applicant,'jml_rating_applicant'=>$jml_rating_applicant,'cnt_rating_applicant'=>$cnt_rating_applicant), array('id_customer'=>$id_member));
		$this->access->updatetable('list_apply', array('rating_by_appl'=>$rating,'ket_by_appl'=>$comment), array('id'=>$id_apply,'status'=>4));
		$this->access->updatetable('task', array('rating'=>$_rating_applicant), array('id_member'=>$id_member_task,'status <'=>2,'deleted_at'=>null));
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'Thank you for your review',
				'err_msg_id'	=> 'Terima kasih atas ulasannya',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function rating_employer(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$rating = isset($param['rating']) ? (int)$param['rating'] : 0;
		$comment = isset($param['comment']) ? $param['comment'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id_member'=>$id_member,'id_task'=>$id_task))->row();
		if($rating > 5){
			$result = [
					'err_code'		=> '08',
					'err_msg'		=> 'rating maksimal 5'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($cek_apply)){
			$result = [
					'err_code'		=> '03',
					'err_msg'		=> 'applicant not apply this task'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status_applicant = (int)$cek_apply->status_applicant;
		$status_applicant = (int)$cek_apply->status;
		$rating_by_emp = (int)$cek_apply->rating_by_emp;
		
		$id_apply = (int)$cek_apply->id;
		if($status_applicant == 2){
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'task belum diapprove oleh employer'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($rating_by_emp > 0){
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'task sudah di rating sebelumnya'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$jml_rating_employee = (int)$login->jml_rating_employee + $rating;
		$cnt_rating_employee = (int)$login->cnt_rating_employee + 1;
		$rating_employee = $jml_rating_employee / $cnt_rating_employee;
		
		
		$this->access->updatetable('customer', array('rating_employee'=>$rating_employee,'jml_rating_employee'=>$jml_rating_employee,'cnt_rating_employee'=>$cnt_rating_employee), array('id_customer'=>$id_member));
		
		
		$this->access->updatetable('list_apply', array('rating_by_emp'=>$rating,'ket_by_emp'=>$comment), array('id'=>$id_apply,'status'=>4));
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'Thank you for your review',
				'err_msg_id'	=> 'Terima kasih atas ulasannya',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function report_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$reason = isset($param['reason']) ? $param['reason'] : '';
		$dt_simpan = array(
			'id_task' 			=> $id_task,
			'id_member_report' 	=> $id_member,
			'reason' 			=> $reason,
			'status' 			=> 1,			
			'created_at' 		=> $tgl
		);
		$save = 0;
		$save = $this->access->inserttable('task_report', $dt_simpan);
		$dt_simpan += array('id_report' => $save);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'Report Submitted',
				'data'		=> $dt_simpan
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function get_review_empty(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		
		$sql = 'SELECT list_apply.id as id_apply,list_apply.id_employer,list_apply.id_member,list_apply.status,list_apply.id_task,list_apply.refund_date,list_apply.status_applicant,list_apply.appr_date,task.title_task,task.deskripsi,task.duration,task.region_name,task.city_name,task.pay_rate,list_apply.appr_date,list_apply.rating_by_appl,list_apply.rating_by_emp,list_apply.ket_by_appl,list_apply.ket_by_emp,list_apply.completed_at,list_apply.complete_applicant_at from list_apply left join task on task.id_task = list_apply.id_task where list_apply.status = 4 and (list_apply.ket_by_appl is null or list_apply.ket_by_emp is null) and (list_apply.id_member ='.$id_member.' or list_apply.id_employer ='.$id_member.') order by list_apply.id ASC';
		$dt_res = array();
		$_dt = $this->db->query($sql)->result_array();
		if(!empty($_dt)){
			foreach($_dt as $dt=>$val){
				$dt_res[] = $val;
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $dt_res
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=> null
			];
		}
			
		http_response_code(200);
		echo json_encode($result);
		
	}
	
	function set_live_upd($id=0, $type=0,$ket=''){
		$tgl = date('Y-m-d H:i:s');
		$dt = array(
			'id_apply'		=> $id,
			'type'			=> $type,
			'ket'			=> $ket,
			'created_at'	=> $tgl
		);
		$this->access->inserttable('live_update', $dt);		
	}
	
	function set_progress(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		$progress = isset($param['progress']) ? (int)$param['progress'] : 0;
		$dt = array(
			'id_apply'		=> $id,
			'type'			=> 4,
			'ket'			=> $progress,
			'created_at'	=> $tgl
		);
		$this->access->inserttable('live_update', $dt);		
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> null
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	
	
	function get_live_upd(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$sort = array('ABS(id)','DESC');
		$id = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id'=>$id))->row();
		$live_upd = $this->access->readtable('live_update','',array('id_apply'=>$id),'','',$sort)->result_array();
		$id_employer_apply = $cek_apply->id_employer;
		$id_member_apply = $cek_apply->id_member;
		$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$id_employer_apply.','.$id_member_apply.')';
		$_dt_member = $this->db->query($sql_member)->result_array();
		$replace = array();
		$replace_with = array();
		if($id_member > 0){
			if(!empty($_dt_member)){
				foreach($_dt_member as $dm){
					$dt_member[$dm['id_customer']] = !empty($dm['last_name']) ? $dm['nama'].' '.$dm['last_name'] : $dm['nama'];
				}
			}
			if($id_employer_apply == $id_member){
				$replace = ['employer','Employer','applicant','Applicant'];
				$replace_with = ['You','You',$dt_member[$id_member_apply],$dt_member[$id_member_apply]];	
			}
			if($id_member_apply == $id_member){
				$replace = ['employer','Employer','applicant','Applicant'];
				$replace_with = [$dt_member[$id_employer_apply],$dt_member[$id_employer_apply],'You','You'];
				// $replace_applicant = ['applicant','Applicant'];
				// $replace_applicant_with = $dt_member[$id_member_apply];
			}
		}
		$_res = array();
		if(!empty($live_upd)){
			foreach($live_upd as $key=>$val){
				$ket_text = '';
				$ket_text = $val['ket'];
				if($val['type'] == 4) $ket_text = 'Progress Task '.$val['ket'];
				if(is_numeric($val['ket'])) $ket_text .='%';
				$val += array('ket_text' => $ket_text);
				$val = str_replace($replace, $replace_with, $val);				
				$_res[] = $val;
			}				
		}
		
		if(!empty($_res)){
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $_res
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=> null
			];
		}
		http_response_code(200);
		echo json_encode($result);
		
	}
	
	function get_info_review(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		$cek_apply = $this->access->readtable('list_apply','',array('id'=>$id_apply))->row();
		$id_employer = $cek_apply->id_employer;
		$id_member = $cek_apply->id_member;
		$id_task = $cek_apply->id_task;
		$task = $this->access->readtable('task','',array('id_task'=>$id_task))->row();
		$login_employer = $this->access->readtable('customer','',array('id_customer'=>$id_employer))->row();
		$login_member = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$_res = array(
			'id_apply'		=> $id_apply,
			'id_employer'	=> $id_employer,
			'id_member'		=> $id_member,
			'nama_emp'		=> $login_employer->nama.' '.$login_employer->last_name,
			'nama_appl'		=> $login_member->nama.' '.$login_member->last_name,
			'photo_emp'		=> !empty($login_employer->photo) ? base_url('uploads/photo_cv/'.$login_employer->photo) : '',	
			'photo_appl'	=> !empty($login_member->photo) ? base_url('uploads/photo_cv/'.$login_member->photo) : '',	
			'title_task'	=> $task->title_task,
			'deskripsi'		=> $task->deskripsi
		);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $_res
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function insert_inbox($id_member=0, $pesan = '', $type=0, $_id=0,$id_apply=0){
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'ABS(notif_status_task)'=>1))->row();
		$this->load->library('send_notif');			
		$send_fcm = '';	
		$ids = array();
		$ids = array($login->fcm_token);
		$notif_status_task = (int)$login->notif_status_task;
		$data_fcm = array(
			'id'					=> $_id,
			'id_member_to'			=> $id_member,
			'nama_pengirim'			=> '',
			'last_name_pengirim'	=> '',
			'title'					=> 'Tasker',
			'type'					=> $type				
		);
		if($type == 5) $data_fcm += array('id_apply'=>$id_apply);
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
		if($notif_status_task > 0) $this->access->inserttable('master_chat', $dt);		
	}
	
	function notify_me($id_employer=0, $id_task=0){
		$this->load->library('send_notif');
		$tgl = date('Y-m-d H:i:s');
		$select = array('customer.*');
		$list_follower = $this->access->readtable('list_follower',$select,array('list_follower.id_employer'=>$id_employer,'ABS(customer.notif_notifyme)' => 1),array('customer'=>'customer.id_customer = list_follower.id_applicant'),'','','LEFT')->result_array();
		// error_log($this->db->last_query());
		$ids = array();
		$dt = array();
		$send_fcm = '';
		if(!empty($list_follower)){
			foreach($list_follower as $gm){
				if(!empty($gm['fcm_token'])){
					array_push($ids, $gm['fcm_token']);
					$dt[] = array(
						'id_member_from'	=> $gm['id_customer'] ,
						'id_member_to'		=> $gm['id_customer'] ,
						'pesan'				=> 'New Task',
						'status_from'		=> 1,
						'status_to'			=> 1,
						'type'				=> 9,
						'_id'				=> $id_task,
						'created_at'		=> $tgl,
					);
				}
			}
			$data_fcm = array(
				'id'					=> $id_task,
				'id_member_to'			=> 0,
				'nama_pengirim'			=> '',
				'last_name_pengirim'	=> '',
				'title'					=> 'Tasker',
				'type'					=> 9				
			);
			$notif_fcm = array(
				'title'		=> 'Tasker',
				'body'		=> 'New Task',
				'badge'		=> 0,
				'sound'		=> 'Default'
			);
			if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);
			if(!empty($dt))	$this->db->update_batch('master_chat', $dt);		
			error_log(serialize($ids));
			error_log(serialize($send_fcm));
		}		
	}
	
	function test($id_member=0){
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'ABS(notif_status_task)'=>1))->row();
		$sql = $this->db->last_query();
		$this->load->library('send_notif');			
		$send_fcm = '';	
		$ids = array();
		$ids = array($login->fcm_token);
		$notif_status_task = (int)$login->notif_status_task;
		$data_fcm = array(
			'id'					=> 5,
			'id_member_to'			=> $id_member,
			'nama_pengirim'			=> '',
			'last_name_pengirim'	=> '',
			'title'					=> 'Tasker',
			'type'					=> 7				
		);
		if($type == 5) $data_fcm += array('id_apply'=>6);
		$notif_fcm = array(
			'title'		=> 'Tasker',
			'body'		=> $pesan,
			'badge'		=> $type,
			'sound'		=> 'Default'
		);
		if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);
		echo $sql;
		print_r($ids);
		print_r($send_fcm);
		// $tgl = date('Y-m-d H:i:s');
		// $dt = array(
			// 'id_member_from'	=> $id_member,
			// 'id_member_to'		=> $id_member,
			// 'pesan'				=> $pesan,
			// 'status_from'		=> 1,
			// 'status_to'			=> 1,
			// 'type'				=> $type,
			// '_id'				=> $_id,
			// 'created_at'		=> $tgl,
		// );
		// if($notif_status_task > 0) $this->access->inserttable('master_chat', $dt);		
	}
	
	function cek_block($id_member_login=0, $id_member=0){
		$where = array('id_member1'=>$id_member_login,'id_member2'=>$id_member2,'deleted_at'=>null);
		$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
		$id_block = (int)$dt_block->id > 0 ? (int)$dt_block->id : 0;
		$id_user_yg_memblock = 0;
		if($id_block > 0){
			$id_user_yg_memblock = $id_member_login;			
		}else{
			$dt_block = '';
			$where = array('id_member1'=>$id_member,'id_member2'=>$id_member_login,'deleted_at'=>null);
			$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
			$id_block = (int)$dt_block->id > 0 ? (int)$dt_block->id : 0;
			if($id_block > 0){
				$id_user_yg_memblock = $id_member;
			}			
		}
		$dt = array(
			'id_user_yg_memblock'	=> $id_user_yg_memblock,
			'id_block'				=> $id_block
		);
		return $dt;
	}
	
	function get_all_block($id_member_login=0){
		$data = array();
		$sql = "select * from list_user_blok where deleted_at is null and (id_member1 = $id_member_login or id_member2 = $id_member_login)";
		$_dt = $this->db->query($sql)->result_array();
		$dt = array();
		if(!empty($_dt)){
			foreach($_dt as $d){
				$data[$d['id_member1']] = array('id_member' => $d['id_member1']);
				$data[$d['id_member2']] = array('id_member' => $d['id_member2']);			
			}
			unset($data[$id_member_login]);
		}
		foreach($data as $key=>$dtt){
			array_push($dt, $key);
		}
		return $dt;
		
	}
	
	function upl_bukti_bayar(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		
		$config = array();
		$config['upload_path'] = "./uploads/bukti_pembayaran/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$simpan = array();
		
		if(!empty($_FILES['img'])){
			$upl = '';
			if($this->upload->do_upload('img')){
				$upl = $this->upload->data();
				$simpan += array("bukti_pembayaran" => base_url('uploads/bukti_pembayaran/'.$upl['file_name']),'upload_date_bukti_pembayaran'=>$tgl,'status_bukti_pembayaran'	=> 1);		
			}
		}
		$this->access->updatetable('list_apply', $simpan, array('id'=>$id_apply));
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $simpan
			];
		http_response_code(200);
		echo json_encode($result);	
	}
	
}
