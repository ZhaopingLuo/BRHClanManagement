<nav class="navbar navbar-expand-lg navbar-dark bg-primary-nav">
  
  <a class="navbar-brand pt-0 pb-0" href="#">
	  <img src="<?=base_url(get_organization_logo_top())?>"/>
  </a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <ul class="navbar-nav mr-auto">
		<?php foreach($nav as $navItem):?>
		
			<?php if(!array_key_exists('Subitems',$navItem) || empty($navItem['Subitems'])):?>
			   <li class="nav-item">
				<a class="nav-link" href="<?=site_url($navItem['Target'])?>"><?=$navItem['Name']?></a>
			  </li>  
			<?php else:?> 
			  
			   <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="<?=site_url($navItem['Target'])?>" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  <?=$navItem['Name']?>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
				  
			      <?php
					  $isFirst = true;
					  foreach($navItem['Subitems'] as $key=>$subNavItem):?>


					  <?php if(array_key_exists('Divider', $subNavItem) && !$isFirst):?>
					  <div class="dropdown-divider"><?=$key?></div>
					<?php endif?>
					<a class="dropdown-item" href="<?=site_url($subNavItem['Target'])?>"><?=$subNavItem['Name']?></a>
				  <?php
					  $isFirst = false;
					  endforeach?>

				</div>
			  </li>
			<?php endif?>
		  
	   <?php endforeach?>
    </ul>

	  <?php if(isset($_SESSION['user_id'])):?>
		  <span class="navbar-nav ml-auto dropdown navbar-text" >
		  
		<?php if(get_user_group_level() >= ADMINISTRATOR):?>
					  <a href="<?=site_url("Organizations/func_cancel_visit")?>" style="color: rgba(255, 255, 255, 0.8); max-width: 180px; margin-top:-5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; vertical-align: middle"><?=get_organization_name()?> - </a>
		<?php endif?>
		  
			  <a class="dropdown-toggle" data-toggle="dropdown" style="cursor:pointer" aria-haspopup="true" aria-expanded="false">
<?=$_SESSION['user_email']?> [<?=$_SESSION['user_group_name']?>]
			  </a>
			  <div class="dropdown-menu dropdown-menu-right">
				  <a class="dropdown-item" href="<?=site_url("Members/view_manage_member")?>" style="color: #212529;">我的资料</a>
				  <a class="dropdown-item" href="<?=site_url("Users/view_changePassword")?>" style="color: #212529;">重设密码</a>
				  <div class="dropdown-divider"></div>
				  <a class="dropdown-item" href="<?php echo site_url('users/func_logout');?>" style="color: #212529;">退出</a>
			  </div>
		  </span>
	  <?php else:?>
	  <span class="navbar-text">
		  <a href="<?php echo site_url('users/view_login');?>">Login</a>
	  </span>
	  <?php endif?>


  </div>
</nav>

<div class="container-fluid">
