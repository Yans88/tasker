<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Send_notif

{
	protected 	$ci;
	public function __construct() {		
		// $this->load->library('converter');
		$this->ci =& get_instance();
		$this->ci->load->library('email');		
	}
	
	
	public function send_email($from=null,$pass=null, $to=null,$subject=null, $body=null){
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			//'smtp_host' => 'mail.idijakbar.org',
			//'smtp_port' => 25,
			'smtp_timeout' => 5,
			'smtp_user' => $from,
			'smtp_pass' => $pass,
			'mailtype' => 'html',
			'charset' => 'iso-8859-1',
			'crlf' => "\r\n",
			'newline' => "\r\n",
			'wordwrap' => TRUE
		);		
			   
		$this->ci->email->initialize($config);
		$this->ci->email->from($from, 'Tasker');
		$this->ci->email->to($to);
		$this->ci->email->subject($subject);
		$this->ci->email->message($body);
		$save = $this->ci->email->send();
		
		return $save;					   
	}
	
	
	
	function send_fcm($data_fcm=array(), $notif_fcm=array(), $target){
		$url = 'https://fcm.googleapis.com/fcm/send';
		
		$server_key = '';
					
		$fields = array();
		
		$fields['data'] = $data_fcm;
		$fields['notification'] = $notif_fcm;
		if(is_array($target)){
			$fields['registration_ids'] = $target;
		}else{
			$fields['to'] = $target;
		}		
		
		$headers = array(
			'Content-Type:application/json',
			'Authorization:key='.$server_key
		);
					
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);		
		return $result;
	}	
	
	
	
}

?>
