<?php


class Tags extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->model('users_model');
        $this->load->model('tags_model');
        $this->load->model('tag_categories_model');

		$this->load->library('form_validation');

		$this->load->helper('form');
		$this->load->helper('url');

		$this->load->helper('User_variables');		
	}

	/**
	 * List all
	 */
	public function view_tags($category)
	{
		$this->view_List_by_search(array("tag_category_id"=>$category));
	}

    public function view_tags_shirt_number($category)
    {
        $this->view_List_by_search_shirt_number(array("tag_category_id"=>$category));
    }

	/**
	 * view the list
	 */
	public function view_List_by_search($conditions = array())
	{


		$data['title'] = 'Tags: ' . ($this->tag_categories_model->read($conditions["tag_category_id"]))["tag_category_name"];
		$data['nav'] = get_nav();

        $data['initSearchData'] = json_encode($conditions);

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('tags/page_index', $data);
		$this->load->view('tags/form_modal_tag', $data);
		$this->load->view('users/inc_footer');
	}

    /**
     * view the list
     */
    public function view_List_by_search_shirt_number($conditions = array())
    {


        $data['title'] = 'Tags: ' . ($this->tag_categories_model->read($conditions["tag_category_id"]))["tag_category_name"];
        $data['nav'] = get_nav();

        $data['initSearchData'] = json_encode($conditions);

        $this->load->view('users/inc_header', $data);
        $this->load->view('users/inc_navigation');
        $this->load->view('tags/page_index_shirt_number', $data);
        $this->load->view('tags/form_modal_tag', $data);
        $this->load->view('users/inc_footer');
    }

	/**
	 * read to generate a data grid
	 */
	public function ajax_listPaging()
	{
		// grab getings
		$datatable_varibles = helper_datatable_varibles($this->input->get());

		// generate the JSON going to return to AJAX
		$returnAJAX = $this->tags_model->read_datatable($datatable_varibles);

		echo json_encode($returnAJAX);
	}

	/**
	 * create tag
	 * for the admin-full-control purpose, the tag id will not be generated by MySql.
	 * instead of that, generated from AJAX
	 */

	public function ajax_create()
	{
		// get data
        $tag_id 	        = $this->input->post('tag_id');
        $tag_name 	        = $this->input->post('tag_name');
        $tag_description 	= $this->input->post('tag_description');
        $tag_value 	        = $this->input->post('tag_value');
        $tag_category_id 	= $this->input->post('tag_category_id');

		$data = array(
            'tag_id'		  => $tag_id,
            'tag_name'		  => $tag_name,
            'tag_description' => $tag_description,
            'tag_value'		  => $tag_value,
            'tag_category_id' => $tag_category_id
		);

        $data = $this->security->xss_clean($data);

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('tag_name', 'name', 'required');
        $this->form_validation->set_rules('tag_category_id', 'category', 'required');
        $this->form_validation->set_rules('tag_id', 'id', 'required');

		$validation_result = func_run_with_ajax($this->form_validation);
		
		if ($validation_result["success"] === TRUE)
		{
            //=====================logo
            $config['upload_path']          = './'.UPLOAD_FOLDER;
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = LIMIT_UPLOAD_SIZE;
            $config['max_width']            = 0;
            $config['max_height']           = 0;
            $config['overwrite'] 			= TRUE;
            $config['encrypt_name'] 		= TRUE;
            $config['detect_mime'] 			= TRUE;

            $this->load->library('upload', $config);
            $isUploadSuccess = $this->upload->do_upload('tag_picture');
            $upload_data = $this->upload->data();
            $upload_data = $this->security->xss_clean($upload_data);

            $error = array('error' => $this->upload->display_errors());

            if($isUploadSuccess && $upload_data)
            {
                $config['height']   	 =  $upload_data["image_height"] > 400? 400: $upload_data["image_height"];
                $config['width']     	 = $upload_data["image_width"] > 400? 400: $upload_data["image_width"];;

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['maintain_ratio'] = TRUE;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
                $data["tag_picture"] = $upload_data["file_name"];

                // delete old logo
                $currentOrg = $this->tags_model->read($tag_id);
                $oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["tag_picture"];

                if(file_exists($oldPath) && !is_dir($oldPath)){
                    $this->load->helper("file");
                    unlink($oldPath);
                }
            }
            //=====================logo

			// if valid, create new customer
			$this->tags_model->create($data);
		}

		echo json_encode($validation_result);
	}

	/**
	 * AJAX update the customers's detail from ajax
	 */

	public function ajax_update()
	{
        // get data
        $tag_id 	        = $this->input->post('tag_id');
        $tag_name 	        = $this->input->post('tag_name');
        $tag_description 	= $this->input->post('tag_description');
        $tag_value 	        = $this->input->post('tag_value');
        $tag_category_id 	= $this->input->post('tag_category_id');

		$data = array(
            'tag_id'		  => $tag_id,
            'tag_name'		  => $tag_name,
            'tag_description' => $tag_description,
            'tag_value'		  => $tag_value,
            'tag_category_id' => $tag_category_id
		);

        $data = $this->security->xss_clean($data);

		// validation
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('tag_name', 'name', 'required');
		$this->form_validation->set_rules('tag_id', 'id', 'required');

		//================== above is codeigniter things==================
		$validation_result = func_run_with_ajax($this->form_validation);
		
		if ($validation_result["success"] === TRUE)
		{
			//=====================logo
			$config['upload_path']          = './'.UPLOAD_FOLDER;
			$config['allowed_types']        = 'gif|jpg|png';
			$config['max_size']             = LIMIT_UPLOAD_SIZE;
			$config['max_width']            = 0;
			$config['max_height']           = 0;
			$config['overwrite'] 			= TRUE;
			$config['encrypt_name'] 		= TRUE;
			$config['detect_mime'] 			= TRUE;

			$this->load->library('upload', $config);
			$isUploadSuccess = $this->upload->do_upload('tag_picture');
			$upload_data = $this->upload->data();
            $upload_data = $this->security->xss_clean($upload_data);

			$error = array('error' => $this->upload->display_errors());
						
			if($isUploadSuccess && $upload_data)
			{
				$config['height']   	 =  $upload_data["image_height"] > 400? 400: $upload_data["image_height"];
				$config['width']     	 = $upload_data["image_width"] > 400? 400: $upload_data["image_width"];;

				$config['image_library'] = 'gd2';
				$config['source_image'] = $upload_data['full_path'];
				$config['maintain_ratio'] = TRUE;
				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				$data["tag_picture"] = $upload_data["file_name"];

				// delete old logo
				$currentOrg = $this->tags_model->read($tag_id);
				$oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["tag_picture"];
				
				if(file_exists($oldPath) && !is_dir($oldPath)){
					$this->load->helper("file");
					unlink($oldPath);
				}					
			}
			//=====================logo
			
			// send the form to proccessing code
			$this->tags_model->update($tag_id, $data);
		}

		echo json_encode($validation_result);
	}
	/**
	 * AJAX read one customers's data, return as JSON
	 */

	public function ajax_details()
	{
		$id = $this->input->post("id");

        $id = $this->security->xss_clean($id);

		$returnAJAX = $this->tags_model->read_form($id);

		echo json_encode($returnAJAX);
	}

	public function ajax_generate_id()
	{
		echo  $this->tags_model->read_generated_id();
	}

	public function ajax_delete()
	{
		$tag_id = $this->input->post('tag_id');

        $tag_id = $this->security->xss_clean($tag_id);

		// delete old logo
		$currentOrg = $this->tags_model->read($tag_id);
		$oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["tag_picture"];
						
		$this->tags_model->delete($tag_id);
		
		if(file_exists($oldPath) && !is_dir($oldPath)){
			$this->load->helper("file");
			unlink($oldPath);
		}
	}

	// customized validations============================================

    //TODO: re-calculate
}
