<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>
</div>
<div class="wizard_panel" style="width: 50%">
	<div class="modal-content">
		<div class="modal-body">
			<div class="card-body">
            
          	  <?php echo form_open('users/form_junks', array('autocomplete' => 'off', 'id' => 'forgot_form')); ?>
				<?php if($messages):?>
                    <div class="alert alert-success" role="alert">
                        <?=rawurldecode($messages)?>
                    </div>
    
                <?php endif?>
				<h5 class="card-title">Hi, Administrator</h5>
				<p class="card-text home_info">
					You are currently in the organization: <b><?=get_organization_name()?></b>.
                    <br/>
                    Your database has <b style="font-size:1.5rem"><?=$count_tokens?></b> junk token(s).		
                     <br/>
                    Your database has <b style="font-size:1.5rem"><?=$count_users?></b> inactive user(s) over <?=EXPIRY_USER_ACTIVE/60?> minutes(s).	
		
				</p>
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">Clean All</button>
             </form>  
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
