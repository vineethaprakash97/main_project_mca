<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Login extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
    } 

    /*
     * Listing of login
     */
    function index()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('password','Password','required');
        if($this->form_validation->run())     
        {
            $username=$this->input->post('username');
            $password=$this->input->post('password');
            //echo $username;
            //echo $password;
            $login= $this->Login_model->get_user($username,$password);
            //var_dump($login);
    
            if($login!=null)
                    {
                        $usertype=$login['user_type'];
                        $username=$login['username'];
                        $this->session->set_userdata("usertype",$usertype);
                        $this->session->set_userdata("username",$username);
                        if($usertype=="admin"){
                            redirect("Village_recovery/admin_show_village_recovery");
                        }else{
                            redirect("Village_recovery/show_monthly_recovery");
                            
                        }
                       
                        
                    }
                    else
                    {
    
                        
                        $this->session->set_flashdata('message','Login failed'); 
                        
                    }
        }


        $this->load->view('login.php');
    }

 
    public function logout()
    {
        $user_data=$this->session->all_userdata();
        foreach ($user_data as $key => $value) 
        {
            if ($key !='session_id' && $key !='ip_address' && $key !='user_agent' && $key !='last_activity')

            {
                $this->session->unset_userdata($key);
            }
        }
        $this->session->sess_destroy();
        $this->session->unset_userdata('usertype');
        $this->session->unset_userdata('username');
        redirect(base_url().'index.php/login');
        
    } 

  
   
   
}
