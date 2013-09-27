<?php
/*
 *Devnotes:
 *20130617 TODO: After insert, present image thumbnail and prompt for descr using News view type
 *20130801 TODO: Parse for quotes and replace with `
*/
class Upload extends CI_Controller {

    const USE_THUMBS = false;
    
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
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
		$config['upload_path'] = './images/user-pix/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png|ico';
		$config['max_size']	= '1000';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$this->load->library('upload', $config);
        $this->upload->initialize($config);
        
        if (!$this->upload->do_multi_upload('Filedata'))
		{
			$error = array('error' => $this->upload->display_errors());
            $data['error'] = $error;
     	    //$this->load->view('templates/header');
			$this->load->view('upload/uploadna', $data);
       	    //$this->load->view('templates/footer');
		}
		else
		{
            $flist = array();
            $first = true;
		    $destdir = './images/user-pix/';
            //Load library once, initialze each call..below
            $this->load->library('image_lib', $config);
            $uplarr = $this->upload->get_multi_upload_data();
            for($i=0; $i<count($uplarr); $i++){
                //echo $uplarr[$i]['file_name'];
                // map, store and insert into tbl_images
                $recarr = $this->gallery_model->get_imagedata(0); // get full record structure
                $recarr['fname'] = $uplarr[$i]['file_name'];
                $recarr['ftype'] = $uplarr[$i]['file_type'];
                $recarr['fpath'] = $uplarr[$i]['full_path'];
                $recarr['fsize'] = $uplarr[$i]['file_size'];
                $recarr['ispic'] = $uplarr[$i]['is_image'];
                $recarr['fwide'] = $uplarr[$i]['image_width'];
                $recarr['fhite'] = $uplarr[$i]['image_height'];
                $recarr['fboth'] = $uplarr[$i]['image_size_str'];
                $message = $this->gallery_model->update_image(INSERT_REC, $recarr);
                $fname = $recarr['fname'];
                $image = $fname;
                $thumb = '';
                if(self::USE_THUMBS)
                {
                    $thumb = $this->gallery_model->rtn_thumb_name($fname);
                    $pos = strpos($fname,'.');
                    $fext = substr($fname,$pos,strlen($fname));
                    $thumb = str_replace($fext,'_thumb'.$fext,$fname);
                    if($this->generateThumb($destdir.$fname, $first))
                    {
                       $image = $thumb;
                    }
                }
       $thumb = $this->gallery_model->rtn_thumb_name($fname);
                $flist[] = array('fname' => $recarr['fname'],
                                 'image' => '<img src="'.$destdir.$thumb.'" />',
                                 'descr' => $recarr['descr'],
                                 'thumb' => $thumb
                                 );
                $first = false;
            }
            
            $data['upload_data'] = $flist;
            
     	    $this->load->view('templates/header');
			$this->load->view('upload/uploadok', $data);
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
}
/*
	function single_imagefile()
	{
        //ref: http://phpsense.com/2007/php-file-uploading/ <---multiple file upload structure etc

		$config['upload_path'] = './images/user-pix/';
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
