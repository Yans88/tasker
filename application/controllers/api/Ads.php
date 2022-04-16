<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Ads extends CI_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model('Access','access',true);
		$this->load->model('Setting_m','sm', true);
		$this->load->library('converter');
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
    }
	
	
	
    function index(){
		$result = array();
		$dt = array();
		$param = $this->input->post();		
		$_sort = array('ABS(id_ads)','DESC');
		$ads = $this->access->readtable('ads','',array('deleted_at'=>null, 'is_publish'=>1),'','','',$_sort)->result_array();
		if(!empty($ads)){
			foreach($ads as $_a){
				$img = !empty($_a['img']) ? base_url('uploads/ads/'.$_a['img']) : '';
				unset($_a['tgl']);
				unset($_a['update_at']);
				unset($_a['deleted_at']);
				unset($_a['publish_by']);
				unset($_a['img']);
				$_a['img'] = $img;
				$dt[] = $_a;
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
	
	function detail(){
		$result = array();
		$list_img = array();
		$dt = array();
		$param = $this->input->post();		
		$id_ads = (int)$param['id_ads'];
		$ads = $this->access->readtable('ads','',array('id_ads'=>$id_ads))->row();
		$images = $this->access->readtable('ads_images','',array('id_ads'=>$id_ads, 'deleted_at'=>null))->result_array();
		if(!empty($images)){
			foreach($images as $_i){
				$img = !empty($_i['img']) ? base_url('uploads/ads/'.$_i['img']) : '';
				$list_img[] = $img;
			}
		}
		
		if(!empty($ads)){
			$img = '';
			$img = !empty($ads->img) ? base_url('uploads/ads/'.$ads->img) : '';
			unset($ads->img);
			unset($ads->tgl);
			unset($ads->update_at);
			unset($ads->deleted_at);
			unset($ads->publish_by);
			$ads->img = $img;
			$ads->list_img = $list_img;
			$result = [
				'err_code'	=> '00',
				'err_msg'	=> 'ok',
				'data'		=> $ads
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
	
}
