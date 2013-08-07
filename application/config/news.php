<?php
class News extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        echo 'invoke news_model';
        $this->load->model('news_model');
    }

    public function index()
    {
	$data['news'] = $this->news_model->get_news();
	$data['title'] = 'News archive';
	echo var_dump("index".$data);
	$this->load->view('templates/header', $data);
	$this->load->view('news/index', $data);
	$this->load->view('templates/footer');
    }
    
    public function view($slug)
    {
	$data['news_item'] = $this->news_model->get_news($slug);
	echo var_dump("view".$data);

	if (empty($data['news_item']))
	{
	        exit('uh-oh');
		show_404();
	}

	$data['title'] = $data['news_item']['title'];

	$this->load->view('templates/header', $data);
	$this->load->view('news/view', $data);
	$this->load->view('templates/footer');
    }
}