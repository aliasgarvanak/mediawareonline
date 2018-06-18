<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class LinkedIn extends CI_Controller
{
    function __construct() {
        parent::__construct();
        
        // Load linkedin config
        $this->load->config('linkedin');
        
        //Load user model
        $this->load->model('FrontLogin','user');
    }
    
    public function index(){
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
                    'email'         => $userInfo->emailAddress,
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
        }else{
            $this->session->set_flashdata('message_notification','Something went wrong, please try again');
            $this->session->set_flashdata('class','danger');
            redirect('login');
        }
        
        $data['userData'] = $userData;
        
        // Load login & profile view
        $this->load->view('linkindex',$data);
    }
}