<?php
//session_start();
class Login extends CI_Controller {
        
    public function __construct()
    {
       parent::__construct();
       $this->load->model('user_model');
       $this->load->library('session');  // for flash data
       $this->load->helper(array('url'));
    }
      
    function index()
    {
       $data['title'] = 'the site';
       $this->load->view('login/index',$data);		
    }
    
    /**
       * resetAuthorized cookie
       * @return  void
    */
    function signout() {
       $this->session->sess_destroy();
       setcookie('authorized', 0, time()-3600);  	    
       header("Location: index.php?login");
    }
     
    function signon()
    {
       $userarr = 'Not logged in yet';
       $userdata = array( 'uname' => $userarr);
       $email = $this->input->post('email');			
       $uname = $this->input->post('uname');
       $userdata = $this->user_model->get_userdata($uname, $email);
    
       if(isset($userdata['uname']) && strtolower($userdata['uname']) == strtolower($uname))
       {
           $udata = array(
                  'usrid' => $userdata['userid'],
                  'fname' => $userdata['fname'],
                  'uname' => $uname,
                  'email' => $email
                  );
           
           $this->session->set_userdata($udata); 
    
           setcookie('authorized', 1, 0);
    
           $userarr = $userdata;
           
       } else {
           $this->index();  //redirect('/login/form/', 'refresh');
       }
       echo json_encode($userarr);
       exit;
    }
     
    function upduser()
    {
      $message = '';
      $emode = $this->input->post('emode');
      $userid = 0;
      $fname = $this->input->post('fname');
      $lname = $this->input->post('lname');
      $email = $this->input->post('email');			
      $uname = $this->input->post('uname');
      $pword = $this->input->post('pword');
      $active = 'Yes';
      
      $sarr = $this->session->all_userdata();
	  if (isset($sarr['usrid']) && $sarr['usrid'] != '' && $sarr['usrid'] != '0') 
          $userid = $sarr['usrid'];
          
      if(isset($emode) &&
        (isset($uname) && $uname != '') &&
        (isset($email) && $email != '')) {
         
         // default all fields with stored values
         
         $userdata = $this->user_model->get_userdata($uname, $email);
         
         $name = $userdata['uname'];
         $mail = $userdata['email'];
         
         if($emode == INSERT_REC &&
            (strtolower($name) == strtolower($uname) ||
             strtolower($mail) == strtolower($email))) {
            $message = 'User name and/or email already in use.';
         } else {
            $userdata['userid'] = $userid;
            $userdata['uname'] = ($userdata['uname'] = $uname) ? $userdata['uname'] : $uname;
            $userdata['fname'] = ($userdata['fname'] = $fname) ? $userdata['fname'] : $fname;
            $userdata['lname'] = ($userdata['lname'] = $lname) ? $userdata['lname'] : $lname;
            $userdata['email'] = ($userdata['email'] = $email) ? $userdata['email'] : $email;
            $userdata['pword'] = ($userdata['pword'] = $pword) ? $userdata['pword'] : $pword;
            $userdata['email'] = ($userdata['email'] = $email) ? $userdata['email'] : $email;
            $userdata['active'] = ($userdata['active'] = $active) ? $userdata['active'] : $active;
            $userdata['roles'] = 'normal';
            $message = $this->user_model->update_user($emode, $userdata);
         }
         /*
         $tmpstr = 'emode: '.$emode.' email: '.$email.' uname: '.$uname;
         print_r($tmpstr);
         $tmpstr = ' fname: '.$fname.' lname: '.$lname.' pword: '.$pword;
         print_r($tmpstr);
         */
      }
      echo $message;
    }
    function editform()
    {
        //todo: put in User controller/view -- needed for dev test of user_model->update_user
        $uname = '';
        $email = '';
        $sarr = $this->session->all_userdata();
        if (isset($sarr['uname']) && $sarr['uname'] != "") {
            $uname = $sarr['uname'];
        }
        if (isset($sarr['email']) && $sarr['email'] != "") {
            $email = $sarr['email'];
        }
        if($uname != '' && $email != '' ) {
            $data['users'] = $this->user_model->get_userdata($uname, $email);
            $data['title'] = 'Edit User Data';
            //var_dump("data ".$data);
            $this->load->view('templates/header', $data);
            $this->load->view('login/editform', $data);
            $this->load->view('templates/footer');
        } else {
			echo 'Username and Email not set';
		}
    }
}
	/*
	function validate_credentials()
	{		
		$this->load->model('membership_model');
		$query = $this->membership_model->validate();
		
		if($query) // if the user's credentials validated...
		{
			$data = array(
				'username' => $this->input->post('username'),
				'is_logged_in' => true
			);
			$this->session->set_userdata($data);
		        $this->load->view('login/logged_in_area', $data);		
		}
		else // incorrect username or password
		{
			$this->index();
		}
	}	
	function create_member()
	{
		$this->load->model('membership_model');
		
		if($query = $this->membership_model->create_member())
		{
 		     $this->index();
		}
		else
		{
		     return false;			
		}
	}
	function success()
	{
		//echo 'in here';
		$data['main_content'] = 'login/includes/success';
		$this->load->view('login/includes/template', $data);
	}

	function signup_form()
	{
		//echo 'in here';
		$data['main_content'] = 'login/signup_form';
		$this->load->view('login/includes/template', $data);
	}
	
	function orig_create_member()
	{
		$this->load->library('form_validation');
		
		// field name, error message, validation rules
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
		
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('login/signup_form');
		}
		
		else
		{			
			$this->load->model('membership_model');
			
			if($query = $this->membership_model->create_member())
			{
				$data['main_content'] = 'login/logged_in_area';
				$this->load->view('login/includes/template', $data);
			}
			else
			{
				$this->load->view('login/signup_form');			
			}
		}
	}
	
        function orig_manage_users(){
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->view('login/index');
    $this->Datagrid->hidePkCol(true);
	    $this->Datagrid->ignoreFields(array('password'));
	    $this->Datagrid->setHeadings(array('email'=>'E-mail'));
	    if($error = $this->session->flashdata('form_error')){
		    echo "<font color=red>$error</font>";
	    }
	    echo form_open('tdatagrid/proc');
	    echo $this->Datagrid->generate();
	    echo Datagrid::createButton('delete','Delete');
	    echo form_close();
        }
	*/
