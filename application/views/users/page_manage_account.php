<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>

<div class="login_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_manage_account', array('autocomplete' => 'off', 'id' => 'manage_form')); ?>
		<div class="modal-body">
		
				
				<div class="card-body">
					<h5 class="card-title">Hi, <?=get_user_email()?>:</h5>
					<p class="card-text">
						You can manage your account here. 
						<br/>
						<b style="color:red">
                            Right now we don't have any information could be managed, so this page is just a template for your future development.
                            There is no responsive codes on the server side.

						</b>
						<br/><br/>
						You may want to delete the entrance when your users table have no additional information to be modified<br/>
						The code of the entrance is in the file: views/users/inc_navigation.php
						
					</p>
				</div>

				<?php if($status == 0):?>
				<div class="alert alert-danger" role="alert">
					<?=rawurldecode($messages)?>
				</div>
				<?php endif?>
				
				<?php if($status == 1):?>
				<div class="alert alert-success" role="alert">
					<?=rawurldecode($messages)?>
				</div>
				<?php endif?>
				
				
				
				<div class="form-group">
					<label for="user_email">Email</label>
					<input type="text" class="form-control" data-validation autocomplete="off" id="user_first_name" name="user_email" 
						   placeholder="" style="background: rgba(255, 255, 255, 0.3);">
				</div>

				
				<div class="form-group">
					<label for="user_first_name">First Name</label>
					<input type="text" class="form-control" data-validation autocomplete="off" id="user_first_name" name="user_first_name" 
						   placeholder="Enter your first name" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				

		</div>

		<div class="form-group" style="text-align: center; padding-left: 15px; padding-right:15px;">
			<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Save</button>
		</div>

		</form>
	</div>
</div>

</div>
<script>

	/*
	 * This part of codes is for retrive data and display the error message(if submit failed).
	 * 
	*/
	$(document).ready(function() {

		// prepare for submit (validation will be run from remote)
		myform = new non_ajax_validation("#manage_form");

		// show error message
		return_json = '<?=$json_error?>';
		myform.show_errors(return_json);
		
		// get data for current user (AJAX)
		myform.read_form(<?=get_user_id()?>, "<?=site_url("Users/ajax_userDetails_me")?>" );
		
		// after submit, fill values back.
		myform.post_back(return_json);

	});

	function success()
	{
		return null;
	}
</script>
