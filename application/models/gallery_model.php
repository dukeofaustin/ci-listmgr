<?php
  
  define("NO_DESCR",'nodescr');
  define("NO_TAGID",'notagid');
  //define("SHOW_ALL", 0);
  //define("SHOW_MINE", 1);
  
  class Gallery_model extends CI_Model {

  public function __construct()
  {
   $this->load->database();
   //$this->load->helper('date');
   $this->load->library('siteprocs');
  }

  public function get_itags($usrid = 0) {
     $query = array();
     $this->db->select('tbl_itags.tagid as tagid,tbl_itags.usrid as usrid,tbl_itags.descr as descr');
     $this->db->from('tbl_itags');
     if($usrid > 0)
        $query = $this->db->where('usrid',$usrid);
     $this->db->order_by('usrid'); 
     $this->db->order_by('descr'); 
     $query = $this->db->get();
     return $query->result();
  } //end 

  public function get_images($tagid = 0, $usrid = 0, $allow = 0) {
     $query = array();
     $this->db->select();
     $this->db->from('tbl_images');
     if($tagid > 0)
        $query = $this->db->where('tagid',$tagid);
     if($usrid > 0)
        $query = $this->db->where('usrid',$usrid);
     if($allow > 0)
        $query = $this->db->where('allow',$allow);
     $this->db->order_by('tagid'); 
     $this->db->order_by('usrid'); 
     $query = $this->db->get();
     return $query->result();
  } //end get_images
  /*
   * Function to retrieve stored image tag record
  */
  public function get_imagetag($tagid = 0) {
 
    //get image data from tbl_images table
    $tagarr = array('tagid' => 0,
                    'usrid' => 0,
                    'descr' => '');
    $this->db->from('tbl_itags');
    $this->db->where('tagid', $tagid);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
       $tagarr = $query->row_array();
    }
    return $tagarr;
   } //end get_imagetag
  /* Function to update an image tag record with tagid, descr based on mode and tagid
  */
  public function update_itag($mode, $tagid, $descr, $usrid = 0)
  {
    $rtn = false;
    if(isset($descr)){
      if(isset($usrid) && $usrid > 0)
         $data = array('tagid' => $tagid, 'descr' => $descr, 'usrid' => $usrid);
      else      
         $data = array('tagid' => $tagid, 'descr' => $descr);
      $this->db->where('tagid', $tagid);
      $query = $this->db->get('tbl_itags');
      if ($tagid > 0 && strlen($descr) > 0 && $query->num_rows() > 0) { // Update
         $this->db->where('tagid', $tagid);
         if($mode == UPDATE_REC){
            $rtn = $this->db->update('tbl_itags', $data); 
         } else if($mode == DELETE_REC) {
            $tables = array('tbl_itags');  
            $this->db->where('tagid', $tagid);
            $rtn = $this->db->delete($tables);
         }
      } else {
         $rtn = $this->db->insert('tbl_itags', $data);
      }
      $query->free_result(); // Release memory
      if (!$rtn){
        show_error('update_itag - tbl_itags error during '+$mode,500); 
      }
    }  
    return $rtn;
  }
  public function get_allimages($limit, $start, $filter = NO_DESCR)
  {
        $this->db->limit($limit, $start);
        $query = array();
        $this->db->select('tbl_itags.descr as type,tbl_itags.tagid as tid, tbl_images.imgid,tbl_images.descr as item, tbl_images.imgid as pid');
        $this->db->from('tbl_itags');
        $this->db->join('tbl_images', 'tbl_itags.tagid = tbl_images.tagid');
        $this->db->order_by('type', 'asc'); 
        $this->db->order_by('item', 'asc'); 
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
  }
  public function find_like($pstr)
  {
      $sqlstr = "SELECT tbl_itags.tagid AS tid,  tbl_itags.descr tdescr,  tbl_images.imgid AS pid,  tbl_images.descr AS pdescr
                 FROM   tbl_images
                 INNER JOIN (tbl_itags) ON  tbl_images.tagid =  tbl_itags.tagid 
                 WHERE  tbl_images.descr LIKE  '%".$this->db->escape_like_str($pstr)."%'
                 ORDER BY  tbl_itags.descr ,  tbl_images.descr;";
       $query = $this->db->query($sqlstr);
       if ($query->num_rows() > 0) {
          foreach ($query->result() as $row) {
              $data[] = $row;
          }
          return $data;
        }
        return false;
  }
  /*
   * Function to retrieve all stored image date
  */
  public function get_imagedata($imgid) {
 
    //get image data from tbl_images table
    $imgarr = array('imgid' => 0,
                    'tagid' => 0,
                    'usrid' => 0,
                    'allow' => 0,
                    'descr' => '<none>',
                    'fname' => '', 
                    'fpath' => '', 
                    'ftype' => '', 
                    'fsize' => '', 
                    'ispic' => '', 
                    'fwide' => '', 
                    'fhite' => '', 
                    'fboth' => '',
                    'udate' => '',
                    'audit' => '');
    if($imgid > 0)  {
        $this->db->from('tbl_images');
        $this->db->where('imgid', $imgid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           $imgarr = $query->row_array();
        }
    }
    return $imgarr;
   } //end get_imagedata
  /*
   * Function to update application user record
  */
   public function update_image($mode, $imgrec)
   {
     $rtn = false;
     $msg = 'update_image_pre';
     $err = 'update_image-Error';
     $usrid = 0;
     $imgid = $imgrec['imgid'];
     $tagid = $imgrec['tagid'];
     $allow = $imgrec['allow'];
     $descr = $imgrec['descr'];
     $fname = $imgrec['fname'];
     $fpath = $imgrec['fpath'];
     $ftype = $imgrec['ftype'];
     $fsize = $imgrec['fsize'];
     $ispic = $imgrec['ispic'];
     $fwide = $imgrec['fwide'];
     $fhite = $imgrec['fhite'];
     $fboth = $imgrec['fboth'];
     $udate = $imgrec['udate'];
     $audit = $imgrec['audit'];
     //$udate = gmt_to_local(now(), 'UM6', TRUE); //use date_helper function now() for config file settings (gmt)
     /*
     $udate = unix_to_human($udate, TRUE, 'us');
     $udate = str_replace('-','',$udate);
     $udate = $mode.'-'.$udate.'-id'.$usrid;
     */
     $sproc = new siteprocs();
        
     if(isset($usrid) && $usrid <= 0)
        $usrid = $sproc->getLoginId();
        
     $udate = $mode.'-'.$sproc->getDateTime().' id-'.$usrid;
     
     if(isset($fname) && isset($mode)){
        $data = array('imgid' => $imgid,
                      'tagid' => $tagid,
                      'usrid' => $usrid,
                      'allow' => $allow,
                      'descr' => $descr,
                      'fname' => $fname, 
                      'fpath' => $fpath, 
                      'ftype' => $ftype, 
                      'fsize' => $fsize, 
                      'ispic' => $ispic, 
                      'fwide' => $fwide, 
                      'fhite' => $fhite, 
                      'fboth' => $fboth,
                      'udate' => $udate);
       $this->db->from('tbl_images');
       $this->db->where('imgid', $imgid);
       $query = $this->db->get();
       if ($query->num_rows() > 0) { // Update
        
         $row = $query->row();
         $imgid = $row->imgid;
         
         if(strlen(trim($fname)) > 0 && strtolower(trim($row->fname)) != strtolower(trim($fname)))
           $data['fname'] = $fname;
           //$msg .= 'fname: '.$fname.' ';
         if(strlen(trim($descr)) > 0 && trim($row->descr) != trim($descr))
           $data['descr'] = $descr;
           //$msg .= 'descr: '.$descr.' ';
         if(trim($row->fpath) != trim($fpath))
           $data['fpath'] = $fpath;
           //$msg .= 'fpath: '.$fpath.' ';
         if(strlen(trim($ftype)) > 0 && trim($row->ftype) != trim($ftype))
           $data['ftype'] = $ftype;
           //$msg .= 'ftype: '.$ftype.' ';
         if(strlen(trim($fwide)) > 0 && trim($row->fwide) != trim($fwide))
           $data['fwide'] = $fwide;
           //$msg .= 'fwide: '.$fwide.' ';
         if(strlen(trim($fhite)) > 0 && trim($row->fhite) != trim($fhite))
           $data['fhite'] = $fhite;
           //$msg .= 'fhite: '.$fhite.' ';
         if(strlen(trim($udate)) > 0 && trim($row->udate) != trim($udate))
           $data['udate'] = $udate;
                   
         //$msg .= 'active: '.$active.' ';
           
         $this->db->where('imgid', $imgid);
         
         if($mode == UPDATE_REC){
            $rtn = $this->db->update('tbl_images', $data);
            if($rtn)
               $msg = "Successful-update";
            else
               $msg = "Error in update";
         } else if($mode == DELETE_REC) {
            $fpath = trim($row->fpath);
            // remove file from user image area:
            for($j=0; $j <= 1;$j++){
               $fname = trim($row->fname);
               $fname = $j == 0 ? $fname : $this->rtn_thumb_name($fname);
               $fpath = './images/user-pix/';
               $filename = $fpath.$fname;
               if(file_exists($filename))
               {
                  unlink($filename) or die('Failed deleting: ' . $filename);
               }
            }
            // delete image data from table:
            $tables = array('tbl_images'); //???? clean up other user stuff? 
            $this->db->where('imgid', $imgid);
            $rtn = $this->db->delete($tables);
            if($rtn)
               $msg = "Successful-deletion";
            else
               $msg = "Error in deletion";
         }
       } else {
            //if($mode == INSERT_REC){
            $rtn = $this->db->insert('tbl_images', $data);
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
  public function item_count() 
  {
    return $this->db->get('tbl_images')->num_rows();
  }
  public function rtn_thumb_name($file)
  {
    $fname = $file;
    $pos = strpos($fname,'.');
    $fext = substr($fname,$pos,strlen($fname));
    $thumb = str_replace($fext,'_thumb'.$fext,$fname);
    return $thumb;
  }

} //end class
