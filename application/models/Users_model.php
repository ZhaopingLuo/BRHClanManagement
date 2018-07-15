<?php
class Users_model extends CI_Model {

	//public $tableName;
	public $current_user_group_level;

	private static $db;
	private static $mainTableName;
	private static $secondaryTableName;
	
	

	public function __construct()
	{
		parent::__construct();
		
		self::$db = &get_instance()->db;
		self::$mainTableName = TABLE_USER;
		self::$secondaryTableName = TABLE_TOKEN;
		
		$this->load->helper('User_email');
		$this->load->helper('User_utilities');
		$this->load->helper('User_variables');
		
		
		$this->current_user_group_level= get_user_group_level();		
	}

	/*
	 * ====data filter====
	 */
	public function data_row_filter()
	{
		self::$db->where(self::$mainTableName.".is_deleted", false);

		if($this->current_user_group_level < ADMINISTRATOR || get_organization_id() != SUPER_ORGANIZATION)
		{
			self::$db->where( self::$mainTableName .'.organization_id =', get_organization_id() );
		}
	}
	public function data_column_filter()
	{
		self::$db->select('*');
	}

	/*
	 * ====data filter====
	 */


	//=============================== basic CRUD below

    // 暂时不用以下的陌生注册，但是管理员还是用这个
	public function create($data)
	{
		self::$db->insert(self::$mainTableName, $data);
		$insert_id = self::$db->insert_id();

		return $insert_id;
	}

	public function read($id)
	{
		$this->data_row_filter();

		//self::$db->select(dateFormat_decode("user_created")." AS user_created");

		self::$db->select("*");

		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		return $query->row_array();
	}

	public function read_from_email($email)
	{
		// not use filter, because its used by visiter
		self::$db->select("*");

		$query = self::$db->get_where(self::$mainTableName , array('user_email' => $email));
		return $query->row_array();
	}
	
	public function read_organization_id($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		return $query->row()->organization_id;
	}

	public function update($id, $data)
	{
		self::$db->where('user_id', $id);
		$this->data_row_filter();

		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		self::$db->set('is_deleted', TRUE);
		self::$db->where('user_id', $id);
		$this->data_row_filter();

		return self::$db->update(self::$mainTableName);
	}

	/**
	 * Will be used for have-not-login user, so dont need the filter
	 * @param $data
	 * @return mixed
	 */
	public function exists($data)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where($data);
		$count = self::$db->count_all_results();
		return $count;
	}

	//=============================== basic CRUD above
	static public function check_password($user_email, $user_password)
	{
		$db = self::$db;

		$tableName = self::$mainTableName;
		$db->from($tableName);
		$db->join(TABLE_USER_GROUP, TABLE_USER_GROUP.'.user_group_id = '.$tableName.'.user_group_id');

		$db->where(array('user_email' => $user_email));
		$db->select('user_id');
		$db->select('user_password');
		$db->select($tableName. '.organization_id AS organization_id');
		$db->select( TABLE_USER_GROUP.'.user_group_name AS user_group_name');

		$query = $db->get();
		
		if($query->num_rows() > 0)
		{
			$dataBasePassword = $query -> row()-> user_password;

			if(password_verify($user_password, $dataBasePassword))
			{
				return $query -> row();
			}
		}

		return false;
	}

	//=============================== basic CRUD above
	static public function get_user_from_token_key($user_id, $token_key)
	{
		$tableName = self::$mainTableName;
		$tableName_token = self::$secondaryTableName;
		
		$db = self::$db;

		$db->from($tableName);
		$db->join(TABLE_USER_GROUP, TABLE_USER_GROUP.'.user_group_id = '.self::$mainTableName.'.user_group_id');
		$db->join($tableName_token, $tableName_token.'.user_id = '.self::$mainTableName.'.user_id');

		// make sure the user id matches the token
		$db->where(array( $tableName.'.user_id' => $user_id));
		$db->where(array( $tableName_token.'.token_key' => $token_key));

		$db->select('user_email');
		$db->select($tableName.'.user_id');
		$db->select('organization_id');
		$db->select(TABLE_USER_GROUP.'.user_group_name AS user_group_name');

		$query = $db->get();

		if($query->num_rows() > 0)
		{
			return $query -> row();
		}
		else
		{
			return false;
		}
	}

	public function check_email_exists($user_email)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where(array('user_email' => $user_email));
		$count = self::$db->count_all_results();
		return $count > 0;
	}
	
	public function check_active($user_id)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where(array('user_id' => $user_id));
		self::$db->where(array('user_active' => TRUE));
		$count = self::$db->count_all_results();
		return $count > 0;
	}

	public function read_form($id)
	{
		$this->data_row_filter();
		$this->data_column_filter();
		self::$db->select(dateFormat_decode("user_created")." AS user_created");
		self::$db->select(dateFormat_decode("user_last_login")." AS user_last_login");


		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		return $query->row_array();
	}

	/*
	 * read users, generate a Datatable
	 *
	 * */
	public function read_datatable($organization_id, $user_id = 0, $datatable_requests)
	{
		// Because it only displays the users whose level lower than current logging in user
		$current_user_level = $this->current_user_group_level;

		// Need to display user group's name, so join to user_groups
		self::$db->from(self::$mainTableName);
        self::$db->join(TABLE_USER_GROUP, TABLE_USER_GROUP.'.user_group_id = '.self::$mainTableName.'.user_group_id');
        self::$db->join("members", 'members.user_id = '.self::$mainTableName.'.user_id', "left");

		// filter for permissions
		$this->data_row_filter();

		// also a filter, but its joining table so need to be here
		if($user_id && $this->current_user_group_level < ADMINISTRATOR)
		{
			self::$db->where( TABLE_USER_GROUP. '.user_group_level <', $current_user_level );
		}

		// extra search code here--------------------
		// used for advanced search
		$extraSearch = $datatable_requests["extraSearch"];
		if(array_key_exists("search_date_start", $extraSearch))
		{
			self::$db->where('user_created >', dateFormat($extraSearch["search_date_start"]));
		}

		if(array_key_exists("search_date_end", $extraSearch))
		{
			self::$db->where('user_created <', dateFormat($extraSearch["search_date_end"]));
		}

		if(array_key_exists("user_id", $extraSearch))
		{
			self::$db->where( self::$mainTableName.'.user_id', $extraSearch["user_id"]);
		}
		// extra search code here---------------------

		self::$db->select('CONCAT("[", '.TABLE_USER_GROUP.'.user_group_id, "] " ,user_group_name)AS user_group');
		self::$db->select(self::$mainTableName.'.user_id AS user_id');
        self::$db->select('user_email AS user_email');

        //self::$db->select('member_start < DATE_ADD(NOW(),INTERVAL 1 MONTH) AS is_intern');

        self::$db->select('member_start < DATE_SUB(NOW(),INTERVAL 1 MONTH) AND user_groups.user_group_id = '. INTERN .' AS internship_due');
        self::$db->select('CONCAT( member_gamename, " (", member_nickname, ")") AS member_name');
        self::$db->select('member_id');


        self::$db->select( dateFormat_decode("user_created"). " AS user_created");
        self::$db->select( dateFormat_decode("user_last_login"). " AS user_last_login");
		self::$db->select('user_active AS user_active');

		// DT_RowId is necessary for Datatable display
		self::$db->select(self::$mainTableName.'.user_id AS DT_RowId');

		$returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

		return $returnAJAX;
	}

	public function switch_active($user_id)
	{
		self::$db->set('user_active', 'NOT user_active', FALSE);
		self::$db->where('user_id', $user_id);
		$this->data_row_filter();

		self::$db->update(self::$mainTableName);
	}
	
	public function update_password($newPassword, $data = array(), $isForgot = false)
	{
		self::$db->set('user_password', "'".$newPassword ."'", FALSE);

		self::$db->where($data);

		self::$db->update(self::$mainTableName);
	}
	
	public function active($user_id)
	{
		// delete the token, active user
		self::$db->from(self::$mainTableName);
		self::$db->set('user_active', TRUE);
		self::$db->where('user_id', $user_id);
		self::$db->update(self::$mainTableName);
		
		self::delete_token($user_id, TOKEN_TYPE_ACTIVE_USER);
		return self::$db->affected_rows() > 0;
	}

    // 在token上套一层邀请制
    public function create_user_with_token_from_invite($data, $tokenKey_of_newuser, $memberCode)
    {

        // create user

        $insert_id = $this->create_user_with_tokenKey($data, $tokenKey_of_newuser);

        // update member


        self::$db->where('member_code', $memberCode);

        self::$db->set('user_id', $insert_id, FALSE);
        self::$db->set('member_code', 'NULL', FALSE);
        self::$db->set('member_status', STATUS_1, FALSE);

        self::$db->update('members');


        return $insert_id;
    }

	public function create_user_with_tokenKey($data, $tokenKey)
	{
		self::$db->insert(self::$mainTableName, $data);
		$insert_id = self::$db->insert_id();

		if($insert_id)
		{
			$payload=array(
				'iss' => TOKEN_TITLE, //who
				'iat' => $_SERVER['REQUEST_TIME'], //when
				'exp' => $_SERVER['REQUEST_TIME'] + Token::token_resetPassword_expiry(),
				'tmnl' => "web",
				'email'=> $data["user_email"]
			);
			
			$token = Token::encode_token($payload , $tokenKey);
			
			// generate the token
			$this->create_token($insert_id, $token, TOKEN_TYPE_ACTIVE_USER, $tokenKey, Token::token_resetPassword_expiry());
		}
		
		return $insert_id;
	}
	
	public function create_token_by_email($user_email, $tokenString = "", $token_type, $key, $expiry )
	{
		$user = $this->read_from_email($user_email);
		
		if($user)
		{
			$this->create_token($user["user_id"], $tokenString, $token_type, $key,  $expiry);
		}
	}	
	
	public function create_token($user_id, $tokenString = "", $token_type, $tokenKey, $expiry = 0 )
	{
		self::delete_token($user_id, $token_type);
				
		$data = array(
			'user_id' => $user_id,
			'token_type' => $token_type,
			'token'  => $tokenString,
			'token_key'  => $tokenKey
		);
		
		self::$db->insert(self::$secondaryTableName, $data);
		
		$insert_id = self::$db->insert_id();
		
		self::$db->set('token_expiry', 'DATE_ADD(token_created, INTERVAL '. $expiry . ' SECOND)', FALSE);
		self::$db->where("token_id", $insert_id);
		self::$db->update(self::$secondaryTableName);
		
		return $insert_id;
	}

	/**
	 * Be used in permission_helper, update current token to renew the login expiry
	 * @param $user_id
	 * @param string $tokenstring
	 */
	static public function update_token($user_id, $tokenString = "", $token_type , $tokenKey = "",  $expiry = 0)
	{
		self::delete_token($user_id ,TOKEN_TYPE_LOGIN);
		
		
		// update last login
		$tableName = self::$mainTableName;
		$tableName_token = self::$secondaryTableName;
		$db = self::$db;
		
		$db->set('user_last_login', 'CURTIME()', FALSE);
		$db->where('user_id', $user_id);
		$db->update($tableName);	
	
		if($tokenString && $tokenKey)
		{
			// update token
			$data = array(
					'user_id' => $user_id,
					'token_type' => $token_type,
					'token'  => $tokenString,
					'token_key'  => $tokenKey
			);

			$db->insert($tableName_token, $data);
			
			$insert_id = $db->insert_id();
			
			self::$db->set('token_expiry', 'DATE_ADD(token_created, INTERVAL '. $expiry . ' SECOND)', FALSE);
			self::$db->where("token_id", $insert_id);
			self::$db->update(self::$secondaryTableName);
		}
	}

	static public function get_tokenKey($tokenString)
	{
		$db = self::$db;

		$db->where("token", $tokenString);
		$db->select("token_key");
		
		$result = $db->get(self::$secondaryTableName)->row();

		if($result)
		{
			$returnValue = $result->token_key;
		}
		else
		{
			$returnValue = "";
		}
		return $returnValue;
	}

	static public function delete_token($user_id =-1, $token_type=-1 , $token_key= -1)
	{	
		$data = array();
		
		if($user_id != -1)
		{
			$data["user_id"] = $user_id;
		}
		if($token_type != -1)
		{
			$data["token_type"] = $token_type;
		}
		if($token_key != -1)
		{
			$data["token_key"] = $token_key;
		}
	
		return self::$db->delete(self::$secondaryTableName, $data);
	}
	
	public function get_token_from_key($token_key)
	{	
		$db = self::$db;

		$db->where("token_key", $token_key);
		$db->select("token");
		
		$result = $db->get(self::$secondaryTableName)->row();

		if($result)
		{
			$returnValue = $result->token;
		}
		else
		{
			$returnValue = "";
		}
		return $returnValue;
	}
	
	public function get_user_id_from_token_key($token_key)
	{	
		$db = self::$db;
		
		$db->where("token_key", $token_key);
		$db->select("user_id");
		
		$result = $db->get(self::$secondaryTableName)->row();

		if($result)
		{
			$returnValue = $result->user_id;
		}
		else
		{
			$returnValue = "";
		}
		return $returnValue;
	}	
	
	public function get_user_expired($range)
	{		
		$returnValue = 0;

		self::$db->from(self::$mainTableName);
		self::$db->join(self::$secondaryTableName, self::$secondaryTableName.'.user_id = '.self::$mainTableName.'.user_id', 'left');				
		self::$db->where('token_expiry < CURTIME()','', FALSE);		
		self::$db->where('token_type', TOKEN_TYPE_ACTIVE_USER);
		

		$returnValue = self::$db->count_all_results();

		//print_r(self::$db->last_query());
		//die();				
		return $returnValue;		
	}
	
	public function get_token_expired($range)
	{
		$returnValue = 0;

		self::$db->from(self::$secondaryTableName);				
		self::$db->where('token_expiry < CURTIME()','', FALSE);		
		//self::$db->where('token_type !=', TOKEN_TYPE_ACTIVE_USER);		
		
		$returnValue = self::$db->count_all_results();
		
		//print_r(self::$db->last_query());
		//die();
		
		return $returnValue;		
	}	
	
	public function delete_user_expired($range)
	{

		#Create where clause
		$this->db->select('user_id');
		self::$db->from(self::$secondaryTableName);
		self::$db->where('token_expiry < CURTIME()','', FALSE);		
		self::$db->where('token_type', TOKEN_TYPE_ACTIVE_USER);
		$where_clause = $this->db->get_compiled_select();
		
		
		#Create main query
		self::$db->where(self::$mainTableName.".user_id IN (". $where_clause .") AND ".self::$mainTableName.".user_active = 0  ", NULL, FALSE);
		self::$db->delete(self::$mainTableName);
		
		return self::$db->affected_rows();
	}		
	
	public function delete_token_expired($range)
	{
		
		//return self::$db->delete(self::$mainTableName, array('organization_id' => $id));	
	
		self::$db->where('token_expiry < CURTIME()','', FALSE);		
		self::$db->delete(self::$secondaryTableName);
		
		return self::$db->affected_rows();
	}		
}



