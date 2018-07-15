<?php
class User_groups_model extends CI_Model {

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();
		self::$db = &get_instance()->db;
		self::$mainTableName = "user_groups";
	}

	//=============================== basic CRUD below
	public function create($data)
	{
		return self::$db->insert(self::$mainTableName, $data);
	}

	public function read($id)
	{
		self::$db->where('is_deleted', false);
		$query = self::$db->get_where(self::$mainTableName , array('user_group_id' => $id));
		return $query->row_array();
	}

	public function update($id, $data)
	{
		self::$db->where('user_group_id', $id);
		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		self::$db->set('is_deleted', TRUE);
		self::$db->where('user_group_id', $id);
		return self::$db->update(self::$mainTableName);
	}

	//=============================== basic CRUD above

	// return max level of a list of user group
	public function read_max_group_level($user_group_id_list)
	{
		self::$db->from(self::$mainTableName);
		self::$db->select_max('user_group_level');
		self::$db->where_in('user_group_id', implode(",",$user_group_id_list));
        self::$db->order_by('user_group_level');
		return self::$db->get()->row()->user_group_level;
	}

	// return min level of a list of user group
	public function read_min_group_level($user_group_id_list)
	{
		self::$db->from(self::$mainTableName);
		self::$db->select_min('user_group_level');
		self::$db->where_in('user_group_id', implode(",",$user_group_id_list));
        self::$db->order_by('user_group_level');
		return self::$db->get()->row()->user_group_level;
	}

	public function read_list_dropdown()
	{
		self::$db->where('is_deleted', false);
		self::$db->where('user_group_name !=', 'Guest');
		self::$db->select('user_group_id AS id');
        self::$db->select('CONCAT("[", user_group_level, "]" ,  user_group_name) AS value');
        self::$db->order_by('user_group_level');

		$result = self::$db->get(self::$mainTableName)->result();
		return $result;
	}
	public function read_list_as_level_dropdown($user_group_level)
	{
		self::$db->from(self::$mainTableName);
		
		if($user_group_level < ADMINISTRATOR)
		{
			self::$db->where(self::$mainTableName.'.user_group_level <', $user_group_level);
		}
		// dont retrive the VISITOR group
		self::$db->where(self::$mainTableName.'.user_group_level !=', '0');
		self::$db->select(self::$mainTableName.'.user_group_id AS id');
        self::$db->select('CONCAT("[", user_group_level, "] " ,  '. self::$mainTableName. '.user_group_name) AS value');
        self::$db->order_by('user_group_level');
		$result = self::$db->get()->result();

		return $result;
	}

	public function read_full_list_dropdown()
	{
		self::$db->where('is_deleted', false);
		self::$db->select('user_group_id AS id');
		self::$db->select('CONCAT("[", user_group_level, "]" ,  user_group_name) AS value');
		self::$db->select('0 AS readonly');
        self::$db->order_by('user_group_level');



		$result = self::$db->get(self::$mainTableName)->result();
		return $result;
	}

	public function read_user_group_level($user_group_id)
	{
		$db = self::$db;
		$db-> from(self::$mainTableName);

		$db->select('user_group_level');
		$db->where('user_group_id' , $user_group_id);

		return $db->get()->row()->user_group_level;
	}
}
