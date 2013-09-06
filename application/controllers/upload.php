<?php
/*
 *Devnotes:
 *Resize (KB/MB) on upload - ref: http://apptha.com/blog/how-to-reduce-image-file-size-while-uploading-using-php-code/
 * ref: http://phpsense.com/2007/php-file-uploading/ <---multiple file upload structure etc
 *20130617 TODO: After insert, present image thumbnail and prompt for descr using News view type
*/
class Upload extends CI_Controller {

    const IMG_MAX_SIZE = '2000'; //2MB
    const IMG_MAX_WIDTH = '3000';
    const IMG_MAX_HEIGHT = '3000';
    const IMG_USE_THUMBS = false;
    const IMG_FILE_TYPES = 'gif|jpeg|jpg|png|ico';
    
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        //$this->load->library('sitefileutils');        
	}

	function index()
	{
 	   
    	$this->load->view('templates/header');
 	    $this->load->view('upload/uploadfrm', array('error' => ' ' ));
       	$this->load->view('templates/footer');

	}

	function imagefile()
	{  //ref: http://phpsense.com/2007/php-file-uploading/ <---multiple file upload structure etc
        
        $this->load->model('gallery_model' );
		$config['upload_path'] = './'.IMG_UPLOAD_PATH;
		$config['allowed_types'] = self::IMG_FILE_TYPES;
		$config['max_size']	   = self::IMG_MAX_SIZE; 
		$config['max_width']   = self::IMG_MAX_WIDTH;
		$config['max_height']  = self::IMG_MAX_HEIGHT;
		$this->load->library('upload', $config);
        $this->upload->initialize($config);
        
        if ($this->upload->do_multi_upload('Filedata'))
		{
            $flist = array();
            $first = true;
		    $destdir = './'.IMG_UPLOAD_PATH;
            //Load library once, initialze each call..below
            $this->load->library('image_lib', $config);
            $uplarr = $this->upload->get_multi_upload_data();
            for($i=0; $i<count($uplarr); $i++){
                // map, store and insert into tbl_images
                $recarr = $this->gallery_model->get_imagedata(0); // get full record structure
                $recarr['descr'] = $uplarr[$i]['file_name'];
                $recarr['fname'] = $uplarr[$i]['file_name'];
                $recarr['ftype'] = $uplarr[$i]['file_type'];
                $recarr['fpath'] = (substr($destdir,0,1) == './') ? substr($destdir,2) : $destdir;
                $recarr['fsize'] = $uplarr[$i]['file_size'];
                $recarr['ispic'] = $uplarr[$i]['is_image'];
                $recarr['fwide'] = $uplarr[$i]['image_width'];
                $recarr['fhite'] = $uplarr[$i]['image_height'];
                $recarr['fboth'] = $uplarr[$i]['image_size_str'];
                //Insert in database
                $message = $this->gallery_model->update_image(INSERT_REC, $recarr, '-initial upload');
                $fname = $recarr['fname'];
                $image = $fname;
                $pos = strpos($fname,'.');
                $fext = substr($fname,$pos,strlen($fname));
                $thumb = str_replace($fext,'_thumb'.$fext,$fname);
                if(self::IMG_USE_THUMBS)
                {
                    if($this->generateThumb($destdir.$fname, $first))
                    {
                       $image = $thumb;
                    }
                }
                $flist[] = array('fname' => $recarr['fname'],
                                 'image' => '<img src="'.$destdir.$fname.'" width="120px onClick="showImageDlg();" />',
                                 'fsize' => $recarr['fsize'],
                                 'fboth' => $recarr['fboth']
                                 );
                
                $first = false;
            }
            
            $data['uploadlist'] = $flist;
            
     	    $this->load->view('templates/header');
			$this->load->view('upload/uploadok', $data);
       	    $this->load->view('templates/footer');
		} else {
   			$error = array('error' => $this->upload->display_errors());
            $data['error'] = $error;
     	    $this->load->view('templates/header');
			$this->load->view('upload/uploadna', $data);
       	    $this->load->view('templates/footer');
		}
	}
    function generateThumb($file, $first)
    {
        
        $config['image_library']  = 'ImageMagick';  //http://www.imagemagick.org/script/index.php
        $config['library_path']   = '/usr/bin/convert';
        $config['source_image']	  = $file;
        $config['create_thumb']   = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']	      = 125;
        $config['height']	      = 100;
        /* INITIALIZE THE LIBRARY INSIDE THE LOOP */    
        $this->image_lib->initialize($config); 
        if (!$this->image_lib->image_process_imagemagick())
        {
            echo $this->image_lib->display_errors();
        }        
    }
}

/*
    function getFolderContents($dirpath)
    {
      $futil = new sitefileutils();
      $farry = $futil->dirToArray($dirpath);
      print_r($farry);
    }
    function cleanUpUserFiles()
    {
       $class = "gallery.php";
       include_once($class);
       $gallery = new Gallery();
       $futil = new sitefileutils();
       for($k = 0; $k <= 1; $k++) {
          $dirpath = ($k == 0) ? IMG_USER_PATH : IMG_UPLOAD_PATH;
          $farry = $futil->dirToArray($dirpath);
            
          for($j=0; $j<count($farry); $j++){
            $file = $dirpath.$farry[$j];
            if(!$gallery->imagfind($file)){
              if(unlink($file))
                $msg = 'Deleted: '.$file;
              else
                $msg = 'Could not delete: '.$file;
              echo '<p>'.$msg;
             }
          }
            
        }
    }
    function codeToMessage($code) 
    { 
        switch ($code) { 
            case UPLOAD_ERR_INI_SIZE: 
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                break; 
            case UPLOAD_ERR_FORM_SIZE: 
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
                break; 
            case UPLOAD_ERR_PARTIAL: 
                $message = "The uploaded file was only partially uploaded"; 
                break; 
            case UPLOAD_ERR_NO_FILE: 
                $message = "No file was uploaded"; 
                break; 
            case UPLOAD_ERR_NO_TMP_DIR: 
                $message = "Missing a temporary folder"; 
                break; 
            case UPLOAD_ERR_CANT_WRITE: 
                $message = "Failed to write file to disk"; 
                break; 
            case UPLOAD_ERR_EXTENSION: 
                $message = "File upload stopped by extension"; 
                break; 

            default: 
                $message = "Unknown upload error"; 
                break; 
        } 
        return $message; 
    }
*/