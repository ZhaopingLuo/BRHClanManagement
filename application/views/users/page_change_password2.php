
<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
<div class="wizard_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_changePassword_sendEmail', array('autocomplete' => 'off', 'id' => 'forgot_form')); ?>

		<div class="modal-body">
			<div class="progress" style="margin-bottom: 20px;">
				<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
					2/4
				</div>

			</div>

			<div class="alert alert-success" role="alert">
				<div class="card-body">
					<h5 class="card-title">Check your email inbox</h5>
					<p class="card-text">
						Now a new password has been sent to: <b><?=$user_email?></b>
						<br/>
						Please click on the link in the email to reset your password.
					</p>

				</div>
			</div>
			<p class="card-text">
				<b>If you don't see our email, you can</b>
			</p>
			<div class="form-group" style="text-align: center;">
				<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Resend</button>
			</div>
		</div>
		</form>
	</div>
</div>

</div>
