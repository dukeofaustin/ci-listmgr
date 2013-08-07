<?php
/*
      A controller is simply a class that helps delegate work. It is the glue of your web application.
      The first thing you're going to do is set up a controller to handle static pages:
*/
class Pages extends CI_Controller {

    public function view($page = 'home')
    {
           // exit('PAGE['.APPPATH.'views/pages/'.$page.'.php'.']');
	    if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
	    {
		// Whoops, we don't have a page for that!
		exit(APPPATH."views/pages/".$page.".php NOT FOUND");
		show_404();
	    }

	    $data['title'] = ucfirst($page); // Capitalize the first letter
	    $this->load->view('templates/header', $data);
	    $this->load->view('pages/'.$page, $data);
	    $this->load->view('templates/footer', $data);
    }
}
/* WBM */
/* End of file pages.php */
/* Location: application/controllers/pages.php  */
