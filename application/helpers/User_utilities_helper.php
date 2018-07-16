<?php
//================================================================ Global current user variables
	/**
	 * will be used everywhere when need a current user's id or other information
	 * if its empty, will try to renew the login status by token.
	 */
	function get_user_id()
	{
		$returnValue = 0;

		// check device
		if (is_browser())
		{
			if(isset($_SESSION['user_id']))
			{
				$returnValue = $_SESSION['user_id'];
			}
			elseif(isset($_COOKIE["token"]) && $_COOKIE["token"])
			{
				$get_token = continue_token($_COOKIE["token"]);
				$returnValue = $get_token["user_id"];
			}
		}
		else
		{
			// application version
		}
			
		return $returnValue;
	}
	  	
	function get_user_email()
	{
		$returnValue = "anonymous";

		if(isset($_SESSION['user_email']))
		{
			$returnValue = $_SESSION['user_email'];
		}
		
		return $returnValue;
	}

	function get_organization_id()
	{
		$returnValue = 0;

		if (is_browser())
		{
			if(isset($_SESSION['organization_id']))
			{
				$returnValue = $_SESSION['organization_id'];
			}
			elseif(isset($_COOKIE["token"]) && $_COOKIE["token"])
			{
				$get_token = continue_token($_COOKIE["token"]);
				$returnValue = $get_token["organization_id"];
			}
		}

		return $returnValue;
	}
	
	function get_organization_name()
	{
		$returnValue = "error";
		
		$result = get_organization();
		$returnValue= $result ? $result->organization_name : $returnValue;	

		return $returnValue;
	}
	
	function get_organization_logo()
	{
		$returnValue = "error.png";

		$result = $result = get_organization();
		$returnValue= $result ? $result->organization_logo : $returnValue;
		$returnValue= $returnValue ? $returnValue : DEFAULT_LOGO;	
		
		return UPLOAD_FOLDER . "/". $returnValue;
	}	
	
	function get_organization_logo_top()
	{
		$result = $result = get_organization();
		$returnValue= $result ? $result->organization_logo : DEFAULT_LOGO;	
		$returnValue= $returnValue ? $returnValue : DEFAULT_LOGO;	
		
		return "uploads/". $returnValue;
	}	
	
	function get_user_group_id()
	{
		$returnValue = 0;
		$result = get_user();
		$returnValue= $result ? $result->user_group_id : $returnValue;			
		return $returnValue;
	}

	function get_user_group_level()
	{
		$returnValue = 0;
		$result = get_user_group();
		$returnValue= $result ? $result->user_group_level : $returnValue;
		
		return $returnValue;
	}

	function get_user_group_name()
	{
		$returnValue = 0;
		$result = get_user_group();
		$returnValue= $result ? $result->user_group_name : $returnValue;				
		
		return $returnValue;
	}
	
//=============get entities===================
	function get_user()
	{
		$returnValue = 0;
		
		$user_id = get_user_id();
				
		if($user_id > 0) {
			$ci =& get_instance();		
			$query = $ci->db->query("SELECT *  FROM ".TABLE_USER." WHERE user_id = ". $user_id);
			$returnValue = $query->row();			
		}
		
		return $returnValue;
	}	
	
	function get_user_group()
	{
		$returnValue = 0;
		
		$user_id = get_user_id();
				
		if($user_id > 0) {
			$ci =& get_instance();
			$query = $ci->db->query("SELECT * FROM ".TABLE_USER." join ".TABLE_USER_GROUP." on ".TABLE_USER.".user_group_id = ".TABLE_USER_GROUP.".user_group_id WHERE ".TABLE_USER.".user_id = " . $user_id);
			$returnValue = $query->row();			
		}
		
		return $returnValue;
	}
	
	function get_organization()
	{
		return get_organization_by_id(get_organization_id());
	}
	
	function get_organization_by_id($id)
	{
		$returnValue = 0;

		$organization_id = $id;
		
		if( $organization_id > 0) {
			$ci =& get_instance();
			$query = $ci->db->query("SELECT * FROM ".TABLE_ORG." WHERE organization_id = " . $organization_id);
			$returnValue = $query->row();
		}
		return $returnValue;
	}	
	
//================================================================ Tokens
	/**
	 * will be called by login, generate a token if user choose "remember", store to cookie
	 */
	function sign_auth($user_email, $user_password, $isRemember = FALSE)
	{
		$returnValue = 0;
		$tokenString = NULL;
		$tokenKey = NULL;

		$ci =& get_instance();
		$ci->load->model("users_model");
		
		$user = Users_model::check_password($user_email, $user_password);

		if($user) {

			if($isRemember)
			{
				$tokenKey = generate_tokenKey();

				$tokenString = generate_token($user->user_id, $tokenKey);
			}
		
			if (is_browser()){
				// what will be stored in session
				$userData = array(
					'user_id' => $user->user_id,
					'user_email' => $user_email,
					'user_group_name' => $user->user_group_name,
					'organization_id' => $user->organization_id,
				);
				$ci->session->set_userdata($userData);
			}
			// update database login information, including token
			Users_model::update_token($user->user_id, $tokenString,TOKEN_TYPE_LOGIN, $tokenKey, Token::token_expiry());

			$returnValue = $user->user_id;
		}

		return $returnValue;
	}

	/**
	 * renew the token: check the token for user, then keep this user login
	 * @param $token
	 */
	function continue_token($token)
	{
		$returnValue = array();
		
		$ci =& get_instance();
		$ci->load->model("users_model");
		
		// match the token with key.
		$getTokenKey = Users_model::get_tokenKey($token); 
		
		// success means correct token
		$tokenArray = Token::decode_token($token, $getTokenKey);

		// login the user
		if(!$tokenArray)
		{
			Users_model::delete_token(-1, TOKEN_TYPE_LOGIN, $getTokenKey);
			$returnValue =  array("user_id" => 0, "organization_id" => 0);
		}
		else
		{
			// get user from token payload
			$user_id = $tokenArray["uid"];
			
			$user = Users_model::get_user_from_token_key($user_id, $getTokenKey);

			if($user)
			{
				// generate a new token to continue the login status
				$tokenKey = generate_tokenKey() ; //generate_tokenKey();
				$tokenString = generate_token($tokenArray["uid"], $tokenKey);

				if (is_browser()){
					$userData = array(
						'user_id' => $user_id,
						'user_email' => $user->user_email,
						'user_group_name' => $user->user_group_name,
						'organization_id' => $user->organization_id,
					);

					$ci->session->set_userdata($userData);
				}
				Users_model::update_token($user->user_id, $tokenString, TOKEN_TYPE_LOGIN ,$tokenKey, Token::token_expiry());
				
				$returnValue = array("user_id" => $user_id, "organization_id" => $user->organization_id);
			}
			else
			{
				$returnValue = array("user_id" => 0, "organization_id" => 0);
			}
		}
		
		return $returnValue;
	}
	
	function clear_token()
	{
		if (is_browser()){
			$ci =& get_instance();
			$ci->session->sess_destroy();	
			delete_cookie("token","");
		}
	}
	
	/**
	 * called by above functions, generate a token then set it to the cookie
	 * @param $uid
	 * @return string
	 */
	function generate_token($uid, $key)
	{
		// generate a token==========
		$payload=array(
			'iss' => "Ace_CI_User", //who
			'iat' => $_SERVER['REQUEST_TIME'], //when
			'exp' => $_SERVER['REQUEST_TIME'] + Token::token_expiry(),
			'tmnl' => "web",
			'uid'=> $uid
		);
		$tokenString = Token::encode_token($payload, $key);
		
		if (is_browser()){
			setcookie("token", $tokenString, time() + Token::token_expiry(), "/");
		}
		else
		{

		}
		
		return $tokenString;
	}

	/**
	 * generate a random key for token
	 * @return string
	 */
	/*function generate_tokenKey()
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 6; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}*/
	
	function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
		if ($range < 1) return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd > $range);
		return $min + $rnd;
	}

	function generate_tokenKey($length = 20)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet); // edited

		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
		}

		return $token;
	}
//================================================================ Navigation bars

	/**
	 * get all permissions for current login user, will be called for build the navigation bar
	 * @return array
	 */
	function get_my_navigation_from_permissions()
	{
		$userGroupId = get_user_group_id();
		$userGroupPermissions = array();
		$permissions_all = variables_get_navigation_permissions();
	
		// get permissions from the user group
		if(array_key_exists($userGroupId, $permissions_all))
		{
			$userGroup = $permissions_all[$userGroupId];
			$userGroupPermissions = $userGroup["NavigationList"];

			// get extends from the user group,  get permissions from extend, remove repeating fields(union arrays)
			foreach ($userGroup["Extended"] as $grpId)
			{
				$userGroupPermissions = array_merge($userGroupPermissions, $permissions_all[$grpId]["NavigationList"]);
				//$userGroupPermissions += $permissions_all[$grpId]["Permissions"];
			};
		}
		
		return $userGroupPermissions;
	}

	/**
	 * create a hashes, will be used to generate the navigation bar
	 * @return array
	 */
	function get_nav()
	{
		$nav = variables_get_navigation();
		
		// get permit codes allowed for current user
		$myPermissions = get_my_navigation_from_permissions();

		// filter permissions: remove all permissions not allowed
		foreach($nav as $navKey => $navItem)
		{
			// delete item which is not permitted
			if(!in_array($navItem['FunctionCode'], $myPermissions)){
				unset($nav[$navKey]);
			}
			else
			{
				// check for submenu
				if(array_key_exists('Subitems', $navItem))
				{
					// delete item from submenu which is not permitted
					foreach($navItem['Subitems'] as $subMenuKey => $subMenuValue)
					{
						if(!in_array($subMenuValue['FunctionCode'], $myPermissions)){
							unset($nav[$navKey]['Subitems'][$subMenuKey]);
						}
					}
				}
			}
		}

		return $nav;
	}

//================================================================ Tools
	function is_browser()
	{
		$ci =& get_instance();	
		$ci->load->library('user_agent');
		return $ci->agent->is_browser();
	}

	function dateFormat($date)
	{
		$dateParsed = date_parse_from_format("M j, yyyy", $date);
		$newDate = $dateParsed["year"]."/".$dateParsed["month"]."/". $dateParsed["day"];
		return $newDate;
	}

	/**
	 * Generate a MySql select string for decode the date formate.
	 * @param $date
	 * @return string
	 */
	function dateFormat_decode($dateColumnName)
	{
		$decode_string = "DATE_FORMAT(".$dateColumnName.", '%b %d, %Y %H:%i')";

		return $decode_string;
	}
	function dateFormat_dateOnly_decode($dateColumnName)
	{
		$decode_string = "DATE_FORMAT(".$dateColumnName.", '%Y年%m月%d')";

		return $decode_string;
	}

	// Not using this for user system.
	function enum_join_statement($db, $table, $column)
	{
		$db->join('enum', '(enum_dir = "'. $table .'/'. $column.'") AND ('.$column.' = enum.enum_code)', 'left');
	}

	function func_run_with_ajax($form){

		$ajax_return = array();
		$ajax_return["success"] = $form->run();

		if ($ajax_return["success"] === FALSE)
		{
			$form->set_error_delimiters('', '');

			$ajax_return["title"] = 'failure';

			$ajax_return["messages"] = array();
			//===========================

			// loop the input data for validation
			foreach($form->validation_data as $key=>$value)
			{
				if(strlen(form_error($key))>0)
				{
					$ajax_return["messages"][$key] = form_error($key);
					//$validationReturn["messages"][]= array("itemName"=>$key, "message" => form_error($key));
				}
			}
		}
		else
		{
			$ajax_return["title"] = 'success';
		}

		$post_back = isset($_POST) ? $_POST : NULL;

		$ajax_return["post_back"] = $post_back;

		return $ajax_return;
	}
	
	/// Get user group id from user id. Work with JQuery plugin: Datatables
	function helper_datatable_varibles($gets)
	{
		// sanitizing
		$datatable_varibles["start"] = $gets["start"];
		$datatable_varibles["length"] = $gets["length"];
		$datatable_varibles["order"] = $gets["order"];
		$datatable_varibles["columns"] = $gets["columns"];
		$datatable_varibles["search"] = $gets["search"];
		$datatable_varibles["draw"] = $gets["draw"];

		// sanitize the extraSearch array
		$extraSearchs = array();
		if(isset($gets["extraSearch"]))
		{
			foreach ( $gets["extraSearch"] as $key=>$extraSearch) {
				if(strlen($extraSearch)>0)
				{
					// sanitizing
					$extraSearchs[$key] = $extraSearch;
				}
			}
		}
		$datatable_varibles["extraSearch"] = $extraSearchs;

		// sanitize the filter array
		$filters = array();
		foreach ( $gets["filters"] as $filter){

			// sanitizing
			$filters[] = $filter;
		}
		$datatable_varibles["filters"] = $filters;

		return $datatable_varibles;
	}

	// work with JQuery plugin: Datatables
	function helper_datatable_db($db, $tableName, $datatable_paging)
	{
		$sql = $db->get_compiled_select();

		// search by the filter
		$filters = $datatable_paging["filters"];
		if($datatable_paging["search"]['value'])
		{
			foreach ( $filters as $item) {
				$db->or_like($item, $datatable_paging["search"]['value']);
			}
		}

		// search for each column, will be auto
		foreach ($datatable_paging["columns"] as $columnItem)
		{
			$columnName = $columnItem['data'];

			if($columnItem['search']['regex'] == "true" && strlen($columnItem['search']['value']) >0)
			{
				$db->like($columnName, $columnItem['search']['value']);
			}
		}

		$count = $db->count_all_results("(".$sql.") subquery", false);

		// calculate the page
		$db->limit($datatable_paging["length"], $datatable_paging["start"] );

		// auto sorting
		foreach ($datatable_paging["order"] as $item) {
			$columnName = $datatable_paging["columns"][$item['column']]['data'];
			$db->order_by($columnName, $item['dir']);
		}

		// use subquery to retrive combo columns
		$query = $db->get();
		$result = $query->result_array();


		// generate the row id to front-end table
		foreach($result as $key => $resultItem)
		{
			$result[$key]["DT_RowId"] = "row_".$resultItem["DT_RowId"];
			$result[$key]["DT_RowAttr"] = array("data-id"=>(int)$resultItem["DT_RowId"]);
		}

		// the same
		$returnAJAX = array(
			"draw" => $datatable_paging["draw"],
			"recordsTotal" => $count,
			"recordsFiltered" => $count,
			"data"=> $result
		);

		return $returnAJAX;
	}

    function filterArray($array)
    {
        if($array)
        {
            return implode(",",array_filter($array));
        }
        else
        {
            return "";
        }
    }



		
	/**
	 * Class Token
	 * The default is 30 days token
	 * can be changed from the function token_expiry and token_resetPassword_expiry
	 */
	Class Token{

		public static function token_expiry()
		{
			// default 30 days, was set in constants.php file
			return EXPIRY_TOKEN; 
		}

		public static function token_resetPassword_expiry()
		{
			// 10 minutes, was set in constants.php file
			return EXPIRY_USER_ACTIVE; 
		}

		// encode a token
		public static function encode_token($payload, $key, $alg = 'SHA256')
		{
			$key = md5($key);
			$jwt = self::urlsafeB64Encode(json_encode(array('typ' => 'JWT', 'alg' => $alg))) .
				'.' . self::urlsafeB64Encode(json_encode($payload));

			return $jwt . '.' . self::signature($jwt, $key, $alg);
		}

		public static function signature($input, $key, $alg)
		{
			return hash_hmac($alg, $input, $key);
		}

		// decode a token
		public static function decode_token($jwt, $key)
		{
		
			$tokens = explode('.', $jwt);
			$key    = md5($key);

			if (count($tokens) != 3)
				return false;

			$header64 = $tokens[0];
			$payload64 = $tokens[1];
			$sign = $tokens[2];

			$header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);

			if (empty($header['alg']))
				return false;

			if (self::signature($header64 . '.' . $payload64, $key, $header['alg']) !== $sign)
				return false;

			$payload = json_decode(self::urlsafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

			$time = $_SERVER['REQUEST_TIME'];
			if (isset($payload['iat']) && $payload['iat'] > $time)
				return false;

			if (isset($payload['exp']) && $payload['exp'] < $time)
				return false;

			return $payload;
		}

		private static function urlsafeB64Encode($string)
		{

			return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
		}

		private static function urlsafeB64Decode($string)
		{
			return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
		}

	}
