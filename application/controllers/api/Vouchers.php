<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Vouchers extends CI_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();       
		$this->load->model('Access','access',true);	
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
	}

    public function index(){
        $tgl = date('Y-m-d');
		$vouchers = '';
		$sort = array();
		$vouchers = $this->access->readtable('voucher','',array('deleted_at'=>null,'publish_by >'=>0,'date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d')),'','',$sort)->result_array();		
		$dt = array();
		$path = '';
		if(!empty($vouchers)){
			foreach($vouchers as $k){
				$path = '';
				$path = !empty($k['img']) ? base_url('uploads/vouchers/'.$k['img']) : null;
				$dt[] = array(
					"id"		=> $k['id'],
					"kode_voucher"		=> $k['kode_voucher'],
					"nilai_potongan"	=> $k['nilai_potongan'],
					"maks_potongan"		=> $k['maks_potongan'],					
					"expired_date"		=> $k['expired_date'],
					"deskripsi"			=> $k['deskripsi'],
					'image'				=> $path,
				);
			}
		}
		$result = array();
		if (!empty($dt)){
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt
				];            
        }else{
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'data not be found'
				];
        }
		http_response_code(200);
		echo json_encode($result);
    }
	
	public function detail(){
		$param = $this->input->post();
		$id = isset($param['id']) ? (int)$param['id'] : 0;
		$vouchers = '';
		$sort = array();
		$vouchers = $this->access->readtable('voucher','',array('id'=>$id,'deleted_at'=>null,'publish_by >'=>0,'date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d')),'','',$sort)->row();		
		$dt = array();
		$path = '';
		$path = !empty($vouchers->img) ? base_url('uploads/vouchers/'.$vouchers->img) : null;
		$dt = array(
			"id"				=> $vouchers->id,
			"kode_voucher"		=> $vouchers->kode_voucher,			
			"nilai_potongan"	=> $vouchers->nilai_potongan,
			"maks_potongan"		=> $vouchers->maks_potongan,
			"expired_date"		=> $vouchers->expired_date,
			"deskripsi"			=> $vouchers->deskripsi,
			"image"				=> $path,
		);
		$result = array();
		if (!empty($dt)){
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt
				];
        }else{
            $result = [
					'err_code'		=> '04',
					'err_msg'		=> 'data not be found'
				];
        }
		http_response_code(200);
		echo json_encode($result);
    }
		
	public function add_myvoucher(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$kode_voucher = isset($param['kode_voucher']) ? strtolower($param['kode_voucher']) : 0;
		$vouchers = '';	
		$vouchers = $this->access->readtable('voucher','',array('LOWER(kode_voucher)'=>$kode_voucher,'deleted_at'=>null,'publish_by >'=>0,'date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d')))->row();	
		$id_vouchers = (int)$vouchers->id;
		$result = array();
		$dt_save = array();		
		$path = '';
		$path = !empty($vouchers->img) ? base_url('uploads/vouchers/'.$vouchers->img) : null;		
		$dt_save = array(
			"id_voucher"		=> $vouchers->id,
			"id_member"			=> $id_member,
			"kode_voucher"		=> $vouchers->kode_voucher,
			"nilai_potongan"	=> $vouchers->nilai_potongan,
			"maks_potongan"		=> $vouchers->maks_potongan,
			"expired_date"		=> $vouchers->expired_date,
			"deskripsi"			=> $vouchers->deskripsi,
			"img"				=> $path,
			"created_at"		=> date('Y-m-d H:i:s'),
		);
		$chk_myvoucher = $this->access->readtable('my_voucher',array('id'),array('id_voucher'=>$id_vouchers,'id_member'=>$id_member))->row();	
		$exist = (int)$chk_myvoucher->id > 0 ? 1 : 0;
		if($exist > 0){
			$result = array(
				'err_code' 	=> '06',
                'err_msg' 	=> 'vouchers sudah ditambahkan sebelumnya',
			);
            http_response_code(200);
			echo json_encode($result);
			return false;
		}
		if (!empty($dt_save)){
			$save = $this->access->inserttable('my_voucher', $dt_save); 
			$dt_save += array('id'=>$save);
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt_save
				];			
        }else{
			$result = [
					'err_code'		=> '04',
					'err_msg'		=> 'data not be found'
				];            
        }
		http_response_code(200);
		echo json_encode($result);
    }	
	
	public function myvouchers(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$vouchers = '';	
		$vouchers = $this->access->readtable('my_voucher','',array('date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d'),'id_member'=>$id_member),'','',$sort)->result_array();		
		$dt = array();
		$path = '';
		if(!empty($vouchers)){
			foreach($vouchers as $k){
				$path = '';
				$path = !empty($k['img']) ? $k['img'] : null;
				$dt[] = array(
					"id_myvoucher"		=> $k['id'],
					"id_voucher"		=> $k['id_voucher'],
					"kode_voucher"		=> $k['kode_voucher'],
					"nilai_potongan"	=> $k['nilai_potongan'],
					"maks_potongan"		=> $k['maks_potongan'],
					"expired_date"		=> $k['expired_date'],
					"deskripsi"			=> $k['deskripsi'],					
					"is_used"			=> !empty($k['is_used']) ? 1 : 0,
					'image'				=> $path,
					"created_at"		=> $k['created_at'],
				);
			}
		}
		$result = array();
		if (!empty($dt)){
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt
				];			
        }else{
            $result = [
					'err_code'		=> '04',
					'err_msg'		=> 'data not be found'
				];            
        }
		http_response_code(200);
		echo json_encode($result);
    }
	
	public function detail_myvoucher(){
		$param = $this->input->post();
		$id_member = isset($param['id_member']) ? (int)$param['id_member'] : 0;
		$id = isset($param['id_myvoucher']) ? (int)$param['id_myvoucher'] : 0;
		$vouchers = '';
		$sort = array();
		$vouchers = $this->access->readtable('my_voucher','',array('id'=>$id,'id_member'=>$id_member,'date_format(expired_date, "%Y-%m-%d") >=' => date('Y-m-d')),'','',$sort)->row();		
		$dt = array();
		$result = array();
		$path = '';
		$path = !empty($vouchers->img) ? base_url('uploads/vouchers/'.$vouchers->img) : null;
		$dt = array(
			"id_myvoucher"		=> $vouchers->id,
			"id_voucher"		=> $vouchers->id_voucher,
			"kode_voucher"		=> $vouchers->kode_voucher,
			"nilai_potongan"	=> $vouchers->nilai_potongan,
			"maks_potongan"		=> $vouchers->maks_potongan,
			"expired_date"		=> $vouchers->expired_date,
			"deskripsi"			=> $vouchers->deskripsi,
			"is_used"			=> !empty($vouchers->is_used) ? 1 : 0,
			"image"				=> $path,
		);
		if (!empty($dt)){
			$result = [
					'err_code'	=> '00',
					'err_msg'	=> 'ok',
					'data'		=> $dt
				];
        }else{
            $result = [
					'err_code'		=> '04',
					'err_msg'		=> 'data not be found'
				];            
        }
		http_response_code(200);
		echo json_encode($result);
    }	
	
	
}
