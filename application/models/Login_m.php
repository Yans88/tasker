<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login_m extends CI_Model {

	public function load_form_rules() {
		$form_rules = array(
			array(
				'field' => 'u_name',
				'label' => 'username',
				'rules' => 'required'
				),
			array(
				'field' => 'pass_word',
				'label' => 'password',
				'rules' => 'required'
				),
			);
		return $form_rules;
	}

	public function validasi() {
		$form = $this->load_form_rules();
		$this->form_validation->set_rules($form);

		if ($this->form_validation->run()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

    // cek status user, login atau tidak?
	public function cek_user() {
		$u_name = strtolower($this->input->post('u_name'));
		$pass_word = $this->input->post('pass_word');
		
		$username = md5($u_name);
		$password = md5($pass_word);
		
		// error_log($password);
		$query = $this->db->select('admin.*,level.*')
		
		->join('level','level.id=admin.level','left')		
		->where('admin.username', $username)
		->where('admin.password', $password)
		->where('admin.status', '1')
		->where('admin.deleted_at', null)
		->limit(1)
		->get('admin');
	
		if ($query->num_rows() == 1) {
			$row = $query->row();			
			$data = array(
				'login'				=> TRUE,
				'u_name' 			=> ucwords($u_name), 
				'name' 				=> $row->name,
				'operator_id' 		=> $row->operator_id,				
				'level_name'		=> $row->level_name,
				'level'				=> $row->level,			
				'role'				=> $row->role,			
				'users'				=> $row->users,			
				'm_coin'			=> $row->m_coin,			
				'm_premium'			=> $row->m_premium,			
				'category'			=> $row->category,			
				'faq'				=> $row->faq,			
				'customer'			=> $row->customer,			
				'setting'			=> $row->setting			
			);
			
			// simpan data session jika login benar
			$this->session->set_userdata($data);
			return TRUE;
		} else {
			return FALSE;
		}
		
	}

	// public function logout() {
		// $this->session->unset_userdata(array('u_name' => '', 'login' => FALSE));
		// $this->session->sess_destroy();
	// }
}
