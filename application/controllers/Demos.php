<?php


class Demos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');

        $this->load->database();
        $this->load->model('demos_model');

        $this->load->helper('form');
        $this->load->helper('url');
    }


    /**
     *
     */
    private function view_userList($conditions = array())
    {
        $data['title'] = 'How to get remote JSON';
        $data['nav'] = get_nav();

        $data['initSearchData'] = json_encode($conditions);

        $this->load->view('users/inc_header', $data);
        $this->load->view('demos/demo_getRemoteJson');
        $this->load->view('users/inc_footer');
    }

    /**
     * r
     */
    public function ajax_listPaging()
    {
         // receive values from advanced search
        $datatable_varibles = helper_datatable_varibles($this->input->get());

        // generate the JSON going to return to AJAX
        // codes in the model is important!
        $returnAJAX = $this->demos_model->read_remoteJson($datatable_varibles);

        echo json_encode($returnAJAX);
    }

}
