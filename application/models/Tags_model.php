<?php
class Tags_model extends CI_Model {

    public $current_user_group_level;
    private static $db;
    private static $mainTableName;

    public function __construct()
    {
        parent::__construct();
        self::$db = &get_instance()->db;
        self::$mainTableName = "tags";

        $this->current_user_group_level= get_user_group_level();
    }

    /*
     * ====data filter====
     */
    public function data_row_filter()
    {
        if($this->current_user_group_level < ADMINISTRATOR || get_organization_id() != SUPER_ORGANIZATION)
        {
           // self::$db->where( self::$mainTableName .'.organization_id =', get_organization_id() );
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
    public function create($data)
    {
        return self::$db->insert(self::$mainTableName, $data);
    }

    public function read($id)
    {
        $query = self::$db->get_where(self::$mainTableName , array('tag_id' => $id));

        return $query->row_array();
    }

    public function update($id, $data)
    {
        self::$db->where('tag_id', $id);
        return self::$db->update(self::$mainTableName, $data);
    }

    public function delete($id)
    {
        return self::$db->delete(self::$mainTableName, array('tag_id' => $id));
    }

    //=============================== basic CRUD above
    public function read_list_dropdown()
    {
        self::$db->select('tag_id AS id');
        self::$db->select('tag_name AS value');
        self::$db->order_by('tag_name');
        return self::$db->get(self::$mainTableName)->result();
    }

    public function read_list_as_level_dropdown($tag_id, $user_group_level)
    {

        if($user_group_level < ADMINISTRATOR)
        {
            self::$db->where('tag_id', $tag_id);
        }
        self::$db->select('tag_id AS id');
        self::$db->select('tag_name AS value');
        self::$db->order_by('tag_name');

        return self::$db->get(self::$mainTableName)->result();
    }

    public function read_datatable($datatable_requests)
    {
        // Need to display user group's name, so join to user_groups
        self::$db->from(self::$mainTableName);
        self::$db->join('members', 'members.member_shirt_number = ' .self::$mainTableName.'.tag_id' , "left");

        // extra search code here
        $extraSearch = $datatable_requests["extraSearch"];

        if(array_key_exists("tag_category_id", $extraSearch))
        {
            self::$db->where( self::$mainTableName.'.tag_category_id', $extraSearch["tag_category_id"]);
        }
        // extra search code here---------------------


        self::$db->select('tag_id');
        self::$db->select('tag_name');
        self::$db->select('tag_description');
        self::$db->select('tag_value');
        self::$db->select('tag_picture');
        self::$db->select('CONCAT("[", member_gamename , "] " , member_nickname )AS member_name');

        // DT_RowId is necessary for Datatable display
        self::$db->select('tag_id AS DT_RowId');

        $returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

        return $returnAJAX;
    }

    public function read_form($id)
    {
        $query = self::$db->get_where(self::$mainTableName , array('tag_id' => $id));
        return $query->row_array();
    }

    public function read_generated_id()
    {
        self::$db->select("MAX(tag_id)+1 AS id");

        return self::$db->get(self::$mainTableName)->row()->id;
    }

    public function read_list_from_category($category_column)
    {
        $baseUrl =  base_url('uploads');

        // Need to display user group's name, so join to user_groups
        self::$db->from(self::$mainTableName);
        self::$db->join('tag_categories', 'tag_categories.tag_category_id = ' .self::$mainTableName.'.tag_category_id');

        self::$db->select('tag_id AS id');
        self::$db->select('tag_name AS value');
        self::$db->select('tag_picture AS picture');
        self::$db->select('tag_value AS rank');

        self::$db->where('tag_categories.tag_category_column', $category_column);
        self::$db->order_by('tag_name');
        $this->data_row_filter();

        return self::$db->get()->result();
    }

    public function read_list_from_category_unique($category_column)
    {
        $baseUrl =  base_url('uploads');

        // Need to display user group's name, so join to user_groups
        self::$db->from(self::$mainTableName);
        self::$db->join('tag_categories', 'tag_categories.tag_category_id = ' .self::$mainTableName.'.tag_category_id');

        self::$db->join('members', 'members.member_shirt_number = ' .self::$mainTableName.'.tag_id', "left");

        self::$db->select('tag_id AS id');
        self::$db->select('CONCAT(tag_name , " (", IFNULL(CONCAT("", member_gamename), \'--\') ,") ") AS value');
        self::$db->select('tag_picture AS picture');
        self::$db->select('tag_value AS rank');
        self::$db->select('tag_description AS description');

        self::$db->distinct('tag_id');

        self::$db->where('tag_categories.tag_category_column', $category_column);
        self::$db->order_by('tag_name');
        $this->data_row_filter();

        $result = self::$db->get()->result();

        return $result;
    }


    public function read_images($condition)
    {
        $condition = explode (",", $condition);

        $returnValue = array();

        self::$db->from(self::$mainTableName);
        self::$db->select('tag_picture');
        self::$db->select('tag_name');

        self::$db->where_in('tag_id', $condition);

        $result = self::$db->get()->result_array();
    /*
        foreach ($result as $obj)
        {
            $returnValue[] = $obj["tag_picture"];
        }

        return json_encode($returnValue);
   */
        return json_encode($result);
    }

    public function read_tags($condition)
    {
        $condition = explode (",", $condition);

        $returnValue = array();

        self::$db->from(self::$mainTableName);
        self::$db->select('tag_picture');
        self::$db->select('CONCAT( "[", tag_value, "] ", tag_name, ": ", tag_description) AS tag_title');
        self::$db->select('tag_name');
        self::$db->select('tag_id AS id');

        self::$db->where_in('tag_id', $condition);

        $result = self::$db->get()->result_array();
        /*
            foreach ($result as $obj)
            {
                $returnValue[] = $obj["tag_picture"];
            }

            return json_encode($returnValue);
       */
        return $result;
    }


}
