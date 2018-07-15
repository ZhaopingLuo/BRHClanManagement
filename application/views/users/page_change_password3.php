
<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
<div class="wizard_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_forgot_changePassword', array('autocomplete' => 'off', 'id' => 'forgot_form')); ?>
		<div class="modal-body">
			<div class="progress" style="margin-bottom: 20px;">
				<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
					3/4
				</div>
			</div>

			<div class="card-body">
				Password has to be more than 8 characters
			</div>

			<?php if($errorMessages):?>
				<div class="alert alert-danger" role="alert">
					<?=rawurldecode($errorMessages)?>
				</div>

			<?php else:?>

				<input type="hidden" class="form-control" data-validation autocomplete="off" id="user_email" name="user_email" aria-describedby="emailHelp"
					   placeholder="Enter email" style="background: rgba(255, 255, 255, 0.3);" value="<?=rawurldecode($user_email)?>">


				<input type="hidden" class="form-control" data-validation autocomplete="off" name="user_token_key"
					   id="user_token_key" placeholder="varification code" style="background: rgba(255, 255, 255, 0.3);"
					   value="<?=rawurldecode($user_token_key)?>">
				<div class="form-group">
					<label for="user_password">New Password</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_password"
						   id="user_password" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				<div class="form-group">
					<label for="user_confirm">Confirm Password</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_confirm"
						   id="user_confirm" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>

				<div class="form-group form-check" style="padding-left: 20px;">
					<input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
					<label class="form-check-label" for="remember_me" style="cursor: pointer;">Remember Me</label>
				</div>
				<div class="form-group" style="text-align: center;">
					<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Submit</button>
				</div>
			<?php endif?>

		</div>
		</form>
	</div>
</div>

</div>
<script>

	$(document).ready(function() {

		validatingForm = new non_ajax_validation("#forgot_form");

		return_json = '<?=$json_error?>';

		validatingForm.show_errors(return_json);
		validatingForm.post_back(return_json);

	});

	function success()
	{
		return null;
	}
</script>
