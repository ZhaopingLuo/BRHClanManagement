<!--
====================================detail/edit form====================================
-->

<div id="tagDetailModal" class="modal-dialog modal-lg" role="document" style="display: none">
	<div class="modal-content">
		<div class="modal-header">
			<h5 id="truckTitle" class="modal-title">Tags</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>

		<div class="modal-body">
			<?php echo form_open('Tags/ajax_update', array('id'=>'tagDetailForm', 'enctype' => 'multipart/form-data')); ?>

			<div class="tab-content" id="myTabContent">

                <input type="hidden" name="tag_category_id"/>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="tagDetailForm-tag_id">Id</label>
						<input data-validation id="tagDetailForm-tag_id" class="form-control" name="tag_id" tabindex="0"/>
					</div>
				</div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tagDetailForm-tag_name">Name</label>
                        <input data-validation id="tagDetailForm-tag_name" class="form-control" name="tag_name"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tagDetailForm-tag_value">Value</label>
                        <input data-validation id="tagDetailForm-tag_value" class="form-control" name="tag_value"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="tagDetailForm-tag_description">Description</label>
                        <textarea class="form-control" id="tagDetailForm-tag_description" name="tag_description" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-row">
					<div class="form-group col-md-12">
						<label for="tagDetailForm-tag_picture">Logo (recommand size: W:400 x H:400)</label>
						<input type="file" data-validation data-not-retrive id="tagDetailForm-tag_picture" class="form-control" name="tag_picture"/>
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

	<div id="tagCreateModal" class="modal-dialog modal-lg" role="document" style="display: none">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create Contact</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<?php echo form_open('Tags/ajax_create', array('id'=>'tagCreateForm')); ?>
				<div class="tab-content" id="myTabContent">
                    <input type="hidden" name="tag_category_id"/>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="tagCreateForm-tag_id">Id</label>
                            <input data-validation id="tagCreateForm-tag_id" class="form-control" name="tag_id" tabindex="0"/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tagCreateForm-tag_name">Name</label>
                            <input data-validation id="tagCreateForm-tag_name" class="form-control" name="tag_name"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tagCreateForm-tag_value">Value</label>
                            <input data-validation id="tagCreateForm-tag_value" class="form-control" name="tag_value"/>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="tagCreateForm-tag_description">Description</label>
                            <textarea class="form-control" id="tagCreateForm-tag_description" name="tag_description" rows="3"></textarea>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="tagCreateForm-tag_picture">Logo (recommand size: W:400 x H:400)</label>
                            <input type="file" data-validation data-not-retrive id="tagCreateForm-tag_picture" class="form-control" name="tag_picture"/>
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

	selector_createModal = '#tagCreateModal';
	selector_createForm = '#tagCreateForm';

	selector_detailForm = '#tagDetailForm';
	selector_detailModal = '#tagDetailModal';


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
