<div class="row page-heading bg-light">
<div class="col page-title"><h2><?php echo $title; ?></h2></div>
</div>

<div class="login_panel" style="width:800px;">
	<div class="modal-content" >


		<?php echo form_open('Members/form_manage_member', array('autocomplete' => 'off', 'id' => 'manage_form', 'enctype' => 'multipart/form-data')); ?>
		<div class="modal-body" >
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

            <div class="form-row">
                <div class="form-group col-md-6">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="member_gamename">游戏内名字</label>
                            <input type="text" class="form-control" data-validation autocomplete="off" id="member_gamename" name="member_gamename"
                                   placeholder="不带前缀(例:Yaksha)">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="member_shirt_number">号码旗</label>
                            <select size="3" data-validation type="text" class="form-control" style="width:100%;" name="member_shirt_number" id="member_shirt_number"></select>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="member_position">比赛时的职业(可多选)</label>
                        <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_position[]" id="member_position"></select>

                    </div>
                    <div class="form-group">
                        <label for="member_games">正在玩的游戏(可多选)</label>
                        <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_games[]" id="member_games"></select>
                    </div>

                    <div class="form-group">
                        <label for="member_description">自我介绍</label>
                        <textarea class="form-control" id="member_description" name="member_description" rows="4"></textarea>
                        <input name="member_picture_display" data-render="preview" data-target="#previewImage" id="member_picture_display" type="hidden"/>
                    </div>

                </div>
                <div class="form-group col-md-6">
                    <div class="form-group">
                    <label for="memberCreateForm-member_picture">上限:400 x 400</label>
                    <input type="file" data-validation data-not-retrive id="member_picture" class="form-control" name="member_picture"/>
                    </div>
                    <div class="form-group">
                        <img src="" height="380" width="380" id="previewImage"/>
                    </div>
                </div>
            </div>



				

		</div>

		<div class="form-group" style="text-align: center; padding-left: 15px; padding-right:15px;">
			<button type="submit" class="btn btn-primary btn-lg" style="width: 100%; ">点击保存</button>
		</div>

		</form>
	</div>
</div>

</div>
<script>


    var Json_member_shirt_number_pick = <?=$member_shirt_number_pick?>;
    var Json_member_position = <?=$member_position?>;
    var Json_member_games = <?=$member_games?>;

    /*
     * This part of codes is for retrive data and display the error message(if submit failed).
     *
    */
	$(document).ready(function() {

        dropdown_member_shirt_number = new read_select2("#member_shirt_number", Json_member_shirt_number_pick, "=== No Number ===");
        dropdown_position = new read_select2("#member_position",Json_member_position);
        dropdown_games = new read_select2("#member_games",Json_member_games);


        // prepare for submit (validation will be run from remote)
		myform = new non_ajax_validation("#manage_form");

		// show error message
		return_json = '<?=$json_error?>';
		myform.show_errors(return_json);
		
		// get data for current user (AJAX)
		myform.read_form(<?=get_user_id()?>, "<?=site_url("Members/ajax_memberDetails_me")?>" );
		
		// after submit, fill values back.
		myform.post_back(return_json);

	});

	function success()
	{
		return null;
	}

    $(document).on('change', '#member_picture', function() {
        changePicture(this, '#previewImage');
    });

    function changePicture(obj, targetSelector)
    {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $(targetSelector).attr('src', e.target.result);
            }

            reader.readAsDataURL(obj.files[0]);
        }
    }

    // from data-render, display the picture
    function preview(item, value)
    {
        targetSelector = $(item).attr("data-target");

        value = value == null? "error.png": value;

        $(targetSelector).attr('src', base_url + "/uploads/" + value);
    }


</script>
