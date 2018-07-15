<!--
====================================detail/edit form====================================
-->

<div id="organizationDetailModal" class="modal-dialog modal-lg" role="document" style="display: none">
	<div class="modal-content">
		<div class="modal-header">
			<h5 id="truckTitle" class="modal-title">User Details</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>

		<div class="modal-body">
			<?php echo form_open('Organizations/ajax_update', array('id'=>'organizationDetailForm', 'enctype' => 'multipart/form-data')); ?>

			<div class="tab-content" id="myTabContent">
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="organizationDetailForm-organization_id">Id</label>
						<input data-validation readonly id="organizationDetailForm-contact_title" class="form-control" name="organization_id" tabindex="0"/>
					</div>

				</div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="organizationDetailForm-organization_name">Name</label>
                        <input data-validation id="organizationDetailForm-organization_name" class="form-control" name="organization_name"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="organizationDetailForm-organization_fullname">Full Name</label>
                        <input data-validation id="organizationDetailForm-organization_fullname" placeholder="name in original language" class="form-control" name="organization_fullname"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="organizationDetailForm-organization_prefix">Prefix</label>
                        <input data-validation id="organizationDetailForm-organization_prefix" placeholder="used to display member's name" class="form-control" name="organization_prefix"/>
                    </div>
                </div>

                <div class="form-row">
					<div class="form-group col-md-12">
						<label for="organizationDetailForm-organization_logo">Logo (recommand size: W:110 x H:30)</label>
						<input type="file" data-validation data-not-retrive id="organizationDetailForm-organization_logo" class="form-control" name="organization_logo"/>
					</div>
				</div>

			</div>

			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="save_button">Save changes</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_button">Close</button>
		</div>
	</div>
</div>



<!--
====================================create form====================================
-->

	<div id="organizationCreateModal" class="modal-dialog modal-lg" role="document" style="display: none">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create Contact</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<?php echo form_open('Organizations/ajax_create', array('id'=>'organizationCreateForm')); ?>
				<div class="tab-content" id="myTabContent">

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="organizationCreateForm-organization_id">Id</label>
							<input data-validation id="organizationCreateForm-organization_id" class="form-control" name="organization_id" tabindex="0"/>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="organizationCreateForm-organization_name">Name</label>
							<input data-validation id="organizationCreateForm-organization_name" class="form-control" name="organization_name" tabindex="0"/>
						</div>
					</div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="organizationCreateForm-organization_fullname">Full Name</label>
                            <input data-validation id="organizationCreateForm-organization_fullname" placeholder="name in original language" class="form-control" name="organization_fullname"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="organizationCreateForm-organization_prefix">Prefix</label>
                            <input data-validation id="organizationCreateForm-organization_prefix" placeholder="used to display member's name" class="form-control" name="organization_prefix"/>
                        </div>
                    </div>
				</div>


				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="save_button">Create</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_button">Close</button>
			</div>
		</div>
	</div>

<script>

	selector_createModal = '#organizationCreateModal';
	selector_createForm = '#organizationCreateForm';

	selector_detailForm = '#organizationDetailForm';
	selector_detailModal = '#organizationDetailModal';


	// generate the Datatable and popup forms
	$(document).ready(function() {

		ajax_createForm = new ajax_validation(selector_createForm, successCreateModal);
		ajax_createForm.modal_selector = selector_createModal;

		// initialize the Ajax submit
		ajax_detailform = new ajax_validation(selector_detailForm, successDetailModal);
		ajax_detailform.modal_selector=selector_detailModal;
		ajax_detailform.isCloseCheck=true;
	});

	//=============events=================
	// detail: save
	$(document).on('click', selector_detailModal + ' #save_button', function() {
		ajax_detailform.submit();
	});

	// submit
	$(document).on('click', selector_createModal + ' #save_button', function() {
		ajax_createForm.submit();
	});

	/**
	 * A callback function to reset all icons for the Create form
	 */
	function createForm_reset()
	{
		ajax_createForm.reset_form();
	}

</script>
