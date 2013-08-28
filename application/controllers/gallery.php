<?php
  define("TEST_MODE",true);
  
  class Gallery extends CI_Controller {
    
    const MAX_PDESC = 40; // grid pic description view length 
    const MAX_TDESC = 12; // grid tag descrtiption view length
    const IMG_WIDE = 640; // target width
    const IMG_HITE = 480; // target height
    const IMG_ISPIC = 2;  // image has been adjusted
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('gallery_model' );
        $this->load->library('session');  // for flash data
        $this->load->library('sitefileutils');  // for cleanup      
	    $this->load->helper(array('url','html'));
    }
    
    public function index()
    {
      	$data['title'] = 'Gallery';
        $query = $this->gallery_model->get_imagelist('tbl_images.descr != "<none>"'); //no filter
        $data['image_list'] = $query;
        $this->load->view('templates/header', $data);
	    $this->load->view('gallery/slideshow', $data);
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
    public function imagfind($fileandpath)
    {
       $rtn = false;
       $query = $this->gallery_model->get_imagename($fileandpath);
       if($query){
         foreach($query as $row) {
           $rtn = stripos($fileandpath,trim($row->filename)) !== false;
           break;
         }
       }
       return $rtn;
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
    function updrecord($imgid = 0, $file)
    {
      $rtn = false;
      $message = 'pre-updrecord';
      $usrid = $this->_get_userid();
      // default all fields with stored values
      $imgrec = $this->gallery_model->get_imagedata($imgid);
      $imgrec['ispic'] = 8;   
      
      if($imgrec && $imgid > 0 && file_exists($file))
      {
         $emode = UPDATE_REC; 
         $fsize = filesize($file);
         if($fsize > 1000)
           $fsize = (int)($fsize/1000);
         
         $finfo = $this->_get_fileinfo($file);
         $fpath = $finfo['dirname'];
         if(!$this->_string_endswith($fpath,'/'))
           $fpath .= '/';
         $fname = $finfo['basename'];
         
         $finfo = getimagesize($file);
         list($fwide, $fhite, $ftype, $fboth) = $finfo;
        
         $imgrec['usrid'] = $usrid;
        
         if(strlen($fname) > 0)
           $imgrec['fname'] = ($imgrec['fname'] == $fname) ? $imgrec['fname'] : $fname;
         if(strlen($fpath) > 0)
           $imgrec['fpath'] = ($imgrec['fpath'] == $fpath) ? $imgrec['fpath'] : $fpath;
         if(strlen($fboth) > 0)
           $imgrec['fboth'] = ($imgrec['fboth'] == $fboth) ? $imgrec['fboth'] : $fboth;
         if($fwide > 0)
           $imgrec['fwide'] = ($imgrec['fwide'] == $fwide) ? $imgrec['fwide'] : $fwide;
         if($fhite > 0)
           $imgrec['fhite'] = ($imgrec['fhite'] == $fhite) ? $imgrec['fhite'] : $fhite;
         if($fsize > 0)
           $imgrec['fsize'] = ($imgrec['fsize'] == $fsize) ? $imgrec['fsize'] : $fsize;
         if(strlen($fboth) > 0)
           $imgrec['fboth'] = ($imgrec['fboth'] == $fboth) ? $imgrec['fboth'] : $fboth;
         
         $message = $this->gallery_model->update_image($emode, $imgrec);
         $rtn = stripos($message,'error') === false ? true : false;
      }
      if(TEST_MODE) {
        $message .= ' usrid('.$usrid.') imgid('.$imgid.') ';
        $message .= ' emode('.$emode.') file('.$file.') ';
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
            $width = $imgrec['fwide'];
            $height = $imgrec['fhite'];
            $fpath = $imgrec['fpath'];
            $fboth = $imgrec['fboth'];
            $file  = $imgrec['fpath'].$imgrec['fname'];
            
            if((strlen($file) > 0) &&
               ((!isset($width) || $width == 0) ||
               (!isset($height) || $height == 0)))
            {
               $fileinfo = getimagesize($file);
               $width = $fileinfo[0];
               $height = $fileinfo[1];
               $fboth = 'width="'.$width.'" height="'.$height.'"';
            }
            
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
            echo '<table cols="3" id="frmtbl" border="0" cellpadding="1" cellspacing="1">';
            echo '<tr><td colspan="3">';
            echo '<class="validateTips">';
            echo '</td></tr><td colspan="3">';     
            $js = 'id="frm-dropdown" class="wijmo-wijdropdown"';
            $ddltitle = $incitem ? 'Assigned Group' : 'Selections';        
            echo form_label($ddltitle,'frm-dropdown');
            echo form_dropdown('frm-dropdown',$grps,$tagid,$js);
            echo '</td>';
            echo '<tr><td rowspan="2" colspan="2">';
            echo '<p><div id="img-box" />';
	        //$file1 = './'.IMG_USER_PATH.$imgrec['fname'];
            $file = $imgrec['fpath'].$imgrec['fname'];
	        echo '<img id="img-box" src="'.$file.'" width="180px" onClick="showImageDlg();" />';
            echo '</td>';
            echo '<td>';	    
            $ddltitle = $incitem ? 'Allow Access' : 'Selections';        
            echo form_label($ddltitle,'radiogrp1');
            $inpattr = array(
                    'name'     => 'radiobtn1',
                    'id'       => 'radiobtn1',
                    'value'    => '0',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => $allow == 0 ? true : false );
            //echo form_label('Public','radiobtn');
            echo form_radio($inpattr).' Public';            
            $inpattr = array(
                    'name'     => 'radiobtn1',
                    'id'       => 'radiobtn1',
                    'value'    => '1',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                    'checked'  => $allow == 1 ? true : false );
            echo form_radio($inpattr).' Private';
            $ddltitle = 'Adjust Image';
            echo form_label($ddltitle,'radiogrp2');
            $inpattr = array(
                    'name'     => 'radiobtn2',
                    'id'       => 'cropimg',
                    'value'    => 'C',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => false );
            echo form_radio($inpattr).' Crop';            
            /*$inpattr = array(
                    'name'     => 'radiobtn2',
                    'id'       => 'rsizimg',
                    'value'    => 'S',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => false );
            echo form_radio($inpattr).' Resize';*/
            $inpattr = array(
                    'name'     => 'radiobtn2',
                    'id'       => 'flipimg',
                    'value'    => 'F',
                    'class'    => 'wijmo-radio',
                    'style'    => 'float: left',
                   'checked'   => false );
            echo form_radio($inpattr).' Flip';
            echo '</td></tr>';
            echo '<tr><td colspan="3"></td></tr><tr><td colspan="3">';
            
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
            echo '<tr><td colspan="3">';
            echo '<input type="hidden" id="imgidnbr" value="'.$imgid.'"/>';
            echo '<input type="hidden" id="filename" value="'.$file.'"/>';
            echo '<input type="hidden" id="tagidnbr" value="'.$tagid.'"/>';
            echo '<input type="hidden" id="allow" value="'.$allow.'"/>';
            echo '<input type="hidden" id="editwhat" value="'.$which.'"/>';
            echo '<input type="hidden" id="imgwide" value="'.$width.'"/>';
            echo '<input type="hidden" id="imghite" value="'.$height.'"/>';
            
            echo '</td></tr><tr><td colspan="3">';
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
            if(TEST_MODE)
              echo '<p> file: '.$file;
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
    function _test_file_exists($file)
    { echo '<p> --------------------------';
      $msg = 'file_exists('.$file.')=';
      $msg .= file_exists($file) ? 'true' : 'false';
      echo '<p>'.$msg;
      $msg = 'file_exists(./'.$file.')=';
      $msg .= file_exists('./'.$file) ? 'true' : 'false';
      echo '<p>'.$msg;
      echo '<p> -------------------------- <p>';       
    }
    public function modimage()
    {
      $rtn = false;
      $rtnmsg = 'nada';
      $origfile = $this->input->post('ifile');
      $newfile = $origfile;
      $imgid = $this->input->post('imgid');
      $emode = $this->input->post('emode'); 
      $which = $this->input->post('which'); 
      $topx  = $this->input->post('topx'); 
      $topy  = $this->input->post('topy'); 
      $botx  = $this->input->post('botx'); 
      $boty  = $this->input->post('boty'); 
      $wide  = (int) $this->input->post('wide'); 
      $hite  = (int) $this->input->post('hite');
      
      //verify valid emode
      $validmodes = 'SCFLRV';
      if(stripos($validmodes,$emode) !== false)
      {
            if((!isset($wide) || $wide == 0) ||
               (!isset($hite) || $hite == 0))
           {
              $fileinfo = getimagesize($origfile);
              $wide = $fileinfo[0];
              $hite = $fileinfo[1];
           }
     
           //$this->_test_file_exists($origfile);
           if(stripos($origfile,IMG_USER_PATH) === false &&
              stripos($origfile,IMG_UPLOAD_PATH) === false)
           {
              $newfile = IMG_USER_PATH.$origfile;
           } elseif(stripos($origfile,IMG_UPLOAD_PATH) !== false &&
                     stripos($origfile,IMG_USER_PATH) === false)
           {
              $newfile = str_replace(IMG_UPLOAD_PATH, IMG_USER_PATH,$origfile);
           }
          
           $renfile = $this->_get_new_filename($origfile);
           
           if(file_exists($origfile) && !(file_exists($renfile))) {
               if(rename('./'.$origfile, './'.$renfile)){
                  $rtnmsg .= ' <p> rename worked! ';
                  if(file_exists('./'.$renfile)){
                     $origfile = $renfile;
                  }
               } else
                 $rtnmsg .= ' <p> rename failed! ';
           } else {
               $rtnmsg = 'file_exists('.$origfile.')= ';
               $rtnmsg .= (file_exists($origfile)) ? 'does exist' : 'does not exist';
               $rtnmsg .= '!(file_exists('.$renfile.')= ';
               $rtnmsg .= (!file_exists($renfile)) ? 'true-does NOT exist' : 'false-does exist';
           }
           $rtn = file_exists($renfile);

           if($rtn)
           {
             $rtn = false;  
             $quality = 85;
             if($emode == 'S') // resize -- do on upload -- rather than user choice
             {
                 $newsize = $this->_get_target_size($wide, $hite);
                 $newwide = $newsize['width'];
                 $newhite = $newsize['height'];
                 
                 if($this->_smart_resize_image($renfile,
                                               $newwide,
                                               $newhite,
                                                   true,
                                               $newfile,
                                                  false,
                                                  false,
                                                $quality)) {
                   $rtnmsg = 'successfully resized '.$newfile;
                   $rtn = true;
                } else {
                   $rtnmsg = 'error resizing '.$newfile;
                   $this->_logerror('error',$rtnmsg);
                 }
             } elseif($emode == 'C') { //crop
                 if($this->_crop_image($renfile,
                                       $newfile,
                                          $topx,
                                          $topy,
                                          $botx,
                                          $boty))
                 {
                     $rtnmsg = 'successfully cropped '. $newfile;
                     $rtn = true;
                 } else {
                     $rtnmsg = 'error croping '.$newfile;
                     $this->_logerror('error',$rtnmsg);
                 }
             } elseif($emode != 'X') {
                $degree = 0;
                switch($emode){
                  case('R'): //90 right
                     $degree = 90; 
                     break;
                  case('L'): //90 left
                     $degree = 270;
                     break;
                  case('V'): //flip vertically
                     $degree = 180;
                     break;
                  default:
                     break;
                }
                if($degree != 0) {
                  if($this->_rotateImage($origfile, $newfile, $degrees, $quality, $save)) {
                     $rtnmsg = 'successfully rotated '. $newfile;
                     $rtn = true;
                  } else {
                     $rtnmsg = 'error rotating '.$newfile;
                     $this->_logerror('error',$rtnmsg);
                  }
                 
                }
             }
             if($rtn){
                 //TODO: update fname and fpath with $newfile 20130812
                 if($this->_string_beginswith($newfile,'.'))
                    $newfile = substr($newfile,1);
                 $rtn = $this->updrecord($imgid, $newfile);
             }
           } else {
              $rtnmsg = 'invalid mode passed to method';
              $this->_logerror('error',$rtnmsg);
           }
      }
      echo $rtnmsg;
    }
    /**
     *
     */
    function _string_endswith($word, $end)
    {
       return (strpos($word, $end, strlen($word) - strlen($end)) !== false);
    }
    function _string_beginswith($word, $beg)
    {
       return (strpos($word, $beg, 0) !== false);
    }
    /**
     * getFileInfo
     * @param filepath
     * @return fileinfo array (dirname, basename, extension, filename)
     */
    function _get_fileinfo($file)
    {
        $rtn_parts = pathinfo($file);
        return $rtn_parts;
    }
    
    function _get_target_size($width, $height)
    {
        $rtn_size = array( 'width'  => $width,
                           'height' => $height );
        
        $wide = self::IMG_WIDE;
        $hite = self::IMG_HITE;
        $rtnwide = $width;
        $rtnhite = $height;
        if($width > 0 && $width > $wide){
           $pcent = ($wide / $width);
           $rtnwide = (int) ($pcent * $width);
        }
        if($height > 0 && $height > $hite){
           $pcent = ($hite / $height);
           $rtnhite = (int) ($pcent * $height);
        }
        $rtn_size['width'] = $rtnwide;
        $rtn_size['height'] = $rtnhite;
        return $rtn_size;
    }
    function _get_new_filename($pfile)
    {
        $rtn_name = $pfile;
        $pos = strrpos($pfile,'.');
        $ext = substr($pfile,$pos); 
        $dir = strrpos($pfile,'/');
        $dr  = substr($pfile,0,($dir+1)); 
        $arr = explode('/',$pfile);
        $fName = trim($arr[(count($arr) - 1)],$ext);
        $rName = '0__'.$fName;
        $rtn_name = str_replace($fName,$rName,$pfile);
        $exist = file_exists($rtn_name);
        if($exist)
        {
            $exist = false;
            $i = 0;
            while(!$exist){
                 if(file_exists($rtn_name)){
                      continue;
                 }else{
                      $exist = true;
                      $rtn_name = $dr.$rName.$i.$ext;
                 }
                 $i++;
            }
        }
        return $rtn_name;
    }
    /**
     * image resize function
     * @param  $file - file name to resize
     * @param  $width - new image width
     * @param  $height - new image height
     * @param  $output - name of the new file (include path if needed)
     * @param  $quality - enter 1-100 (100 is best quality) default is 100
     * @return boolean|resource
     * ref: https://github.com/Nimrod007/PHP_image_resize/blob/master/smart_resize_image.function.php
     */
    function _crop_image($origfile,
                         $newfile,
                         $x1  = 0, 
                         $y1  = 0,
                         $x2  = 0,
                         $y2  = 0
                         )
    {
       // include ImageManipulator class
       require_once('application/libraries/ImageManipulator.php');
       $rtn = false;

       $fileinfo = getimagesize($origfile); // returns array [0]=width,
                                        // [1]=height,
                                        // [2]=IMAGE_XXX (TYPE),
                                        //[3]=string height="nn" width="nn"
       if(($x1 > 0 && $y1 > 0 && $x2 > 0 && $y2 > 0) &&
          (($fileinfo[2] == IMAGETYPE_JPEG) ||
           ($fileinfo[2] == IMAGETYPE_PNG) ||
           ($fileinfo[2] == IMAGETYPE_GIF)))
       {
           $manipulator = new ImageManipulator($origfile);
           $width  = $manipulator->getWidth();
           $height = $manipulator->getHeight();
           $newImage = $manipulator->crop($x1, $y1, $x2, $y2);
           $manipulator->save($newfile);
           $rtn = file_exists($newfile);
        }
        return $rtn;
    }
    /*
    * ref: http://www.nodstrum.com/2006/12/09/image-manipulation-using-php/
    * rotateImage function
    * @param $file - image file path and file name 
    * @param $degrees - degrees to rotate (anticlockwise i think) by
    * @param $quality - image quality
    * @param $save - save or not (if you wanted to output to a temp to preview, if not used, leave as 1
    */
    function _rotateImage($origfile, $degrees, $quality, $save = true)
    {
        $finfo = $this->_get_fileinfo($origfile);

        // Open the original image.
        list($width, $height, $type, $attr) = getimagesize("$origfile");

        $fpath = $finfo['dirname'];
        if(!$this->_string_endswith($fpath,'/'))
           $fpath .= '/';
        $fname = $finfo['basename'];
        $ftype = $finfo['extension'];
        
        # Loading image to memory according to type
        switch ( $type ) {
          case IMAGETYPE_GIF:
            $image = imagecreatefromgif($origfile) or $this->_logerror('error','opening '.$origfile);
            break;
          case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($origfile) or $this->_logerror('error','opening '.$origfile);
            break;
          case IMAGETYPE_PNG:
            $image = imagecreatefrompng($origfile) or $this->_logerror('error','opening '.$origfile);
            break;
          default: return false;
        }
        
     
        // Resample the image.
        $tempImg = imagecreatetruecolor($width, $height) or $this->_logerror('error','creating temporary image for processing');
        imagecopyresized($tempImg, $origfile, 0, 0, 0, 0, $width, $height, $width, $height) or $this->_logerror('error','cannot resize '.$origfile);
     
        // Rotate the image.
        $rotate = imagerotate($origfile, $degrees, 0);
     
        // Save.
        if($save)  // always true unless you are returning a view before saving interactively
        {
            // Create the new file name.
            imagejpeg($rotate, $newfile, $quality) or $this->_logerror('error','saving image');
        }
     
        // Clean up.
        imagedestroy($origfile);
        rename($newfile, $origfile);
        imagedestroy($tempImg);
        return true;
    }
         
    /**
     * easy image resize function
     * @param  $file - file name to resize
     * @param  $width - new image width
     * @param  $height - new image height
     * @param  $proportional - keep image proportional, default is no
     * @param  $output - name of the new file (include path if needed)
     * @param  $delete_original - if true the original image will be deleted
     * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
     * @param  $quality - enter 1-100 (100 is best quality) default is 100
     * @return boolean|resource
     * ref: https://github.com/Nimrod007/PHP_image_resize/blob/master/smart_resize_image.function.php
     */
    function _smart_resize_image($file,
                                $width              = 0, 
                                $height             = 0, 
                                $proportional       = false, 
                                $output             = 'file', 
                                $delete_original    = true, 
                                $use_linux_commands = false,
                                $quality = 100) {
      
        if($height <= 0 && $width <= 0 ) return false;
           # Setting defaults and meta
           $info                         = getimagesize($file);
           $image                        = '';
           $final_width                  = 0;
           $final_height                 = 0;
           list($width_old, $height_old) = $info;
                
           # Calculating proportionality
           if ($proportional) {
              if      ($width  == 0)  $factor = $height/$height_old;
              elseif  ($height == 0)  $factor = $width/$width_old;
              else                    $factor = min( $width / $width_old, $height / $height_old );
              $final_width  = round( $width_old * $factor );
              $final_height = round( $height_old * $factor );
            }
        else {
           $final_width = ( $width <= 0 ) ? $width_old : $width;
           $final_height = ( $height <= 0 ) ? $height_old : $height;
        }
    
        # Loading image to memory according to type
        switch ( $info[2] ) {
          case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
          case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
          case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
          default: return false;
        }
        
        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
          $transparency = imagecolortransparent($image);
          if ($transparency >= 0) {
            $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
            $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
            imagefill($image_resized, 0, 0, $transparency);
            imagecolortransparent($image_resized, $transparency);
          }
          elseif ($info[2] == IMAGETYPE_PNG) {
            imagealphablending($image_resized, false);
            $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
            imagefill($image_resized, 0, 0, $color);
            imagesavealpha($image_resized, true);
          }
        }
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
        # Taking care of original, if needed
        if ( $delete_original ) {
          if ( $use_linux_commands ) exec('rm '.$file);
          else @unlink($file);
        }
        # Preparing a method of providing result
        switch ( strtolower($output) ) {
          case 'browser':
            $mime = image_type_to_mime_type($info[2]);
            header("Content-type: $mime");
            $output = NULL;
            break;
          case 'file':
            $output = $file;
             break;
          case 'return':
            return $image_resized;
            break;
          default:
          break;
       }
        
        # Writing image according to type to the output destination and image quality
       switch ( $info[2] ) {
          case IMAGETYPE_GIF:   imagegif($image_resized, $output, $quality);    break;
          case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
          case IMAGETYPE_PNG:   imagepng($image_resized, $output, $quality);    break;
          default: return false;
       }
        return true;
    }
    /*
     * Utility to clean up all files in IMG_USER_PATH/IMG_UPLOAD_PATH not stored in table
     */
    function cleanup()
    {
       $futil = new sitefileutils();
       $flist = array('cleanup utility');
       
       for($k = 0; $k <= 1; $k++) {
       
          $dirpath = ($k == 0) ? IMG_USER_PATH : IMG_UPLOAD_PATH;
          $farry = $futil->dirToArray($dirpath);
            
          for($j=0; $j<count($farry); $j++){
       
            $file = $dirpath.$farry[$j];
       
            if(!$this->imagfind($file)){
               if(unlink($file))
                  $msg = 'Deleted: '.$file;
               else
                  $msg = 'Could not delete: '.$file;
               $flist[] = $msg;
            }
            $data['result'] = $flist;
          }
        }
        $this->load->view('templates/header');
        $this->load->view('pages/utility',$data);
	    $this->load->view('templates/footer');
    }
    /*
     * logerror function
     * @param $which - message level must be 'error', 'debug' or 'info' per CI (see link below)
     * @param $errmsg - error to log
     * ref: http://ellislab.com/codeigniter/user-guide/general/errors.html 
     */
    function _logerror($which,$errmsg)
    {
        $thisclass = basename(__FILE__, '.php');
        $logmsg = $thisclass.'-'.$errmsg;
        log_error($thisclass,$logmsg);
    }
} //end class
