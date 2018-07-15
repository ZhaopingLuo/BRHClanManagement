<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
</div>
<div class="wizard_panel" style="width: 80%">
	<div class="modal-content">
		<div class="modal-body">
			<div class="card-body">
				<h5 class="card-title">Welcome!</h5>
				<p class="card-text home_info">
					Please active your account before you can use it. <br/>
					
					<a href="<?php echo site_url('users/view_login');?>" style="color: #999">
					<i class="material-icons" style="color: #999">arrow_back</i>
					Back To Login</a>
				</p>
			</div>
		</div>
	</div>
</div>

<style>
.home_info label{
	display:inline-block;
	width: 200px;
	font-weight:bold;
}
.home_info span{
	display:inline-block;
	text-align:center;
	width: 50px;
	font-weight:bold;
}


</style>
