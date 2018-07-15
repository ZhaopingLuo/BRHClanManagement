<html>
        <head>
			<title><?= $title?></title>

            <script>
                var base_url = '<?php echo base_url();?>';
            </script>

			<!--used for everything-->
			<!--
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
			-->
            <script type="text/javascript" src="<?php echo base_url('ace_assets/fromremote/jquery.min.js');?>"></script>

			<!--used for data table-->
			<!--
			<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
			<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.10/css/dataTables.checkboxes.css" rel="stylesheet" />
			-->

            <link href="<?php echo base_url('ace_assets/fromremote/jquery.dataTables.min.css');?>" rel="stylesheet">
            <link href="<?php echo base_url('ace_assets/fromremote/dataTables.checkboxes.css');?>" rel="stylesheet">


			<script type="text/javascript" src="<?php echo base_url('ace_assets/js/jquery.dataTables.min.js');?>"></script>
			<script type="text/javascript" src="<?php echo base_url('ace_assets/js/dataTables.checkboxes.min.js');?>"></script>
			<script type="text/javascript" src="<?php echo base_url('ace_assets/js/create_datatable.js');?>"></script>

			<!--used for icons-->
			<link href="<?php echo base_url('ace_assets/fromremote/Material+Icons.css');?>" rel="stylesheet">

            <!--
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
                  rel="stylesheet">
            -->


			<!--used for bootstrap-->	  
			<link href="<?php echo base_url('ace_assets/css/bootstrap.css');?>" rel="stylesheet">  
				  
		</head>
        <body>

<!--
====================================Modal shell====================================
-->
<div class="modal fade" style="display: none" >
</div>
