<?php

class Smileys extends CI_Controller {

    $data = array();
    
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {

        $this->load->library('table');

        $image_array = get_clickable_smileys(base_url().'smileys/', 'comments');

        $col_array = $this->table->make_columns($image_array, 8);

        $data['smiley_table'] = $this->table->generate($col_array);

        $this->load->view('smileys', $data);
    }

}
?>
