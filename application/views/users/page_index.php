<!--
====================================heading of page====================================
-->
<div class="row page-heading bg-light">
	<div class="col page-title"><h2><?php echo $title; ?></h2></div>

	<div class="col tool-bar">

		<!--
		====================================advanced search====================================
		-->
		<div class="btn-group dropdown dropleft">
			<button class="btn btn-info dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuAdvancedSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Advanced Search
			</button>
			<div class="dropdown-menu search-panel" aria-labelledby="dropdownMenuAdvancedSearch">
				<form class="px-4 py-3" id="advanced_search">
					<input type="hidden" name="user_id" value=""/>
					<div class="form-group">
						<label for="search_date_start">Created Date:</label>

						<div class="input-daterange input-group" id="datepicker_logsearch">
							<input type="text" class="form-control" id="search_date_start" name="search_date_start" />
							<span class="input-group-text input-group-addon"> to </span>
							<input type="text" class="form-control" id="search_date_end" name="search_date_end" />
						</div>
					</div>
					<!--search conditions here-->

				</form>
				<div class="form-group_advanced_search">
					<button form="advanced_search" class="btn btn-primary" id="button_search"><i class="material-icons">search</i> Search</button>
					<button form="advanced_search" class="btn btn-light " id="button_refresh"><i class="material-icons">refresh</i> All</button>
				</div>
			</div>
		</div>
		<!--end: advanced search panel-->
		<button  data-toggle="modal" data-target=".modal" data-backdrop="static" class="btn btn-warning" id="button_modalUserCreate"><i class="material-icons ico_person_add">person_add</i>  New User</button>
	</div>
</div>

<!--
====================================Datatable====================================
-->
<table id="list_users" class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100%">
	<thead>
	<tr>
		<th data-source="user_id" data-filter style="width:10px;">#</th>
		<th data-source="user_group" data-filter >User Group</th>
        <th data-source="user_email" data-filter >Email</th>
        <th data-source="internship_due" data-render="render_user_group" data-filter >Internship</th>
        <th data-source="member_name" data-filter data-render="render_member_name">Member</th>
        <th data-source="user_created" data-filter data-orderby-desc >Created Date</th>
        <th data-source="user_created" data-filte >Last Login</th>

		<th data-source="user_active" data-icon="boolean" data-class="btn_active">Active</th>
		<th data-source="user_id" data-class="edit_column" style="width: 120px;" data-render="toolbar" data-toolbar></th>
	</tr>
	</thead>
</table>
<!--
====================================scripts:====================================
-->
<script>

	// modal size could be set when its open
	var ajax_detailform;
	var ajax_createForm;
	selector_dataTable = "#list_users";

	// generate the Datatable and popup forms
	$(document).ready(function() {

		// initialize the Datatable
		// Advanced search
		searchInitialize = <?=$initSearchData?>;
		for(var obj in searchInitialize)
		{
			$("[name = " +obj +"]").val(searchInitialize[obj]);
		}

		// datepicker
		$('#advanced_search .input-daterange').datepicker(dateFormatSetting());

		// initialize the Datatable
		ajaxTarget = "<?php echo site_url('Users/ajax_listPaging'); ?>";

		advancedSearchFormSelector = "#advanced_search";

		oTable = new create_dataTable(
			selector_dataTable,
			ajaxTarget,
			advancedSearchFormSelector
		);
	} );

	/* register events of buttons */
	// inline action: switch the user between active and inactive
	$(document).on('click', 'td.btn_active', function() {

		id = $(this).parent().attr("data-id");
		displayName = ($(this).siblings().get(2)).innerText;

		$.confirm({
			title: 'Confirm',
			content: 'Are you going to switch the activity of user -' + displayName+ ' ?',
			buttons: {
				confirm: function () {
					ajax_active_user(id);
				},
				cancel: function () {
				}
			}
		});
	});

	// user detail events =============================================
	// user detail: display
	$(document).on('click', '#toolButtons_modalDetail', function() {
		id = $(this).parent().parent().attr("data-id");
		ajax_detailform.read_form(id,'<?php echo site_url('Users/ajax_userDetails');?>');
		ajax_detailform.initializeModal();
	});

	// callback of successful save: those callbacks were registered from the form php pages
	function successDetailModal(){
		reload_dataTable(selector_dataTable);
	}

	function successCreateModal(){

		// function defined in form_modal_user.php, reset the form values and the icons' color
		createForm_reset();
		reload_dataTable(selector_dataTable);
	}

	// create user events =============================================
	// create user: display
	$(document).on('click', '#button_modalUserCreate', function() {
		ajax_createForm.read_form();
		ajax_createForm.initializeModal();
	});


	// other events =============================================
	// delete user
	$(document).on('click', '.delete_button', function() {

		id = $(this).parent().parent().attr("data-id");
		displayName = ($(this).parent().siblings().get(2)).innerText;

		$.confirm({
			title: 'Confirm',
			content: 'Are you going to delete the user [' + displayName + "]?",
			buttons: {
				confirm: function () {
					ajax_delete_user(id);
				},
				cancel: function () {
				}
			}
		});
	});

//-------------------user AJAX------------------//

function ajax_active_user(id)
{
	data = {"user_id": id};

	$.ajax({
		type: "POST",
		url: "<?php echo site_url('Users/ajax_switchActive'); ?>",
		data: data,
		success: function(data)
		{
			reload_dataTable(selector_dataTable);
		}
	}).fail(function(data){
		$("html").html(data.responseText);
	});
}

function ajax_delete_user(id)
{
	data = {"user_id": id};

	$.ajax({
		type: "POST",
		url: "<?php echo site_url('Users/ajax_userDelete'); ?>",
		data: data,
		success: function(data)
		{
			reload_dataTable(selector_dataTable);
		}
	}).fail(function(data){
		$("html").html(data.responseText);
	});
}


// ==========call back functions
function toolbar(data, type, row, meta)
{
	//console.log(data);
	toolButtons = '<i id="toolButtons_modalDetail" data-toggle="modal" data-keyboard="true" data-target=".modal" data-backdrop="static" class="material-icons" title="User Detail">settings</i>';

	// only for admin users
	toolButtons += '<i class="material-icons delete_button" title="Delete">delete_forever</i>';

	//var toolButtons = ' <i class="material-icons log_button" title="User Log">history</i> <i class="material-icons delete_button" title="Delete">delete_forever</i>';
	return 	toolButtons;
}

// 显示实习期是否结束
function render_user_group(data, type, row, meta)
{
    if(row.internship_due == 1)
    {
        return "<span style='color:red; font-size: 20px'>Over</span>";
    }
    else
    {
        return "--";
    }
}

function render_member_name(data, type, row, meta)
{
    if(row.member_id)
    {
        return '<a target="_blank" href="<?=site_url("Members/view_List_by_member_id/")?>' +row.member_id+'">'  + data +"</a>"
    }
    else
    {
        return "--";
    }
}

</script>
