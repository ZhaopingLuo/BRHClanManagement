<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>

<div class="login_panel">
	<div class="modal-content">
		<?php echo form_open('users/form_signup', array('autocomplete' => 'off', 'id' => 'signup_form')); ?>
		<div class="modal-body">
		
				<div class="progress" style="margin-bottom: 20px;">
					<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
						1/2
					</div>
				</div>
				
				<div class="card-body">
					<h5 class="card-title">Sign Up:</h5>
					<p class="card-text">
						1. Enter your email and password.	
						<span style="font-size:11px; color:grey;font-style: italic;"> - Password is between 8 and 32 characters</span><br/> 		
						3. Active your account from your email.
					</p>
				</div>


                <div class="form-group">
                    <label for="member_code">邀请码</label>
                    <input type="text" class="form-control" value="<?=$inviteCode?>" data-validation autocomplete="off" id="member_code" name="member_code" placeholder="邀请码由人事提供" style="background: rgba(255, 255, 255, 0.3);">
                </div>

				<div class="form-group">
					<label for="user_email">Email</label>
					<input type="email" class="form-control" data-validation autocomplete="off" id="user_email" name="user_email" aria-describedby="emailHelp"
						   placeholder="Enter email" style="background: rgba(255, 255, 255, 0.3);">
				</div>

				<div class="form-group">
					<label for="user_password">Password</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_password"
						   id="user_password" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				
				<div class="form-group">
					<label for="user_confirm">Confirm</label>
					<input type="password" class="form-control" data-validation autocomplete="off" name="user_confirm"
						   id="user_confirm" placeholder="Password" style="background: rgba(255, 255, 255, 0.3);">
				</div>
				
				

		</div>

		<div class="form-group" style="text-align: center; padding-left: 15px; padding-right:15px;">
			<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Create Account</button>
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

		loginform = new non_ajax_validation("#signup_form");

		return_json = '<?=$json_error?>';

		loginform.show_errors(return_json);

		loginform.post_back(return_json);

	});

	function success()
	{
		return null;
	}
</script>
