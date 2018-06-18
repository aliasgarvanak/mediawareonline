<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function __Construct()
	{
		parent::__Construct();
		$this->load->config('linkedin');
	   	$this->load->model('FrontLogin','login');
	}	

	public function index()
	{
		$userData = array();
        
        //Include the linkedin api php libraries
        include_once APPPATH."libraries/linkedin-oauth-client/http.php";
        include_once APPPATH."libraries/linkedin-oauth-client/oauth_client.php";
        
        
        //Get status and user info from session
        $oauthStatus = $this->session->userdata('oauth_status');
        $sessUserData = $this->session->userdata('userData');

        if(isset($oauthStatus) && $oauthStatus == 'verified'){
            //User info from session
            $userData = $sessUserData;
        }elseif((isset($_REQUEST["oauth_init"]) && $_REQUEST["oauth_init"] == 1) || (isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']))){
            $client = new oauth_client_class;
            $client->client_id = $this->config->item('linkedin_api_key');
            $client->client_secret = $this->config->item('linkedin_api_secret');
            $client->redirect_uri = base_url().$this->config->item('linkedin_redirect_url');
            $client->scope = $this->config->item('linkedin_scope');
            $client->debug = false;
            $client->debug_http = true;
            $application_line = __LINE__;
            
            //If authentication returns success
            if($success = $client->Initialize()){
                if(($success = $client->Process())){
                    if(strlen($client->authorization_error)){
                        $client->error = $client->authorization_error;
                        $success = false;

                    }elseif(strlen($client->access_token)){
                        $success = $client->CallAPI('http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 
                        'GET',
                        array('format'=>'json'),
                        array('FailOnAccessError'=>true), $userInfo);
                    }
                }
                $success = $client->Finalize($success);
            }
            
            if($client->exit) exit;
    
            if($success){
                $userData = array(
                    'email'     => $userInfo->emailAddress,
                    'id'        => $userInfo->id
                );
                //Insert or update user data
                $userID = $this->user->checkUser($userData);
                
                if($userID>0)
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
                

            }else{
                $this->session->set_flashdata('message_notification','Something went wrong, please try again');
                $this->session->set_flashdata('class','danger');
                redirect('login');
            }
        }elseif(isset($_REQUEST["oauth_problem"]) && $_REQUEST["oauth_problem"] <> ""){
                    $this->session->set_flashdata('message_notification','Something went wrong, please try again');
                    $this->session->set_flashdata('class','danger');
                    redirect('login');
		}
		$data['oauthURL'] = base_url().$this->config->item('linkedin_redirect_url').'?oauth_init=1';
		$this->load->view('login',$data);
	}

	public function logout()
	{
		$this->login->sessionLogout();
	}

	public function submit()
	{
		/*echo '<pre>';
		print_r($_POST);
		exit;*/
		$this->form_validation->set_rules('username', 'Username','required',array('required'=>'Please Enter The User Name'));
		$this->form_validation->set_rules('password', 'Password', 'required',array('required'=>'Please Enter The Password'));
		if ($this->form_validation->run() == FALSE)
                {
					$this->session->set_flashdata('message_notification',validation_errors());
					$this->session->set_flashdata('class',A_FAIL);
					redirect('login');						
                }
        else
                {
						$response = $this->login->doLogin($this->input->post());
						if($response!='' and $response>0)
						{
							$this->session->set_flashdata('message_notification','You are already logged in. If you want to login then click <a href="'.base_url('login/proceed_login').'">here</a> to proceed again');
							$this->session->set_flashdata('class','danger');
							redirect('login/already');
						}
						else
						{
							$this->session->set_flashdata('message_notification','Invalid username or password');
							$this->session->set_flashdata('class','danger');
							// Username and Password is wrong.
							redirect('login');
						}                   
                }
	}

	public function already()
	{
		$this->load->view('already');
	}

	public function proceed_login()
	{
		$this->login->proceed_login();
	}
}
