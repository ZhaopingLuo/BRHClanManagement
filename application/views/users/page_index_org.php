<!--
====================================heading of page====================================
-->
<div class="row page-heading bg-light">
	<div class="col page-title"><h2><?php echo $title; ?></h2></div>

	<div class="col tool-bar">


		<button  data-toggle="modal" data-target=".modal" data-backdrop="static" class="btn btn-warning" id="button_create"><i class="material-icons">import_contacts</i>  New Organization</button>

	</div>
</div>

<!--
====================================Datatable====================================
-->
<table id="list_organizations" class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100%">
	<thead>
	<tr>
		<th data-source="organization_id" data-filter data-orderby-asc style="width:10px;">#</th>
		<th data-source="organization_logo" data-filter data-render="showLogo">Logo</th>
        <th data-source="organization_name" data-filter>Name</th>
        <th data-source="organization_fullname" data-filter>Full Name</th>
        <th data-source="organization_prefix" data-filter>Prefix</th>
		<th data-source="organization_id" data-class="edit_column" style="width: 120px;" data-render="toolbar" data-toolbar></th>
	</tr>
	</thead>
</table>


<!--
====================================scripts:====================================
-->
<script>

	var ajax_detailform;
	var ajax_createForm;
	selector_dataTable = "#list_organizations";

	// generate the Datatable and popup forms
	$(document).ready(function() {

		// initialize the Datatable
		ajaxTarget = "<?php echo site_url('Organizations/ajax_listPaging'); ?>";

		oTable = new create_dataTable(
			selector_dataTable,
			ajaxTarget
		);
	} );

	/* register events of buttons */
	// detail events =============================================
	// detail: display
	$(document).on('click', '#toolButtons_modalDetail', function() {
		id = $(this).parent().parent().attr("data-id");
		ajax_detailform.read_form(id,'<?php echo site_url('Organizations/ajax_details');?>');
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
		data = {"organization_id": id};

		$.ajax({
			type: "POST",
			url: "<?php echo site_url('Organizations/ajax_delete'); ?>",
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
			url: "<?php echo site_url('Organizations/ajax_generate_id'); ?>",
			success: function(data)
			{
				$("#organizationCreateForm-organization_id").val(data);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}

	function ajax_temp_join()
	{
		data = {"organization_id": id};

		$.ajax({
			type: "POST",
			url: "<?php echo site_url('Organizations/ajax_temp_join'); ?>",
			data: data,
			success: function(data)
			{
				if(data == "1")
				{
					location.reload();
				}
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});
	}

	// ==========call back functions
	function toolbar(data, type, row, meta)
	{
		toolButtons = '<i id="toolButtons_modalDetail" data-toggle="modal" data-keyboard="true" data-target=".modal" data-backdrop="static" class="material-icons" title="Organization Detail">settings</i>';
		toolButtons += '<i id="toolButtons_filter" data-backdrop="static" class="material-icons" title="Temporary Join">domain</i>';
		toolButtons += '<i class="material-icons delete_button" title="Delete">delete_forever</i>';

		return 	toolButtons;
	}

	function showLogo(data, type, row, meta)
	{
		picName = data?data:"error.png";
		
		return "<img src='<?=base_url(UPLOAD_FOLDER)?>/" + picName + "' />";
	}

</script>
