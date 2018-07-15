<?php
class Demos_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	/*
	 *
	 *
	 * */
	public function read_remoteJson($datatable_requests)
	{

        $json = file_get_contents("http://localhost:31337/ajax/remoteJson.php?key=whatever");

        $jsonArray = json_decode( $json, true );


/*
		// extra search code here--------------------
		// used for advanced search
		$extraSearch = $datatable_requests["extraSearch"];

		if(array_key_exists("search_date_end", $extraSearch))
		{
			self::$db->where('user_created <', dateFormat($extraSearch["search_date_end"]));
		}

		// extra search code here---------------------
*/



		self::$db->select('CONCAT("[", '.TABLE_USER_GROUP.'.user_group_id, "] " ,user_group_name)AS user_group');
		self::$db->select('user_id AS user_id');
		self::$db->select('user_email AS user_email');
		self::$db->select( dateFormat_decode("user_created"). " AS user_created");
		self::$db->select('user_active AS user_active');

		// DT_RowId is necessary for Datatable display
		self::$db->select('user_id AS DT_RowId');

		$returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

		return $returnAJAX;
	}

    // work with JQuery plugin: Datatables
    function helper_datatable_json($array, $datatable_paging)
    {
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



}



