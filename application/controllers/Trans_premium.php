<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trans_premium extends MY_Controller {

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
		$this->data['judul_browser'] = 'Transaksi Premium';
		$this->data['judul_utama'] = 'Pending';
		$this->data['judul_sub'] = 'Premium';
		$this->data['status'] = 1;
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone');
		$where = array();
		$where = array('transaksi.status'=>1,'transaksi.type'=>2);		
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
		$this->data['transaksi'] = $this->access->readtable('transaksi',$selects,$where,array('customer'=>'customer.id_customer = transaksi.id_member'),'','','LEFT')->result_array();
		
		$this->data['tgl'] = $tgl;
		$this->data['url'] = site_url('trans_coin');
		$this->data['isi'] = $this->load->view('trans_coin/trans_premium_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	
	public function confirm() {
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';	
		$this->data['judul_browser'] = 'Transaksi Premium';
		$this->data['judul_utama'] = 'Confirm Payment';
		$this->data['judul_sub'] = 'Premium';
		$this->data['status'] = 2;
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone');
		$where = array();
		$where = array('transaksi.status'=>2,'transaksi.type'=>2);
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
		$this->data['transaksi'] = $this->access->readtable('transaksi',$selects,$where,array('customer'=>'customer.id_customer = transaksi.id_member'),'','','LEFT')->result_array();
		$this->data['tgl'] = $tgl;
		$this->data['isi'] = $this->load->view('trans_coin/trans_premium_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function appr() {
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';	
		$this->data['judul_browser'] = 'Transaksi Premium';
		$this->data['judul_utama'] = 'Success';
		$this->data['judul_sub'] = 'Premium';
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone','admin.fullname');
		$where = array();
		$this->data['status'] = 4;
		$where = array('transaksi.status'=>4,'transaksi.type'=>2);
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
		$this->data['isi'] = $this->load->view('trans_coin/trans_premium_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}
	
	public function reject(){
		if(!$this->session->userdata('login') || !$this->session->userdata('m_premium')){
			$this->no_akses();
			return false;
		}
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';		
		$this->data['judul_browser'] = 'Transaksi Premium';
		$this->data['judul_utama'] = 'Cancelled';
		$this->data['judul_sub'] = 'Premium';
		$this->data['status'] = 3;
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone','admin.fullname');
		$where = array();
		$where = array('transaksi.status'=>3,'transaksi.type'=>2);
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
		$this->data['isi'] = $this->load->view('trans_coin/trans_premium_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	public function appr_reject(){
		$tgl = date('Y-m-d H:i:s');
		$where = array(
			'id_trans' 		=> (int)$_POST['id_trans']			
		);
		$_data = array();
		$dt_cron = array();
		$_data = array(
			'status'	=> (int)$_POST['status'],
			'reason'	=> $_POST['alasan'],
			'status_date'	=> $tgl,
			'status_by'	=> $this->session->userdata('operator_id')
		);
		$trans = $this->access->readtable('transaksi','',$where)->row();
		$id_member = (int)$trans->id_member;
		if((int)$_POST['status'] == 4){			
			$buy_hari = (int)$trans->jml;
			$customer = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
			$premium_start_date = !empty($customer->premium_start_date) ? $customer->premium_start_date : '';
			$premium_end_date = !empty($customer->premium_end_date) ? $customer->premium_end_date : '';
			$_tgl = date('Y-m-d');
			$tgl_awal = $_tgl;
			$premium_start_date = $_tgl;
			if(!empty($premium_start_date) && !empty($premium_end_date)){
				if ($_tgl >= $premium_start_date && $_tgl <= $premium_end_date){
					$tgl_awal = $premium_end_date;
					$premium_start_date = $premium_start_date;
				}		
			}
			$_add = date('Y-m-d', strtotime($tgl_awal. ' + '.$buy_hari.' days'));
			$data = array();
			$data = array('premium_start_date'=>$premium_start_date,'premium_end_date'=>$_add);
			$this->access->updatetable('customer', $data, array('id_customer'=>$id_member));
			$_data += array('premium_start'=>$tgl_awal,'premium_end'=>$_add);
			$_kali = $buy_hari / 30;
			
			$dt_cron = array();
			$customer = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
			$my_coin = (int)$customer->coin;
			$ttl_coin = $my_coin + 1;
			$this->access->updatetable('customer', array('coin'=>$ttl_coin), array('id_customer'=>$id_member));
			$dt_history = array();
			if($tgl_awal == $_tgl){
				$dt_history = array(
					'id_ta' 		=> (int)$_POST['id_trans'],
					'id_act' 		=> (int)$_POST['id_trans'],
					'id_member' 	=> $id_member,
					'coin' 			=> 1,
					'ttl_coin' 		=> (int)$ttl_coin,
					'type' 			=> 4,				// bonus coin premium
					'created_at' 	=> $tgl,
					'ket' 			=> 'Bonus coin premium #'.(int)$_POST['id_trans']
				);
				$this->access->inserttable('history_coin', $dt_history);
			}else{
				$dt_cron[] = array(
					'id_member'		=> $id_member,
					'id_transaksi'	=> (int)$_POST['id_trans'],
					'tgl'			=> $tgl_awal,
					'coin'			=> 1,
					'created_at'	=> $tgl,
					'status'		=> 0
				);
			}
			for($i=1;$i<(int)$_kali;$i++){
				$tgl_add = 0;
				$tgl_add = (int)$i * 30;
				$_tgl = '';
				$_tgl = date('Y-m-d', strtotime($tgl_awal. ' + '.$tgl_add.' days'));
				$dt_cron[] = array(
					'id_member'		=> $id_member,
					'id_transaksi'	=> (int)$_POST['id_trans'],
					'tgl'			=> $_tgl,
					'coin'			=> 1,
					'created_at'	=> $tgl,
					'status'		=> 0
				);
			}
			$this->htg_trans_prem($id_member);
		}
		if((int)$_POST['status'] == 3){
			$pesan = 'Pembelian Premium #'.$_POST['id_trans'].' tidak disetujui';
			if(!empty($alasan)) $pesan .= ' karena '.$alasan;
			$this->insert_inbox($id_member, $pesan,9,$_POST['id_trans']);
		}
		if(!empty($dt_cron)){
			$this->db->insert_batch('cron_coin_premium', $dt_cron);
		}
		$this->access->updatetable('transaksi', $_data, $where);
	}
	
	function htg_trans_prem($id_member=0){
		$tgl = date('Y-m-d H:i:s');
		$where = array();
		$where = array('transaksi.status'=>4,'transaksi.type'=>2,'id_member'=>$id_member);
		$transaksi = $this->access->readtable('transaksi',array('id_trans'),$where)->result_array();
		$cnt_transaksi = count($transaksi);
		$cnt_transaksi = (int)$cnt_transaksi;
		$achievement = 0;
		$next_cnt_transaksi = 0;
		if($cnt_transaksi == 1){
			$achievement = 1;
			$next_cnt_transaksi = 3;
		}
		if($cnt_transaksi == 3){
			$achievement = 1;
			$next_cnt_transaksi = 5;
		}
		if($cnt_transaksi > 4 && $cnt_transaksi % 5 == 0){
			$achievement = 1;
			$next_cnt_transaksi = $cnt_transaksi + 5;
		}
		$dt_achievement = array();
		if((int)$achievement > 0){
			$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 4,'status' =>-1));
			$cnt_achievement = $this->access->readtable('achievement',array('id_member'),array('id_member'=> (int)$id_member,'type' => 4,'status' =>-1))->row();
			$_cnt_achievement = (int)$cnt_achievement->id_member > 0 ? (int)$cnt_achievement->id_member : 0;
			if($cnt_achievement > 0){
				$this->access->updatetable('achievement',array('status'=>0),array('id_member'=> (int)$id_member,'type' => 4,'status' =>-1));
			}else{
				$dt_achievement = array(
					'id_member'		=> (int)$id_member,
					'type'			=> 4,
					'title'			=> 'Achievement 4',
					'keterangan'	=> 'Bonus achievement subscription  '.$next_cnt_transaksi.'x',
					'coin'			=> 1,
					'status'		=> -2,
					'created_at'	=> $tgl
				);
				$this->access->inserttable('achievement',$dt_achievement);
			}
			
		}		
	}
	
	public function export_r(){
		$tgl = date('dmY');
		$this->load->library('excel');
		$tgl = isset($_POST['tgl']) ? $_POST['tgl'] : '';	
		$status = isset($_POST['tgl']) ? (int)$_POST['status'] : 0;	
		$selects = array('transaksi.*','customer.nama','customer.last_name','customer.phone');
		$where = array();
		$where = array('transaksi.status'=>$status,'transaksi.type'=>2);
		$from = '';
		$to = '';
		if(!empty($tgl) && $tgl != 'Tanggal harus diisi'){
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
		if($status == 1) $status_name = 'New Order';
		if($status == 2) $status_name = 'Confirmed';
		if($status == 3) $status_name = 'Rejected';
		if($status == 4) $status_name = 'Approved';	
		$transaksi = $this->access->readtable('transaksi',$selects,$where,array('customer'=>'customer.id_customer = transaksi.id_member'),'','','LEFT')->result_array();
		
		$filename = 'Transaksi Premium_'.$status_name;
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('data_transaksi');		
		$this->excel->getActiveSheet()->setCellValue('A1', 'Transaksi Premium - '.$status_name);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
		$this->excel->getActiveSheet()->getStyle('A3:H3')->getFont()->setSize(12);				
		$this->excel->getActiveSheet()->getStyle('A3:H3')->getFont()->setBold(true);
		$styleArray = array(
		  'borders' => array(
			'allborders' => array(
			  'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		  )
		);
		$this->excel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->setCellValue('A3', 'No.');
        $this->excel->getActiveSheet()->setCellValue('B3', 'Tanggal');
		$this->excel->getActiveSheet()->setCellValue('C3', 'Member');
        $this->excel->getActiveSheet()->setCellValue('D3', 'Payment');
        $this->excel->getActiveSheet()->setCellValue('E3', 'Jumlah Hari');
        $this->excel->getActiveSheet()->setCellValue('F3', 'Nominal');
        $this->excel->getActiveSheet()->setCellValue('G3', 'Kode Unik');
        $this->excel->getActiveSheet()->setCellValue('H3', 'Total');
		
		$this->excel->getActiveSheet()->getStyle('A3:H3')->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFE8E5E5');
		$this->excel->getActiveSheet()->getStyle('A3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=4;
		$no = 1;
		if(!empty($transaksi)){
			foreach($transaksi as $t){
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $no++.'.');
				$this->excel->getActiveSheet()->setCellValue('B'.$i, date('d M Y', strtotime($t['created_at'])));	
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $t['nama'].' '.$t['last_name'].' - '.$t['phone']);	
				$this->excel->getActiveSheet()->setCellValue('D'.$i, 'Manual Transfer - '.$t['bank_name']);	
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $t['jml']);	
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $t['nominal']);	
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $t['kode_unik']);	
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $t['total']);	
				
				$this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($styleArray);
				$this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setSize(12);
				$this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);	
				if($t['jml'] >= 100) $this->excel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('0,000');
				if($t['nominal'] >= 100) $this->excel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode('0,000');
				if($t['total'] >= 100) $this->excel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('0,000');
				
				$this->excel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->excel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
				$this->excel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$i++;
			}
			unset($styleArray);	
		}
		$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
		
		$filename = $filename.'.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"'); 
		header('Cache-Control: max-age=0'); 					 
		
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  		
		$objWriter->save('php://output');
	}
	
	function insert_inbox($id_member=0, $pesan = '', $type=0, $_id=0){
		$login = $this->access->readtable('customer','',array('id_customer'=>$id_member))->row();
		$this->load->library('send_notif');			
		$send_fcm = '';	
		$ids = array();
		$ids = array($login->fcm_token);
		$data_fcm = array(
			'id'					=> $_id,
			'id_member_to'			=> $id_member,
			'nama_pengirim'			=> '',
			'last_name_pengirim'	=> '',
			'title'					=> 'Tasker',
			'type'					=> $type				
		);
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
		$this->access->inserttable('master_chat', $dt);		
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
