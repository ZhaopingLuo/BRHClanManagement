<!--
====================================user edit form====================================
-->

<div id="userDetailModal" class="modal-dialog modal-lg" role="document" style="display: none">
	<div class="modal-content">
		<div class="modal-header">
			<h5 id="truckTitle" class="modal-title">User Details</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<?php echo form_open('Users/ajax_update', array('id'=>'userDetailForm')); ?>
			<input type="hidden" id="user_id" name="user_id"/>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="userDetailForm-organization_id">Organization</label>
					<select data-validation type="text" class="form-control" style="width:100%;" name="organization_id" id="userDetailForm-organization_id" placeholder="" autocomplete="off" tabindex="0"></select>
				</div>
				<div class="form-group col-md-6">
					<label for="userDetailForm-user_group_id">User Group</label>
					<select data-validation id="userDetailForm-user_group_id" style="width:100%;" class="form-control" name="user_group_id" tabindex="1">
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="userDetailForm-user_email">Email</label>
					<input data-validation type="email" readonly class="form-control" name="user_email" id="userDetailForm-user_email" placeholder="email@randomtransport.com" autocomplete="off" value="">
				</div>
				<div class="form-group col-md-6">
					<label for="userDetailForm-user_password">Password <span class="text-danger">(leave it if not changing)</span></label>
					<input data-validation data-not-retrive type="text" class="form-control" name="user_password" id="userDetailForm-user_password" placeholder="******" autocomplete="off" value="">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="userDetailForm-user_created">Created Time</label>
					<input type="text" readonly class="form-control date_format" name="user_created" id="userDetailForm-user_created" placeholder="" autocomplete="off" value="">
				</div>
				<div class="form-group col-md-6">
					<label for="userDetailForm-user_last_login">Last Login Time</label>
					<input type="text" readonly class="form-control date_format" name="user_last_login" id="userDetailForm-user_last_login" placeholder="" autocomplete="off" value="">
				</div>
			</div>
			<div class="form-group" >
				<div class="form-check">
					<input type="checkbox" class="form-check-input" name="user_active" id="userDetailForm-user_active" value="1"/>
					<label class="form-check-label" for="userDetailForm-user_active">
						Active
					</label>
				</div>
			</div>

			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary detail_save_button" id="detail_save_button">Save changes</button>
			<button type="button" class="btn btn-secondary detail_close_button" data-dismiss="modal" id="detail_close_button">Close</button>
		</div>
	</div>
</div>

<!--
====================================user edit form====================================
-->

	<div id="userCreateModal" class="modal-dialog modal-lg" role="document" style="display: none">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="truckTitle" class="modal-title">Create A New User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php echo form_open('Users/ajax_create', array('id'=>'userCreateForm')); ?>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="userCreateForm-organization_id">Organization</label>
						<select data-validation type="text" class="form-control" style="width:100%;" name="organization_id" id="userCreateForm-organization_id" placeholder="" autocomplete="off" tabindex="0"></select>
					</div>
					<div class="form-group col-md-6">
						<label for="userCreateForm-user_group_id">User Group</label>
						<select data-validation id="userCreateForm-user_group_id" style="width:100%;" class="form-control" name="user_group_id" data-render="edit_user_group_render" tabindex="1">
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-6">
						<label for="userCreateForm-user_email">Email</label>
						<input style="padding-right: 40px;"  data-validation type="email" class="form-control" name="user_email" id="userCreateForm-user_email" placeholder="email@randomtransport.com" autocomplete="off" value="">
					</div>

					<div class="form-group col-0" style="padding-top: 40px; margin-left: -40px; width: 40px; z-index: 2000" id="button_checkEmail">
						<i class="ico_help_outline material-icons" id="button_checkEmail_ico" style=" display: inline-block;cursor: pointer; z-index: 2000" title="Check Email">help_outline</i>
					</div>

					<div class="form-group col-6">
						<label for="userCreateForm-user_password">Password</label>
						<input style="padding-right: 40px;" data-validation data-not-retrive type="text" class="form-control" name="user_password" id="userCreateForm-user_password" placeholder="******" autocomplete="off" value="">
					</div>

					<div class="form-group col-0" style=" width: 40px; padding-top: 40px; margin-left: -40px; z-index: 2000" id="button_generateRandomPassword">
						<i class="ico_cache_read material-icons rotating_hover" style=" display: inline-block;cursor: pointer;" title="Random Password">cached</i>
					</div>
				</div>

				<div class="form-group" >
					<div class="form-check">
						<input type="checkbox" class="form-check-input" name="user_active" id="userCreateForm-user_active" value="1" checked/>
						<label class="form-check-label" for="userCreateForm-user_active">
							Active
						</label>
					</div>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" name="is_email_inform" id="userCreateForm-is_email_inform" value="1" checked/>
						<label class="form-check-label" for="userCreateForm-is_email_inform">
							Send the password to user's email
						</label>
					</div>
				</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="userCreateForm-save_button">Create</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" id="userCreateForm-close_button">Close</button>
			</div>
		</div>
	</div>

<script>
	selector_detail_organization_id = '#userDetailForm #userDetailForm-organization_id';
	selector_detail_user_group_id = '#userDetailForm #userDetailForm-user_group_id';
	selector_detailForm = '#userDetailForm';
	selector_detailModal = '#userDetailModal';

	selector_create_organization_id = '#userCreateForm #userCreateForm-organization_id';
	selector_create_user_group_id = '#userCreateForm #userCreateForm-user_group_id';
	selector_createModal = '#userCreateModal';
	selector_createForm = '#userCreateForm';


	// generate the Datatable and popup forms
	$(document).ready(function() {

		// initialize the select12
		select2_detail_organization = read_select2("#userDetailForm-organization_id", <?php echo $organizations?>);
		select2_create_organization = read_select2("#userCreateForm-organization_id", <?php echo $organizations?>);
	
		select2_detail_usergroup = read_select2("#userDetailForm-user_group_id", <?php echo $user_groups?>);
		select2_create_usergroup = read_select2("#userCreateForm-user_group_id", <?php echo $user_groups?>);

		// initialize the Ajax submit
		ajax_detailform = new ajax_validation(selector_detailForm, successDetailModal);
		ajax_detailform.modal_selector=selector_detailModal;
		ajax_detailform.isCloseCheck=true;

		ajax_createForm = new ajax_validation(selector_createForm, successCreateModal);
		ajax_createForm.modal_selector = selector_createModal;
	});

	// user detail: save
	$(document).on('click', '.detail_save_button', function() {
		ajax_detailform.submit();
	});


	// submit
	$(document).on('click', '#userCreateForm-save_button', function() {
		ajax_createForm.submit();
	});

	$(document).on('paste', '#userCreateForm-user_email', function() {
		checkEmailExisting($(this).val());
	});

	$(document).on('keyup', '#userCreateForm-user_email', function() {
		checkEmailExisting($(this).val());
	});

	$(document).on('blur', '#userCreateForm-user_email', function() {
		checkEmailExisting($(this).val());
	});


	$(document).on('click', '#button_checkEmail', function() {
		checkEmailExisting($('#userCreateForm-user_email').val());
	});

	$(document).on('click', '#button_generateRandomPassword', function() {
		//$("#button_generateRandomPassword i").addClass("rotating");
		generateNewPassword();
	});

	/**
	 * A callback function to reset all icons for the Create User form
	 */
	function createForm_reset()
	{
		ajax_createForm.reset_form();
		$("#button_generateRandomPassword i").removeClass("rotating");
		$("#button_checkEmail_ico").html("help_outline");
		$("#button_checkEmail_ico").removeClass("ico_done").removeClass("ico_highlight_off").removeClass("ico_help_outline").addClass("ico_help_outline");
	}

	/**
	 * Generate a new random password and fill into the item
	 */
	function generateNewPassword()
	{
		$(selector_createForm + " [name='user_password']").val(generatePassword());
	}

	/**
	 * check if email exists
	 */
	function checkEmailExisting(value)
	{
		$.ajax({
			type: 'POST',
			data:{"user_email": value},
			url: '<?php echo site_url('Users/ajax_emailExists');?>',
			success: function (result) {

				errorElement = $("#userCreateForm-user_email");

				errorId =$(errorElement).attr("name") + "_errorMessage";
				$("#"+errorId).remove();

				if(result)
				{
					var obj = document.createElement("div");

					obj.setAttribute("id", errorId);
					obj.setAttribute("class", "invalid-feedback");
					obj.textContent= result;

					$(errorElement).after(obj);
					$(errorElement).addClass("is-invalid", 1000, "easeOutBounce");
					$("#button_checkEmail_ico").html("highlight_off");
					$("#button_checkEmail_ico").removeClass("ico_help_outline").removeClass("ico_done").addClass("ico_highlight_off");

				}else
				{
					$(errorElement).removeClass("is-invalid").addClass("is-valid", 1000, "easeOutBounce");
					$("#button_checkEmail_ico").html("done");
					$("#button_checkEmail_ico").removeClass("ico_help_outline").removeClass("ico_highlight_off").addClass("ico_done");
				}
			}
		}).fail(function (result) {
			$("html").html(result.responseText);
		});
	}
</script>
