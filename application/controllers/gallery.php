<?php
  define("TEST_MODE",true);
  
  class Gallery extends CI_Controller {
    
    const MAX_PDESC = 40;
    const MAX_TDESC = 12;
 
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('gallery_model' );
        $this->load->library('session');  // for flash data
	    $this->load->helper(array('url','html'));
    }
    
    public function index()
    {
      	$data['title'] = 'Gallery-Index';
	    $this->load->view('templates/header', $data);
	    $data['ipath'] = base_url().'images/user-pix/';
	    if (file_exists($data['ipath'].'AnArtistsJourney.jpg'))
 	       $fexist = "Yes";
	    else
	       $fexist = "Nah";
	    $data['fexist'] = $fexist;
	    $this->load->view('gallery/index', $data);
	    $this->load->view('templates/footer');
    }
    public function images()
    {
         $this->load->library('pagination');
         $this->load->library('table');
         $data['listtype'] = 'Pictures';
         $ajaxcall = false;
         /* AJAX check  */
         if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            $ajaxcall = true;
         }
         $config['base_url'] = base_url().'index.php?gallery/images';
         $config['total_rows'] =  $this->gallery_model->item_count();
         $config['per_page'] = 15;
         $config['num_links'] = 10; // number of numeric pages shown 
         $config['uri_segment'] = 3;
         $config['full_tag_open'] = '<div class="pagination-digg">';
         $config['full_tag_close'] = '</div>';
         $this->pagination->initialize($config);
         $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
         // generate table data
         $query = $this->gallery_model->get_allimages($config["per_page"], $page);
         $tmplate = array ('table_open'  => '<table id="gridtable" border="1" cellpadding="1" cellspacing="1" class="ui-widget-content">' );
         $this->table->set_template($tmplate);
         $this->table->set_empty('&nbsp;');
         $tbl_heading = array(
             '0' => array('data' => 'Tagged',           'style' => 'width: 20%;'),
             '1' => array('data' => 'Description', 'style' => 'width: 75%;'),
             '2' => array('data' => 'Edit', 'style' => 'text-align: center; width: 5%;'));
         $this->table->set_heading($tbl_heading);
         
         foreach($query as $row) {
            $say = $this->_trim_descr($row->type, self::MAX_TDESC);
            $txt = $say.'</td><td>';
            $say = $this->_trim_descr($row->item, self::MAX_PDESC);
            $txt .= $say.'</td>';
            $act = '<td><a class="editthis" href="#" onclick="javascript:getImageForm('.$row->tid.','.$row->pid.',\''.$row->item.'\')">  <img src="'.base_url().'images/edit.gif" width="12" height="12"/>         </a>';
            $this->table->add_row($txt.$act);
         }
         if($ajaxcall){
            $this->load->view('gallery/imagepage',$data);
         } else {
            $this->load->view('templates/header', $data);
            $this->load->view('gallery/imagegrid',$data);
            $this->load->view('templates/footer');
         }
    }
    public function itemfind()
    {
         $reply = '';
         $this->load->library('table');
         $strtofind = $this->input->post('descr');
         $ajaxcall = false;
         $data['title'] = 'Search with Wildcard';
         /* AJAX check  */
         if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            $ajaxcall = true;
         }
         if($ajaxcall){
            if(isset($strtofind) && strlen($strtofind) > 1) {
               $query = $this->gallery_model->find_like($strtofind);
               if($query) {
                  echo '<script src="'.base_url().'assets/js/imagegrd1.js" type="text/javascript"></script>';
                  $tmplate = array ('table_open'  => '<table id="gridtable" border="1" cellpadding="1" cellspacing="1" class="ui-widget ui-widget-content">' );
                  $this->table->set_template($tmplate);
                  $this->table->set_empty('&nbsp;');
                 if(TEST_MODE) {
                     $tbl_heading = array(
                          '0' => array('data' => 'Id', 'style' => 'text-align: center', 'style' => 'width: 10'),
                          '1' => array('data' => 'Tag', 'style' => 'width: 100px', 'style' => 'white-space: nowrap;'),
                          '2' => array('data' => 'Id', 'style' => 'text-align: center', 'style' => 'width: 10'),
                          '3' => array('data' => 'Description', 'style' => 'width: 200px', 'style' => 'white-space: nowrap;'),
                          '4' => array('data' => 'Edit', 'style' => 'text-align: center'));
                     $this->table->set_heading($tbl_heading);
                     foreach($query as $row) {
                        $say = $this->_trim_descr($row->tdescr, self::MAX_TDESC);
                        $txt = $row->tid.'</td><td>'.$say.'</td>';
                        $say = $this->_trim_descr($row->pdescr, self::MAX_PDESC);
                        $txt .= '<td>'.$row->pid.'</td><td>'.$say.'</td>';
                        $act = '<td><a class="editthis" href="#" onclick="javascript:getImageForm('.$row->tid.','.$row->pid.',\''.$row->pdescr.'\')">        <img src="'.base_url().'images/edit.gif" width="12" height="12"/>         </a>';
                        $this->table->add_row($txt.$act);
                     }
                  } else {
                     $tbl_heading = array(
                      '0' => array('data' => 'Group',           'style' => 'width: 20%;'),
                      '1' => array('data' => 'Item Description', 'style' => 'width: 75%;'),
                      '2' => array('data' => 'Edit', 'style' => 'text-align: center; width: 5%;'));
                      $this->table->set_heading($tbl_heading);
                      foreach($query as $row) {
                         $say = $this->_trim_descr($row->tdescr, self::MAX_TDESC);
                         $txt = $say.'</td>';
                         $say = $this->_trim_descr($row->pdescr, self::MAX_PDESC);                         
                         $txt .= '<td>'.$say.'</td>';
                         $act = '<td><a class="editthis" href="#" onclick="javascript:getImageForm('.$row->tid.','.$row->pid.',\''.$row->pdescr.'\')">        <img src="'.base_url().'images/edit.gif" width="12" height="12"/></a>';
                         $this->table->add_row($txt.$act);
                      }
                   }
                    echo $this->table->generate();
               }
               else {
                 $reply = 'nothing found';
               }
            } else {
               $reply = 'nothing sent to find';
            }
            if(strlen($reply) > 0)
               echo $reply;
         } else {
            $this->load->view('templates/header', $data);
            $this->load->view('gallery/itemfind',$data);
            $this->load->view('templates/footer');
         }
    }
    function updimage()
    {
      $message = 'pre-updimage';
      $emode = $this->input->post('emode'); //? $this->input->post('emode') : '';
      $imgid = $this->input->post('imgid'); //? $this->input->post('imgid') : 0;
      $usrid = $this->_get_userid();
      // default all fields with stored values
      $imgrec = $this->gallery_model->get_imagedata($imgid);

      if(isset($emode) && isset($imgid))
      {
         
        $tagid = $this->input->post('tagid') ? $this->input->post('tagid') : 0;
        $descr = $this->input->post('descr') ? $this->input->post('descr') : '';
        $allow = $this->input->post('allow'); //? $this->input->post('allow') : 0;
        
        $imgrec['usrid'] = $usrid;
        $imgrec['tagid'] = ($imgrec['tagid'] == $tagid) ? $imgrec['tagid'] : $tagid;
        $imgrec['descr'] = ($imgrec['descr'] == $descr) ? $imgrec['descr'] : $descr;
        $imgrec['allow'] = ($imgrec['allow'] == $allow) ? $imgrec['allow'] : $allow;
        $imgrec['ispic'] = 0;
        $message = 'pre-update_image()';
        $message = $this->gallery_model->update_image($emode, $imgrec);
      }
      if(TEST_MODE) {
        $message .= ' usrid('.$usrid.') imgid('.$imgid.') tagid('.$tagid.')';
        $message .= ' emode('.$emode.') allow('.$allow.') ';
        $message .= ' descr('.$descr.') ';
      }
      echo $message;
    }
    public function upditag()  
    {
        $rtn = 'pre-update';
        $mode = $this->input->post('mode');
        if(isset($mode)) {
          $usrid = $this->_get_userid();
          $tagid = $this->input->post('tagid');
          $descr = $this->input->post('descr');
          
          $rtn = 'mode '+$mode+' tagid '+$tagid+' imgid '+$imgid+' descr'+$descr;
          if(!$this->gallery_model->update_itag($mode,$tagid,$descr,$usrid)){
            $rtn = 'Error updating user selected list';
          }
        }
        echo $rtn;
    }
    public function findform()
    {
        $this->load->helper('form');
        echo '<script src="'.base_url().'assets/js/imagefind.js" type="text/javascript"></script>';
        echo '<div class="gridcolumn">';
        echo '<div id="fndform" class="ui-widget-content">';
        $frmtitle = 'Search for Image LIKE';
        //echo form_fieldset('<b><style="text-align:center;">'.$frmtitle.'</style></b>');
        echo form_fieldset('<b>');
        echo "<label for='searchstr'>".$frmtitle."</label>";
        echo "<input type='text' name='searchstr' id='searchstr' value='' maxlength='80' size='80' style='width:80%;'/>";
        echo '<br/>';
        
        $btnattr = array( 'name' => 'findbtn',
                          'id' => 'findbtn',
                          'class' => 'findbtn',
                          'content' => 'Search');
        echo form_button($btnattr);        
        
        $btnattr = array( 'name' => 'nonebtn',
                          'id' => 'nonebtn',
                          'class' => 'nonebtn',
                          'content' => 'Cancel');
        echo form_button($btnattr);        
        echo form_fieldset_close();
        
        $formattr = "</div></div>";
        echo $formattr;
      return;
    }
    public function getform()
    {
        $this->load->helper('form');
                
        $which = GROUP_STR;
        $descr = '';
        $tagid = 0;
        $imgid = 0;
        
        $which = $this->input->post('which');
        $tagid = $this->input->post('tagid');
        
        if($which == GROUP_STR){
            $this->_get_tag_frm();
            return;
        }
        
        $imgid = $this->input->post('imgid');
        $descr = $this->input->post('descr');
        
        $imgrec = $this->gallery_model->get_imagedata($imgid);
        if($imgrec) {
            
            $allow = $imgrec['allow'];
            $incitem = (isset($which) &&  strtolower($which) == ITEM_STR) ? true : false; // include item or group only update
            
            if($incitem) {
               $descr = (strlen($descr) > 1 || $imgid > 0) ? $descr : ADD_NEW_REC;
               $grps = $this->_get_alltags(false);
            } else {
               $grps = $this->_get_alltags();
            }
            echo '<script src="'.base_url().'assets/js/imageedit.js" type="text/javascript"></script>';
            echo '<div class="gridcolumn">';
            echo '<div id="itmform" class="ui-widget-content">';
            $frmtitle = $incitem ? 'Image Information' : 'Group Information';        
            echo form_fieldset('<b><style="text-align:center;">'.$frmtitle.'</style></b>');
            echo '<table cols="2" id="frmtbl" border="0" cellpadding="1" cellspacing="1">';
            echo '<tr><td colspan="2">';
            echo '</td></tr><tr>';
            echo '<p class="validateTips"></p>';
            echo '</td></tr><td>';     
            $js = 'id="frm-dropdown" class="wijmo-wijdropdown"';
            $ddltitle = $incitem ? 'Assigned Group' : 'Selections';        
            echo form_label($ddltitle,'frm-dropdown');
            echo form_dropdown('frm-dropdown',$grps,$tagid,$js);
            echo '</td><td rowspan="2">';	    
            $ddltitle = $incitem ? 'Allow Access' : 'Selections';        
            echo form_label($ddltitle,'radiogrp');
            $inpattr = array(
                    'name'     => 'radiobtn',
                    'id'       => 'radiobtn',
                    'value'    => '0',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => $allow == 0 ? TRUE : FALSE );
            //echo form_label('Public','radiobtn');
            echo form_radio($inpattr).' Public';            
            $inpattr = array(
                    'name'     => 'radiobtn',
                    'id'       => 'radiobtn',
                    'value'    => '1',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                    'checked'  => $allow == 1 ? TRUE : FALSE );
            //echo form_label('Private','radiobtn');
            echo form_radio($inpattr).' Private';
            echo '</td></tr><tr><td colspan="2">';
            echo '<p><div id="img-box" />';
	        $file = './images/user-pix/'.$imgrec['fname'];
	        echo '<img id="img-box" src="'.$file.'" width="140px" onClick="showImageDlg();" />';
            echo '</td></tr><tr><td colspan="2">';	    
            $ddltitle = $incitem ? 'Image Description' : 'Group Description';        
            echo form_label($ddltitle,'descr');
            $inpattr = array(
                  'name'        => 'descr',
                  'id'          => 'descr',
                  'value'       => $imgrec['descr'],
                  'cols'        => '50',
                  'rows'        => '3',
                  'style'       => 'width:95%',
                  'class'       => 'text ui-widget-content ui-corner-all'
                );
            echo form_textarea($inpattr);
	    
            //echo '<textarea id="descr" rows="3" cols="50" class="text ui-widget-content ui-corner-all" value="'.$descr.'"/>';
            echo '</td></tr>';
            echo '<tr><td colspan="2">';
            echo '<input type="hidden" id="imgidnbr" value="'.$imgid.'"/>';
            echo '<input type="hidden" id="filename" value="'.$file.'"/>';
            echo '<input type="hidden" id="tagidnbr" value="'.$tagid.'"/>';
            echo '<input type="hidden" id="allow" value="'.$allow.'"/>';
            echo '<input type="hidden" id="editwhat" value="'.$which.'"/>';
            echo '</td></tr><tr><td colspan="2">';
            $btnattr = array( 'name' => 'delete',
                              'id' => 'frmdel',
                              'class' => 'frmdel',
                              'content' => 'Delete');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'update',
                              'id' => 'frmupd',
                              'class' => 'frmupd',
                              'content' => 'Update');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'frmout',
                              'id' => 'frmout',
                              'class' => 'frmout',
                              'content' => 'Cancel');
            echo form_button($btnattr);
            echo '</td></tr></table>';
            $formattr = "</div></div>";
            echo form_fieldset_close($formattr);
        }
      return;
    }
    public function _get_tag_frm()
    {
        //$this->load->helper('form');
                
        $which = GROUP_STR;
        $descr = '';
        $tagid = 0;
        
        $which = $this->input->post('which');
        $tagid = $this->input->post('tagid');
        $descr = $this->input->post('descr');
        
        $tagrec = $this->gallery_model->get_imagetag($tagid);
        if($tagrec) {
            $descr = (strlen($descr) > 1 || $tagid > 0) ? $descr : ADD_NEW_REC;
            $grps = $this->_get_alltags(true);
            echo '<script src="'.base_url().'assets/js/imageedit.js" type="text/javascript"></script>';
            echo '<div class="gridcolumn">';
            echo '<div id="itmform" class="ui-widget-content">';
                
            $frmtitle = 'Image Categories';        
            echo form_fieldset('<b><style="text-align:center;">'.$frmtitle.'</style></b>');
            echo '<p class="validateTips"></p>';
            $js = 'id="frm-dropdown" class="wijmo-wijdropdown"';
            $ddltitle = 'Selections';        
            echo form_label($ddltitle,'frm-dropdown');
            echo form_dropdown('frm-dropdown',$grps,$tagid,$js);
            echo '<br/>';
            //allow (access)
            echo '<p>';
            //file path
            echo form_label('Description ','descr');
            $inpattr = array(
                    'name'        => 'descr',
                    'id'          => 'descr',
                    'value'       => $tagrec['descr'],
                    'maxlength'   => '128',
                    'size'        => '80',
                    'style'       => 'width:95%'   );
            echo form_input($inpattr);

            echo '<input type="hidden" id="tagidnbr" value="'.$tagid.'"/>';
            echo '<input type="hidden" id="editwhat" value="'.$which.'"/>';
            echo '<br />';
    
            $btnattr = array( 'name' => 'delete',
                              'id' => 'frmdel',
                              'class' => 'frmdel',
                              'content' => 'Delete');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'update',
                              'id' => 'frmupd',
                              'class' => 'frmupd',
                              'content' => 'Update');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'frmout',
                              'id' => 'frmout',
                              'class' => 'frmout',
                              'content' => 'Cancel');
            echo form_button($btnattr);        
            echo form_fieldset_close();
            $formattr = "</div></div>";
            echo $formattr;
        }
      return;
    }
    function _get_alltags($addnew=true)
    {
        if($addnew)
    	   $grps = array(0 => ADD_NEW_REC);
        else
    	   $grps = array(0 => '--Select--'); // force onchange event
           
        $query = $this->gallery_model->get_itags();
    	foreach($query as $grp){
    	  $grps[$grp->tagid] = $grp->descr;
    	}
    	return $grps;
    }
    function _get_userid()
    {
       $usrid = 0;
       $sarr = $this->session->all_userdata();
 	   if (isset($sarr['usrid']) && $sarr['usrid'] != '' && $sarr['usrid'] != '0') 
          $usrid = $sarr['usrid'];
       return $usrid;
    }
    /* if load->helper('text'), can use ellipsize()
     */
    function _trim_descr($what, $howmuch)
    {
        $rtn = strlen($what) > $howmuch ? substr($what,0, $howmuch).'...' : $what;
        return $rtn;
    }
} //end class
/*
 *    function editform()
    {
        //todo: put in User controller/view -- needed for dev test of user_model->update_user
        $uname = '';
        $email = '';
        //$usrid = 0;
        $imgid = $this->input->post('imgid');
        $tagid = $this->input->post('tagid');
        
        $grps = $this->_get_alltags(false);
        $data['groups'] = $grps;
        $sarr = $this->session->all_userdata();
        if (isset($sarr['uname']) && $sarr['uname'] != "") {
            $uname = $sarr['uname'];
        }
        if (isset($sarr['email']) && $sarr['email'] != "") {
            $email = $sarr['email'];
        }
        //var_dump($sarr);
        //var_dump($grps);
        print_r('sarr('.$sarr.') uname('.$uname.') email('.$email.')');
        print_r('imgid('.$imgid.') tagid('.$tagid.') grps['.$grps.']');
        if($uname != '' && $email != '' ) {
            $data['image'] = $this->gallery_model->get_imagedata($imgid);
            $data['title'] = 'Edit Image Data';
            //var_dump("data ".$data);
            //$this->load->view('templates/header', $data);
            $this->load->view('gallery/editimage', $data);
            //$this->load->view('templates/footer');
        }
    }
       public function getform()
    {
        $this->load->helper('form');
                
        $which = GROUP_STR;
        $descr = '';
        $tagid = 0;
        $imgid = 0;
        
        $which = $this->input->post('which');
        $tagid = $this->input->post('tagid');
        
        if($which == GROUP_STR){
            $this->_get_tag_frm();
            return;
        }
        
        $imgid = $this->input->post('imgid');
        $descr = $this->input->post('descr');
        
        $imgrec = $this->gallery_model->get_imagedata($imgid);
        if($imgrec) {
            
            $allow = $imgrec['allow'];
            $incitem = (isset($which) &&  strtolower($which) == ITEM_STR) ? true : false; // include item or group only update
            
            if($incitem) {
               $descr = (strlen($descr) > 1 || $imgid > 0) ? $descr : ADD_NEW_REC;
               $grps = $this->_get_alltags(false);
            } else {
               $grps = $this->_get_alltags();
            }
            echo '<script src="'.base_url().'assets/js/imageedit.js" type="text/javascript"></script>';
            echo '<div class="gridcolumn">';
            echo '<div id="itmform" class="ui-widget-content">';
                
            $frmtitle = $incitem ? 'Image Information' : 'Group Information';        
            echo form_fieldset('<b><style="text-align:center;">'.$frmtitle.'</style></b>');
            echo '<p class="validateTips"></p>';
            $js = 'id="frm-dropdown" class="wijmo-wijdropdown"';
            $ddltitle = $incitem ? 'Assigned Group' : 'Selections';        
            echo form_label($ddltitle,'frm-dropdown');
            echo form_dropdown('frm-dropdown',$grps,$tagid,$js);
            echo '<br/>';
            //allow (access)
            $ddltitle = $incitem ? 'Allow Access' : 'Selections';        
            echo form_label($ddltitle,'radiogrp');
            $inpattr = array(
                    'name'     => 'radiobtn',
                    'id'       => 'radiobtn',
                    'value'    => '0',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => $allow == 0 ? TRUE : FALSE );
            //echo form_label('Public','radiobtn');
            echo form_radio($inpattr).' Public';            
            $inpattr = array(
                    'name'     => 'radiobtn',
                    'id'       => 'radiobtn',
                    'value'    => '1',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                    'checked'  => $allow == 1 ? TRUE : FALSE );
            //echo form_label('Private','radiobtn');
            echo form_radio($inpattr).' Private';
            echo '<p><div id="img-box" />';
            //file path
            echo form_label('File Path','fpath');
            $inpattr = array(
                    'name'        => 'fpath',
                    'id'          => 'fpath',
                    'value'       => $imgrec['fpath'],
                    'maxlength'   => '128',
                    'size'        => '80',
                    'style'       => 'width:95%'   );
            echo form_input($inpattr);
            //file name
            echo form_label('File Name','fname');
            $inpattr = array(
                    'name'        => 'fname',
                    'id'          => 'fname',
                    'value'       => $imgrec['fname'],
                    'maxlength'   => '128',
                    'size'        => '80',
                    'style'       => 'width:95%'   );
            echo form_input($inpattr);
            //file description
            $ddltitle = $incitem ? 'Image Description' : 'Group Description';        
            echo form_label($ddltitle,'descr');
            $inpattr = array(
                  'name'        => 'descr',
                  'id'          => 'descr',
                  'value'       => $imgrec['descr'],
                  'cols'        => '50',
                  'rows'        => '3',
                  'style'       => 'width:95%',
                  'class'       => 'text ui-widget-content ui-corner-all'
                );
            echo form_textarea($inpattr);
            //echo '<textarea id="descr" rows="3" cols="50" class="text ui-widget-content ui-corner-all" value="'.$descr.'"/>';
    
            echo '<input type="hidden" id="imgidnbr" value="'.$imgid.'"/>';
            echo '<input type="hidden" id="tagidnbr" value="'.$tagid.'"/>';
            echo '<input type="hidden" id="allow" value="'.$allow.'"/>';
            echo '<input type="hidden" id="editwhat" value="'.$which.'"/>';
            echo '<br />';
                
            $btnattr = array( 'name' => 'show',
                              'id' => 'frmsho',
                              'class' => 'frmsho',
                              'content' => 'Show');
            echo form_button($btnattr);
            
            $btnattr = array( 'name' => 'delete',
                              'id' => 'frmdel',
                              'class' => 'frmdel',
                              'content' => 'Delete');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'update',
                              'id' => 'frmupd',
                              'class' => 'frmupd',
                              'content' => 'Update');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'frmout',
                              'id' => 'frmout',
                              'class' => 'frmout',
                              'content' => 'Cancel');
            echo form_button($btnattr);        
            echo form_fieldset_close();
            $formattr = "</div></div>";
            echo $formattr;
        }
      return;
    }
    public function _get_tag_frm()
    {
        //$this->load->helper('form');
                
        $which = GROUP_STR;
        $descr = '';
        $tagid = 0;
        
        $which = $this->input->post('which');
        $tagid = $this->input->post('tagid');
        $descr = $this->input->post('descr');
        
        $tagrec = $this->gallery_model->get_imagetag($tagid);
        if($tagrec) {
            $descr = (strlen($descr) > 1 || $tagid > 0) ? $descr : ADD_NEW_REC;
            $grps = $this->_get_alltags(true);
            echo '<script src="'.base_url().'assets/js/imageedit.js" type="text/javascript"></script>';
            echo '<div class="gridcolumn">';
            echo '<div id="itmform" class="ui-widget-content">';
                
            $frmtitle = 'Image Categories';        
            echo form_fieldset('<b><style="text-align:center;">'.$frmtitle.'</style></b>');
            echo '<p class="validateTips"></p>';
            $js = 'id="frm-dropdown" class="wijmo-wijdropdown"';
            $ddltitle = 'Selections';        
            echo form_label($ddltitle,'frm-dropdown');
            echo form_dropdown('frm-dropdown',$grps,$tagid,$js);
            echo '<br/>';
            //allow (access)
            echo '<p>';
            //file path
            echo form_label('Description ','descr');
            $inpattr = array(
                    'name'        => 'descr',
                    'id'          => 'descr',
                    'value'       => $tagrec['descr'],
                    'maxlength'   => '128',
                    'size'        => '80',
                    'style'       => 'width:95%'   );
            echo form_input($inpattr);

            echo '<input type="hidden" id="tagidnbr" value="'.$tagid.'"/>';
            echo '<input type="hidden" id="editwhat" value="'.$which.'"/>';
            echo '<br />';
    
            $btnattr = array( 'name' => 'delete',
                              'id' => 'frmdel',
                              'class' => 'frmdel',
                              'content' => 'Delete');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'update',
                              'id' => 'frmupd',
                              'class' => 'frmupd',
                              'content' => 'Update');
            echo form_button($btnattr);        
            
            $btnattr = array( 'name' => 'frmout',
                              'id' => 'frmout',
                              'class' => 'frmout',
                              'content' => 'Cancel');
            echo form_button($btnattr);        
            echo form_fieldset_close();
            $formattr = "</div></div>";
            echo $formattr;
        }
      return;
    }
*/