<?php
class Members_model extends CI_Model {

	public $current_user_group_level;

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();

		self::$db = &get_instance()->db;
		self::$mainTableName = "members";
        $this->load->helper('User_utilities');
		$this->current_user_group_level= get_user_group_level();
	}

	/*
	 * ====data filter====
	 */
	public function data_row_filter()
	{
		self::$db->where(self::$mainTableName.".is_deleted", false);

		if($this->current_user_group_level < ADMINISTRATOR  || get_organization_id() != SUPER_ORGANIZATION)
		{
			self::$db->where('members.organization_id =', get_organization_id() );
		}
	}
	public function data_column_filter()
	{
		self::$db->select('*');
	}

	/*
	 * ====data filter====
	 */

	public function create($data)
	{
		self::$db->insert(self::$mainTableName, $data);
		$insert_id = self::$db->insert_id();

		return $insert_id;
	}

	public function read($id)
	{
		$this->data_row_filter();
		$query = self::$db->get_where(self::$mainTableName , array('member_id' => $id));
		return $query->row_array();
	}

    public function read_by_user($id)
    {
        $this->data_row_filter();
        $query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
        return $query->row_array();
    }



	public function update($id, $data)
	{
		self::$db->where('member_id', $id);
		$this->data_row_filter();
		return self::$db->update(self::$mainTableName, $data);
	}

    public function update_by_user($user_id, $data)
    {
        self::$db->where('user_id', $user_id);
        $this->data_row_filter();
        self::$db->update(self::$mainTableName, $data);
    }



	public function delete($id)
	{
		self::$db->set('is_deleted', TRUE);
		self::$db->where('member_id', $id);
		$this->data_row_filter();

		return self::$db->update(self::$mainTableName);
	}


    public function check_member_code($member_code)
    {
        self::$db->from(self::$mainTableName);
        self::$db->where(array('member_code' => $member_code));
        $count = self::$db->count_all_results();
        return $count > 0;
    }



	//=============================== basic CRUD below
	/*
	 * read users, generate a Datatable
	 *
	 * */
	public function read_datatable($organization_id, $user_id = 0, $datatable_requests)
	{
		// Need to display user group's name, so join to user_groups
		self::$db->from(self::$mainTableName);
		self::$db->join('users', 'users.user_id = ' .self::$mainTableName.'.user_id', 'left');
        self::$db->join('tags shirt_tags', 'shirt_tags.tag_id = ' .self::$mainTableName.'.member_shirt_number', 'left');
        self::$db->join('tags status_tags', 'status_tags.tag_id = ' .self::$mainTableName.'.member_status', 'left');
        self::$db->join('user_groups', 'user_groups.user_group_id = users.user_group_id', 'left');
        self::$db->join('organizations', 'organizations.organization_id = members.organization_id');

		// Use data filter
		$this->data_row_filter();

		// extra search code here
		$extraSearch = $datatable_requests["extraSearch"];

		// select from a string with commas
		if(array_key_exists("member_perks", $extraSearch))
		{
			self::$db->where('FIND_IN_SET(' .$extraSearch["member_perks"] . ' , member_perks)');
		}

        if(array_key_exists("member_position", $extraSearch))
        {
            self::$db->where('FIND_IN_SET(' .$extraSearch["member_position"] . ' , member_position)');
        }

        if(array_key_exists("member_status", $extraSearch))
        {
            self::$db->where('FIND_IN_SET(' .$extraSearch["member_status"] . ' , member_status)');
        }

		if(array_key_exists("user_id", $extraSearch))
		{
			self::$db->where('users.user_id', $extraSearch["user_id"]);
		}

		if(array_key_exists("member_id", $extraSearch))
		{
			self::$db->where('member_id', $extraSearch["member_id"]);
		}

        self::$db->select('member_id');
        self::$db->select('user_email');
        self::$db->select(self::$mainTableName.'.user_id');
		self::$db->select('LENGTH(member_code) > 0 AS member_code_invited');

        self::$db->select('CONCAT("[",user_groups.user_group_id, "] " ,user_group_name) AS user_group_name');
        self::$db->select("member_gamename");
        self::$db->select("organization_prefix");
        self::$db->select("member_nickname");
        self::$db->select("member_picture");
        self::$db->select("member_description");
        self::$db->select(dateFormat_dateOnly_decode("member_start"). " AS member_start");
        self::$db->select(dateFormat_dateOnly_decode("member_end"). " AS member_end");
        self::$db->select("status_tags.tag_name AS member_status");
        self::$db->select("status_tags.tag_picture AS member_status_picture");

        self::$db->select("member_perks");

        self::$db->select("member_position"); // multiple, cant's filtered

        self::$db->select("shirt_tags.tag_name AS member_shirt_number");
        self::$db->select("shirt_tags.tag_picture AS member_shirt_number_picture");
        self::$db->select("shirt_tags.tag_description AS member_shirt_number_description");


        self::$db->select("member_medals");
        self::$db->select("member_tagvalue");
        self::$db->select("member_KPI");
        self::$db->select("member_KPI + IFNULL(member_tagvalue, 0) AS member_value");

        self::$db->select("member_games");

		// DT_RowId is necessary for Datatable display
		self::$db->select('member_id AS DT_RowId');

		$returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

		return $returnAJAX;
	}

	public function read_form($id)
	{
		$this->data_row_filter();
		$this->data_column_filter();
		self::$db->select(dateFormat_dateOnly_decode("member_start")." AS member_start");
        self::$db->select(dateFormat_dateOnly_decode("member_end")." AS member_end");
        self::$db->select("member_picture AS member_picture_display");

        self::$db->select('member_code AS member_code_link');
        self::$db->select('member_code');

		$query = self::$db->get_where(self::$mainTableName , array('member_id' => $id));
		return $query->row_array();
	}

    public function read_form_from_user($user_id)
    {
       /* self::$db->from(self::$mainTableName);
        self::$db->join('users', 'users.user_id = ' .self::$mainTableName.'.user_id', 'left');
*/
     //   $this->data_column_filter();

        $this->data_row_filter();
        $this->data_column_filter();
        self::$db->select("member_picture AS member_picture_display");

        self::$db->select('member_gamename AS member_gamename');



        $query = self::$db->get_where(self::$mainTableName , array('members.user_id' => $user_id));

        return $query->row_array();
    }



	static public function read_member_id_by_user_id($id)
	{
		self::$db->where("user_id", $id);
		$result = self::$db->get(self::$mainTableName);

		if($result){
			$result_rows = $result->row_array();
			return $result_rows["member_id"];
		}
		else
		{
			return 0;
		}
	}

	static public function read_organization_id_by_member_id($id)
	{
		self::$db->where("member_id", $id);
		$result = self::$db->get(self::$mainTableName);

		if($result){
			$result_rows = $result->row_array();
			return $result_rows["organization_id"];
		}
		else
		{
			return 0;
		}
	}

	public function read_list_dropdown()
	{
		$this->data_row_filter();

		self::$db->select('member_id AS id');
		self::$db->select('CONCAT("[",member_gamename, "]" ,member_nickname) AS value');

		$result = self::$db->get(self::$mainTableName)->result();
		return $result;
	}

	public function read_list_dropdown_user_id()
	{
		$this->data_row_filter();

		self::$db->select('user_id AS id');
        self::$db->select('CONCAT("[",member_gamename, "]" ,member_nickname) AS value');

		$result = self::$db->get(self::$mainTableName)->result();
		return $result;
	}

	/*
	 * 下拉框改变状态
	 */
    public function update_Status($member_id, $status)
    {
        self::$db->set('member_status', $status, FALSE);
        self::$db->where('member_id', $member_id);
        $this->data_row_filter();

        self::$db->update(self::$mainTableName);
    }
}



