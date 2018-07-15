
<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
<div class="wizard_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_forgot_sendEmail', array('autocomplete' => 'off', 'id' => 'forgot_form')); ?>
		<div class="modal-body">
			<div class="progress" style="margin-bottom: 20px;">
				<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
					1/4
				</div>
			</div>

			<div class="card-body">
				<h5 class="card-title">Reset Your Password:</h5>
				<p class="card-text">
					Enter your email, and then reset the password from the link provided.
				</p>
			</div>

			<?php if($errorMessages):?>
				<div class="alert alert-danger" role="alert">
					<?=rawurldecode($errorMessages)?>
				</div>
			<?php endif?>

			<div class="form-group">
				<input type="email" class="form-control" data-validation autocomplete="off" id="user_email" name="user_email" aria-describedby="emailHelp"
					   placeholder="Enter email" style="background: rgba(255, 255, 255, 0.3);" >
			</div>
			<div class="form-group" style="text-align: center;">
				<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Submit</button>
			</div>
		</div>

		<div class="modal-footer">
			<div class="col-md-12" style="padding-left: 10px;">
				<a href="<?php echo site_url('users/view_login');?>" style="color: #999">
					<i class="material-icons" style="color: #999">arrow_back</i>
					Back To Login</a>
			</div>
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
