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
    
    public function index1()
    {
      	$data['title'] = 'Gallery-Index';
	    $this->load->view('templates/header', $data);
	    $data['ipath'] = base_url().IMG_USER_PATH;
	    if (file_exists($data['ipath'].'AnArtistsJourney.jpg'))
 	       $fexist = "Yes";
	    else
	       $fexist = "Nah";
	    $data['fexist'] = $fexist;
	    $this->load->view('gallery/index', $data);
	    $this->load->view('templates/footer');
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
    public function updsize()
    {
      $origfile = $this->input->post('ifile');  
      $imgid = $this->input->post('imgid');
      $emode = $this->input->post('emode'); 
      $which = $this->input->post('which'); 
      $topx  = $this->input->post('topx'); 
      $topy  = $this->input->post('topy'); 
      $botx  = $this->input->post('botx'); 
      $boty  = $this->input->post('boty'); 
      $wide  = $this->input->post('wide'); 
      $hite  = $this->input->post('hite');
      $newfile = str_replace(IMG_USER_PATH,IMG_UPLOAD_PATH,$origfile);
      /*      
      //$test = 'ifile='.$ifile.' newfile='.$newfile;
      //$test .= ' replace('.IMG_USER_PATH.','.IMG_UPLOAD_PATH.','.$ifile.')';
      //die($test);
      //_smart_resize_image creates copy of original but to new dimensions
      if($this->_smart_resize_image( $ifile,
                                      $wide,
                                      $hite,
                                       true,
                                   $newfile,
                                      false,
                                       true,
                                        100))
                                        */
      if($this->_crop_image($origfile,
                            $newfile,
                            $topx,
                            $topy,
                            $botx,
                            $boty))
      {
        echo $newfile.' created';
      } else {
        echo 'error creating '.$newfile;
      }
      /*
     * @param  $file - file name to resize
     * @param  $width - new image width
     * @param  $height - new image height
     * @param  $proportional - keep image proportional, default is no
     * @param  $output - name of the new file (include path if needed)
     * @param  $delete_original - if true the original image will be deleted
     * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
     * @param  $quality - enter 1-100 (100 is best quality) default is 100
      */
      /*
      $msg = 'ifile['.$ifile.'] ';
      $msg .= 'imgid['.$imgid.'] ';
      $msg .= 'emode['.$emode.'] ';
      $msg .= 'which['.$which.'] ';
      $msg .= 'topx['.$topx.'] ';
      $msg .= 'topy['.$topy.'] ';  
      $msg .= 'botx['.$botx.'] ';  
      $msg .= 'boty['.$boty.'] ';  
      $msg .= 'wide['.$wide.'] ';
      $msg .= 'hite['.$hite.']';
      echo $msg;
      */
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
	        //$file1 = './'.IMG_USER_PATH.$imgrec['fname'];
            $file = $imgrec['fpath'].$imgrec['fname'];
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
       //$validExtensions = array('.jpg', '.jpeg', '.gif', '.png');
       // get extension of the uploaded file
       //$fileExtension = strrchr($origfile, ".");
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
                //$centreX = round($width / 2);
                //$centreY = round($height / 2);
                // our dimensions will be 200x130
                //$x1 = $centreX - 100; // 200 / 2
                //$y1 = $centreY - 65; // 130 / 2
                //$x2 = $centreX + 100; // 200 / 2
                //$y2 = $centreY + 65; // 130 / 2
                // center cropping to 200x130
                // saving file to uploads folder
                echo 'Done ...';
        }
        return $rtn;
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
      
     if ( $height <= 0 && $width <= 0 ) return false;
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