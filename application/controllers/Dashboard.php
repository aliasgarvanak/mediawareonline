<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __Construct()
	{
	   	 parent::__Construct();
	   	 $this->load->model('FrontLogin','login');
		 $this->login->check_isvalidated();
	}	


	public function index()
	{
		$data = array();
		$data['states'] = $this->login->state();
		$this->load->view('dashboard',$data);
	}

	public function getDistrict(){
        $districts = array();
		$state = $this->input->post('state');
		if($state){
           
            $districts = $this->login->getDistrict($state);
        }
        echo json_encode($districts);
    }
    
    public function getCity(){
        $cities = array();
        $district = $this->input->post('district');
        if($district){
            $cities = $this->login->getCity($district);
        }
        echo json_encode($cities);
	}
	
	public function search()
	{
		$data['cityDetail'] = $this->login->getCityDetail($_GET['city']);
		//exit;
		$this->load->view('city',$data);


	}

}
