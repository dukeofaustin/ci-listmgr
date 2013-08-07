<?php
  class Wmtest_model extends CI_Model {
  
  public function __construct()
  {
   $this->load->database();
  }
  
  public function get_grpitems() {
     $query = array();
     $this->db->select('tbl_lstgrp.descr as type,tbl_lstitem.itemid,tbl_lstitem.descr as item');
     $this->db->from('tbl_lstgrp');
     $this->db->join('tbl_lstitem', 'tbl_lstgrp.grpid = tbl_lstitem.grpid');
     $this->db->order_by("type", "item"); 
     $query = $this->db->get();
     return $query;
  } //end get_users

  public function fetch_listitems($limit, $start) {
        $this->db->limit($limit, $start);
        $query = array();
        $this->db->select('tbl_lstgrp.descr as type,tbl_lstitem.itemid,tbl_lstitem.descr as item');
        $this->db->from('tbl_lstgrp');
        $this->db->join('tbl_lstitem', 'tbl_lstgrp.grpid = tbl_lstitem.grpid');
        $this->db->order_by("type", "item"); 
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
  }
  function get_record($record_id)
  {
     $this->db->where('itemid',$record_id);        //we want the row whose ID matches the value were passing in
     $query = $this->db->get('tbl_lstitem');  //get the table and put it into an object named $query
     if ($query->num_rows() > 0) {
        $row = $query->row();                   //gets the first row of the resulting dataset.  In this case, only 1 row will ever be returned
        return $row;
     }
      //based on the key.
     return false;   //send the record back to the controller
  }

  public function item_count() 
  {
    return $this->db->get('tbl_lstitem')->num_rows();
  }

} //end class