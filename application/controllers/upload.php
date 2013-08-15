<?php
/*
 *Devnotes:
 *Resize (KB/MB) on upload: http://apptha.com/blog/how-to-reduce-image-file-size-while-uploading-using-php-code/
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
        $this->load->library('sitefileutils');        
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
                //echo $uplarr[$i]['file_name'];
                // map, store and insert into tbl_images
                $recarr = $this->gallery_model->get_imagedata(0); // get full record structure
                $recarr['fname'] = $uplarr[$i]['file_name'];
                $recarr['ftype'] = $uplarr[$i]['file_type'];
                $recarr['fpath'] = $config['upload_path']; //$uplarr[$i]['full_path'];
                $recarr['fsize'] = $uplarr[$i]['file_size'];
                $recarr['ispic'] = $uplarr[$i]['is_image'];
                $recarr['fwide'] = $uplarr[$i]['image_width'];
                $recarr['fhite'] = $uplarr[$i]['image_height'];
                $recarr['fboth'] = $uplarr[$i]['image_size_str'];
                //Insert in database
                $message = $this->gallery_model->update_image(INSERT_REC, $recarr);
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
}
/*
	function xdo_upload()
	{
		$config['upload_path'] = './'.IMG_USER_PATH;
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
    	    $this->load->view('templates/header');
			$this->load->view('upload/upload_success', $data);
       	    $this->load->view('templates/footer');
		}
		
	}
	function single_imagefile()
	{
        //ref: http://phpsense.com/2007/php-file-uploading/ <---multiple file upload structure etc

		$config['upload_path'] = './'.IMG_USER_PATH;
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size']	= '1000';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$this->load->library('upload', $config);

        if($_FILES['Filedata']['error'] == UPLOAD_ERR_OK)
        {
            echo 'Upload successful';
            echo print_r($_FILES);
        } else {
            echo 'File upload error';
        }

		if (!$this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload/uploadfrm', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
            $fpath = $upload_data['full_path'];
            $fname = $upload_data['file_name']; 
            $ftype = $upload_data['file_ext']; 
            $fsize = $upload_data['file_size'];
            $ispic = $upload_data['is_image'];
            $fwide = $upload_data['image_width'];
            $fhite = $upload_data['image_height'];
            $fboth = $upload_data['image_size_str'];
            
     	    $this->load->view('templates/header');
			$this->load->view('upload/uploadok', $data);
       	    $this->load->view('templates/footer');
		}
		
	}
    function _do_upload()
    {
		$info = array();

		// compatibility for flash uploader and browser not supporting multiple upload
		if (is_array($_FILES['Filedata']) && !is_array($_FILES['Filedata']['tmp_name']))
		{
			$_FILES['Filedata']['tmp_name'] = array($_FILES['Filedata']['tmp_name']);
			$_FILES['Filedata']['name'] = array($_FILES['Filedata']['name']);
		}

		for ($file = 0; $file < count($_FILES['Filedata']['tmp_name']); $file++)
		{
			$valid = explode('|', 'png|zip|rar|gif|jpg|jpeg');
			if (!in_array(strtolower(substr($_FILES['Filedata']['name'][$file], -3)), $valid))
				continue;

			if (!in_array(strtolower(substr($_FILES['Filedata']['name'][$file], -3)), array('zip', 'rar')))
				$pages = $this->files_model->page($_FILES['Filedata']['tmp_name'][$file], $_FILES['Filedata']['name'][$file], $this->input->post('chapter_id'));
			else
				$pages = $this->files_model->compressed_chapter($_FILES['Filedata']['tmp_name'][$file], $_FILES['Filedata']['name'][$file], $this->input->post('chapter_id'));

			foreach ($pages as $page)
			{
				$info[] = array(
					'name' => $page->filename,
					'size' => $page->size,
					'url' => $page->page_url(),
					'thumbnail_url' => $page->page_url(TRUE),
					'delete_url' => site_url("admin/series/delete/page"),
					'delete_data' => $page->id,
					'delete_type' => 'POST'
				);
			}
		}

		// return a json array
		$this->output->set_output(json_encode($info));
		return true;
    }
*/
?>
