<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Transaksi extends CI_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model('Access','access',true);
		$this->load->model('Setting_m','sm', true);
		$this->load->library('converter');
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
    }
	
	
	
    function buy_coin(){	
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;	
		$id_coin = isset($param['id_coin']) ? (int)$param['id_coin'] : 0;	
		$payment = isset($param['payment']) ? (int)$param['payment'] : 1;	
		$bank = isset($param['bank']) ? (int)$param['bank'] : '';			
		$where = array('master_coin.deleted_at'=>null,'id_coin'=>$id_coin);
		$master_coin = $this->access->readtable('master_coin','',$where)->row();
		$payment = 1;
		$bank_name = '';
		if($bank == 1) $bank_name = 'BRI';		
		if($bank == 2) $bank_name = 'BCA';
		$kode_unik = rand(100,1000);	
		$dt_simpan = array(
			'id_member'		=> $id_member,
			'type'			=> 1,
			'id_cp'			=> $id_coin,
			'jml'			=> $master_coin->jml_coin,
			'nominal'		=> $master_coin->nominal,
			'kode_unik'		=> $kode_unik,
			'total'			=> $master_coin->nominal + $kode_unik,
			'payment'		=> $payment,
			'bank'			=> $bank,
			'bank_name'		=> $bank_name,
			'status'		=> 1,
			'created_at'	=> $tgl
			
		);
		$save = $this->access->inserttable('transaksi', $dt_simpan);
		$no_trans = 'INV'.date('y').''.date('m').'000000'.$save;
		$this->access->updatetable('transaksi', array('no_trans'=>$no_trans), array('id_trans'=>$save));		
		$dt_simpan += array('id_transaksi'=>$save,'no_trans'=>$no_trans);
		$result = [
					'err_code'	=> '00',
					'err_msg_id'	=> 'Transaksi anda sedang diproses, mohon segera selesaikan pembayaran sesuai dengan instruksi yang tertera.',
					'err_msg'	=> 'Your Purchase request is processed, kindly complete the payment process.',
					'data'		=> $dt_simpan
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function buy_premium(){	
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;	
		$id_premium = isset($param['id_premium']) ? (int)$param['id_premium'] : 0;	
		$payment = isset($param['payment']) ? (int)$param['payment'] : 1;	
		$bank = isset($param['bank']) ? (int)$param['bank'] : '';			
		$where = array('master_premium.deleted_at'=>null,'id_premium'=>$id_premium);
		$master_coin = $this->access->readtable('master_premium','',$where)->row();
		$payment = 1;
		$bank_name = '';
		if($bank == 1) $bank_name = 'BRI';		
		if($bank == 2) $bank_name = 'BCA';
		$kode_unik = rand(100,1000);	
		$dt_simpan = array(
			'id_member'		=> $id_member,
			'type'			=> 2,
			'id_cp'			=> $id_premium,
			'jml'			=> $master_coin->jml_hari,
			'nominal'		=> $master_coin->nominal,
			'kode_unik'		=> $kode_unik,
			'total'			=> $master_coin->nominal + $kode_unik,
			'payment'		=> $payment,
			'bank'			=> $bank,
			'bank_name'		=> $bank_name,
			'status'		=> 1,
			'created_at'	=> $tgl			
		);
		$save = $this->access->inserttable('transaksi', $dt_simpan);
		$no_trans = 'INV'.date('y').''.date('m').'000000'.$save;
		$this->access->updatetable('transaksi', array('no_trans'=>$no_trans), array('id_trans'=>$save));
		$dt_simpan += array('id_transaksi'=>$save,'no_trans'=>$no_trans);
		$result = [
					'err_code'	=> '00',
					'err_msg_id'	=> 'Transaksi anda sedang diproses, mohon segera selesaikan pembayaran sesuai dengan instruksi yang tertera.',
					'err_msg'	=> 'Your Purchase request is processed, kindly complete the payment process.',
					'data'		=> $dt_simpan
				];
		http_response_code(200);
		echo json_encode($result);
	}
	
	function confirm_payment(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();		
		$id_transaksi = isset($param['id_transaksi']) ? (int)$param['id_transaksi'] : 0;
		$bank = isset($param['bank']) ? $param['bank'] : '';
		$sender_name = isset($param['sender_name']) ? $param['sender_name'] : '';
		$no_rek = isset($param['no_rek']) ? $param['no_rek'] : '';
		$config = array();
		$config['upload_path'] = "./uploads/payment/";
		$config['allowed_types'] = "jpg|png|jpeg|";
		$config['max_size']	= '4096';		
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload',$config);
		$simpan = array();
		$simpan = array(
			'confirm_date'		=> $tgl,
			'confirm_bank'		=> $bank,
			'confirm_sender'	=> $sender_name,
			'confirm_rek'		=> $no_rek,
			'status'			=> 2
		);
		if(!empty($_FILES['img'])){
			$upl = '';
			if($this->upload->do_upload('img')){
				$upl = $this->upload->data();
				$simpan += array("confirm_img" => $upl['file_name']);			
			}
		}
		$this->access->updatetable('transaksi', $simpan, array('id_trans'=>$id_transaksi));
		$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $simpan
			];
		http_response_code(200);
		echo json_encode($result);	
	}
	
	function history_coin(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$start_date = isset($param['start_date']) && !empty($param['start_date']) ? new DateTime($param['start_date']) : '';
		$end_date = isset($param['end_date']) && !empty($param['end_date']) ? new DateTime($param['end_date']) : '';
		$start_date = !empty($start_date) ? $start_date->format('Y-m-d') : '';
		$end_date = !empty($end_date) ? $end_date->format('Y-m-d') : '';
		$where = array('id_member'=>$id_member,'type'=>1);
		$sort = array('abs(id_trans)', 'DESC');
		if($status > 0){
			$where += array('status'=>$status);
		}
		if(!empty($start_date)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") >= '=>$start_date);
		}
		if(!empty($end_date)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") <= '=>$end_date);
		}
		$sort = array('id_trans','DESC');
		$transaksi = $this->access->readtable('transaksi','',$where,'','',$sort)->result_array();
		$dt = array();		
		$status_name = '';
		if(!empty($transaksi)){
			foreach($transaksi as $ti){
				$status_name = '';
				
				if($ti['status'] == 1) $status_name = 'Pending';
				if($ti['status'] == 2) $status_name = 'Confirm Payment';
				if($ti['status'] == 3) $status_name = 'Cancelled';
				if($ti['status'] == 4) $status_name = 'Success';
				$dt[] = array(
					'id_transaksi'	=> $ti['id_trans'], 
					'no_trans'		=> $ti['no_trans'],
					'id_member'		=> $ti['id_member'],
					'id_coin'		=> $ti['id_cp'],
					'jml'			=> $ti['jml'],
					'nominal'		=> $ti['nominal'],
					'kode_unik'		=> $ti['kode_unik'],
					'total'			=> $ti['total'],
					'payment'		=> $ti['payment'],
					'bank'			=> $ti['bank'],
					'bank_name'		=> $ti['bank_name'],
					'status'		=> $ti['status'],
					'status_name'	=> $status_name,
					'tgl'			=> $ti['created_at']
				);
			}
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
	
	function history_premium(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$status = isset($param['status']) ? (int)$param['status'] : 0;
		$start_date = isset($param['start_date']) && !empty($param['start_date']) ? new DateTime($param['start_date']) : '';
		$end_date = isset($param['end_date']) && !empty($param['end_date']) ? new DateTime($param['end_date']) : '';
		$start_date = !empty($start_date) ? $start_date->format('Y-m-d') : '';
		$end_date = !empty($end_date) ? $end_date->format('Y-m-d') : '';
		$where = array('id_member'=>$id_member,'type'=>2);
		if($status > 0){
			$where += array('status'=>$status);
		}
		if(!empty($start_date)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") >= '=>$start_date);
		}
		if(!empty($end_date)){
			$where += array('date_format(transaksi.created_at, "%Y-%m-%d") <= '=>$end_date);
		}
		$sort = array('id_trans','DESC');
		$transaksi = $this->access->readtable('transaksi','',$where,'','',$sort)->result_array();
		$dt = array();		
		$status_name = '';
		if(!empty($transaksi)){
			foreach($transaksi as $ti){
				$status_name = '';
				$premium_start = '';
				$premium_end = '';
				if($ti['status'] == 1) $status_name = 'Pending';
				if($ti['status'] == 2) $status_name = 'Confirm Payment';
				if($ti['status'] == 3) $status_name = 'Cancelled';
				if($ti['status'] == 4) $status_name = 'Success';
				if(!empty($ti['premium_start'])) $premium_start = $ti['premium_start'];
				if(!empty($ti['premium_end'])) $premium_end = $ti['premium_end'];
				$dt[] = array(
					'id_transaksi'	=> $ti['id_trans'],
					'no_trans'		=> $ti['no_trans'],
					'id_member'		=> $ti['id_member'],
					'id_premium'	=> $ti['id_cp'],
					'jml'			=> $ti['jml'],
					'nominal'		=> $ti['nominal'],
					'kode_unik'		=> $ti['kode_unik'],
					'total'			=> $ti['total'],
					'payment'		=> $ti['payment'],
					'bank'			=> $ti['bank'],
					'bank_name'		=> $ti['bank_name'],
					'status'		=> $ti['status'],
					'status_name'	=> $status_name,
					'premium_start'	=> $premium_start,
					'premium_end'	=> $premium_end,
					'tgl'			=> $ti['created_at']
				);
			}
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
	
	
}
