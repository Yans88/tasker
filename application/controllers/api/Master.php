<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Master extends CI_Controller {

    function __construct(){        
        parent::__construct();	
		
		$this->load->model('Api_m');	
		$this->load->model('Access','access',true);	
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
    }	
	
	
	function tc(){		
		$term_condition = $this->Api_m->get_key_val();
		$tc = isset($term_condition['term_condition']) ? $term_condition['term_condition'] : '';
		$tcku = array();
		$dataku = array();
		if(!empty($tc)){
			$tc = preg_replace("/<p[^>]*?>/", "", $tc);
			$tc = str_replace("</p>", "", $tc);
			//$tc = str_replace("\r\n","<br />",$tc);
			// $tcku = [
					// 'term_condition' 	=> $tc		
			// ];
			$dataku = array(				
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $tc	
			);
			
		}else{
			$dataku = array(				
                'err_code' => '04',
                'err_msg' => 'Data not be found',
				'data' 	=> $tcku
			);
			
		}
		http_response_code(200);
		echo json_encode($dataku);
	}
	
	function policy(){		
		$policy = $this->Api_m->get_key_val();
		$p = isset($policy['policy']) ? $policy['policy'] : '';
		$tc = array();
		$dataku = array();
		if(!empty($p)){
			$p = preg_replace("/<p[^>]*?>/", "", $p);
			$p = str_replace("</p>", "", $p);
			//$p = str_replace("\r\n","<br />",$p);
			$tc = [
					'policy' 	=> $p		
			];
			$dataku = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',				
				'data' 		=> $p	
			);
			
		}else{
			$dataku = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
		}
		http_response_code(200);
		echo json_encode($dataku);
	}
	
	function about_us(){		
		$policy = $this->Api_m->get_key_val();
		$p = isset($policy['about_us']) ? $policy['about_us'] : '';
		$tc = array();
		$dataku = array();
		if(!empty($p)){
			$p = preg_replace("/<p[^>]*?>/", "", $p);
			$p = str_replace("</p>", "", $p);
			//$p = str_replace("\r\n","<br />",$p);
			$tc = [
					'policy' 	=> $p		
			];
			$dataku = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',				
				'data' 		=> $p	
			);
			
		}else{
			$dataku = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
		}
		http_response_code(200);
		echo json_encode($dataku);
	}
	
	function contact_us(){		
		$policy = $this->Api_m->get_key_val();
		$p = isset($policy['contact_us']) ? $policy['contact_us'] : '';
		$tc = array();
		$dataku = array();
		if(!empty($p)){
			$p = preg_replace("/<p[^>]*?>/", "", $p);
			$p = str_replace("</p>", "", $p);
			//$p = str_replace("\r\n","<br />",$p);
			$tc = [
					'policy' 	=> $p		
			];
			$dataku = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',				
				'data' 		=> $p	
			);
			
		}else{
			$dataku = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
		}
		http_response_code(200);
		echo json_encode($dataku);
	}
	
	function faq(){		
		$faq = $this->access->readtable('faq','',array('deleted_at'=>null))->result_array();
		$dt = array();
		$result = array();
		$answer = '';
		$question = '';
		if(!empty($faq)){
			foreach($faq as $f){
				$answer = preg_replace("/<p[^>]*?>/", "", $f['answer']);
				$answer = str_replace("</p>", "", $answer);
				//$answer = str_replace("\r\n","<br />",$answer);
				$question = preg_replace("/<p[^>]*?>/", "", $f['question']);
				$question = str_replace("</p>", "", $question);
				//$question = str_replace("\r\n","<br />",$question);
				$dt[] = array(
					"id_faq"		=> $f['id_faq'],
					"question"		=> $question,
					"answer"		=> $answer
				);
			}
		}
		
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
	function get_category(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? $param['keyword'] : '';
		$_like = array();
		if(!empty($keyword)){
			$keyword = $this->db->escape_str($keyword);
			$_like = array('kategori.nama_kategori'=>$keyword);
		}
		$sort = array('kategori.nama_kategori','ASC');
		$where = array('kategori.deleted_at'=>null);
		$kategori = $this->access->readtable('kategori','',$where,'','',$sort,'','',$_like)->result_array();
		$dt = array();
		if(!empty($kategori)){
			foreach($kategori as $k){				
				if($k['nama_kategori'] != 'Banner'){
					$path = !empty($k['img']) ? base_url('uploads/kategori/'.$k['img']) : null;
					$dt[] = array(
						"id_kategori"		=> $k['id_kategori'],
						"nama_kategori"		=> $k['nama_kategori'],
						"image"				=> $path
					);
				}
			}
		}
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
	function get_coin(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? str_replace(',','',$param['keyword']) : '';
		$_like = array();
		if(!empty($keyword)){
			$keyword = str_replace('.','',$param['keyword']);
			$keyword = $this->db->escape_str($keyword);
			$_like = array('master_coin.jml_coin'=>(int)$keyword);
		}
		$sort = array('master_coin.jml_coin','ASC');
		$where = array('master_coin.deleted_at'=>null);
		$master_coin = $this->access->readtable('master_coin','',$where,'','',$sort,'','',$_like)->result_array();		
		$dt = array();
		if(!empty($master_coin)){
			foreach($master_coin as $mc){				
				$dt[] = array(
					"id_coin"	=> $mc['id_coin'],
					"jml_coin"	=> $mc['jml_coin'],
					"nominal"	=> $mc['nominal'],
					"info"		=> !empty($mc['deskripsi']) ? $mc['deskripsi'] : ''
				);
			}
		}
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
	function get_premium(){
		$tgl = date('Y-m-d H:i:s');
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? str_replace(',','',$param['keyword']) : '';
		$_like = array();
		if(!empty($keyword)){
			$keyword = str_replace('.','',$param['keyword']);
			$keyword = $this->db->escape_str($keyword);
			$_like = array('master_premium.jml_hari'=>(int)$keyword);
		}
		$sort = array('master_premium.jml_hari','ASC');
		$where = array('master_premium.deleted_at'=>null);
		$master_premium = $this->access->readtable('master_premium','',$where,'','',$sort,'','',$_like)->result_array();		
		$dt = array();
		if(!empty($master_premium)){
			foreach($master_premium as $mp){				
				$dt[] = array(
					"id_premium"	=> $mp['id_premium'],
					"jml_hari"		=> $mp['jml_hari'],
					"nominal"		=> $mp['nominal']
				);
			}
		}
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
	function get_region(){
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? str_replace(',','',$param['keyword']) : '';
		
		$_like = array();
		if(!empty($keyword)){
			$keyword = str_replace('.','',$param['keyword']);
			$keyword = $this->db->escape_str($keyword);
			$_like = array('region.region_name'=> $keyword);
		}
		$sort = array('region.region_name','ASC');
		$where = array('region.deleted_at'=>null);
		$master_region = $this->access->readtable('region','',$where,'','',$sort,'','',$_like)->result_array();		
		$dt = array();
		if(!empty($master_region)){
			foreach($master_region as $mr){				
				$dt[] = array(
					"id_region"		=> $mr['id_region'],
					"region_name"	=> $mr['region_name']
				);
			}
		}
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=> ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
	function get_city(){
		$param = $this->input->post();
		$keyword = isset($param['keyword']) ? str_replace(',','',$param['keyword']) : '';
		$region = isset($param['id_region']) ? (int)$param['id_region'] : 0;
		$_like = array();
		if(!empty($keyword)){
			$keyword = str_replace('.','',$param['keyword']);
			$keyword = $this->db->escape_str($keyword);
			$_like = array('city.nama_city'=> $keyword);
		}
		$sort = array('city.nama_city','ASC');
		$where = array('city.deleted_at'=>null,'city.region'=>$region);
		$master_region = $this->access->readtable('city','',$where,'','',$sort,'','',$_like)->result_array();	
// $sql = $this->db->last_query();		
		$dt = array();
		if(!empty($master_region)){
			foreach($master_region as $mr){				
				$dt[] = array(
					"id_city"		=> $mr['id_city'],
					"city_name"		=> $mr['nama_city']
				);
			}
		}
		if (!empty($dt)){
			$result = array(
				'err_code' 	=> '00',
				'err_msg' 	=> 'ok',
				'data' 		=> $dt	
			);
            http_response_code(200);
			echo json_encode($result);
        }else{
            $result = array(
				'err_code' 	=> '04',
				'err_msg' 	=> 'Data not be found',
				'data' 		=>  ''	
			);
            http_response_code(200);
			echo json_encode($result);
        }
	}
	
}
