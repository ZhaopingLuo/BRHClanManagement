<?php
class Organizations_model extends CI_Model {

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();
		self::$db = &get_instance()->db;
		self::$mainTableName = TABLE_ORG;
	}

	//=============================== basic CRUD below
	public function create($data)
	{
		return self::$db->insert(self::$mainTableName, $data);
	}

	public function read($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('organization_id' => $id));

		return $query->row_array();
	}

	public function update($id, $data)
	{
		self::$db->where('organization_id', $id);
		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		return self::$db->delete(self::$mainTableName, array('organization_id' => $id));
	}

	//=============================== basic CRUD above
	public function read_list_dropdown()
	{
		self::$db->select('organization_id AS id');
		self::$db->select('organization_name AS value');

		return self::$db->get(self::$mainTableName)->result();
	}

	public function read_list_as_level_dropdown($organization_id, $user_group_level)
	{

		if($user_group_level < ADMINISTRATOR)
		{
			self::$db->where('organization_id', $organization_id);
		}
		self::$db->select('organization_id AS id');
		self::$db->select('organization_name AS value');


		return self::$db->get(self::$mainTableName)->result();
	}

	public function read_datatable($datatable_requests)
	{
		// Need to display user group's name, so join to user_groups
		self::$db->from(self::$mainTableName);

		// extra search code here
		$extraSearch = $datatable_requests["extraSearch"];

		self::$db->select('organization_id');
		self::$db->select('organization_logo');
        self::$db->select('organization_name');
        self::$db->select('organization_fullname');
        self::$db->select('organization_prefix');

		// DT_RowId is necessary for Datatable display
		self::$db->select('organization_id AS DT_RowId');

		$returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

		return $returnAJAX;
	}

	public function read_form($id)
	{
		$query = self::$db->get_where(self::$mainTableName , array('organization_id' => $id));
		return $query->row_array();
	}

	public function read_generated_id()
	{
		self::$db->select("MAX(organization_id)+1 AS id");

		return self::$db->get(self::$mainTableName)->row()->id;
	}
}
