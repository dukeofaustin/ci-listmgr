<?php
 class Users extends CI_Controller {
  public function __construct() 
  {
	parent::__construct();
	$this->load->model( 'user_model' );
	$this->load->library('session');  // for flash data
	//Datagrid inclusion:
	$this->load->helper(array('datagrid','url'));
	$this->Datagrid = new Datagrid('tbl_appusers','userid');
  }
  public function index()
  {
	$this->load->view('templates/header');
	$this->load->view('users/index');
	$this->load->view('templates/footer');
  }
  function grid(){
	$this->load->helper('form');
	$this->load->library('session');
	$this->load->view('templates/header');
	$this->load->view('users/index');
	$this->load->view('templates/footer');
	/*
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
	*/
  }
  function post($request_type = ''){
	$this->load->helper('url');
    $rtn = 'getPostAction(): ['.Datagrid::getPostAction().'] ';
	if($action = Datagrid::getPostAction()){
	  $error = "";
      $rtn .= 'getpostitems['.Datagrid::getPostItems().'] ';
      return $rtn;      
	  switch($action){
		case 'delete' :
		  if(!$this->Datagrid->deletePostSelection()){
			$error = 'Items could not be deleted';
		  } else {
			$error = 'Groovy man';
		  }
		  break;
		case 'insert' :
		  if(!$this->Datagrid->insertPostSelection()){
			$error = 'Items could not be inserted';
		  } else {
			$error = 'Insert item is not yet implemented';
		  }
		  //echo $action;
		  break;
	  }
	  if($request_type!='ajax'){
		$this->load->library('session');
		$this->session->set_flashdata('form_error',$error);
		redirect('users/index');
	  } else {
		echo json_encode(array('error' => $error));
	  }
	} else {
	  die("Bad Request");
	}
    return $rtn;    
  }
    
} //end class