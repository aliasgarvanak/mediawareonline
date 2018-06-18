<?php

class FrontLogin extends CI_Model {
	
	function __construct(){
		parent::__construct();
	}
	
	public function doLogin($data){
		unset($data['submit']);
		$where = array("password"=>base64_encode($data['password']),"username"=>$data['username']);
    	$query = $this->db->select('id,latestLogin')->from('user')->where($where)->get();		
		$record = $query->row();
		if(!empty($record))
		{
			if($record->latestLogin!='')
			{
				$this->session->set_userdata('user_id',$record->id);
				return $record->id;
			}
			else{
				$this->session->set_userdata('user_id',$record->id);
				$this->proceed_login();
			}
		}
		else{
			return false;
		}
	}

	public function checkUser($data)
	{
		/*echo '<pre>';
		print_r($data);
		exit;*/
		$query = $this->db->select('id,latestLogin')->from('user')->where('username',$data['email'])->get();
		$record = $query->row();
		if(!empty($record))
		{
			if($record->latestLogin!='')
			{
				$this->session->set_userdata('user_id',$record->id);
				return $record->id;
			}
			else{
				$this->session->set_userdata('user_id',$record->id);
				$this->proceed_login();
			}
		}
		else{
			// It means this user doesn't exist. So, we have to add new user in the database.
			$insertData = array();
			$cookie_value = $this->random_number();
			// Remove old cookie value and insert new cookie value
			$cookie = array(
				'name'   => 'latestLogin',
				'value'  => $cookie_value,
				'expire' => '15000000',
				'prefix' => ''
			 );
			
			$this->input->set_cookie($cookie);
			$insertData = array(
				'username' => $data['email'],
				'password' => $data['id'],
				'latestLogin' => $cookie_value,

			);
			$this->db->insert('user',$insertData);
			return $this->db->affected_rows();

		}
	
	}

	public function sessionLogout($session_log)
	{
		
		// Remove the latest cookie ID from the user table
		$where = array("latestLogin"=>$this->input->cookie('latestLogin', TRUE));
		$data = array(
			"latestLogin"=>""
		);
		$this->db->where($where);
		$this->db->update('user',$data);
		$result = $this->db->affected_rows();	
		if($result>0)
		{
			delete_cookie('latestLogin');
			$this->session->set_flashdata('message_notification','You have been logged out successfully');
			$this->session->set_flashdata('class','success');
        	redirect('login');
		}
		else{
		
			$this->session->set_flashdata('message_notification','Something went wrong, please try again later');
			$this->session->set_flashdata('class','danger');
        	redirect('dashboard');
		}
			
	}
	
	public function check_isvalidated(){
		$exist_cookie = $this->input->cookie('latestLogin', TRUE);
		if($exist_cookie!='' ){
		//It means browser already logged in before.

		$where = array("latestLogin"=>$exist_cookie);
    	$query = $this->db->select('id')->from('user')->where($where)->get();		
		$record = $query->row();
		if(empty($record))
		{
			$this->session->set_userdata('redirect_url', base_url(uri_string()));
			$this->session->set_flashdata('message_notification','Please login to continue');
			$this->session->set_flashdata('class','danger');
            redirect('login');
		}
		else{
			return true;
		}
		}
		else{
			$this->session->set_userdata('redirect_url', base_url(uri_string()));
			$this->session->set_flashdata('message_notification','Please login to continue');
			$this->session->set_flashdata('class','danger');
            redirect('login');	
		}
	}
	
	function random_number($l = 8) {
		return substr(md5(uniqid(mt_rand(), true)), 0, $l);
	}

	function proceed_login()
	{
		if(! $this->session->userdata('user_id')){
			$this->session->set_flashdata('message_notification','Something went wrong, please try again');
			$this->session->set_flashdata('class','danger');
            redirect('login');
		}
		else{
			$cookie_value = $this->random_number();
			// Remove old cookie value and insert new cookie value
			$cookie = array(
				'name'   => 'latestLogin',
				'value'  => $cookie_value,
				'expire' => '15000000',
				'prefix' => ''
			 );
			
			$this->input->set_cookie($cookie);
			$data = array(
				"latestLogin" => $cookie_value
			);
			// Put latest cookie as a login
			$where = array("id"=>$this->session->userdata('user_id'));
			$this->db->where($where);
			$this->db->update('user',$data);
			$response = $this->db->affected_rows();
			if($response>0)
			{
				$this->session->set_flashdata('message_notification','You are logged in successufully');
				$this->session->set_flashdata('class','success');
            	redirect();
			}
			else{
				$this->session->set_flashdata('message_notification','Something went wrong, please try again');
				$this->session->set_flashdata('class','danger');
            	redirect('login');
			}

		}
	}

	public function state()
	{
		$query = $this->db->select('id,name')
		->from('state')
		->get();		
		return $query->result();
	}
	public function getDistrict($state = array())
	{
		$this->db->select('id,name');
		$this->db->from('district');
		foreach($state as $k=>$v)
		{
			$this->db->or_where('state',$v);
		}
		$result = $this->db->get()->result();		
		return $result;
	}
	public function getCity($district = array())
	{
		$this->db->select('id,name');
		$this->db->from('city');
		foreach($district as $k=>$v)
		{
			$this->db->or_where('district',$v);
		}
		$result = $this->db->get()->result();		
		return $result;
	}

	public function getCityDetail($city = array())
	{
		/*echo '<pre>';
		print_r($city);
		exit;*/
		$this->db->select('*');
		$this->db->from('city');
		foreach($city as $k=>$v)
		{
			$this->db->or_where('id',$v);
		}
		$result = $this->db->get()->result();
		return $result;
	}
}

?>
