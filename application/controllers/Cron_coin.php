<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_coin extends CI_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model('Access','access',true);		
    }
	
	function index(){
		$tgl = date('Y-m-d H:i:s');
	    // ini_set('memory_limit', '256M');
		$this->db->trans_begin();
		$sort = array('ABS(id)','ASC');
		$where = array();
		$where = array('refund_date'=>null,'status'=>6);
		$select = array('id','id_member','status','id_task');
		$dt_ok = $this->access->readtable('list_apply',$select,$where,'',30,$sort,'','','','',0)->result_array();
		if(empty($dt_ok)){
			$dt_ok = '';
			$where = array();
			$where = array('refund_date'=>null,'status'=>7);
			$dt_ok = $this->access->readtable('list_apply',$select,$where,'',30,$sort,'','','','',0)->result_array();
		}
		if(empty($dt_ok)){
			$dt_ok = '';
			$where = array();
			$where = array('refund_date'=>null,'status'=>3);
			$dt_ok = $this->access->readtable('list_apply',$select,$where,'',50,$sort,'','','','',0)->result_array();
		}
		if(empty($dt_ok)){
			$dt_ok = '';
			$where = array();
			$where = array('refund_date'=>null,'status'=>9);
			$dt_ok = $this->access->readtable('list_apply',$select,$where,'',50,$sort,'','','','',0)->result_array();
		}
		$id_ta = array();
		$id_member = array();
		$_dt = '';
		$dt_history = array();
		$dt_member = array();
		$dt_upd = array();
		$dt_inbox = array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $d){
				$id_ta[] = '"'.$d['id'].'"';
				$id_member[] = '"'.$d['id_member'].'"';
				$_id_ta = implode(',',$id_ta);				
				$_id_member = implode(',',$id_member);				
				if($d['status'] == 6){
					$pesan = 'Task #'.$d['id_task'].' di take down oleh admin';
					$dt_inbox[] = array(
						'id_member_from'	=> $d['id_member'],
						'id_member_to'		=> $d['id_member'],
						'pesan'				=> $pesan,
						'status_from'		=> 1,
						'status_to'			=> 1,
						'type'				=> 8,
						'_id'				=> $d['id_task'],
						'created_at'		=> $tgl,
					);
				}
				if($d['status'] == 7){
					$pesan = 'Cancel Task #'.$d['id_task'];
					$dt_inbox[] = array(
						'id_member_from'	=> $d['id_member'],
						'id_member_to'		=> $d['id_member'],
						'pesan'				=> $pesan,
						'status_from'		=> 1,
						'status_to'			=> 1,
						'type'				=> 7,
						'_id'				=> $d['id_task'],
						'created_at'		=> $tgl,
					);
				}
				if($d['status'] == 9){
					$pesan = 'Deleted Task #'.$d['id_task'].' by employer';
					$dt_inbox[] = array(
						'id_member_from'	=> $d['id_member'],
						'id_member_to'		=> $d['id_member'],
						'pesan'				=> $pesan,
						'status_from'		=> 1,
						'status_to'			=> 1,
						'type'				=> 7,
						'_id'				=> $d['id_task'],
						'created_at'		=> $tgl,
					);
				}
			}
			$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$_id_member.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
			$dt_coin = array();
			foreach($_dt_member as $dm){
				$dt_coin[$dm['id_customer']] = (int)$dm['coin'];
			}
			$sql = 'SELECT * FROM history_coin WHERE type = 2 and id_ta IN ('.$_id_ta.')';
			$_dt = $this->db->query($sql)->result_array();
			if(!empty($_dt)){
				foreach($_dt as $dd){
					$_coin[$dd['id_member']] = (int)$dd['coin'];
					$dt_coin[$dd['id_member']] += $_coin[$dd['id_member']];
					
					$dt_history[] = array(
						'id_ta' 		=> $dd['id_ta'],
						'id_act' 		=> $dd['id_act'],
						'id_member' 	=> $dd['id_member'],
						'coin' 			=> $dd['coin'],
						'ttl_coin' 		=> (int)$dt_coin[$dd['id_member']],
						'type' 			=> 3,				// refund apply task
						'created_at' 	=> $tgl,
						'ket' 			=> 'Refund apply task #'.$dd['id_act']	
					);
					$dt_upd[] = array(
						'id' 			=> $dd['id_ta'],						
						'refund_date'	=> $tgl
					);
					$dt_member[$dd['id_member']] = array(
						'id_customer' 	=> $dd['id_member'],						
						'coin'	=> (int)$dt_coin[$dd['id_member']]
					);
					
				}
			}		
		}
		if(!empty($dt_inbox))$this->db->insert_batch('master_chat', $dt_inbox);
		if(!empty($dt_history) && !empty($dt_upd)){
			$this->db->update_batch('customer', $dt_member, 'id_customer');
			$this->db->update_batch('list_apply', $dt_upd, 'id');
			$this->db->insert_batch('history_coin', $dt_history);						
		}else{
			$this->db->trans_commit();
			$this->cek_task();
			$this->cek_complete();
		}
		
		$this->db->trans_commit();
	}
	
	function bonus_prem(){
		// $tgl = date('Y-m-d');
		$tgl = date('Y-m-d H:i:s');
		$this->db->trans_begin();
		$sort = array('ABS(id)','ASC');
		$where = array('date_format(tgl, "%Y-%m-%d") <= '=>$tgl,'status'=>0);
		$select = array('id','id_member','id_transaksi','coin');
		$dt_ok = $this->access->readtable('cron_coin_premium',$select,$where,'',50,$sort,'','','','',0)->result_array();
		
		$id_member = array();
		$_dt = '';
		$dt_history = array();
		$dt_member = array();
		$dt_upd = array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $d){
				
				$id_member[] = '"'.$d['id_member'].'"';
								
				$_id_member = implode(',',$id_member);				
				
			}
			$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$_id_member.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
			$dt_coin = array();
			foreach($_dt_member as $dm){
				$dt_coin[$dm['id_customer']] = (int)$dm['coin'];
			}
			foreach($dt_ok as $dd){
				$_coin[$dd['id_member']] = (int)$dd['coin'];
				$dt_coin[$dd['id_member']] += $_coin[$dd['id_member']];
				$dt_history[] = array(
					'id_ta' 		=> $dd['id_transaksi'],
					'id_act' 		=> $dd['id_transaksi'],
					'id_member' 	=> $dd['id_member'],
					'coin' 			=> (int)$dd['coin'],
					'ttl_coin' 		=> (int)$dt_coin[$dd['id_member']],
					'type' 			=> 4,				// bonus premium
					'created_at' 	=> $tgl,
					'ket' 			=> 'Bonus coin premium #'.$dd['id_transaksi']	
				);				
				$dt_upd[] = array(
					'id' 			=> $dd['id'],						
					'status'		=> 1
				);
				$dt_member[] = array(
					'id_customer' 	=> $dd['id_member'],						
					'coin'			=> (int)$dt_coin[$dd['id_member']]
				);
			}
			
		}
		if(!empty($dt_history) && !empty($dt_upd)){
			$this->db->update_batch('customer', $dt_member, 'id_customer');
			$this->db->update_batch('cron_coin_premium', $dt_upd, 'id');
			$this->db->insert_batch('history_coin', $dt_history);			
		}
		$this->db->trans_commit();
	}
	
	function cek_task(){
		$tgl = date('Y-m-d H:i:s');
		$this->db->trans_begin();
		$sort = array('ABS(id_task)','ASC');
		$where = array('date_format(start_date, "%Y-%m-%d") <'=>$tgl,'status'=>1,'deleted_at'=>null);
		$select = array('id_task');
		$dt_ok = $this->access->readtable('task',$select,$where,'',50,$sort,'','','','',0)->result_array();
		$id_ta = array();
		$id_task = '';
		$dt_upd = array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $dd){	
				$id_ta[] = $dd['id_task'];
				$id_task = implode(',',$id_ta);	
				$dt_upd[] = array(
					'id_task' 	=> $dd['id_task'],						
					'status'	=> 4
				);				
			}
		}
		$sql = '';
		if(!empty($dt_upd)){			
			$this->db->update_batch('task', $dt_upd, 'id_task');	
			$sql = 'UPDATE list_apply set status = 7, status_applicant = 7 WHERE (status = 1 or status = 2) and id_task IN ('.$id_task.')';	
			$this->db->query($sql);
			$dt_oks = $this->access->readtable('list_apply',array('id_apply'),array('status'=>7,'status_applicant'=>7),'',50)->result_array();
			$dt_live = array();
			if(!empty($dt_ok)){
				foreach($dt_ok as $d){
					$dt_live[] = array(
						'id_apply'		=> $d['id_apply'],
						'type'			=> 2,
						'ket'			=> 'Reject task by system',
						'created_at'	=> $tgl
					);						
				}
				$this->db->insert_batch('live_update', $dt_live);		
			}
			
		}
		$this->db->trans_commit();
	}
	
	function cek_complete(){
		$select = array();
		$this->db->trans_begin();
		$sort = array('ABS(id)','ASC');
		$tgl = date('Y-m-d H:i:s');
		$tgl_exec = date('Y-m-d H:i', strtotime('-7 day', strtotime($tgl)));
		$where = array('date_format(complete_applicant_at, "%Y-%m-%d %H:%i") <='=>$tgl_exec,'status'=>5,'ststus_applicant'=>4,'refund_date'=>null);
		$dt_ok = $this->access->readtable('list_apply',$select,$where,'',30,$sort)->result_array();		
		$dt_upd = array();
		$id_member = array();
		$id_ta = array();
		$dt_coin = array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $d){
				$id_member[] = '"'.$d['id_member'].'"';								
				$_id_member = implode(',',$id_member);
				$id_ta[] = '"'.$d['id_task'].'"';
				$_id_ta = implode(',',$id_ta);	
				$dt_upd[] = array(
					'id' 			=> $d['id'],						
					'status'		=> 4,
					'completed_by'	=> -1
				);
				$this->set_live_upd($d['id'],5,'Task complete by system');
			}
			$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$_id_member.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
			$dt_coin = array();
			foreach($_dt_member as $dm){
				$dt_coin[] = array(
					'id_customer'		=> (int)$dm['id_customer'],
					'completed_task'	=> (int)$dm['completed_task'] + 1
				);
			}
		}
		$dt_task = array();
		if(!empty($dt_upd)){			
			$this->db->update_batch('list_apply', $dt_upd, 'id');			
			$this->db->update_batch('customer', $dt_coin, 'id_customer');
			$sql_task = 'SELECT * FROM task WHERE id_task IN ('.$_id_ta.')';
			$_dt_task = $this->db->query($sql_member)->result_array();
			foreach($_dt_task as $dm){				
				if($dm['appr_applicant'] == $dm['completed_applicant']){
					$dt_task[] = array(
						'id_task'		=> (int)$dm['id_task'],
						'completed_at'	=> $tgl,
						'status'		=> 3
					);
					
				}
				
			}
		}
		if(!empty($dt_task)){
			$this->db->update_batch('task', $dt_task, 'id_task');	
		}
		$this->db->trans_commit();
	}
	
	function set_emp(){
		$dt_upd = array();
		$dt_ok = $this->access->readtable('task',array('id_task','id_member'))->result_array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $d){
				
				$dt_upd[] = array(
					'id_task' 		=> $d['id_task'],						
					'id_employer' 	=> $d['id_member']
				);
			}			
		}
		if(!empty($dt_upd)){
			$this->db->update_batch('list_apply', $dt_upd, 'id_task');	
		}
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
	
	function inbox_canceled(){
		$tgl = date('Y-m-d H:i:s');
	    // ini_set('memory_limit', '256M');
		$this->db->trans_begin();
		$sort = array('ABS(id)','ASC');
		$where = array();
		$where = array('refund_date'=>null,'status'=>7,'status_applicant'=>7);
		$select = array('id','id_member','status','id_task');
		$dt_ok = $this->access->readtable('list_apply',$select,$where,'',30,$sort,'','','','',0)->result_array();
		$dt_inbox = array();
		$dt_upd = array();
		if(!empty($dt_ok)){
			foreach($dt_ok as $d){
				$pesan = 'Task #'.$d['id_task'].' cancelled';
				$dt_inbox[] = array(
					'id_member_from'	=> $d['id_member'],
					'id_member_to'		=> $d['id_member'],
					'pesan'				=> $pesan,
					'status_from'		=> 1,
					'status_to'			=> 1,
					'type'				=> 7,
					'_id'				=> $d['id_task'],
					'created_at'		=> $tgl,
				);
				$dt_upd[] = array(
					'id' 			=> $d['id'],						
					'status'		=> 3,
					'status_applicant'	=> 3
				);
			}
		}
		if(!empty($dt_upd)) $this->db->update_batch('list_apply', $dt_upd, 'id');
		if(!empty($dt_inbox)) $this->db->insert_batch('master_chat', $dt_inbox);	
		$this->db->trans_commit();
	}
	
	function send_reminder(){
		$tgl = date('Y-m-d H:i:s');
		$sort = array('abs(task.id_task)','ASC');
		$where = array('date_format(task.start_date, "%Y-%m-%d") >= '=>$tgl,'task.deleted_at'=>null,'ABS(is_reminder)'=>0);
		$task = $this->access->readtable('task',array('id_task'),$where,'',array(200),$sort,'','','')->result_array();
		$id_ta = array();
		$_dt_member = array();
		if(!empty($task)){
			foreach($task as $t){
				$id_ta[] = '"'.$t['id_task'].'"';
				$_id_ta = implode(',',$id_ta);
				$upd_task[] = array(
					'id_task'		=> $t['id_task'],
					'is_reminder'	=> 1,
				);
			}
			$sql_member = 'SELECT id,token_reminder FROM list_apply WHERE token_reminder!=1 and status = 2 and id_task IN ('.$_id_ta.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
		}
		$ids = array();
		$notif_fcm = array();
		$data_fcm = array();
		$upd_apply = array();
		if(!empty($_dt_member)){
			foreach($_dt_member as $dm){
				$notif_fcm = array(
					'title'		=> 'Tasker',
					'body'		=> 'Reminder task',
					'badge'		=> 1,
					'sound'		=> 'Default'
				);
				$data_fcm = array(
					'title'		=> 'Tasker',
					'type'		=> 11				
				);
				if(!empty($dm['token_reminder'])){
					array_push($ids, $dm['token_reminder']);
				}
				$upd_apply = array(
					'id '				=> $dm['id'],
					'token_reminder'	=> 1
				);
			}
		}
		$this->load->library('send_notif');			
		$send_fcm = '';	
		if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);
		if(!empty($upd_task)) $this->db->update_batch('task', $upd_task, 'id_task');
		if(!empty($upd_apply)) $this->db->update_batch('list_apply', $upd_apply, 'id');
	}
	
	
	function test_cron_gc(){
		$tgl = date('Y-m-d H:i:s');
		error_log('test_cron_gc : cron from google');
	}
	
	
	
}