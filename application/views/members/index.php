<!--
====================================heading of page====================================
-->
<div class="row page-heading bg-light">
	<div class="col page-title"><h2><?php echo $title; ?></h2></div>

	<div class="col tool-bar">
		<button class="btn btn-light" id="button_sendEmail"><i class="material-icons">email</i> Send Email</button>

<!--
====================================advanced search====================================
-->
		<div class="btn-group dropdown dropleft">
			<button class="btn btn-info dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuAdvancedSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Advanced Search
			</button>
			<div class="dropdown-menu search-panel" aria-labelledby="dropdownMenuAdvancedSearch">
				<form class="px-4 py-3" id="advanced_search">

					<input type="hidden" name="member_id" id="member_id"/>

					<div class="form-group">

                        <div class="form-group col-md-12">
                            <label for="member_status">Status</label>
                            <select type="text" class="form-control" style="width:100%;" name="member_status" id="member_status" placeholder="" autocomplete="off"></select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="member_position">Position</label>
                            <select type="text" class="form-control" style="width:100%;" name="member_position" id="member_position" placeholder="" autocomplete="off"></select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="member_perks">Perks</label>
                            <select type="text" class="form-control" style="width:100%;" name="member_perks" id="member_perks" placeholder="" autocomplete="off"></select>
                        </div>

					</div>
					<!--search conditions here-->

				</form>
				<div class="form-group_advanced_search">
					<button form="advanced_search" class="btn btn-primary" id="button_search"><i class="material-icons">search</i> Search</button>
					<button form="advanced_search" class="btn btn-lights" id="button_refresh"><i class="material-icons">refresh</i> All</button>
				</div>
			</div>
		</div>
		<!--end: advanced search panel-->

        <button  data-toggle="modal" data-target=".modal" data-backdrop="static" class="btn btn-warning" id="button_create"><i class="material-icons">import_contacts</i>  New Member</button>

	</div>
</div>

<!--
====================================Datatable====================================
-->
<table id="list_members" class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100%">
	<thead>
	<tr>
		<th data-source="member_id" data-filter data-multiselect style="width:10px;"></th>
        <th data-class="fix_picture" data-source="member_picture" data-render="callback_list_picture" style="width:60px;"></th>
        <th data-source="member_gamename" data-render="callback_list_name" data-filter>Name</th>
        <th data-source="user_email" data-render="callback_list_account" data-filter >Account</th>
        <th data-class="with_thumb" style="width:180px;" data-source="member_position" data-render="callback_list_position" data-filter>Position</th>

		<th data-class="index_list_center" style="width:130px;" data-source="member_shirt_number" data-render="callback_list_shirt_number" data-filter >Shirt#</th>
        <th data-class="index_list_right no_popover" data-source="member_KPI" style="width:80px;" data-filter >KPI</th>
        <th data-class="index_list_right no_popover" data-source="member_value" style="width:80px;" data-filter >Value</th>

        <th data-class="index_list_right no_popover" data-source="member_start" data-filter>Join Time</th>
		<th data-class="index_list_center no_popover" data-source="member_status" style="width:60px;" data-filter data-orderby-desc data-class="btn_available no_popover">Status</th>
		<th data-source="member_id" data-class="edit_column no_popover" style="width: 120px;" data-render="toolbar" data-toolbar></th>
	</tr>
	</thead>
</table>

<!--
====================================scripts:====================================
-->
<script>

	var ajax_detailform;
	var ajax_createForm;
	selector_dataTable = "#list_members";

	var Json_member_shirt_number = <?=$member_shirt_number?>;
    var Json_member_shirt_number_pick = <?=$member_shirt_number_pick?>;

    var Json_member_perks = <?=$member_perks?>; // used to test
    var Json_member_position = <?=$member_position?>;
    var Json_member_games = <?=$member_games?>;
    var Json_member_medals = <?=$member_medals?>;
    var Json_member_status = <?=$member_status?>;


	// generate the Datatable and popup forms
	$(document).ready(function() {

		// Advanced search
		searchInitialize = <?=$initSearchData?>;

		// datepicker
		// $('#advanced_search .input-daterange').datepicker(dateFormatSetting());

		// initialize the Datatable
		ajaxTarget = "<?php echo site_url('Members/ajax_listPaging'); ?>";
		advancedSearchFormSelector = "#advanced_search";

        search_member_status = new read_select2( advancedSearchFormSelector + " #member_status",Json_member_status, "== All ==");
        search_member_perks = new read_select2(advancedSearchFormSelector + " #member_perks",Json_member_perks, "== All ==");
        search_member_position = new read_select2(advancedSearchFormSelector + " #member_position",Json_member_position, "== All ==");


		oTable = new create_dataTable(
			selector_dataTable,
			ajaxTarget,
			advancedSearchFormSelector,
			searchInitialize
		);

		// display when hover the row
		oTable.popover("#popover_contents");
	} );

	/* register events of buttons */

    // inline action: switch the user between active and inactive
	/*$(document).on('change', '', function() {

		id = $(this).parent().attr("data-id");
		displayName = ($(this).siblings().get(2)).innerText;
		ajax_available(id);

	});*/

	// detail events =============================================
	// detail: display
	$(document).on('click', '#toolButtons_modalDetail', function() {
		id = $(this).parent().parent().attr("data-id");
        ajax_detailform.read_form(id,'<?php echo site_url('Members/ajax_details');?>');
        clearPicture();
		ajax_detailform.initializeModal();
	});

	// callback of successful save: those callbacks were registered from the form php pages
	function successDetailModal(){
		reload_dataTable(selector_dataTable);
	}

	// create events =============================================
	// create: display
	$(document).on('click', '#button_create', function() {
		ajax_createForm.read_form();
        clearPicture();
		ajax_createForm.initializeModal();
	});

	function successCreateModal(){
		createForm_reset();
		reload_dataTable(selector_dataTable);
	}

	// other events =============================================
	// delete user
	$(document).on('click', '.delete_button', function() {

		id = $(this).parent().parent().attr("data-id");
		displayName = ($(this).parent().siblings().get(2)).innerText;

		$.confirm({
			title: 'Confirm',
			//columnClass: 'large',
			content: 'Are you going to delete the member [' + displayName + "]?",
			buttons: {
				confirm: function () {
					ajax_delete(id);
				},
				cancel: function () {
				}
			}
		});
	});

	// email
	$(document).on('click', '#button_sendEmail', function() {

		var rows_selected  = oTable.get_datatable().column(0).checkboxes.selected();

		var array = [];
		$.each(rows_selected, function(index, rowId){

			email = oTable.get_datatable().cell('#row_'+rowId,4).data();

			if(email !== null)
			{
				array.push(email);
			}
		});
		window.location.replace("mailto:" + array.toString());
	});

	//-------------------AJAX------------------//
	/*function ajax_available(id)
	{
		data = {"member_id": id};

		$.ajax({
			type: "POST",
			url: "<---?php echo site_url('Members/ajax_switchAvailability'); ?>",
			data: data,
			success: function(data)
			{
				reload_dataTable(selector_dataTable);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}*/

	function ajax_delete(id)
	{
		data = {"member_id": id};

		$.ajax({
			type: "POST",
			url: "<?php echo site_url('Members/ajax_delete'); ?>",
			data: data,
			success: function(data)
			{
				reload_dataTable(selector_dataTable);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}

	/* 删了。留着只是参考语法
	function ajax_list_user(id)
	{
		data = {"id": id};

		$.ajax({
			type: "POST",
			url: "<---?php echo site_url('Members/ajax_list_user'); ?>",
			data: data,
			success: function(data)
			{
				var user_list_objects_detail = JSON.parse(data);;
				user_list_objects_detail.unshift( {"id":"","value":"=== Unlink The Account ==="} );
				read_dropdown(selector_detail_user_id, user_list_objects_detail);
				ajax_detailform.read_form(id,'<---?php echo site_url('Members/ajax_details');?>');
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}*/

	// ==========call back functions
	function toolbar(data, type, row, meta)
	{

        toolButtons = '<i id="toolButtons_modalDetail" data-toggle="modal" data-keyboard="true" data-target=".modal" data-backdrop="static" class="material-icons" title="Tag Detail">settings</i>';

        if(row.user_id !== null)
        {
            toolButtons += '<a target="_blank" href="<?=site_url("Users/view_userList_by_id/")?>' +row.user_id+'"><i class="material-icons log_button" title="User">account_circle</i></a>';
        }
        else
        {
            toolButtons += '<i class="material-icons disabled" title="User">account_circle</i>';
        }

        toolButtons += '<i class="material-icons delete_button" title="Delete">delete_forever</i>';

		return 	toolButtons;
	}

	// preview picture
    function callback_list_picture(data, type, row, meta) {

        picName = data?data:"error.png";
        element=thumb(picName);
        return element;
    }

    // member positions
    function callback_list_position(data, type, row, meta) {

        element = "";

        picturesJson = JSON.parse(row.member_position_picture);
        //nameJson = JSON.parse(row.member_position);

        $.each(picturesJson, function(i,v){

            // simply build thumb from tools.js
            element += thumb_small(picturesJson[i]);
        })
        return element;
    }

    // member name
    function callback_list_name(data, type, row, meta) {

        element = "<span style='font-size:18px; font-weight:bold'>" + row.organization_prefix + data + "</span>";
        element += "<br/>";
        element += row.member_nickname;


        return element;
    }

    // shirt number
    function callback_list_shirt_number(data, type, row, meta) {

        element = "<span class=class_shirt_" + row.member_shirt_number_description + ">" + data + "</span>";

        return element;
    }

    // account
    function callback_list_account(data, type, row, meta) {

	    // 用户组放前面方便排序
        var element = "";
        if(data == null)
        {
            element += row.member_code_invited == 1?"<span style='color:red; font-size: 24px'>Invited</span>":"--";

        }
        else
        {
            element += "<div style='font-size:18px; margin-bottom: 5px; border-bottom: 1px solid #EFEFEF;'>" + row.user_group_name + " </div>";
            element += "<span style='font-size:12px;'>" + data+ "</span>";
            element += row.member_code_invited == 1?"<span style='color:red;'> Re-invited</span>":"";
        }



        return element;
    }



</script>


<!--
<div class="popover bs-popover-bottom fade bs-popover-auto show" role="tooltip" style="position: absolute; top: 0px; left: 0px; " x-placement="bottom">

    <div class="popover_inner">
        <div class="arrow" style="left: 606px;"></div>

        <div class="popover-body personal_information">
            <div class="personal_information_header">
                <div class="personal_name" name="member_name">YYY_Yaksha</div>
                <div class="personal_score">
                    综合评价：<span name="member_totalvalue">605</span>
                    <i class="material-icons">
                        star
                    </i>
                </div>
            </div>
            <div class="personal_information_content">
                <div class="personal_picture">
                    <p>
                    <img src="http://localhost:31337/yyyclan//uploads/bf44b58ac00e15dd70d3b158b8c237ca.jpg" height="200" width="200"/></p>
                </div>
                <div class="personal_description">
                    <p name="member_description">说明文字第一行，灰色底</p>
                </div>
                <div class="personal_games">
                    <label>正在玩
                        <i class="material-icons">
                            arrow_right
                        </i>
                    </label>
                    <ul class="personal_tags">
                        <li data-loop-title="tag_title" data-loop = "member_games" data-loop-limit="-1">
                            <span name="tag_name"></span>
                        </li>
                    </ul>
                </div>

                <div class="personal_text">
                    <div class="personal_perks personal_tags_container">
                        <label>
                            <i class="material-icons">
                                flag
                            </i> 特技：
                        </label>
                        <ul class="personal_tags">
                            <li data-loop-title="tag_title" data-loop = "member_perks" data-loop-limit="-1">
                                <img name="tag_picture"/>
                                <span name="tag_name"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="personal_medals personal_tags_container">
                        <label>
                            <i class="material-icons">
                                turned_in
                            </i> 功勋：
                        </label>
                        <ul class="personal_tags">
                            <li data-loop-title="tag_title" data-loop = "member_medals" data-loop-limit="-1">
                                <img name="tag_picture"/>
                                <span name="tag_name"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div></div>
</div>-->