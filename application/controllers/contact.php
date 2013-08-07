<?php
class Contact extends CI_Controller {
	
	function index() {
		
		$data['main_content'] = 'contact/contact_form';
		$this->load->view('login/includes/template', $data);
		
	}
	
	function submit() {
		
		$name = $this->input->post('name');
		
		$data['main_content'] = 'contact/contact_submitted';
		
		if ($this->input->post('ajax')) {
			$this->load->view($data['main_content']);			
		} else {
			$this->load->view('login/includes/template', $data);
		}
	}
	
}
