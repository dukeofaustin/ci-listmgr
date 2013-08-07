<?php

class Membership_model extends CI_Model {

	public function __construct()
	 {
	  $this->load->database();
	 }
	 
	function validate()
	{
		$this->db->where('username', $this->input->post('username'));
		$this->db->where('password', md5($this->input->post('password')));
		$query = $this->db->get('membership');
		
		if($query->num_rows == 1)
		{
			return true;
		}
		
	}
	public function get_members() {
       
	      //get all records from users table
	      $query = $this->db->get( 'membership' );
	      if( $query->num_rows() > 0 ) {
		  return $query->result_array();
	      } else {
		  return array();
	      }
       
	} //end get_users	
	function create_member()
	{
		
		$new_member_insert_data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email_address' => $this->input->post('email_address'),			
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'audit' => date('Y-m-d H:i:s')
		);
		
		$insert = $this->db->insert('membership', $new_member_insert_data);
		return $insert;
	}
	function update_member()
	{
		
		$data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email_address' => $this->input->post('email_address'),			
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'audit' => date('Y-m-d H:i:s') 
		);
		$this->db->where('id',$this->input->post('id'));
		$update = $this->db->update('membership', $data);
		return $update;
	}
}