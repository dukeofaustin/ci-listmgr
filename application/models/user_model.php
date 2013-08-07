<?php
  class User_model extends CI_Model {
  
  public function __construct()
  {
     $this->load->database();
     $this->load->helper('date');
  }

  /*
   * Function to retrieve all users from application
   */
  public function get_allusers() {
 
     //get all records from users table
     $this->db->from('tbl_appusers');
     $this->db->order_by('uname'); 
     $query = $this->db->get();
     if( $query->num_rows() > 0 ) {
         return $query->result_array();
     } else {
         return array();
     }
   } //end get_users

  /*
   * Function to retrieve all users from application
   */
  public function get_userdata($uname, $email) {
 
    //get user data from users table
    $userar = array('userid' => 0,
                    'uname' => '',
                    'fname' => '',
                    'lname' => '',
                    'email' => '',
                    'pword' => '',
                    'active' => '');
    $this->db->from('tbl_appusers');
    $this->db->where('uname', $uname);
    $this->db->where('email', $email);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
       $userar = $query->row_array();
    }
    return $userar;
   } //end get_userdata

  /*
   * Function to update application user record
   */
  //public function update_user($mode, $userid, $uname, $email, $fname = '', $lname = '', $pword = '', $roles = NORML_ROLE)
  public function update_user($mode, $userrec)
  {
     $rtn = false;
     $msg = '';
     $err = 'update_user-Error';
     $usrid = $userrec['userid'];
     $uname = $userrec['uname'];
     $email = $userrec['email'];
     $fname = $userrec['fname'];
     $lname = $userrec['lname'];
     $pword = $userrec['pword'];
     $roles = $userrec['roles'];
     $active = $userrec['active'];
     
     if(isset($uname) && isset($email)){
       $data = array('userid' => $usrid,
                     'uname' => $uname,
                     'email' => $email);

       $data['update'] = date('Y-m-d H:i:s', strtotime('now'));
       $this->db->from('tbl_appusers');

       if($usrid > 0){
         $this->db->where('userid', $usrid);
         //$msg = 'Usrid='.$usrid.' ';
       } else {
         $this->db->where('uname', $uname);
         $this->db->where('email', $email);
         //$msg = 'Not set - Usrid='.$usrid.' ';
       }
       $query = $this->db->get();
       if ($query->num_rows() > 0) { // Update
        
         $row = $query->row();
         $userid = $row->userid;
         
         if(strlen(trim($uname)) > 0 && strtolower(trim($row->uname)) != strtolower(trim($uname)))
           $data['uname'] = $uname;
           //$msg .= 'uname: '.$uname.' ';
         if(strlen(trim($email)) > 0 && trim($row->email) != trim($email))
           $data['email'] = $email;
           //$msg .= 'email: '.$email.' ';
         if(strlen(trim($fname)) > 0 && trim($row->fname) != trim($fname))
           $data['fname'] = $fname;
           //$msg .= 'fname: '.$fname.' ';
         if(strlen(trim($lname)) > 0 && trim($row->lname) != trim($lname))
           $data['lname'] = $lname;
           //$msg .= 'lname: '.$lname.' ';
         if(strlen(trim($pword)) > 0 && trim($row->pword) != trim($pword))
           $data['pword'] = $pword;
           //$msg .= 'pword: '.$pword.' ';
         if(strlen(trim($roles)) > 0 && trim($row->roles) != trim($roles))
           $data['roles'] = $roles;
           //$msg .= 'roles: '.$roles.' ';
         if(strlen(trim($active)) > 0 && trim($row->active) != trim($active))
           $data['active'] = $active;
                   
         //$msg .= 'active: '.$active.' ';
           
         $this->db->where('userid', $userid);
         
         if($mode == UPDATE_REC){
            $rtn = $this->db->update('tbl_appusers', $data);
            if($rtn)
               $msg = "Successful-update";
            else
               $msg = "Error in update";
         } else if($mode == DELETE_REC) {
            $tables = array('tbl_appusers'); //???? clean up older user stuff? 
            $this->db->where('userid', $userid);
            $rtn = $this->db->delete($tables);
            if($rtn)
               $msg = "Successful-deletion";
            else
               $msg = "Error in deletion";
         }
       } else {
         //if($mode == INSERT_REC){
            $data['uname'] = $uname;
            $data['email'] = $email;
            $data['fname'] = $fname;
            $data['lname'] = $lname;
            $data['pword'] = $pword;
            $data['roles'] = $roles;
            $data['active'] = $active;
            $rtn = $this->db->insert('tbl_appusers', $data);
            if($rtn)
               $msg = "Successful-insert";
            else
               $msg = "Error in insert";
         //}
       }
       $query->free_result(); // Release memory
     }
     return $msg;
  }

} //end class


/*
  public function get_allusers() {
     $query = array();
     $this->db->select('tbl_appusers.userid as userid,
                        tbl_appusers.uname as uname,
                        tbl_appusers.fname as fname,
                        tbl_appusers.lname as lname,
                        tbl_appusers.email as email,
                        tbl_appusers.roles as roles,
                        tbl_appusers.update as update,
                        tbl_appusers.audit as audit');
     $this->db->from('tbl_appusers');
     $this->db->order_by('uname'); 
     $query = $this->db->get();
     return $query->result();
  } //end get_lsttypes

  `uname`
  `fname`
  `lname`
  `pword`
  `salt` 
  `email`
  `roles`
  `update`
  `audit`
 */