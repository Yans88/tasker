<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Member extends CI_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model('Access','access',true);
		$this->load->model('Setting_m','sm', true);
		$this->load->library('converter');
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
    }
	
	function generate_referal_code() {
		$token = '';
		do{
			$token = '';
			$_tgl = date("YmdHis");
			$_day = date("l");
			$alphabet  = $_tgl.'ABCDEFGH1IJKL2MN3OPQ4RST5UVW6XYZ7abcd8efgh9ijklmnopqrst0uvwxyz'.$_day;
			$token = substr(str_shuffle($alphabet), 0, 7);
			$token = strtoupper($token);
			$chk_token = $this->access->readtable('customer',array('id_customer'),array('referal_code'=>$token))->row();
		}while((int)$chk_token > 0);
		return $token;		
	}
	
	function generate_phone_token() {
		$token = '';
		do{
			$token = '';
			$_tgl = date("YmdHis");
			$_day = date("l");
			$numerics = '0123456789'.$_tgl;
			$alphabet  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$_day;		
			$token = substr(str_shuffle($numerics), 0, 4);
			$chk_token = $this->access->readtable('customer',array('id_customer'),array('verify_phone'=>$token))->row();
		}while((int)$chk_token > 0);
		return $token;
		
	}
	
    function reg(){	
		$tgl = date('Y-m-d H:i:s');
		$login = '';
		$param = $this->input->post();
		$first_name = isset($param['first_name']) ? $param['first_name'] : '';
		$last_name = isset($param['last_name']) ? $param['last_name'] : '';		
		$email = isset($param['email']) ? strtolower($param['email']) : '';		
		$pin = isset($param['pin']) ? $param['pin'] : '';		
		$phone_number = isset($param['phone_number']) ? $param['phone_number'] : '';		
		$referal_code = isset($param['referal_code']) ? strtoupper($param['referal_code']) : '';		
		
		$login = '';
		$result = array();
		if(empty($first_name)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'first_name require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($email)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'email require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {			
			$result = [
					'err_code'		=> '05',
					'err_msg'		=> 'email invalid format'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($pin)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'pin require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(strlen($pin) < 6){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'pin must be 6 digits'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(empty($phone_number)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'phone_number require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$ptn = "/^0/";
		$rpltxt = "62";
		$phone_number = preg_replace($ptn, $rpltxt, $phone_number);		
		$login = $this->access->readtable('customer','',array('email'=>$email))->row();
		$id_customer =  0;
		$id_customer = (int)$login->id_customer;
		if($id_customer > 0){			
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'email already exist'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('phone'=>$phone_number))->row();
		$id_customer =  0;
		$id_customer =  (int)$login->id_customer;
		if($id_customer > 0){			
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'phone_number already exist'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$invite_by = '';
		$invite_code = '';
		if(!empty($referal_code)){
			$login = $this->access->readtable('customer','',array('UPPER(referal_code)'=>$referal_code,'verify_acc'=>1))->row();					
			$invite_by = (int)$login->id_customer;
			$invite_code = $referal_code;
			if($invite_by <= 0){					
				$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'referal_code not found'
				];
				http_response_code(200);
				echo json_encode($result);
				return false;
			}
		}
		$generate_referal_code = $this->generate_referal_code();
		$generate_phone_token = $this->generate_phone_token();
		$dt_simpan = array();
		$dt_simpan = array(
				'nama'			=> $first_name,
				'last_name'		=> $last_name,
				'email'			=> $email,
				'phone'			=> $phone_number,
				'coin'			=> 0,			
				'pin'			=> md5($pin),
				'_pin'			=> $this->converter->encode($pin),
				'referal_code'	=> $generate_referal_code,
				'status'		=> 0,
				'verify_acc'	=> 0,
				'verify_email'	=> 0,
				'verify_phone'	=> $generate_phone_token,
				'invite_by'		=> $invite_by,
				'invite_code'	=> $invite_code,
				'created_at'	=> $tgl
			);
		
		$save = 0;
		$id = '';
		$save = $this->access->inserttable('customer', $dt_simpan);
		//send sms OTP
		if((int)$save > 0){
			$this->load->library('send_api');
			$to_sms = $phone_number;
			$a = "Tasker, Token for register : ".$generate_phone_token;
			$from = "EDOT";
			$url_sms = "http://www.etracker.cc/bulksms/mesapi.aspx?user=Andtechtest01&pass=@Pass123&type=0&to=".$to_sms."&from=".rawurlencode($from)."&text=".rawurlencode($a)."&servid=TET032";
			// error_log($url_sms);
			$send = $this->send_api->send_data($url_sms, '','','','POST');
			
		}
		$id = $this->converter->encode($save);
		$this->access->updatetable('customer', array('_id'=>$id), array('id_customer'=>$save));		
		unset($dt_simpan['verify_email']);
		unset($dt_simpan['pin']);
		unset($dt_simpan['_pin']);
		$dt_history_token = array(
			'id_member'		=> $save,
			'phone'			=> $phone_number,
			'token'			=> $generate_phone_token,
			'tipe'			=> 1,
			'ket'			=> 'Register',
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> $save,
			'type'			=> 1,
			'title'			=> 'Achievement 1',
			'keterangan'	=> 'Verify KTP',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> $save,
			'type'			=> 2,
			'title'			=> 'Achievement 2',
			'keterangan'	=> 'Complete Profile by Filling',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> (int)$id_member,
			'type'			=> 3,
			'title'			=> 'Achievement 3',
			'keterangan'	=> 'Bonus achievement transaksi coin 1x',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> (int)$id_member,
			'type'			=> 4,
			'title'			=> 'Achievement 4',
			'keterangan'	=> 'Bonus achievement subscription 1x',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> (int)$id_member,
			'type'			=> 5,
			'title'			=> 'Achievement 5',
			'keterangan'	=> 'Bonus achievement complete task 1x',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$dt_achievement[] = array(
			'id_member'		=> (int)$id_member,
			'type'			=> 6,
			'title'			=> 'Achievement 6',
			'keterangan'	=> 'Bonus achievement post task complete 1x',
			'coin'			=> 1,
			'status'		=> -1,
			'created_at'	=> $tgl
		);
		$this->db->insert_batch('achievement', $dt_achievement);
		$this->access->inserttable('history_token', $dt_history_token);
		$dt_simpan += array('id_member'	=> $save,'is_premium' => 0,'premium_start_date'	=> '','premium_end_date' => '');
		if((int)$save > 0 && (int)$invite_by > 0){
			$pesan = 'Free coin referal akan di dapat setelah verifikasi ktp';
			$this->insert_inbox($save, $pesan,6, $save);
		}
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt_simpan,
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function send_verify_email(){		
		$link = '';
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		if(empty($id_member)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_member require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'verify_email'=>0))->row();
		$id_customer =  (int)$login->id_customer;
		if($id_customer > 0){
			$id = $login->_id;
			$link = VERIFY_REGISTER_LINK.'='.urlencode($id);
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> array('url'=>$link),
				];
		}else{
			$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'data not found'
				];
		}
		http_response_code(200);
		echo json_encode($result);
	}

	function verify_phone(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$token = isset($param['token']) ? strtoupper($param['token']) : '';
		
		if(empty($token) || empty($id_member)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_member & token require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'verify_phone'=>$token))->row();
		$id_customer =  (int)$login->id_customer;
		if($id_customer > 0){
			$data = array('verify_phone' => 1,'status'=>1);
			$this->access->updatetable('customer',$data, array("id_customer" => $id_customer));	
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok'
				];
		}else{
			$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'data not found'
				];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function resend_token(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		if(empty($id_member)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_member require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'verify_phone !='=>1))->row();
		$id_customer =  (int)$login->id_customer;
		if($id_customer > 0){
			$token = array('verify_phone' => $login->verify_phone);
			//send sms OTP
			$this->load->library('send_api');
			$to_sms = $login->phone;
			$a = "Tasker, Token for register : ".$login->verify_phone;
			$from = "EDOT";
			$url_sms = "http://www.etracker.cc/bulksms/mesapi.aspx?user=Klikdiskon01&pass=YWtdn)0G&type=0&to=".$to_sms."&from=".rawurlencode($from)."&text=".rawurlencode($a)."&servid=TET032";
			$this->send_api->send_data($url_sms, '','','','POST');
		
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $token,
				];
		}else{
			$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'data not found'
				];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function login(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$pin = isset($param['pin']) && !empty($param['pin']) ? md5($param['pin']) : '';		
		$phone_number = isset($param['phone_number']) ? $param['phone_number'] : '';
		$token = isset($param['token']) ? $param['token'] : '';
		if(empty($pin) || empty($phone_number)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'pin && phone_number require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$ptn = "/^0/";
		$rpltxt = "62";
		$phone_number = preg_replace($ptn, $rpltxt, $phone_number);	
		$where = array();
		$where = array('phone'=>$phone_number,'pin'=>$pin);
		if(!empty($token)) $where = array('verify_login'=>$token);
		$login = $this->access->readtable('customer','',$where)->row();
		$id_customer =  (int)$login->id_customer;
		$status =  (int)$login->status;
		$verify_phone =  (int)$login->verify_phone;
		$dt_cust = array();
		$result = array();
		$generate_phone_token = '';
		$tokenku = array();
		if($id_customer > 0){
			if($verify_phone != 1){
				$result = [
					'err_code'	=> '03',
					'err_msg'	=> 'Failed, silahkan verifikasi No.Hp',
					'id_member'	=> $id_member,
					'data'		=> array('id_member'=>$id_customer)
				];
			}
			
			if($status == 2){
				$result = [
					'err_code'	=> '08',
					'err_msg'	=> 'Failed, akun di non aktifkan oleh admin',
					'id_member'	=> $id_member,
					'data'		=> array('id_member'=>$id_customer)
				];
				http_response_code(200);
				echo json_encode($result);	
				return false;
			}
			
			if($verify_phone == 1){				
				$dt_cust = array(
					'id_member'		=> $id_customer,
					'nama'			=> $login->nama,
					'last_name'		=> $login->last_name,
					'email'			=> $login->email,
					'phone'			=> $phone_number,
					'coin'			=> $login->coin,
					'is_premium'	=> 0,
					'premium_start_date'	=> $login->premium_start_date,
					'premium_end_date'		=> $login->premium_end_date,					
					'referal_code'	=> (int)$login->verify_acc == 1 ? $login->referal_code : '-',
					'completed_task'	=> $login->completed_task,
					'status'		=> $status,
					'verified_acc'	=> $login->verify_acc,
					'verify_email'	=> $login->verify_email,
					'verify_phone'	=> $verify_phone,
					'invite_by'		=> $login->invite_by,
					'invite_code'	=> $login->invite_code
				);
				$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt_cust
				];
			}
		}else{
			$result = [
					'err_code'	=> '06',
					'err_msg'	=> 'login invalid'
				];
		}
		http_response_code(200);
		echo json_encode($result);		
	}
	
	function forgot_pin(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$email = isset($param['email']) ? strtolower($param['email']) : '';	
		if(empty($email)){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'email require'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {			
			$result = [
					'err_code'		=> '05',
					'err_msg'		=> 'email invalid format'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('email'=>$email))->row();
		$id_customer =  0;
		$id_customer = (int)$login->id_customer;
		$result = array();
		
		if($id_customer <= 0){			
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'email not found'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		// if((int)$login->verify_email <= 0){
			// $result = [
					// 'err_code'		=> '07',
					// 'err_msg'		=> 'please, verify your email'
				// ];
			// http_response_code(200);
			// echo json_encode($result);
			// return false;
		// }
		$opsi_val_arr = $this->sm->get_key_val();
		foreach ($opsi_val_arr as $key => $value){
			$out[$key] = $value;
		}
		$this->load->library('send_notif');
		$from = $out['email'];
		$pass = $out['pass'];
		$to = $email;
		$subject = 'Forgot PIN';
		$phone = $login->phone;
		$_pin = mt_rand(100000, 999999);
		$content_member = $out['content_forgotPin'];
		$content = str_replace('[#phone_number#]', $phone, $content_member);
		$content = str_replace('[#pin#]', $_pin, $content);
		$data = array('pin' => md5($_pin),'_pin'=>$this->converter->encode($_pin));
		$this->access->updatetable('customer',$data, array("id_customer" => $id_customer));
		$this->send_notif->send_email($from,$pass, $to,$subject, $content);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'OK, New password was send to your email',
				'pin'		=> $_pin
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function profile(){
		$tgl = date('Y-m-d');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id_member_view = isset($param['id_member_view']) ? (int)$param['id_member_view'] : 0;
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$id_customer =  (int)$login->id_customer;
		$premium_start_date = !empty($login->premium_start_date) ? $login->premium_start_date : '';
		$premium_end_date = !empty($login->premium_end_date) ? $login->premium_end_date : '';
		$is_premium = 0;
		if(!empty($premium_start_date) && !empty($premium_end_date)){
			if ($tgl >= $premium_start_date && $tgl <= $premium_end_date){
				$is_premium = 1;
			}		
		}
	
		$cek_active_job = $this->access->readtable('list_apply','',array('id_member'=>$id_member),'','','','','','','', 'status',array(2,5))->result_array();
		
		$cnt_active_job = count($cek_active_job);
		$dt_cust = array();
		$result = array();
		$id_chat = 0;
		$is_followed = 0;
		$id_block = 0;
		$id_user_yg_memblock = 0;
		if($id_member_view > 0 && $id_member_view != $id_member){
			$sql = 'SELECT master_chat.id_chat FROM `master_chat` WHERE (id_member_to = '.$id_member.' or id_member_to = '.$id_member_view.') AND (id_member_from = '.$id_member_view.' or id_member_from = '.$id_member.') and type=1 order by master_chat.updated_at DESC';			
			$dt = $this->db->query($sql)->row();
			$id_chat = (int)$dt->id_chat > 0 ? (int)$dt->id_chat : 0;
			$followed = $this->access->readtable('list_follower',array('id_employer'),array('id_applicant'=>$id_member_view,'id_employer'=>$id_member))->row();
			$is_followed = !empty($followed) || (int)$followed->id_employer > 0 ? 1 : 0;
			$dt_block = $this->cek_block($id_member_view, $id_member);		
			error_log($this->db->last_query());			
			$id_block = $dt_block['id_block'];
			$id_user_yg_memblock = $dt_block['id_user_yg_memblock'];
		}
		if($id_customer > 0){
			$dt_cust = array(
				'id_member'		=> $id_customer,
				'nama'			=> $login->nama,
				'last_name'		=> $login->last_name,
				'dob'			=> $login->dob,
				'email'			=> $login->email,
				'phone'			=> $login->phone,
				'coin'			=> $login->coin,
				'is_premium'	=> (int)$is_premium,
				'active_job'	=> (int)$cnt_active_job,
				'premium_start_date'	=> $premium_start_date,
				'premium_end_date'		=> $premium_end_date,					
				'accomp_task'		=> $login->accomp_task,			
				'completed_task'		=> $login->completed_task,			
				'rating_dari_applicant'		=> (int)$login->rating_applicant > 0 ? $login->rating_applicant : 5,			
				'rating_dari_employee'		=> (int)$login->rating_employee > 0 ? $login->rating_employee : 5,							
				'cnt_rating_dari_applicant'	=> $login->cnt_rating_applicant,							
				'cnt_rating_dari_employee'	=> $login->cnt_rating_employee,							
				'photo'			=> !empty($login->photo) ? base_url('uploads/photo_cv/'.$login->photo) : '',			
				'cv_file'		=> !empty($login->cv_file) ? base_url('uploads/photo_cv/'.$login->cv_file) : '',			
				'facebook'		=> $login->fb,			
				'instagram'		=> $login->ig,			
				'twitter'		=> $login->twitter,			
				'linkedin'		=> $login->linkedin,			
				'info'		=> $login->info,			
				'referal_code'	=> (int)$login->verify_acc == 1 ? $login->referal_code : '-',
				'status'		=> $login->status,
				'verified_acc'	=> (int)$login->verify_acc,
				'verify_email'	=> $login->verify_email,
				'verify_phone'	=> $login->verify_phone,
				'verify_login'	=> $login->verify_login,
				'invite_by'		=> $login->invite_by,
				'invite_code'	=> $login->invite_code,
				'id_chat'		=> (int)$id_chat,
				'is_followed'	=> (int)$is_followed,
				'id_block'	=> (int)$id_block,
				'id_user_yg_memblock'	=> (int)$id_user_yg_memblock,
				'fcm_token'		=> $login->fcm_token,
				'device'		=> (int)$login->device
			);
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
		if((int)$login->achievement2 <= 0){
			$login_achievement = $this->access->readtable('customer','',array('id_customer'=>$id_member,'cv_file is not null'=>null,'fb is not null'=>null,'ig is not null'=>null,'twitter is not null'=>null,'linkedin is not null'=>null,'abs(achievement2)'=>0))->row();
			if(!empty($login_achievement)){
				$this->access->updatetable('customer',array('achievement2'=>1), array("id_customer" => $id_member,'abs(achievement2)'=>0));
				
				$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 2,'status' => -1));
			}
		}
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function chg_pin(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$old_pin = isset($param['old_pin']) ? $param['old_pin'] : '';
		$new_pin = isset($param['new_pin']) ? $param['new_pin'] : '';
		if($id_member == 0 || empty($old_pin) || empty($new_pin)){
			$result = [
				'err_code'	=> '07',
				'err_msg'	=> 'id_member, old_pin and new_pin require'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(strlen($new_pin) < 6){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'pin must be 6 digits'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member,'pin'=>md5($old_pin)))->row();
		$_id = (int)$login->id_customer > 0 ? (int)$login->id_customer : 0;
		if($_id > 0){
			$this->access->updatetable('customer',array('pin'=>md5($new_pin),'_pin'	=> $this->converter->encode($new_pin)), array("id_customer" => $_id));
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok'
			];
			http_response_code(200);
			echo json_encode($result);
		}else{
			$result = [
				'err_code'	=> '05',
				'err_msg'	=> 'old_pin not match'
			];
			http_response_code(200);
			echo json_encode($result);
		}
	}
	
	function verifikasi_user(){
		$result = array();
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$ktp_name = isset($param['ktp_name']) ? $param['ktp_name'] : '';
		$ktp_number = isset($param['ktp_number']) ? $param['ktp_number'] : '';
		$upl = '';
		$simpan = array();
		if($id_member == 0 || empty($ktp_name) || empty($ktp_number) || empty($_FILES['img_ktp']['name']) || empty($_FILES['img_selfie']['name'])){
			$result = [
				'err_code'	=> '07',
				'err_msg'	=> 'id_member, ktp_name and ktp_number, img_ktp, img_selfie require'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$config['upload_path'] = "./uploads/ktp_selfie/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';
		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$simpan = array('ktp_name'=>$ktp_name,'ktp_number'=>$ktp_number,'verify_acc'=>2);
		if(!empty($_FILES['img_ktp'])){
			$upl = '';
			if($this->upload->do_upload('img_ktp')){
				$upl = $this->upload->data();
				$simpan += array("photo_ktp"	=> $upl['file_name']);
			}
		}
		if(!empty($_FILES['img_selfie'])){
			$upl = '';
			if($this->upload->do_upload('img_selfie')){
				$upl = $this->upload->data();
				$simpan += array("photo_selfie"	=> $upl['file_name']);
			}
		}
		$this->access->updatetable('customer',$simpan, array("id_customer" => $id_member));
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'Photo / receipt succesfuly uploaded. Thank you, your transaction is being processed. Coin balance will be added after validation by our team.',
			'err_msg_id'	=> 'Foto berhasil di tambahkan. Terimakasih, transaksi anda sedang diproses. Koin akan otomatis bertambah setelah divalidasi oleh team kami.',
		];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function edit(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$first_name = isset($param['first_name']) ? $param['first_name'] : '';
		$last_name = isset($param['last_name']) ? $param['last_name'] : '';
		$info = isset($param['info']) ? $param['info'] : '';
		$email = isset($param['email']) ? strtolower($param['email']) : '';	
		$dob = isset($param['dob']) && !empty($param['dob']) ? new DateTime($param['dob']) : '';
		$dob = !empty($dob) ? $dob->format('Y-m-d') : '';
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();	
		$_email = strtolower($login->email);	
		if($id_member == 0 || empty($email)){
			$result = [
				'err_code'	=> '07',
				'err_msg'	=> 'id_member, email require'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {			
			$result = [
					'err_code'		=> '05',
					'err_msg'		=> 'email invalid format'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$_login = '';
		$id_customer = 0;
		$dt_simpan = array();
		$dt_simpan = array(
				'nama'			=> $first_name,
				'last_name'		=> $last_name,
				'dob'			=> $dob,
				'info'			=> $info,
				'email'			=> $email
			);
		$link = '';
		if($email != $_email) {
			$_login = $this->access->readtable('customer','',array('email'=>$email))->row();
			$id_customer = !empty($_login) ? (int)$_login->id_customer : 0;
			$id = $login->_id;
			$link = VERIFY_REGISTER_LINK.'='.urlencode($id);
			$dt_simpan += array("verify_email"	=> 0);
		}
		
		if($id_customer > 0){			
			$result = [
					'err_code'		=> '07',
					'err_msg'		=> 'email already exist'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		
		$config['upload_path'] = "./uploads/photo_cv/";
		$config['allowed_types'] = "*";
		$config['max_size']	= '4096';
		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$upl = '';
		if(!empty($_FILES['cv_file'])){
			$upl = '';
			if($this->upload->do_upload('cv_file')){
				$upl = $this->upload->data();
				$dt_simpan += array("cv_file"	=> $upl['file_name']);
			}
		}
		if(!empty($_FILES['photo'])){
			$upl = '';
			if($this->upload->do_upload('photo')){
				$upl = $this->upload->data();
				$dt_simpan += array("photo"	=> $upl['file_name']);
			}
		}
		$this->access->updatetable('customer',$dt_simpan, array("id_customer" => $id_member));
		$login_achievement = $this->access->readtable('customer','',array('id_customer'=>$id_member,'cv_file is not null'=>null,'fb is not null'=>null,'ig is not null'=>null,'twitter is not null'=>null,'linkedin is not null'=>null,'abs(achievement2)'=>0))->row();
		if(!empty($login_achievement)){
			$this->access->updatetable('customer',array('achievement2'=>1), array("id_customer" => $id_member,'abs(achievement2)'=>0));
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 2,'status' => -1));
		}
		$dt_simpan += array('url'=>$link);
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'Profile updated.',
			'err_msg_id'	=> 'Profil anda telah berhasil diperbaharui.',
			'data'		=> $dt_simpan
		];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function add_social(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$fb = isset($param['fb']) ? $param['fb'] : '';
		$ig = isset($param['ig']) ? $param['ig'] : '';
		$twitter = isset($param['twitter']) ? $param['twitter'] : '';
		$linkedin = isset($param['linkedin']) ? $param['linkedin'] : '';
		$dt_simpan = array(
				'fb'		=> $fb,
				'ig'		=> $ig,
				'twitter'	=> $twitter,
				'linkedin'	=> $linkedin
			);
		$this->access->updatetable('customer',$dt_simpan, array("id_customer" => $id_member));
		$login_achievement = $this->access->readtable('customer','',array('id_customer'=>$id_member,'cv_file is not null'=>null,'fb is not null'=>null,'ig is not null'=>null,'twitter is not null'=>null,'linkedin is not null'=>null,'abs(achievement2)'=>0))->row();
		if(!empty($login_achievement)){
			$this->access->updatetable('customer',array('achievement2'=>1), array("id_customer" => $id_member,'abs(achievement2)'=>0));
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 2,'status' =>-1));
			
		}
		$dt_simpan += array('id_member'=>$id_member);
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'ok',
			'data'		=> $dt_simpan
		];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function add_wishlist(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$task = $this->access->readtable('task','',array('id_task'=>$id_task,'task.deleted_at'=>null))->row();
		$result = array();
		if(empty($task)){
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$cek_wishlist = $this->access->readtable('wishlist_task','',array('id_member'=>$id_member,'id_task'=>$id_task,'deleted_at'=>null))->row();
		if(!empty($cek_wishlist)){
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Sudah di wishlist sebelumnya'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$status = (int)$task->status;
		$id_member_task = (int)$task->id_member;
		$need_applicant = (int)$task->need_applicant;
		$jml_applicant = (int)$task->jml_applicant;
		$start_date = $task->start_date;
		$_tgl = date('Y-m-d');
		if($id_member == $id_member_task){
			$result = [
				'err_code'		=> '06',
				'id_member'	=> $id_member,
				'id_member_task'	=> $id_member_task,
				'err_msg'		=> 'Not Available, id member harus berbeda dengan id member task'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($jml_applicant == $need_applicant){
			$result = [
				'err_code'		=> '03',
				'need_applicant'	=> $need_applicant,
				'jml_applicant'	=> $jml_applicant,
				'err_msg'		=> 'Not Available, need applicant sudah cukup'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if ($_tgl >= $start_date){
			$result = [
				'err_code'		=> '03',
				'start_date'	=> $start_date,
				'err_msg'		=> 'Not Available, start date sudah lewat'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$dt = array(
			'id_member'	=> $id_member,
			'id_task'	=> $id_task,
			'created_at'	=> $tgl
		);
		$save = $this->access->inserttable('wishlist_task', $dt);
		if($save){
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'Added to favourite',
				'err_msg_id'	=> 'Sukses menambahkan ke favorit',
				'data'		=> $dt
			];
		}else{
			$result = [
				'err_code'	=> '05',
				'err_msg'	=> 'insert has problem',
				'data'		=> $dt
			];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function del_wishlist(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		
		$this->access->deletetable('wishlist_task', array("id_member" => $id_member,'id_task'=>$id_task));
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'Removed from favourite',
				'err_msg_id'	=> 'Sukses menghapus favorit',
				'data'		=> $param
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function my_wishlist(){
		$param = $this->input->post();
		$id_task = isset($param['id_task']) ? (int)$param['id_task'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$_sort = isset($param['sort']) ? (int)$param['sort'] : 1;
		$sort = array('abs(wishlist_task.id)','ASC');
		if($_sort == 2) $sort = array('abs(wishlist_task.id)','DESC');
		$where = array('wishlist_task.deleted_at'=>null,'wishlist_task.id_member'=>$id_member,'task.deleted_at'=>null,'task.status !='=>3);
		$select = array('task.*','customer.nama','customer.last_name','customer.photo');
		$task = $this->access->readtable('wishlist_task',$select,$where,array('task'=>'task.id_task = wishlist_task.id_task','customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT')->result_array();
		$_tgl = date('Y-m-d');
		if(!empty($task)){
			foreach($task as $t){
				$start_date = '';
				$need_applicant = '';
				$jml_applicant = '';
				$status = '';
				$start_date = $t['start_date'];
				$need_applicant = (int)$t['need_applicant'];
				$jml_applicant = (int)$t['jml_applicant'];
				$status = (int)$t['status'];				
				$is_available = 1;
				if ($_tgl >= $start_date || $need_applicant >= $jml_applicant || $status > 2){
					$is_available = 0;
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
					'is_available'		=> (int)$is_available,
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
	
	function my_apply_task(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$where = array();
		$where = array('list_apply.id_member'=>$id_member);
		$field_in = '';
		$where_in = '';
		if($status == 2){ //onprocess
			$field_in = 'list_apply.status';
			$where_in = array(2,5);
		}
		if($status == 3){ //reject
			$field_in = 'list_apply.status';
			$where_in = array(3,6,7);
		}
		if($status == 4 || $status == 1){ 
			$field_in = '';
			$where_in = '';
			$where += array('list_apply.status' => $status);
		}
		
		$select = array('customer.id_customer','customer.nama','customer.last_name','customer.accomp_task','customer.rating_applicant','customer.rating_employee','customer.photo','list_apply.status','list_apply.id_task','list_apply.refund_date','list_apply.status_applicant','list_apply.id','list_apply.appr_date','task.title_task','task.deskripsi','task.duration','task.region_name','task.city_name','task.pay_rate','list_apply.appr_date','list_apply.rating_by_appl','list_apply.rating_by_emp','list_apply.ket_by_appl','list_apply.ket_by_emp','list_apply.completed_at','list_apply.complete_applicant_at');
		$sort = array('abs(list_apply.id)','ASC');
		$dt = $this->access->readtable('list_apply',$select,$where,array('task'=>'task.id_task = list_apply.id_task','customer'=>'customer.id_customer = task.id_member'),'',$sort,'LEFT','','','', $field_in, $where_in)->result_array();
		
		$result = array();
		$dt_cust = array();
		$favorite = array();
		$dt_fav = $this->access->readtable('wishlist_task','',array('id_member'=>$id_member))->result_array();
		if(!empty($dt_fav)){
			foreach($dt_fav as $df){
				array_push($favorite, $df['id_task']);
			}
		}
		if(!empty($dt)){
			foreach($dt as $members){
				$is_wishlist = 0;
				if (in_array($t['id_task'], $favorite)){
					$is_wishlist = 1;
				}
				$task_img = '';
				$dt_img = array();
				$task_img = $this->access->readtable('task_img','',array('id_task'=>$members['id_task'],'deleted_at'=>null))->result_array();
				if(!empty($task_img)){
					foreach($task_img as $ti){
						$dt_img[] = array(
							'id_img'	=> $ti['id_img'],
							'image'		=> !empty($ti['image']) ? base_url('uploads/task/'.$ti['image']) : '',
						);
					}
				}
				$dt_cust[] = array(
					'id_apply'			=> $members['id'],
					'id_task'			=> $members['id_task'],
					'no_task'			=> $members['no_task'],
					'id_member'			=> $members['id_customer'],
					'nama'				=> $members['nama'],
					'last_name'			=> $members['last_name'],	
					'title_task'		=> $members['title_task'],
					'deskripsi'			=> $members['deskripsi'],
					'pay_rate'			=> $members['pay_rate'],
					'duration'			=> $members['duration'],					
					'region_name'		=> $members['region_name'],
					'city_name'			=> $members['city_name'],
					'accomp_task'		=> $members['accomp_task'],			
					'rating_by_emp'		=> $members['rating_by_emp'],			
					'rating_by_appl'	=> $members['rating_by_appl'],							
					'ket_by_emp'		=> $members['ket_by_emp'],							
					'ket_by_appl'		=> $members['ket_by_appl'],									
					'status_employer'	=> (int)$members['status'] == 7 || (int)$members['status'] == 6 ? 3 : (int)$members['status'],			
					'status_applicant'	=> (int)$members['status_applicant'] == 7 || (int)$members['status'] == 6 ? 3 : (int)$members['status_applicant'],							
					'reject_date'		=> (int)$members['appr_date'],
					'completed_at'		=> $members['completed_at'],							
					'complete_applicant_at'		=> $members['complete_applicant_at'],
					'refund_date'		=> $members['refund_date'],	
					'is_wishlist'		=> $is_wishlist,	
					'photo'				=> !empty($members['photo']) ? base_url('uploads/photo_cv/'.$members['photo']) : '',		
					'image'				=> $dt_img
						
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
	
	//SELECT * FROM `chat_detail` WHERE (id_member_to = 2 or id_member_from = 2) AND (id_member_to = 1 or id_member_from = 1)
	
	function list_chat(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$type = isset($param['type']) ? (int)$param['type'] : 0;
		
		$dt_block = $this->get_all_block($id_member);
		$sql = 'SELECT master_chat.* FROM `master_chat` WHERE (id_member_to = '.$id_member.' or id_member_from = '.$id_member.') AND (id_member_to = '.$id_member.' or id_member_from = '.$id_member.')';
		if($type == 1) $sql .=' and master_chat.type = 1';
		if($type == 2) $sql .=' and master_chat.type > 1';
		$sql .=' order by master_chat.updated_at DESC';
		$dt = $this->db->query($sql)->result_array();		
		$_res = array();
		$result = array();
		$id_cust = array();
		$_id_member = '';
		if(!empty($dt)){
			foreach($dt as $_dd){
				$id_cust[$_dd['id_member_from']] = '"'.$_dd['id_member_from'].'"';
				$id_cust[$_dd['id_member_to']] = '"'.$_dd['id_member_to'].'"';
				$_id_member = implode(',',$id_cust);			
			}
			$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$_id_member.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
			foreach($_dt_member as $dm){
				$nama[$dm['id_customer']] = $dm['nama'];
				$last_name[$dm['id_customer']] = $dm['last_name'];
			}
			foreach($dt as $_d){
				if($_d['id_member_from'] == $id_member){
					$cnt_unread = $_d['status_from'];
				}
				
				if($_d['id_member_to'] == $id_member){
					$cnt_unread = $_d['status_to'];
				}	
				$is_block = 0;
				if (in_array($_d['id_member_to'], $dt_block) || in_array($_d['id_member_from'], $dt_block)){
					$is_block = 1;
				}
				$_res[] = array(
					'id_chat'			=> $_d['id_chat'],
					'id_act'			=> (int)$_d['type'] == 1 ? $_d['id_chat'] : $_d['_id'],
					'type'				=> $_d['type'],
					'id_member_from'	=> $_d['id_member_from'],
					'nama_from'			=> $nama[$_d['id_member_from']],
					'last_name_from'	=> $last_name[$_d['id_member_from']],
					'id_member_to'		=> $_d['id_member_to'],
					'nama_to'			=> $nama[$_d['id_member_to']],
					'last_name_to'		=> $last_name[$_d['id_member_to']],
					'pesan'				=> $_d['pesan'],
					'gbr'				=> $_d['gbr'],
					'cnt_unread'		=> (int)$cnt_unread,
					'status_from'		=> $_d['status_from'],
					'status_to'			=> $_d['status_to'],
					'updated_at'		=> $_d['updated_at'],
					'is_block'			=> (int)$is_block
				);
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $_res
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
	
	function send_chat(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_chat = isset($param['id_chat']) ? (int)$param['id_chat'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id_member_to = isset($param['id_member_to']) ? (int)$param['id_member_to'] : 0;
		$dt_block = $this->cek_block($id_member, $id_member_to);
		$id_block = (int)$dt_block['id_block'];
		if($id_block > 0){
			$result = [
					'err_code'	=> '02',
					'err_msg'	=> 'Tidak bisa mengirim pesan, karena sedang diblock',
					// 'data'		=> $id_block
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$pesan = isset($param['pesan']) ? $param['pesan'] : '';
		$status_from = 1;
		$status_to = 1;
		$save = 0;
		$dt_upd = array();
		$config['upload_path'] = "./uploads/chat/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';
		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$gbr = '';
		if(!empty($_FILES['gbr'])){
			$upl = '';
			if($this->upload->do_upload('gbr')){
				$upl = $this->upload->data();
				$gbr = base_url('uploads/chat/'.$upl['file_name']);								
			}
		}
		if($id_chat > 0){
			$dt = $this->access->readtable('master_chat','',array('id_chat' => $id_chat))->row();
			$status_from = (int)$dt->status_from + $status_from;
			$status_to = (int)$dt->status_to + $status_to;
			$id_member_from = (int)$dt->id_member_from;
			$_id_member_to = (int)$dt->id_member_to;
			if($id_member_from == $id_member){
				$dt_upd = array('status_to' => $status_to);
			}
			
			if($_id_member_to == $id_member){
				$dt_upd = array('status_from' => $status_from);
			}
			$dt_upd += array(
				'pesan'			=> $pesan,
				'gbr'			=> $gbr,
				'_id'			=> $id_chat
			);
			$this->access->updatetable('master_chat',$dt_upd, array("id_chat" => $id_chat));
			$save = $id_chat;
		}else{
			$dt_upd = array();
			$dt_upd = array(
				'id_member_from'	=> $id_member,
				'id_member_to'		=> $id_member_to,
				'pesan'				=> $pesan,
				'status_to' 		=> $status_to,
				'gbr'				=> $gbr,
				'type' 				=> 1,
				'created_at'		=> $tgl
			);
			$save = $this->access->inserttable('master_chat', $dt_upd);
		}
		$dt_res = array();
		$result = array();
		$ids = array();
		$notif_fcm = array();
		if((int)$save > 0){			
			
			$data_save = array(
				'id_chat'			=> $save,
				'id_member_from'	=> $id_member,
				'id_member_to'		=> $id_member_to,
				'pesan'				=> $pesan,
				'gbr'				=> $gbr,
				'status_from'		=> 0,
				'status_to'			=> 1,
				'created_at'		=> $tgl,
				'created_by'		=> $id_member
			);
			$this->access->inserttable('chat_detail', $data_save);
			
			$data_fcm = array(
				'id'			=> $save,
				'id_member_to'	=> $id_member_to,
				'title'			=> 'Tasker',
				'type'			=> 1				
			);
			$notif_fcm = array(
				'title'		=> 'Tasker',
				'body'		=> $pesan,
				'badge'		=> 1,
				'sound'		=> 'Default'
			);
						
			$this->load->library('send_notif');			
			$send_fcm = '';			
			$sort = array('abs(id)','ASC');
			$dt_chat = $this->access->readtable('chat_detail','',array('id_chat' => $save),'','',$sort)->result_array();
			$id_cust = array();
			$_id_member = '';
			if(!empty($dt_chat)){
				foreach($dt_chat as $_dd){
					$id_cust[$_dd['id_member_from']] = '"'.$_dd['id_member_from'].'"';
					$id_cust[$_dd['id_member_to']] = '"'.$_dd['id_member_to'].'"';
					$_id_member = implode(',',$id_cust);			
				}
				$sql_member = 'SELECT id_customer,nama,last_name,fcm_token,notif_chat FROM customer WHERE id_customer IN ('.$_id_member.')';
				
				$_dt_member = $this->db->query($sql_member)->result_array();
				$nama = '';
				$last_name = '';
				$fcm_token = '';
				$notif_chat = 0;
				foreach($_dt_member as $dm){
					$nama[$dm['id_customer']] = $dm['nama'];
					$notif_chat[$dm['id_customer']] = (int)$dm['notif_chat'];
					$last_name[$dm['id_customer']] = $dm['last_name'];
					$fcm_token[$dm['id_customer']] = $dm['fcm_token'];
				}
				if($notif_chat[$id_member_to] > 0) $ids = array($fcm_token[$id_member_to]);
				// error_log(serialize($ids));
				$data_fcm = array(
					'id'					=> $save,
					'id_member_pengirim'	=> $id_member,
					'id_member_to'			=> $id_member_to,
					'nama_pengirim'			=> $nama[$id_member],
					'last_name_pengirim'	=> $last_name[$id_member],
					'title'					=> 'Tasker',
					'type'					=> 1				
				);
				$notif_fcm = array(
					'title'		=> 'Tasker',
					'body'		=> $pesan,
					'image'		=> $gbr,
					'badge'		=> 1,
					'sound'		=> 'Default'
				);
				
				if(!empty($ids)) $send_fcm = $this->send_notif->send_fcm($data_fcm, $notif_fcm, $ids);				
				// error_log(serialize($send_fcm));
				foreach($dt_chat as $dc){
					$status_to = 0;
					if($id_member == $dc['id_member_to']){
						$status_to = $dc['status_to'];
					}
					$dt_res[] = array(
						'id_chat'			=> $dc['id_chat'],
						'id_chat_detail'	=> $dc['id'],
						'id_member_from'	=> $dc['id_member_from'],
						'nama_from'			=> $nama[$dc['id_member_from']],
						'last_name_from'	=> $last_name[$dc['id_member_from']],
						'id_member_to'		=> $dc['id_member_to'],
						'nama_to'			=> $nama[$dc['id_member_to']],
						'last_name_to'		=> $last_name[$dc['id_member_to']],
						'pesan'				=> $dc['pesan'],
						'gbr'				=> $dc['gbr'],
						'status'			=> $status_to,
						'created_at'		=> $dc['created_at'],
						'created_by'		=> $dc['created_by']
					);
				}
				$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt_res
				];
			}else{
				$result = [
					'err_code'	=> '04',
					'err_msg'	=> 'Data not found'
				];
			}			
		}else{
			$result = [
				'err_code'	=> '05',
				'err_msg'	=> 'Insert has problem'
			];
		}
		
		http_response_code(200);
		echo json_encode($result);
	}
	
	function chat_detail(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_chat = isset($param['id_chat']) ? (int)$param['id_chat'] : 0;
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		
		$this->access->updatetable('chat_detail',array('status_to'=>0), array("id_chat" => $id_chat,'id_member_to'=>$id_member));
		$dt = $this->access->readtable('master_chat','',array('id_chat' => $id_chat))->row();			
		$id_member_from = (int)$dt->id_member_from;
		$_id_member_to = (int)$dt->id_member_to;
		$dt_block = array();
		if($id_member_from == $id_member){
			$dt_upd = array('status_from' => 0);	
			$dt_block = $this->cek_block($id_member, $_id_member_to);
		}
			
		if($_id_member_to == $id_member){
			$dt_upd = array('status_to' => 0);
			$dt_block = $this->cek_block($id_member, $id_member_from);
		}
		$this->access->updatetable('master_chat',$dt_upd, array("id_chat" => $id_chat));
					
		$id_block = $dt_block['id_block'];
		$id_user_yg_memblock = $dt_block['id_user_yg_memblock'];
		$sort = array('abs(id)','ASC');
		$dt_chat = $this->access->readtable('chat_detail','',array('id_chat' => $id_chat),'','',$sort)->result_array();
		$id_cust = array();
		$result = array();
		$_id_member = '';
		if(!empty($dt_chat)){
			foreach($dt_chat as $_dd){
				$id_cust[$_dd['id_member_from']] = '"'.$_dd['id_member_from'].'"';
				$id_cust[$_dd['id_member_to']] = '"'.$_dd['id_member_to'].'"';
				$_id_member = implode(',',$id_cust);			
			}
			$sql_member = 'SELECT * FROM customer WHERE id_customer IN ('.$_id_member.')';
			$_dt_member = $this->db->query($sql_member)->result_array();
			foreach($_dt_member as $dm){
				$nama[$dm['id_customer']] = $dm['nama'];
				$last_name[$dm['id_customer']] = $dm['last_name'];
			}
			foreach($dt_chat as $dc){
				$status_to = 0;
				if($id_member == $dc['id_member_to']){
					$status_to = $dc['status_to'];
				}
				$dt_res[] = array(
					'id_chat'			=> $dc['id_chat'],
					'id_chat_detail'	=> $dc['id'],
					'id_member_from'	=> $dc['id_member_from'],
					'nama_from'			=> $nama[$dc['id_member_from']],
					'last_name_from'	=> $last_name[$dc['id_member_from']],
					'id_member_to'		=> $dc['id_member_to'],
					'nama_to'			=> $nama[$dc['id_member_to']],
					'last_name_to'		=> $last_name[$dc['id_member_to']],
					'pesan'				=> $dc['pesan'],
					'gbr'				=> $dc['gbr'],
					'status'			=> $status_to,
					'created_at'		=> $dc['created_at'],
					'created_by'		=> $dc['created_by']
				);
			}
			
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'id_block'				=> (int)$id_block,
				'id_user_yg_memblock'	=> (int)$id_user_yg_memblock,
				'data'		=> $dt_res
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
	
	function read_inbox(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_chat = isset($param['id_chat']) ? (int)$param['id_chat'] : 0;		
		$dt = $this->access->readtable('master_chat','',array('id_chat' => $id_chat))->row();
		$type = $dt->type;
		$result = array();
		if((int)$type > 0 && (int)$type == 1){
			$result = [
				'err_code'	=> '02',
				'err_msg'	=> 'inbox ini tidak bisa dibaca',
				'data'		=> null
			];
		}
		if((int)$type > 1){
			$this->access->updatetable('master_chat', array('status_from'=>0,'status_to'=>0), array('id_chat'=>$id_chat, 'type !='=>1));			
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> null
			];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function upd_fcm(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;	
		$device = isset($param['device']) ? (int)$param['device'] : 1;	
		$fcm_token = isset($param['fcm_token']) ? $param['fcm_token'] : '';
		$result = array();
		if($id_member == 0){
			$result = [
				'err_code'	=> '07',
				'err_msg'	=> 'id_member require'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$simpan = array(
			'device'	=> $device,
			'fcm_token'	=> $fcm_token
		);
		$this->access->updatetable('customer',$simpan, array("id_customer"=>$id_member));
		$this->access->updatetable('list_apply',array('token_reminder'=>$fcm_token), array("id_member"=>$id_member,'status'=>2,'token_reminder !='=>1));
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'ok'
		];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function insert_inbox($id_member=0, $pesan = '', $type=0, $_id=0){
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
	
	function get_achievement(){
		$sort = array('abs(achievement.id)','DESC');
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$login = $this->access->readtable('customer',array('completed_task'),array('id_customer'=>$id_member))->row();
		$completed_task = (int)$login->completed_task;
		$achievement_compl_task = (int)$login->achievement_compl_task;
		$_achievement = 0;
		$next_completed_task = 0;
		if($completed_task == 1){
			$_achievement = 1;
			$next_completed_task = 10;
		}
		if($completed_task > 9 && $completed_task % 10 == 0 && $achievement_compl_task != $completed_task){
			$_achievement = 1;
			$next_completed_task = $completed_task + 10;
		}
		if((int)$_achievement > 0){
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 5,'status' =>-1));
			$dt_achievement = array(
				'id_member'		=> (int)$id_member,
				'type'			=> 5,
				'title'			=> 'Achievement 5',
				'keterangan'	=> 'Bonus achievement complete task  '.$next_completed_task.'x',
				'coin'			=> 1,
				'status'		=> -2,
				'created_at'	=> $tgl
			);
			$this->access->inserttable('achievement',$dt_achievement);
			$this->access->updatetable('customer', array('achievement_compl_task'=>$completed_task), array('id_customer'=>$id_member));
		}
		$_achievement = 0;
		$cek_compl_task = $this->access->readtable('task','',array('task.deleted_at'=>null,'id_member'=>$id_member,'status' => 3))->result_array();
		$cnt_compl_task = count($cek_compl_task);
		$cnt_compl_task = (int)$cek_compl_task;	
		$achievement_post_task_complete = (int)$achievement_post_task_complete;	
		$next_cnt_compl_task = 0;
		if($cnt_compl_task == 1){
			$_achievement = 1;
			$next_cnt_compl_task = 10;
		}
		if($cnt_compl_task > 9 && $cnt_compl_task % 10 == 0 && $achievement_post_task_complete != $cnt_compl_task){
			$_achievement = 1;
			$next_cnt_compl_task = $next_cnt_compl_task + 10;
		}
		if((int)$_achievement > 0){
			$dt_achievement = array();
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 6,'status' =>-1));
			$dt_achievement = array(
				'id_member'		=> (int)$id_member,
				'type'			=> 6,
				'title'			=> 'Achievement 6',
				'keterangan'	=> 'Bonus achievement post task complete  '.$next_cnt_compl_task.'x',
				'coin'			=> 1,
				'status'		=> -2,
				'created_at'	=> $tgl
			);
			$this->access->inserttable('achievement',$dt_achievement);
			$this->access->updatetable('customer', array('achievement_post_task_complete'=>$cnt_compl_task), array('id_customer'=>$id_member));
		}
		$achievement = $this->access->readtable('achievement','',array('abs(id_member)'=> $id_member,'status >=' => -1),'','',$sort)->result_array();
		
		$dt = array();
		if(!empty($achievement)){
			foreach($achievement as $key=>$val){
				$dt[] = $val;
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
	
	function claim_achievement(){
		$sort = array('abs(achievement.id)','DESC');
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id = isset($param['id_achievement']) ? (int)$param['id_achievement'] : 0;		
		if($id <= 0){			
			$result = [
					'err_code'		=> '06',
					'err_msg'		=> 'id_achievement require'
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
		$achievement = $this->access->readtable('achievement','',array('abs(id_member)' => $id_member,'abs(id)' => $id))->row();
		if(empty($achievement)){
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'achievement not found'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if((int)$achievement->status > 0){
			$result = [
					'err_code'		=> '02',
					'err_msg'		=> 'status already update'
				];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$login = $this->access->readtable('customer','',array('id_customer'=>$_POST['id_member']))->row();
		$my_coin = (int)$login->coin;
		$ttl_coin = $my_coin + 1;
		$type = (int)$achievement->type;
		$dt_history = array();
		$ket = '';
		if($type == 1) $ket = 'Bonus achievement verify KTP ';
		if($type == 2) $ket = 'Bonus achievement complete profile';
		if($type == 3){
			$this->access->updatetable('achievement',array('status'=>-1,'created_at'=>$tgl),array('id_member'=> (int)$id_member,'type' => 3,'status' =>-2));
			$ket = 'Bonus achievement transaksi coin';
		}
		if($type == 4){
			$this->access->updatetable('achievement',array('status'=>-1,'created_at'=>$tgl),array('id_member'=> (int)$id_member,'type' => 4,'status' =>-2));
			$ket = 'Bonus achievement subscription';
		}
		if($type == 5){
			$this->access->updatetable('achievement',array('status'=>-1,'created_at'=>$tgl),array('id_member'=> (int)$id_member,'type' => 5,'status' =>-2));
			$ket = 'Bonus achievement completed task';
		}
		if($type == 6){
			$this->access->updatetable('achievement',array('status'=>-1,'created_at'=>$tgl),array('id_member'=> (int)$id_member,'type' => 6,'status' =>-2));
			$ket = 'Bonus achievement post task complete';
		}
		$dt_history[] = array(
			'id_ta' 		=> $id_member,
			'id_act' 		=> $id,
			'id_member' 	=> $id_member,
			'coin' 			=> 1,
			'ttl_coin' 		=> $ttl_coin,
			'type' 			=> 6,				// Bonus Achievement
			'created_at' 	=> $tgl,
			'ket' 			=> $ket
		);
		$this->access->updatetable('achievement', array('status'=>1), array('id'=>$id));
		$this->access->updatetable('customer', array('coin'=>$ttl_coin), array('id_customer'=>$id_member));
		$this->db->insert_batch('history_coin', $dt_history);
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok'					
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function follow(){
		$param = $this->input->post();	
		$tgl = date('Y-m-d H:i:s');
		$id_applicant = isset($param['id_applicant']) ? (int)$param['id_applicant'] : 0;  //user login
		$id_employer = isset($param['id_employer']) ? (int)$param['id_employer'] : 0; 
		$data = array(
			'id_applicant'	=> $id_applicant,
			'id_employer'	=> $id_employer,
		);
		$this->db->delete('list_follower', $data);
		$data += array('created_at'=>$tgl);
		$this->access->inserttable('list_follower', $data);
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok'					
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function unfollow(){
		$param = $this->input->post();	
		$tgl = date('Y-m-d H:i:s');
		$id_applicant = isset($param['id_applicant']) ? (int)$param['id_applicant'] : 0;  //user login
		$id_employer = isset($param['id_employer']) ? (int)$param['id_employer'] : 0; 
		$data = array(
			'id_applicant'	=> $id_applicant,
			'id_employer'	=> $id_employer,
		);
		$this->db->delete('list_follower', $data);
		
		$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok'					
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function get_myfollower(){
		$result = array();
		$dt = array();
		$param = $this->input->post();	
		$tgl = date('Y-m-d H:i:s');
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;  //user login
		$dt_block = $this->get_all_block($id_member);
		$field_notin = '';
		if(!empty($dt_block)){
			$field_notin = 'list_follower.id_applicant';
		}
		$select = array('customer.*');
		$list_follower = $this->access->readtable('list_follower',$select,array('list_follower.id_employer'=>$id_member),array('customer'=>'customer.id_customer = list_follower.id_applicant'),'','','LEFT','', '', '', '', '', '', '', $field_notin,$dt_block)->result_array();
		if(!empty($list_follower)){
			foreach($list_follower as $lw){
				$dt[] = array(
					'id_member'	=> $lw['id_customer'],
					'nama'		=> $lw['nama'],
					'last_name'	=> $lw['last_name'],
					'photo'		=> !empty($lw['photo']) ? base_url('uploads/photo_cv/'.$lw['photo']) : '',
				);
			}
			
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
	
	function get_myfollowed(){
		$result = array();
		$dt = array();
		$param = $this->input->post();	
		$tgl = date('Y-m-d H:i:s');
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;  //user login
		$dt_block = $this->get_all_block($id_member);
		$field_notin = '';
		if(!empty($dt_block)){
			$field_notin = 'list_follower.id_employer';
		}
		$select = array('customer.*');
		$list_follower = $this->access->readtable('list_follower',$select,array('list_follower.id_applicant'=>$id_member),array('customer'=>'customer.id_customer = list_follower.id_employer'),'','','LEFT','', '', '', '', '', '', '', $field_notin,$dt_block)->result_array();
		
		if(!empty($list_follower)){
			foreach($list_follower as $lw){
				$dt[] = array(
					'id_member'	=> $lw['id_customer'],
					'nama'		=> $lw['nama'],
					'last_name'	=> $lw['last_name'],
					'photo'		=> !empty($lw['photo']) ? base_url('uploads/photo_cv/'.$lw['photo']) : '',
				);
			}
			
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
	
	function set_notif(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : '';
		$notif_chat = isset($param['notif_chat']) ? (int)$param['notif_chat'] : '';
		$notif_status_task = isset($param['notif_status_task']) ? (int)$param['notif_status_task'] : '';
		$notif_notifyme = isset($param['notif_notifyme']) ? (int)$param['notif_notifyme'] : '';
		
		$dt_upd = array();
		if($notif_chat > 0){
			$notif_chat = $notif_chat > 1 ? 0 : $notif_chat;
			$dt_upd += array('notif_chat'=>$notif_chat);
		}
		if($notif_status_task > 0){
			$notif_status_task = $notif_status_task > 1 ? 0 : $notif_status_task;
			$dt_upd += array('notif_status_task'=>$notif_status_task);
		}
		if($notif_notifyme > 0){
			$notif_notifyme = $notif_notifyme > 1 ? 0 : $notif_notifyme;
			$dt_upd += array('notif_notifyme'=>$notif_notifyme);
		}
		
		if(!empty($dt_upd))	$this->access->updatetable('customer', $dt_upd, array('id_customer'=>$id_member)); 
		$dt_notif = $this->access->readtable('customer','',array('id_customer '=>$id_member))->row();
		$dt = array(
			'id_member'				=> (int)$dt_notif->id_customer,
			'notif_notifyme'		=> (int)$dt_notif->notif_notifyme,
			'notif_chat'			=> (int)$dt_notif->notif_chat,
			'notif_status_task'		=> (int)$dt_notif->notif_status_task
		);
		$result = array();
		$result = array(
			'err_code'	=> '00',
			'err_msg'	=> 'ok',
			'data'		=> $dt			
		);
		http_response_code(200);
		echo json_encode($result);
	}
	
	function get_notif(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : '';		
		$dt_notif = $this->access->readtable('customer','',array('id_customer '=>$id_member))->row();
		$dt = array(
			'id_member'				=> (int)$dt_notif->id_customer,
			'notif_notifyme'		=> (int)$dt_notif->notif_notifyme,
			'notif_chat'			=> (int)$dt_notif->notif_chat,
			'notif_status_task'		=> (int)$dt_notif->notif_status_task
		);
		$result = array();
		$result = array(
			'err_code'	=> '00',
			'err_msg'	=> 'ok',
			'data'		=> $dt			
		);
		http_response_code(200);
		echo json_encode($result);
	}
	
	function req_cancel(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		if($id_member == 0 || $id_apply == 0){
			$result = [
				'err_code'	=> '07',
				'err_msg'	=> 'id_member & id_apply require'
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$sql = "select * from list_apply where id_member =".$id_member." and id =".$id_apply;
		$dt_apply = $this->db->query($sql)->row();
		$result = array();
		if(!empty($dt_apply)){
			$status = (int)$dt_apply->status;
			$_id_ta = (int)$dt_apply->id;
			if($status == 3 || $status == 7){
				$result = [
					'err_code'	=> '05',
					'err_msg'	=> 'Task sudah direject',
					'data'		=>  null
				];
			}
			if($status == 6){
				$result = [
					'err_code'	=> '05',
					'err_msg'	=> 'Task di take down oleh admin',
					'data'		=>  null
				];
			}
			if($status == 4){
				$result = [
					'err_code'	=> '05',
					'err_msg'	=> 'Task sudah complete',
					'data'		=>  null
				];
			}
			if($status == 8){
				$result = [
					'err_code'	=> '05',
					'err_msg'	=> 'Task sudah request cancel sebelumnya',
					'data'		=>  null
				];
			}
			if($status == 1){
				$sql = 'SELECT * FROM history_coin WHERE type = 2 and id_ta ='.$_id_ta;
				$dt = $this->db->query($sql)->row();
				$sql_member = 'SELECT * FROM customer WHERE id_customer ='.$id_member;
				$_dt_member = $this->db->query($sql_member)->row();
				$coin = $_dt->coin;
				$coin_member = $_dt_member->coin;
				$ttl_coin = $coin + $coin_member;
				$dt_history = array();
				$dt_history = array(
					'id_ta' 		=> $dt->id_ta,
					'id_act' 		=> $dt->id_act,
					'id_member' 	=> $id_member,
					'coin' 			=> $coin,
					'ttl_coin' 		=> $ttl_coin,
					'type' 			=> 7,				// request cancel
					'created_at' 	=> $tgl,
					'ket' 			=> 'Refund request cancel task #'.$dt->id_act	
				);				
				$this->access->updatetable('customer', array('coin'	=> $ttl_coin), array('id_customer'=>$id_member));
				$this->access->updatetable('list_apply', array('refund_date'=> $tgl,'status'=>8), array('id'=>$dt->id_ta));
				$this->access->inserttable('history_coin', $dt_history);
				$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=>  null
				];
			}
			if($status == 2 || $status == 5){
				$sql = 'SELECT * FROM history_coin WHERE type = 2 and id_ta ='.$_id_ta;
				$dt = $this->db->query($sql)->row();
				$this->access->updatetable('list_apply', array('status'=>8), array('id'=>$dt->id_ta));
				$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=>  null
				];
			}
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
	
	function block_user(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member1 = isset($param['id_member_login']) ? (int)$param['id_member_login'] : 0;
		$id_member2 = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$where = array();
		$result = array();
		$data = array();
		if($id_member1 == 0 || $id_member2 == 0){
			$result = [
				'err_code'	=> '03',
				'err_msg'	=> 'id_member_login & id_member require',
				'data'		=>  array("id_member_login"=>$id_member1, "id_member"=>$id_member2)
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if($id_member1 == $id_member2){
			$result = [
				'err_code'	=> '03',
				'err_msg'	=> 'Data invalid',
				'data'		=>  array("id_member_login"=>$id_member1, "id_member"=>$id_member2)
			];
			http_response_code(200);
			echo json_encode($result);
			return false;
		}
		$where = array('id_member1'=>$id_member1,'id_member2'=>$id_member2,'deleted_at'=>null);
		$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
		$id_block = (int)$dt_block->id > 0 ? (int)$dt_block->id : 0;
		if($id_block > 0){
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Anda sudah memblok user ini sebelumnya',
				'data'		=>  $dt_block
			];
		}else{
			$data = array(
				'id_member1'	=> $id_member1,
				'id_member2'	=> $id_member2,
				'created_at'	=> $tgl,
			);
			$id_block = $this->access->inserttable('list_user_blok', $data);
			$data += array('id'=>$id_block);
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=>  $data
			];
		}
		if($id_block <= 0){
			$result = [
				'err_code'	=> '02',
				'err_msg'	=> 'insert has problem',
				'data'		=>  ''
			];
		}
		
		http_response_code(200);
		echo json_encode($result);
	}
	
	function unblock_user(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$id_member1 = isset($param['id_member_login']) ? (int)$param['id_member_login'] : 0;
		$id_member2 = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$where = array('id_member1'=>$id_member1,'id_member2'=>$id_member2,'deleted_at'=>null);
		$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
		$id_block = (int)$dt_block->id > 0 ? (int)$dt_block->id : 0;
		if($id_block > 0){
			$this->access->updatetable('list_user_blok', array('deleted_at'=> $tgl,'updated_by'=>$id_member1), array('id'=>$id_block));
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=>  $id_block
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=>  ''
			];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function list_block_user(){
		$param = $this->input->post();
		$id_member1 = isset($param['id_member_login']) ? (int)$param['id_member_login'] : 0;
		$where = array('id_member1'=>$id_member1,'list_user_blok.deleted_at'=>null);
		$dt_block = $this->access->readtable('list_user_blok','',$where,array('customer'=>'customer.id_customer = list_user_blok.id_member2'),'','','LEFT')->result_array();
		$dt = array();
		if(!empty($dt_block)){
			foreach($dt_block as $db){
				$dt[] = array(
					'id_block'		=> $db['id'],
					'id_customer'	=> $db['id_customer'],
					'nama'			=> $db['nama'],
					'last_name'		=> $db['last_name'],
					'email'			=> $db['email'],
					'phone'			=> $db['phone_number'],
				);
			}
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=>  $dt
			];
		}else{
			$result = [
				'err_code'	=> '04',
				'err_msg'	=> 'Data not found',
				'data'		=>  ''
			];
		}
		http_response_code(200);
		echo json_encode($result);
	}
	
	function cek_block($id_member_login=0, $id_member=0){
		$where = array('id_member1'=>$id_member_login,'id_member2'=>$id_member,'deleted_at'=>null);
		$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
		error_log($this->db->last_query());	
		$id_block = (int)$dt_block->id > 0 ? (int)$dt_block->id : 0;
		$id_user_yg_memblock = 0;
		if($id_block > 0){
			$id_user_yg_memblock = $id_member_login;			
		}else{
			$dt_block = '';
			$where = array('id_member1'=>$id_member,'id_member2'=>$id_member_login,'deleted_at'=>null);
			$dt_block = $this->access->readtable('list_user_blok','',$where)->row();
			error_log($this->db->last_query());	
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
	
	function upd_stts_bukti_byr(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;		
		
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$remark = isset($param['remark']) ? $param['remark'] : '';
		$simpan = array(
			'status_date'	=> $tgl,
			'status_bukti_pembayaran'	=> $status,
			'ket_stts_bukti_pembayaran'	=> $remark
		);
		$simpan_issue = array();
		if($status == 2){
			$cek_apply = $this->access->readtable('list_apply','',array('id'=>$id_apply))->row();
			$simpan_issue = array(
				'id_apply'		=> $id_apply,
				'img'			=> base_url('uploads/bukti_pembayaran/'.$cek_apply->bukti_pembayaran),
				'id_member'		=> $cek_apply->id_employer,
				'member'		=> 'Employer',
				'created_at'	=> $cek_apply->upload_date_bukti_pembayaran
			);
			$this->access->inserttable('issue_bp', $simpan_issue);
			$simpan_issue = array(
				'id_apply'		=> $id_apply,
				'keterangan'	=> $remark,
				'id_member'		=> $cek_apply->id_member,
				'member'		=> 'Tasker',
				'created_at'	=> $tgl
			);
			$this->access->inserttable('issue_bp', $simpan_issue);
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
	
	function reply_issue_bp(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;	
		$id_employer = isset($param['id_employer']) ? (int)$param['id_employer'] : 0;	
		$id_applicant = isset($param['id_applicant']) ? (int)$param['id_applicant'] : 0;	
		$id_member = $id_employer > 0 ? $id_employer : $id_applicant;
		$member = $id_employer > 0 ? 'Employer' : 'Tasker';
		$remark = isset($param['remark']) ? $param['remark'] : '';
		$simpan_issue = array();
		$simpan_issue = array(
			'id_apply'		=> $id_apply,
			'keterangan'	=> $remark,
			'id_member'		=> $id_member,
			'member'		=> $member,
			'created_at'	=> $tgl
		);
		$config = array();
		$config['upload_path'] = "./uploads/bukti_pembayaran/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		
		if(!empty($_FILES['img'])){
			$upl = '';
			if($this->upload->do_upload('img')){
				$upl = $this->upload->data();
				$simpan_issue += array("img" => base_url('uploads/bukti_pembayaran/'.$upl['file_name']));		
			}
		}
		$this->access->inserttable('issue_bp', $simpan_issue);
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $simpan_issue
			];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function close_complain(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;	
		$id_applicant = isset($param['id_applicant']) ? (int)$param['id_applicant'] : 0;
		$remark = isset($param['remark']) ? $param['remark'] : 'Pembayaran diterima';
		$simpan_issue = array();
		$simpan_issue = array(
			'id_apply'		=> $id_apply,
			'keterangan'	=> $remark,
			'id_member'		=> $id_applicant,
			'member'		=> 'Tasker',
			'created_at'	=> $tgl
		);
		$config = array();
		$config['upload_path'] = "./uploads/bukti_pembayaran/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		
		if(!empty($_FILES['img'])){
			$upl = '';
			if($this->upload->do_upload('img')){
				$upl = $this->upload->data();
				$simpan_issue += array("img" => base_url('uploads/bukti_pembayaran/'.$upl['file_name']));		
			}
		}
		$this->access->inserttable('issue_bp', $simpan_issue);
		$simpan = array();
		$simpan = array(
			'status_date'	=> $tgl,
			'status_bukti_pembayaran'	=> 3,
			'ket_stts_bukti_pembayaran'	=> $remark
		);
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'ok',
			'data'		=> $simpan
		];
		$this->access->updatetable('list_apply', $simpan, array('id'=>$id_apply));
		http_response_code(200);
		echo json_encode($result);
	}
	
	function history_complain_bp(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_apply = isset($param['id_apply']) ? (int)$param['id_apply'] : 0;
		$select = array();
		$where = array('list_apply.id'=>$id_apply);
		$select = array('list_apply.*','customer.nama','customer.last_name');
		$list_apply = $this->access->readtable('list_apply',$select,$where,array('customer' => 'customer.id_customer = list_apply.id_member'),'','','LEFT')->row();
		$sort = array('abs(id_bp)','ASC');
		$issue_bp = $this->access->readtable('issue_bp','',array('id_apply'=>$id_apply),'','',$sort)->result_array();
		$bukti_pembayaran = !empty($list_apply->bukti_pembayaran) ? $list_apply->bukti_pembayaran : '';
		$upload_date_bukti_pembayaran = !empty($list_apply->upload_date_bukti_pembayaran) ? $list_apply->upload_date_bukti_pembayaran : null;
		
		$dt_res = array();
		$dt_res[] = array(
			'id_member'		=> $list_apply->id_employer,
			'name'			=> 'Employer',
			'img'			=> $bukti_pembayaran,
			'remark'		=> 'Bukti Pembayaran',
			'created_at'	=> $upload_date_bukti_pembayaran,
		);
		if(!empty($issue_bp)){
			$i=0;
			foreach($issue_bp as $ib){
				if($i > 0){
					$dt_res[] = array(
						'id_member'			=> $ib['id_member'],
						'name'			=> $ib['member'],
						'img'			=> $ib['img'],
						'remark'		=> $ib['keterangan'],
						'created_at'	=> $ib['created_at'],
					);
				}
				$i++;
			}
		}
		$result = [
			'err_code'	=> '00',
			'err_msg'	=> 'ok',
			'status_bp'	=> (int)$list_apply->status_bukti_pembayaran,
			'data'		=> $dt_res
		];
		http_response_code(200);
		echo json_encode($result);
	}
	
}
