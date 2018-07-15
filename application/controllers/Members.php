<?php


class Members extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

        $this->load->database();

        $this->load->model('users_model');
        $this->load->model('user_groups_model');
		$this->load->model('members_model');
        $this->load->model('tags_model');

        $this->load->library('form_validation');
		$this->load->model('organizations_model');
		//$this->load->helper('datatable');
		$this->load->helper('email');


        $this->load->helper('form');
        $this->load->helper('url');

        $this->load->helper('User_email');
        $this->load->helper('User_variables');
	}

	/**
	 * List all members
	 */
	public function index()
	{
		$this->view_List_by_search();
	}

	public function view_List_by_id($user_id = "")
	{
		$this->view_List_by_search(array("user_id"=>$user_id));
	}

	public function view_List_by_member_id($member_id = "")
	{
		$this->view_List_by_search(array("member_id"=>$member_id));
	}

	/**
	 * view the members
	 */
	public function view_List_by_search($conditions = array())
	{
		$data['title'] = 'Members';
		$data['nav'] = get_nav();

		/*==========initial drop downs===========*/
		$data['member_shirt_number']    = json_encode($this->tags_model->read_list_from_category('member_shirt_number'));
        $data['member_shirt_number_pick']    = json_encode($this->tags_model->read_list_from_category_unique('member_shirt_number'));

		$data['member_perks']           = json_encode($this->tags_model->read_list_from_category('member_perks'));
		$data['member_position']        = json_encode($this->tags_model->read_list_from_category('member_position'));
		$data['member_games']           = json_encode($this->tags_model->read_list_from_category('member_games'));
        $data['member_medals']          = json_encode($this->tags_model->read_list_from_category('member_medals'));
        $data['member_status']          = json_encode($this->tags_model->read_list_from_category('member_status'));

		$data['initSearchData'] = json_encode($conditions);

        $this->load->view('users/inc_header', $data);
        $this->load->view('users/inc_navigation');
		$this->load->view('members/index', $data);
		$this->load->view('members/form_modal_member', $data);
		$this->load->view('members/popover', $data); // medals

        $this->load->view('users/inc_footer');
	}


    public function view_manage_member($messages = "", $json_error = "", $status = -1)
    {
        $data['title'] = 'My Account';
        $data['messages'] = $messages;
        $data['status'] = $status;
        $data['json_error'] = $json_error;
        $data['nav'] = get_nav();

        $data['member_position']          = json_encode($this->tags_model->read_list_from_category('member_position'));
        $data['member_games']             = json_encode($this->tags_model->read_list_from_category('member_games'));
        $data['member_shirt_number_pick']    = json_encode($this->tags_model->read_list_from_category_unique('member_shirt_number'));


        $this->load->view('users/inc_header', $data);
        $this->load->view('users/inc_navigation');
        $this->load->view('Members/page_manage_member', $data);
        $this->load->view('users/inc_footer');
    }

    public function form_manage_member($errorMessages = "", $json_error = "")
    {
        $user_id = get_user_id();


        // step 1: set what data you want to update
        $data = array(

            'member_gamename'	    => $this->input->post('member_gamename'),
            'member_shirt_number'	=> $this->input->post('member_shirt_number'),
            'member_position'	    => filterArray($this->input->post('member_position'),""),
            'member_games'		    => filterArray($this->input->post('member_games'),""),
            'member_description'    => $this->input->post('member_description'),
        );

        $data = $this->security->xss_clean($data);

        // step 2: set validation rules
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('member_gamename', '游戏名称', 'trim|required');

        $validation_result = func_run_with_ajax($this->form_validation);

        if ($validation_result["success"] === TRUE)
        {
            // 先更新一次保存资料，获得会员id

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
            $isUploadSuccess = $this->upload->do_upload('member_picture');
            $upload_data = $this->upload->data();
            $upload_data = $this->security->xss_clean($upload_data);

            $error = array('error' => $this->upload->display_errors());

          //  print_r($isUploadSuccess);
          //  print_r( $upload_data);


          //  die();

            if($isUploadSuccess && $upload_data)
            {
                $config['height']   	 =  $upload_data["image_height"] > 400? 400: $upload_data["image_height"];
                $config['width']     	 = $upload_data["image_width"] > 400? 400: $upload_data["image_width"];;

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['maintain_ratio'] = TRUE;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
                $data["member_picture"] = $upload_data["file_name"];

                // delete old logo
                $currentOrg = $this->members_model->read_by_user($user_id);
                $oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["member_picture"];

                if(file_exists($oldPath) && !is_dir($oldPath)){
                    $this->load->helper("file");
                    unlink($oldPath);
                }
            }
            //=====================logo
            $this->members_model->update_by_user($user_id, $data);


            $this->view_manage_member("资料成功保存", $json_error,1);
        }
        else
        {
            $json_error = json_encode($validation_result);
            $this->view_manage_member("出错了", $json_error,0);
        }
    }

	/**
	 * read member to generate a data grid
	 */
	public function ajax_listPaging()
	{
		// grab getings
		$datatable_varibles = helper_datatable_varibles($this->input->get());

		// generate the JSON going to return to AJAX
		$returnAJAX = $this->members_model->read_datatable(0, get_user_id(), $datatable_varibles);


		//=============================插入计算后的数据

        $newData = $returnAJAX["data"];

        foreach($newData as $key=>$sub){

            //0:picture, 1:name
            $newData[$key]["member_position_picture"] = $this->tags_model->read_images($sub["member_position"]);
           // $newData[$key]["member_position_name"] = $this->tags_model->read_names($sub["member_position"]);
        }
        $returnAJAX["data"] = $newData;

        //=============================
		echo json_encode($returnAJAX);
	}




	/**
	 * create member
	 */
	public function ajax_create()
	{
		// get data
		$organization_id = get_organization_id();

		$dataMember = array(
			'member_gamename'	    => $this->input->post('member_gamename'),
			'member_nickname'	    => $this->input->post('member_nickname'),
			'member_description'    => $this->input->post('member_description'),
			'member_status'		    => $this->input->post('member_status'),
			'member_perks'		    => filterArray($this->input->post('member_perks'),""),
			'member_position'	    => filterArray($this->input->post('member_position'),""),
			'member_shirt_number'	=> $this->input->post('member_shirt_number'),
			'member_kpi'	        => $this->input->post('member_kpi'),
			'member_medals'		    => filterArray($this->input->post('member_medals'),""),
			'member_tagvalue'		=> $this->input->post('member_tagvalue'),
			'member_games'		    => filterArray($this->input->post('member_games'),""),
            'member_code'		    => $this->input->post('member_code'),
            'organization_id'	    => $organization_id,
			'is_deleted' 		    => false
		);

        $member_start = $this->input->post('member_start');
		if($member_start)
        {
            $dataMember["member_start"] = dateFormat($member_start);
        }

		$dataValid = $dataMember;

		$this->form_validation->set_rules('member_nickname', 'name', 'required');

		$this->form_validation->set_data($dataValid);

        $validation_result = func_run_with_ajax($this->form_validation);

		if($validation_result["success"] === TRUE)
		{
			// if valid, create new member
			$member_id = $this->members_model->create($dataMember);
			//add_log("/Members/Create, id:" . $member_id, USERLOG_CREATE_EVENT);

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
            $isUploadSuccess = $this->upload->do_upload('member_picture');
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
                $dataMember["member_picture"] = $upload_data["file_name"];

                // delete old logo
                $currentOrg = $this->members_model->read($member_id);
                $oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["member_picture"];

                if(file_exists($oldPath) && !is_dir($oldPath)){
                    $this->load->helper("file");
                    unlink($oldPath);
                }
            }
            //=====================logo

            $this->members_model->update($member_id, $dataMember);
		}

		echo json_encode($validation_result);
	}

	/**
	 * AJAX update the members's detail from ajax
	 */
	public function ajax_update()
	{
		// get data
		$organization_id = get_organization_id();
		$member_id = $this->input->post('member_id');


        $dataMember = array(
            'member_id'             => $this->input->post('member_id'),
            'member_gamename'	    => $this->input->post('member_gamename'),
            'member_nickname'	    => $this->input->post('member_nickname'),
            'member_description'    => $this->input->post('member_description'),
            'member_start'		    => dateFormat($this->input->post('member_start')),
            'member_status'		    => $this->input->post('member_status'),
            'member_perks'		    => filterArray($this->input->post('member_perks')),
            'member_position'	    => filterArray($this->input->post('member_position')),
            'member_shirt_number'	=> $this->input->post('member_shirt_number'),
            'member_kpi'	        => $this->input->post('member_kpi'),
            'member_medals'		    => filterArray($this->input->post('member_medals')),
            'member_tagvalue'		=> $this->input->post('member_tagvalue'),
            'member_games'		    => filterArray($this->input->post('member_games')),
            'organization_id'	    => $organization_id,
            'is_deleted' 		    => false,
            //------------need update--------------
            'member_code'		    => $this->input->post('member_code'),
            'member_end'		    => dateFormat($this->input->post('member_end')),
        );

		// validation
		$this->form_validation->set_data($dataMember);
		$this->form_validation->set_rules('member_nickname', 'Name', 'required');
		$this->form_validation->set_rules('member_id', 'Member Id', 'required');

		//================== above is codeigniter things==================
        $validation_result = func_run_with_ajax($this->form_validation);


		if($validation_result["success"] === TRUE)
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
            $isUploadSuccess = $this->upload->do_upload('member_picture');
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
                $dataMember["member_picture"] = $upload_data["file_name"];

                // delete old logo
                $currentOrg = $this->members_model->read($member_id);
                $oldPath = "./" .UPLOAD_FOLDER. "/". $currentOrg["member_picture"];

                if(file_exists($oldPath) && !is_dir($oldPath)){
                    $this->load->helper("file");
                    unlink($oldPath);
                }
            }
            //=====================logo

            $this->members_model->update($member_id, $dataMember);
		}
        echo json_encode($validation_result);
	}

	/**
	 * AJAX read one members's data, return as JSON
	 */
	public function ajax_details()
	{
		$member_id = $this->input->post("id");
		
		$returnAJAX = $this->members_model->read_form($member_id);

		echo json_encode($returnAJAX);
	}

	public function ajax_memberDetails_me()
    {
        $user_id = get_user_id();

        $returnAJAX = $this->members_model->read_form_from_user($user_id);
        //$returnAJAX = $this->users_model->read_form($user_id);
        echo json_encode($returnAJAX);
    }

	public function ajax_delete()
	{
		$member_id = $this->input->post('member_id');

		$this->members_model->delete($member_id);
	}

	public function	ajax_switchAvailability()
	{

		$id = $this->input->post('member_id');

		$this->members_model->switch_Availability($id);
	}

	public function ajax_popover()
	{
		$id = $this->input->post("id");

		// 读取角色基础数据
        $returnValue = $this->members_model->read($id);

		//$returnValue = $this->members_model->read_popover($id);

		//$tasks =  $this->members_model->read_popover_detail($id);

		// emulating data
		$member_games = $this->tags_model->read_tags($returnValue["member_games"]);
        $member_perks = $this->tags_model->read_tags($returnValue["member_perks"]);
        $member_medals = $this->tags_model->read_tags($returnValue["member_medals"]);


        $returnValue["member_totalvalue"] = strval($returnValue["member_kpi"] + $returnValue["member_tagvalue"]);
        $returnValue["member_name"] =  $returnValue["member_gamename"] . " ".$returnValue["member_nickname"];
       // $returnValue["member_picture"] =  "hihihi";



        $returnValue["member_games"] = $member_games;
        $returnValue["member_perks"] = $member_perks;
        $returnValue["member_medals"] = $member_medals;

		echo json_encode($returnValue);
	}


	// customized validations============================================
}
