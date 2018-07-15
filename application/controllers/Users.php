<?php


class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->model('users_model');
        $this->load->model('user_groups_model');
        $this->load->model('members_model');
		$this->load->model('organizations_model');
		
		$this->load->library('form_validation');

		$this->load->helper('form');
		$this->load->helper('url');
		
		$this->load->helper('User_email');
		$this->load->helper('User_variables');
	}

	/**
	 * List all users
	 */
	public function index()
	{
		$this-> view_userList();
	}

	public function view_userList_by_id($id = "")
	{
		$this-> view_userList(array("user_id" => $id));
	}


	/**
	 * view the user List page
	 */
	private function view_userList($conditions = array())
	{
		$data['title'] = 'User List';
		$data['nav'] = get_nav();

		/*==========initial drop downs===========*/
		$data['organizations'] =  json_encode($this->organizations_model->read_list_as_level_dropdown(get_organization_id(), get_user_group_level()));
		$data['user_groups'] =  json_encode($this->user_groups_model->read_list_as_level_dropdown(get_user_group_level()));

		$data['initSearchData'] = json_encode($conditions);

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_index', $data);
		$this->load->view('users/form_modal_user.php', $data);
		$this->load->view('users/inc_footer');
	}
	
	public function view_junks()
	{
		$data['title'] = 'Junk data cleaner';
		$data['nav'] = get_nav();
		$data['messages'] = "";	

		//EXPIRY_USER_ACTIVE EXPIRY_TOKEN
		$data['count_users'] = $this->users_model->get_user_expired( EXPIRY_USER_ACTIVE );
		$data['count_tokens'] = $this->users_model->get_token_expired( EXPIRY_TOKEN );
				
		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_junks', $data);
		$this->load->view('users/inc_footer');
	}
	
	public function form_junks()
	{
		$data['title'] = 'Junk data cleaner';
		$data['nav'] = get_nav();
		
		// be careful, must clean junk user first, else can not get user from token		
		$count_user_cleaned = $this->users_model->delete_user_expired( EXPIRY_USER_ACTIVE );
		$count_token_cleaned = $this->users_model->delete_token_expired( EXPIRY_TOKEN );

		$data['messages'] = "You deleted " .$count_user_cleaned ." junk user(s) and ".$count_token_cleaned." junk token(s)";
		
		$data['count_users'] = $this->users_model->get_user_expired( EXPIRY_USER_ACTIVE );
		$data['count_tokens'] = $this->users_model->get_token_expired( EXPIRY_TOKEN );		
		
		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_junks', $data);
		$this->load->view('users/inc_footer');
	}
	
	public function view_manage_account($messages = "", $json_error = "", $status = -1)
	{
		$data['title'] = 'My Account';
		$data['messages'] = $messages;
		$data['status'] = $status;
		$data['json_error'] = $json_error;
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_manage_account', $data);
		$this->load->view('users/inc_footer');
	}
	
	/**
	 * Its a template of update user information. However, we have nothing to be updated at this point.
	 */
	public function form_manage_account($errorMessages = "", $json_error = "")
	{
		$user_id = get_user_id();
		$user_first_name		 = $this->input->post('user_first_name');
		
		// step 1: set what data you want to update
		$data = array(
			'user_first_name' 		=> $user_first_name,
		);

        $data = $this->security->xss_clean($data);

		// step 2: set validation rules
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('user_first_name', 'First name', 'trim|required');
		
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === TRUE)
		{	
			// we dont have the column right now, so just comment it out
			//$this->users_model->update($user_id, $data);	
			$this->view_manage_account("You successed to submit but there is nothing to change", $json_error,1);
		}
		else
		{
			$json_error = json_encode($validation_result);
			$this->view_manage_account("Oops, please check your inputs", $json_error,0);	
		}
	}
	
	
	
	/**
	 * read users to generate a data grid
	 */
	public function ajax_listPaging()
	{
		// grab getings
		$datatable_varibles = helper_datatable_varibles($this->input->get());

		// generate the JSON going to return to AJAX
		$returnAJAX = $this->users_model->read_datatable(0, get_user_id(), $datatable_varibles);

		echo json_encode($returnAJAX);
	}

	/*
	 * ====================================================================
	 * login display and functions
	 */
	/**
	 * @param string $errorMessages = if get an error when login, errors are shown on the top
	 * @param string $json_error = if get an error before login, errors are shown under textbox
	 */
	public function view_login($errorMessages = "", $json_error = "")
	{
		$data['title'] = 'User Login';
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/page_login', $data);
		$this->load->view('users/inc_footer');
	}

	public function form_login()
	{
		$user_email = $this->input->post('user_email');
		$user_password = $this->input->post('user_password');
		$remember_me =  $this->input->post('remember_me');

		// 3 attemptings
		if(!isset($_SESSION['login_attempting']))
		{
			$_SESSION['login_attempting'] = 0;
		}
		$isShowCaptcha = $_SESSION['login_attempting'] >= LOGIN_ATTEMPTING_LIMIT;

		// validation
		$v_data['user_email'] = $user_email;
		$v_data['user_password'] = $user_password;

        $v_data = $this->security->xss_clean($v_data);
	
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email');
		$this->form_validation->set_rules('user_password', 'password', 'trim|required|min_length[8]|max_length[32]');
        $this->form_validation->set_message('min_length', '%s: the minimum of characters is %s');

		if($isShowCaptcha)
		{
			$v_data['captcha'] = $this->input->post('captcha');
			$this->form_validation->set_rules('captcha', 'captcha', 'required|callback_validate_captcha');
			$this->form_validation->set_message('validate_captcha', "Wrong Captcha");
			//. $_SESSION["captcha"]);
		}
		
		$this->form_validation->set_data($v_data);
		
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_login("", $json_error);
		}
		else
		{
			// sign_auth is in Permission_helper.php, used to sign the authorization to an user
			$user_id = sign_auth($user_email, $user_password, !empty($remember_me));

			if($user_id)
			{		
				$_SESSION['login_attempting'] = 0;
				redirect('/'. LOGIN_REDIRECTION);
			}
			else
			{
				if($_SESSION['login_attempting'] < LOGIN_ATTEMPTING_LIMIT){$_SESSION['login_attempting'] += 1;}
				$attemptingCountMessage = $_SESSION['login_attempting'] . "/" . LOGIN_ATTEMPTING_LIMIT;

				$this->view_login(rawurlencode("Wrong username or password. Attemptings: " . $attemptingCountMessage));
			}
		}
	}
	
	/**
	 * The first page of login
	 */
	public function view_login_success()
	{
		$data['title'] = 'Success';
		
		$isActive = $this->users_model->check_active(get_user_id());
		$data['nav'] = get_nav();
		
		$this->load->view('users/inc_header', $data);
		if($isActive)
		{
			
			$this->load->view('users/inc_navigation');
			$this->load->view('users/page_home', $data);			
		}
		else
		{	
			$this->load->view('users/page_home_inactive', $data);	
		}

		$this->load->view('users/inc_footer');
	}
	
	public function view_home()
	{
		$data['title'] = 'Home';
		
		$data['nav'] = get_nav();
		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_home', $data);
		$this->load->view('users/inc_footer');
	}
	
	
	/*
	 * ====================================================================
	 * signup display and functions
	 */
	/**
	 * @param string $errorMessages = if get an error when login, errors are shown on the top
	 * @param string $json_error = if get an error before login, errors are shown under textbox
	 */
	public function view_signup($errorMessages = "", $json_error = "", $inviteCode = "" )
	{
		$data['title'] = 'User Signup';
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;
		$data['nav'] = get_nav();
        $data['inviteCode'] = $inviteCode;

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_signup', $data);
		$this->load->view('users/inc_footer');
	}

    public function view_invite_signup($inviteCode = "")
    {
        $data['title'] = 'User Signup';
        $data['nav'] = get_nav();
        $data['inviteCode'] = $inviteCode;


        $this->load->view('users/inc_header', $data);
        $this->load->view('users/inc_navigation');
        $this->load->view('users/page_signup', $data);
        $this->load->view('users/inc_footer');
    }

    public function form_signup()
	{
		// for extension: the project doing email validation, user will be inactive when signup, otherwise change it to FALSE.
		$is_email_inform = TRUE;

		// for extension: if this project doing organizations, need more functions to get the org id
		$organization_id = 1;

		$user_email		 = $this->input->post('user_email');
		$user_password	 = $this->input->post('user_password');
		$user_confirm	 = $this->input->post('user_confirm');
        $member_code	 = $this->input->post('member_code');

		$user_group_id = INTERN;
		$result = 0;
		
		$validata = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,	
			'user_confirm'		=> $user_confirm,
            'member_code'       => $member_code
		);
		
		$data = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,
			//'user_created' 		=> date("Y-m-d H:i:s"),  // sometimes its different from mysql time
			'user_active' 		=> !$is_email_inform,
			'organization_id' 	=> $organization_id,
			'user_group_id' 	=> $user_group_id,
			'is_deleted' 		=> false
		);

        $data = $this->security->xss_clean($data);
        $validata = $this->security->xss_clean($validata);

		// validation
		$this->form_validation->set_data($validata);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email|is_unique['.TABLE_USER.'.user_email]');

        $this->form_validation->set_rules('member_code', '邀请码', 'trim|required|callback_validate_member_code');

		$this->form_validation->set_rules('user_password', 'password', 'required|min_length[8]|max_length[32]|trim|callback_validate_match_password');
		$this->form_validation->set_rules('user_confirm', 'password', 'required|min_length[8]|max_length[32]|trim|callback_validate_match_password');
		$this->form_validation->set_message('validate_match_password', 'Your password has to be matched.');
        $this->form_validation->set_message('min_length', '%s: the minimum of characters is %s');

		$validation_result = func_run_with_ajax($this->form_validation);
	
		
		if ($validation_result["success"] === TRUE)
		{		
			if($is_email_inform)
			{
				$tokenKey_of_newuser = generate_tokenKey();
				$data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);
				
				$callback_url = site_url("users/func_active/".$tokenKey_of_newuser);
				
				// The content be modified from User_variables_helper.php
				$email['email_message'] = variables_emails("sign_up", $user_email, array($callback_url));			
				$email['email_subject'] = 'Do-Not-Reply: Your account has been created';
				$email['email_to'] = $user_email;

				try{
					send_email($email);
					$result = $this->users_model->create_user_with_token_from_invite($data, $tokenKey_of_newuser, $member_code);
				}
				catch(Exception $e)
				{
					$validation_result["success"] = FALSE;
					$validation_result["messages"]["user_email"] = $e->getMessage();
				}	
			}
			else
			{
				$result = $this->users_model->create($data, $member_code);
			}
		}
		
		$validation_result["success"] = $validation_result["success"] && $result;
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_signup("", $json_error);
		}
		else
		{
			$data['type'] = 1;
			$data['title'] = "Account Created";
			$data['messages'] = 'Your account has been created. Please active your account.';	
			$data['nav'] = get_nav();
			
			$this->load->view('users/inc_header', $data);
			$this->load->view('users/inc_navigation');
			$this->load->view('users/page_message', $data);
			$this->load->view('users/inc_footer');
		}
	}	 
	
	public function func_active($key="")
	{
		$data['title'] = 'User Active';
		$data['type'] = 1;
		$data['messages'] = '';	
		$data['nav'] = get_nav();
		
		$key = rawurldecode($key);		
		$token = $this->users_model->get_token_from_key($key);
		
		$user_id = $this->users_model->get_user_id_from_token_key($key);
		

		// try decode the token
		$tokenDecoded = Token::decode_token($token, $key);
			
		if(!$tokenDecoded)
		{			
			$data['type'] = 0;
			$data['messages'] = 'Request Expired';
			
			// delete the user
			$this->users_model->delete($user_id);
		}
		else
		{
			$this->users_model->active($user_id);

			$data['type'] = 1;
			$data['messages'] = 'Active Successed';
		}
		
		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_message', $data);
		$this->load->view('users/inc_footer');
	}

	 
	/*
	 * ====================================================================
	 * forgot password display and functions
	 */
	public function view_forgot($errorMessages = "", $json_error = "")
	{
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$data['title'] = 'Forgot Password - Send Email';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot2($args=array('user_email'=>''))
	{
		$data['title'] = 'Forgot Password - Email Has Been Sent';
		$data['nav'] = get_nav();
		$data['user_email'] = $args['user_email'];

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot2', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot3($email="", $key = "", $expired = FALSE, $json_error = "")
	{
		$data['json_error'] = $json_error;
		$data['title'] = 'Change Password - Change Password';
		$data['nav'] = get_nav();
		$data['errorMessages'] = '';
		$data['user_email'] = rawurldecode($email);
		$data['user_token_key'] = $key;
		
		$key = rawurldecode($key);
		
		$token = $this->users_model->get_token_from_key($key);

		// try decode the token
		$tokenDecoded = Token::decode_token($token, $key);
		$expired = $expired || !$tokenDecoded;

		if($expired)
		{
			$data['errorMessages'] = "Request Expired";
		}

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot3', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot4()
	{
		$data['title'] = 'Forgot Password - Success';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot4', $data);
		$this->load->view('users/inc_footer');
	}

	/**
	 * send email of: forgot password verification code
	 */
	public function form_forgot_sendEmail()
	{
		$user_email = $this->input->post('user_email');

		// validation
		$v_data['user_email'] = $user_email;

        $v_data = $this->security->xss_clean($v_data);

		$this->form_validation->set_data($v_data);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email');
	
		// call a function: validate_emailExists to validate the email
		$this->form_validation->set_rules('user_email', 'user_email', 'callback_validate_emailExists');
		$this->form_validation->set_message('validate_emailExists', 'Sorry, its a wrong email.');

		$validation_result = func_run_with_ajax($this->form_validation);
		
		if ($validation_result["success"] === TRUE)
		{
			$callback = 'users/view_forgot3';
			
			try{
				$this->func_sendResetPasswordEmail($user_email, $callback);
			}
			catch(Exception $e)
			{
				$validation_result["success"] = FALSE;
				$validation_result["messages"]["user_email"] = $e->getMessage();
			}
		}
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);
			$this->view_forgot("",$json_error);
		}
		else
		{
			$this->view_forgot2(array("user_email" => $user_email));
		}
	}

	/**
	 * After click in from the email, fill in password, then will change the password
	 */
	public function form_forgot_changePassword()
	{
		$user_email = $this->input->post('user_email');
		$user_token_key = $this->input->post('user_token_key');
		$user_new_password = $this->input->post('user_password');
		$user_confirm = $this->input->post('user_confirm');
		$remember_me = $this->input->post('remember_me');

		// validation
		$v_data['user_password'] = $user_new_password;
		$v_data['user_confirm'] = $user_confirm;

        $v_data = $this->security->xss_clean($v_data);

		$this->form_validation->set_data($v_data);
		
		$this->form_validation->set_rules('user_password', 'password', 'required|trim|min_length[8]|max_length[32]|callback_validate_match_password');
		$this->form_validation->set_rules('user_confirm', 'password', 'required|trim|min_length[8]|max_length[32]|callback_validate_match_password');
        $this->form_validation->set_message('min_length', '%s: the minimum of characters is %s');

		$this->form_validation->set_message('validate_match_password', 'Your password has to be matched.');

		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_forgot3($user_email, $user_token_key, FALSE , $json_error);
		}
		else
		{
			// check the token
			$token = $this->users_model->get_token_from_key($user_token_key);
		
			$tokenDecoded = Token::decode_token($token, $user_token_key);
			
			if($tokenDecoded)
			{
				//delete token

				$this->users_model->update_password(password_hash($user_new_password, PASSWORD_DEFAULT), array('user_email' => $user_email), false);

				$user_id = sign_auth($user_email, $user_new_password, !empty($remember_me));
				
				Users_model::delete_token($user_id, TOKEN_TYPE_CHANGE_PASSWORD);
				
				$this->view_forgot4();
			}
			else
			{
				$this->view_forgot3($user_email, $user_token_key,TRUE);
			}
		}
	}

	/**
	 *
	 * @param string $errorMessages
	 * @param string $json_error
	 */
	public function view_changePassword($errorMessages = "", $json_error = "")
	{
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$data['title'] = 'Change Password - Send Email';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_changePassword2($args=array('user_email'=>''))
	{
		$data['title'] = 'Change Password - Email Has Been Sent';
		$data['nav'] = get_nav();
		$data['user_email'] = $args['user_email'];

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password2', $data);
		$this->load->view('users/inc_footer');
	}

	/**
	 * active from email, submit to change password
	 */
	public function view_changePassword3($email="", $key = "", $expired = FALSE, $json_error = "")
	{

		$data['json_error'] = $json_error;
		$data['title'] = 'Change Password - Change Password';
		$data['nav'] = get_nav();
		$data['errorMessages'] = '';

		
		$key = rawurldecode($key);
		
		$token = $this->users_model->get_token_from_key($key);

		// try decode the token
		$tokenDecoded = Token::decode_token($token, $key);

		if(!$tokenDecoded)
		{
			Users_model::delete_token(get_user_id(),TOKEN_TYPE_CHANGE_PASSWORD, $key);			
			$data['errorMessages'] = "Request Expired";
		}
		else
		{
			$data['user_email'] = $tokenDecoded["email"];
			$data['user_token_key'] = $key;
		}

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password3', $data);
		$this->load->view('users/inc_footer');
	}

	public function form_changePassword_sendEmail()
	{
		$get_user = $this->users_model->read(get_user_id());
		
		$user_email = $get_user["user_email"];

		if ($user_email)
		{
			$callback = 'users/view_changePassword3';
			
			try{
				$this->func_sendResetPasswordEmail($user_email, $callback);
				$this->view_changePassword2(array("user_email" => $user_email));
			}
			catch(Exception $e)
			{
				$this->view_changePassword($e->getMessage());
			}
		}
	}

	private function func_sendResetPasswordEmail($user_email = "", $callback_base_url="")
	{
		// generate a new password for this user
		$payload=array(
			'iss' => TOKEN_TITLE, //who
			'iat' => $_SERVER['REQUEST_TIME'], //when
			'exp' => $_SERVER['REQUEST_TIME'] + Token::token_resetPassword_expiry(),
			'tmnl' => "web",
			'email'=> $user_email
		);

		$tokenKey = $this->func_randomPassword();

		$token = Token::encode_token($payload , $tokenKey);

		
		// generate a link
		$callback_url = site_url($callback_base_url . "/" . rawurlencode($user_email). "/".rawurlencode($tokenKey));
		
		// The content be modified from User_variables_helper.php
		$email['email_message'] = variables_emails("reset_password", $user_email, array($callback_url));				
		$email['email_subject'] = 'Do-Not-Reply: Password Reset For Randomtransport Application';
		$email['email_to'] = $user_email;

		// call function in Email_helper
		send_email($email);
		
		// update new password to database
		$this->users_model->create_token_by_email($user_email, $token, TOKEN_TYPE_CHANGE_PASSWORD, $tokenKey, Token::token_resetPassword_expiry());		
	}
	
	/**
	 * The only entrance is right side of navigation bar, will be logout and clear the "remember me"
	 */
	public function func_logout()
	{
		// clear token from database
		Users_model::delete_token(get_user_id(), TOKEN_TYPE_LOGIN);
		clear_token();

		$this->view_login();
	}
	
	public function func_kick_out($message="")
	{
		clear_token();
		
		// clear token from database
		Users_model::delete_token(get_user_id(), TOKEN_TYPE_LOGIN);
		$this->view_login($message);
	}

	/**
	 * new password will be generated then email to user
	 */
	public function ajax_create()
	{
		$organization_id = $this->input->post('organization_id');

		$user_email		 = $this->input->post('user_email');
		$user_password	 = $this->input->post('user_password');

		$is_email_inform = filter_var($this->input->post('is_email_inform'), FILTER_VALIDATE_BOOLEAN);

		$data = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,
			//'user_created' 		=> date("Y-m-d H:i:s"),
			'user_active' 		=> filter_var($this->input->post('user_active'), FILTER_VALIDATE_BOOLEAN),
			'organization_id' 	=> $organization_id,
			'user_group_id' 	=> $this->input->post('user_group_id'),
			'is_deleted' 		=> false
		);

        $data = $this->security->xss_clean($data);

		// validation
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email|is_unique['.TABLE_USER.'.user_email]');
		$this->form_validation->set_rules('user_password', 'password', 'trim|required|min_length[8]|max_length[32]');
        $this->form_validation->set_message('min_length', '%s: the minimum of characters is %s');

		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] !== FALSE)
		{

			$data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);

			// send the form to proccessing code
			$result = $this->users_model->create($data);

			if($result && $is_email_inform)
			{
				
				// The content be modified from User_variables_helper.php
				$email['email_message'] = variables_emails("create_user", $user_email, array($user_password));			
				$email['email_subject'] = 'Do-Not-Reply: Your account has been created';
				$email['email_to'] = $user_email;

				// call function in Email_helper
				send_email($email);
			}
		}
		
		echo json_encode($validation_result);
	}

	/**
	 * AJAX update the user's detail from ajax
	 */
	public function ajax_update()
	{
		$organization_id = $this->input->post('organization_id');

		$user_id = $this->input->post('user_id');

		// keys have to be the same
		$data = array(
			'user_email' 		=> $this->input->post('user_email'),
			'organization_id' 	=> $organization_id,
			'user_active' 		=>  filter_var($this->input->post('user_active'), FILTER_VALIDATE_BOOLEAN)
		);

        $data = $this->security->xss_clean($data);

		if(strlen($this->input->post('user_group_id'))>0)
		{
			$data['user_group_id'] = $this->input->post('user_group_id');
		}
		// only change password if we need
		if(strlen($this->input->post('user_password')) > 0)
		{
			$data['user_password'] = $this->input->post('user_password');
		}

		// validation
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('user_email', 'email', 'required');
		$this->form_validation->set_rules('user_password', 'password', 'min_length[8]|max_length[32]|trim');

		//================== above is codeigniter things==================
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] !== FALSE)
		{

            if(strlen($this->input->post('user_password')) > 0)
            {
                $data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);
            }


			// send the form to proccessing code
			$this->users_model->update($user_id, $data);
		}

		echo json_encode($validation_result);
	}

	/**
	 * AJAX read one user's data, return as JSON
	 */
	public function ajax_userDetails()
	{
		$user_id = $this->input->post("id");

        $user_id = $this->security->xss_clean($user_id);

		// todo: need to change, only permitted data be retrived
		$returnAJAX = $this->users_model->read_form($user_id);

		echo json_encode($returnAJAX);
	}
	
	/**
	 * AJAX read one user's data, return as JSON
	 */
	public function ajax_userDetails_me()
	{
		$user_id = get_user_id();

		$returnAJAX = $this->users_model->read_form($user_id);

		echo json_encode($returnAJAX);
	}
	

	/**
	 *  AJAX activate or lock the user, the active users will be check from hooks/ManageAuth.php: checkAuth
	 *  locked user's permission is the same as GUEST
	 */
	public function ajax_switchActive()
	{
		$user_id = $this->input->post('user_id');

        $user_id = $this->security->xss_clean($user_id);

		$this->users_model->switch_active($user_id);
	}

	public function ajax_userDelete()
	{
		$user_id = $this->input->post('user_id');

        $user_id = $this->security->xss_clean($user_id);

		$this->users_model->delete($user_id);
		Users_model::delete_token($user_id);		
	}

	public function ajax_randomPassword() {
		echo $this->func_randomPassword();
	}

	public function ajax_emailExists() {

		$ajaxText = "";

		$user_email = $this->input->post('user_email');

        $user_email = $this->security->xss_clean($user_email);

		$is_valid_email = filter_var($user_email, FILTER_VALIDATE_EMAIL);
		$email_exists = $this->users_model->check_email_exists($user_email);

		$ajaxText = $is_valid_email?$ajaxText:"It is an invalid email.";
		$ajaxText = $email_exists?"Your email account already exists in our system;":$ajaxText;

		echo $ajaxText;
	}

	/**
	 * @return string: generate a random password for those user forgot password, or when created by admin
	 */
	private function func_randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 12; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	// customized validations============================================

	/*
	 * validate if the email exists in accounts(not contact emails)
	 */
	public function validate_emailExists($str="")
	{
		return $this->users_model->check_email_exists($str);
	}

	public function validate_match_password($str)
	{
		return $this->input->post('user_password') == $this->input->post('user_confirm');
	}

    public function validate_member_code($str=""){
        return $this->members_model->check_member_code($str);
    }

	public function validate_captcha($str=""){
		return $this->session->userdata("captcha") == $str;
	}





	
	/*
	 * clear session for test
	 */
	public function test_clear_session()
	{
		$this->session->sess_destroy();
		$this->view_login();
	}
}
