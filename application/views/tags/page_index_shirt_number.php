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

            <div class="dropdown-menu search-panel" aria-labelledby="dropdownMenuAdvancedSearch">
                <form class="px-4 py-3" id="advanced_search">


                    <input type="hidden" name="tag_category_id" value=""/>

                    <!--这个别删，从别的页面访问用-->

                </form>
                <div class="form-group_advanced_search">
                    <button form="advanced_search" class="btn btn-primary" id="button_search"><i class="material-icons">search</i> Search</button>
                    <button form="advanced_search" class="btn btn-light " id="button_refresh"><i class="material-icons">refresh</i> All</button>
                </div>
            </div>
        </div>
        <!--end: advanced search panel-->

		<button  data-toggle="modal" data-target=".modal" data-backdrop="static" class="btn btn-warning" id="button_create"><i class="material-icons">import_contacts</i>  New Tag</button>

	</div>
</div>

<!--
====================================Datatable====================================
-->
<table id="list_tags" class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100%">
	<thead>
	<tr>
		<th data-source="tag_id" data-filter data-orderby-asc style="width:10px;">#</th>
        <th data-class="index_list_center" data-source="tag_name" data-filter data-render="callback_list_shirt_number">Name</th>
        <th data-source="tag_description" data-filter>Description</th>
        <th data-source="tag_value" data-filter>Value</th>
        <th data-source="member_name" data-filter>Member</th>

		<th data-source="tag_id" data-class="edit_column" style="width: 120px;" data-render="toolbar" data-toolbar></th>
	</tr>
	</thead>
</table>


<!--
====================================scripts:====================================
-->
<script>

	var ajax_detailform;
	var ajax_createForm;
	selector_dataTable = "#list_tags";

	// generate the Datatable and popup forms
	$(document).ready(function() {

		// initialize the Datatable
		ajaxTarget = "<?php echo site_url('Tags/ajax_listPaging'); ?>";

        // Advanced search
        searchInitialize = <?=$initSearchData?>;
        for(var obj in searchInitialize)
        {
            $("[name = " +obj +"]").val(searchInitialize[obj]);
        }

        advancedSearchFormSelector = "#advanced_search";

		oTable = new create_dataTable(
			selector_dataTable,
			ajaxTarget,
            advancedSearchFormSelector
		);
	} );

	/* register events of buttons */
	// detail events =============================================
	// detail: display
	$(document).on('click', '#toolButtons_modalDetail', function() {
		id = $(this).parent().parent().attr("data-id");
		ajax_detailform.read_form(id,'<?php echo site_url('Tags/ajax_details');?>');
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
		ajax_createForm.initializeModal();
		ajax_generate_id();
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
			content: 'Are you going to delete [' + displayName + "]?",
			buttons: {
				confirm: function () {
					ajax_delete(id);
				},
				cancel: function () {
				}
			}
		});
	});

	$(document).on('click', '#toolButtons_filter', function() {
		id = $(this).parent().parent().attr("data-id");
		ajax_temp_join(id);
	});

	//-------------------AJAX------------------//
	function ajax_delete(id)
	{
		data = {"tag_id": id};

		$.ajax({
			type: "POST",
			url: "<?php echo site_url('Tags/ajax_delete'); ?>",
			data: data,
			success: function(data)
			{
				reload_dataTable(selector_dataTable);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}

	function ajax_generate_id()
	{
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('Tags/ajax_generate_id'); ?>",
			success: function(data)
			{
				$("#tagCreateForm-tag_id").val(data);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}

	// ==========call back functions
	function toolbar(data, type, row, meta)
	{
		toolButtons = '<i id="toolButtons_modalDetail" data-toggle="modal" data-keyboard="true" data-target=".modal" data-backdrop="static" class="material-icons" title="Tag Detail">settings</i>';
		toolButtons += '<i class="material-icons delete_button" title="Delete">delete_forever</i>';

		return 	toolButtons;
	}

	function showLogo(data, type, row, meta)
	{
		picName = data?data:"error.png";

		return "<img src='<?=base_url(UPLOAD_FOLDER)?>/" + picName + "' />";
	}

    // shirt number
    function callback_list_shirt_number(data, type, row, meta) {

        element = "<span class=class_shirt_" + row.tag_description + ">" + data + "</span>";

        return element;
    }

</script>
