<?php
  class Wmtest extends CI_Controller {
 
    public function __construct() 
    {
        parent::__construct();
        $this->load->model( 'wmtest_model' );
	 $this->load->helper(array('url'));

    }

    public function index()
    {
      	$data['title'] = 'WMTEST---Index for JQuery';
    	$this->load->view('templates/header', $data);
        $this->load->view('wmtest/show');
    	$this->load->view('templates/footer');
	//echo "<pre>";
	//die(print_r('goob',TRUE));   
    }
    public function view()
    {
	$data['records'] = $this->wmtest_model->get_grpitems(); //($config['per_page'], $this->uri->segment(3));
	$this->load->library('pagination');
	$this->load->library('table');
	//$this->table->set_heading('Id', 'Id2', 'Item');
	$config['base_url'] = base_url().'index.php?wmtest/view';
	$config['total_rows'] =  $this->wmtest_model->item_count();
	$config['per_page'] = 20;
	$config['num_links'] = 20;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
	//$data['records'] = $this->db->get('tbl_lstitem', $config['per_page'], $this->uri->segment(3));
      	$data['title'] = 'WMTEST-View';
      	$data['total'] = $this->wmtest_model->item_count();
    	$this->load->view('templates/header', $data);
        $this->load->view('wmtest/view');
    	$this->load->view('templates/footer');
    }
    public function show()
    {
        $config = array();
	$this->load->library('pagination');
	$this->load->library('table');
	$config['base_url'] = base_url().'index.php?wmtest/show';
	$config['total_rows'] =  $this->wmtest_model->item_count();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["results"] = $this->wmtest_model->fetch_listitems($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
	$data["testvar"] = "[".$this->uri->segment(2)."]";
    	$this->load->view('templates/header', $data);
        $this->load->view('wmtest/show',$data);
    	$this->load->view('templates/footer');
    }
  function xjqry($record_id)
  {
     //$record_id = $_POST['record_id'];   //set the record ID
     //$this->load->library(database);   //load the database library to connect to your database
     //$this->load->model(records);      //inside your system/application/models folder, create a model based on the procedure
     $data['record'] = $this->wmtest_model->get_record($record_id);   //get the record from the database
     echo var_dump($data);
  }
  function jqry()
  {
    //http://www.ryantetek.com/2009/12/how-to-create-a-simple-ajax-post-in-codeigniter-using-jquery/
    $this->load->view('wmtest/jqry');
  }
  function xpost_action()
  {
    $message ="username/password not defined";
    $bg_color = "#b0e0e6";
    if(isset($_POST['username']) && isset($_POST['password']))
    {
      if(($_POST['username'] == "") || ($_POST['password'] == ""))
      {
	$message = "Please fill up blank fields";
	$bg_color = "#FFEBE8";
      
      }elseif(($_POST['username'] != "myusername") || ($_POST['password'] != "mypassword")){
	$message = "Username and password do not match.";
	$bg_color = "#FFEBE8";
      }else{
	$message = "Username and password matched.";
	$bg_color = "#FFA";
      }
    }
    $output = '{ "message": "'.$message.'", "bg_color": "'.$bg_color.'" }';
    echo $output;
    return true;
    exit;
  }   
  function post_action()
  {
    $message ="post_action()-username: not defined";
    $bg_color = "#b0e0e6";
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $message = '<div class="ajaxmsg">user['.$username.'] password['.$password.']</div>';
    echo $message;
    //$message = '{ "message": "user['.$username.']", "bg_color": "'.$bg_color.'" }';
    //echo $message;
    return true;
    exit;
  }      

} //end class