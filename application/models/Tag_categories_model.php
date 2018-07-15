<?php
class Tag_categories_model extends CI_Model {

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();
		self::$db = &get_instance()->db;
		self::$mainTableName = "tag_categories";
	}

	//=============================== basic CRUD below
	public function create($data)
	{
		return self::$db->insert(self::$mainTableName, $data);
	}

	public function read($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('tag_category_id' => $id));

		return $query->row_array();
	}

	public function update($id, $data)
	{
		self::$db->where('tag_category_id', $id);
		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		return self::$db->delete(self::$mainTableName, array('tag_category_id' => $id));
	}

	//=============================== basic CRUD above
	public function read_list_dropdown()
	{
		self::$db->select('tag_category_id AS id');
		self::$db->select('tag_category_name AS value');

		return self::$db->get(self::$mainTableName)->result();
	}

	public function read_list_as_level_dropdown($organization_id, $user_group_level)
	{

		if($user_group_level < ADMINISTRATOR)
		{
			self::$db->where('tag_category_id', $organization_id);
		}
		self::$db->select('tag_category_id AS id');
		self::$db->select('tag_category_name AS value');


		return self::$db->get(self::$mainTableName)->result();
	}

	public function read_form($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('tag_category_id' => $id));
		return $query->row_array();
	}

	public function read_generated_id()
	{
		self::$db->select("MAX(tag_category_id)+1 AS id");

		return self::$db->get(self::$mainTableName)->row()->id;
	}
}
