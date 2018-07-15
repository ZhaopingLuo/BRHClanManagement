
<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
<div class="wizard_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_changePassword_sendEmail', array('autocomplete' => 'off', 'id' => 'forgot_form')); ?>
		<div class="modal-body">
			<div class="progress" style="margin-bottom: 20px;">
				<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
					1/4
				</div>
			</div>

			<div class="card-body">
				<h5 class="card-title">Reset Your Password:</h5>
				<p class="card-text">
					Reset the password from the link provided.
				</p>
			</div>
			
			<?php if($errorMessages):?>
				<div class="alert alert-danger" role="alert">
					<?=rawurldecode($errorMessages)?>
				</div>
			<?php endif?>

			<div class="form-group" style="text-align: center;">
				<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Submit</button>
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
