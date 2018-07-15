<!--
====================================detail/edit form====================================
-->

<div id="memberDetailModal" class="modal-dialog modal-lg" role="document" style="display: none">
	<div class="modal-content">
		<div class="modal-header">
			<h5 id="truckTitle" class="modal-title">Member Details</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
<!--
====================================tab menu items====================================
-->

		<ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="ed-information-tab" data-toggle="tab" href="#ed-information" role="tab" aria-controls="ed-information" aria-selected="true">
                    Member Information</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="ed-information_p2-tab" data-toggle="tab" href="#ed-information_p2" role="tab" aria-controls="ed-information_p2" aria-selected="false">
                    Role</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="ed-information_p3-tab" data-toggle="tab" href="#ed-information_p3" role="tab" aria-controls="ed-information_p3" aria-selected="false">
                    User</a>
            </li>
		</ul>

		<div class="modal-body">
			<?php echo form_open('Members/ajax_update', array('id'=>'memberDetailForm')); ?>
			<input type="hidden" id="member_id" name="member_id"/>
            <div class="tab-content" id="myTabContent">

                <!--
                ====================================Tab container 1====================================
                -->
                <div class="tab-pane fade show active" id="ed-information" role="tabpanel" aria-labelledby="ed-information-tab">
                    <div class="alert alert-light" role="alert">
                        游戏中的名字不要带前缀，系统会自动加。新建时只需要Nick Name和邀请码，基础信息让队员自己填
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_nickname">Nick Name *</label>
                            <input data-validation id="memberDetailForm-member_nickname" class="form-control" name="member_nickname" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_gamename">Name in Game(Avatar)</label>
                            <input data-validation id="memberDetailForm-member_gamename" placeholder="Don't put prefix! i.e. Yaksha" class="form-control" name="member_gamename" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_shirt_number">Shirt Number</label>
                            <select data-validation type="text" class="form-control" style="width:100%;" name="member_shirt_number" id="memberDetailForm-member_shirt_number" ></select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_status">Status</label>
                            <select data-validation type="text" class="form-control" style="width:100%;" name="member_status" id="memberDetailForm-member_status" ></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_tagvalue">Medal$Tag Value</label>
                            <input data-validation readonly id="memberDetailForm-member_tagvalue" class="form-control" name="member_tagvalue" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_kpi">KPI</label>
                            <input data-validation id="memberDetailForm-member_kpi" class="form-control" name="member_kpi" value="50" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <div class="form-group">
                                <label for="memberDetailForm-member_picture">Picture (recommand size: W:400 x H:400)</label>
                                <input type="file" data-validation data-not-retrive id="memberDetailForm-member_picture" class="form-control" name="member_picture"/>
                                <input name="member_picture_display" data-render="preview" data-target="#memberDetailForm #previewImage" id="member_picture_display" type="hidden"/>
                            </div>
                            <div class="form-group">
                                <label for="memberDetailForm-member_description">Description</label>
                                <textarea class="form-control" id="memberDetailForm-member_description" name="member_description" rows="5"></textarea>
                            </div>

                        </div>
                        <div class="form-group col-md-4" style="padding-top: 15pt">
                            <img src="" height="240" width="240" id="previewImage"/>
                        </div>
                    </div>
                </div>

                <!--
                ====================================Tab container 2====================================
                -->
                <div class="tab-pane fade" id="ed-information_p2" role="tabpanel" aria-labelledby="ed-information_p2-tab">
                    <div class="alert alert-light" role="alert">
                        这里都是可多选的
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="memberDetailForm-member_position">Position</label>
                            <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_position[]" id="memberDetailForm-member_position"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="memberDetailForm-member_games">Games Playing</label>
                            <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_games[]" id="memberDetailForm-member_games"></select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="memberDetailForm-member_perks">Perks</label>
                            <select data-validation type="text" class="form-control select2_big" multiple style="width:100%;" name="member_perks[]" id="memberDetailForm-member_perks"></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="memberDetailForm-member_medals">Medals</label>
                            <select data-validation type="text" class="form-control select2_big" multiple style="width:100%;" name="member_medals[]" id="memberDetailForm-member_medals"></select>
                        </div>
                    </div>
                </div>

                <!--
                ====================================Tab container 3====================================
                -->
                <div class="tab-pane fade" id="ed-information_p3" role="tabpanel" aria-labelledby="ed-information_p3-tab">
                    <div class="alert alert-light" role="alert">
                        让队员用邀请链接注册自己的邮箱；然后用邮箱登录
                    </div>

                    <div class="form-row">
                        <div class="form-group col-4">
                            <label for="memberDetailForm-member_code">Invite Code</label>
                            <input style="padding-right: 40px;" data-validation type="text" class="form-control" name="member_code" id="memberDetailForm-member_code" placeholder="邀请码" autocomplete="off" value="">
                        </div>
                        <div class="form-group col-0" style=" width: 40px; padding-top: 40px; margin-left: -40px; z-index: 2000" id="detail_button_generateRandomCode">
                            <i class="ico_cache_read material-icons rotating_hover" style=" display: inline-block;cursor: pointer;" title="Random Code">cached</i>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="memberDetailForm-member_code_link">Registration Link</label>
                            <input tabindex="0" data-render="callback_member_code_link" data-validation id="memberDetailForm-member_code_link" class="form-control" name="member_code_link" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-user_id">User Id</label>
                            <input tabindex="0" readonly data-validation id="memberDetailForm-user_id" class="form-control" name="user_id" />
                        </div>

                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_start">Join Date</label>
                            <input data-validation id="memberDetailForm-member_start" class="form-control" placeholder="12-31-1900 (Default today)" name="member_start"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="memberDetailForm-member_end">Leave Date</label>
                            <input data-validation id="memberDetailForm-member_end" class="form-control" placeholder="12-31-1900" name="member_end" />
                        </div>
                    </div>
                </div>


            </div>

			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="save_button">Save changes</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_button">Close</button>
		</div>
	</div>
</div>

<!--
====================================create form====================================
-->

	<div id="memberCreateModal" class="modal-dialog modal-lg" role="document" style="display: none">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create A Member</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
<!--
====================================Tab menu items====================================
-->
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="information-tab" data-toggle="tab" href="#information" role="tab" aria-controls="information" aria-selected="true">
						Member Information</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="information_p2-tab" data-toggle="tab" href="#information_p2" role="tab" aria-controls="information_p2" aria-selected="false">
                        Role</a>
				</li>
                <li class="nav-item">
                    <a class="nav-link" id="information_p3-tab" data-toggle="tab" href="#information_p3" role="tab" aria-controls="information_p3" aria-selected="false">
                        User</a>
                </li>
			</ul>
			<div class="modal-body">
				<?php echo form_open('Members/ajax_create', array('id'=>'memberCreateForm')); ?>
				<div class="tab-content" id="myTabContent">

<!--
====================================Tab container 1====================================
-->
					<div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="information-tab">
						<div class="alert alert-light" role="alert">
							游戏中的名字不要带前缀，系统会自动加。新建时只需要Nick Name和邀请码，基础信息让队员自己填
						</div>

						<div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_nickname">Nick Name *</label>
                                <input data-validation id="memberCreateForm-member_nickname" class="form-control" name="member_nickname" />
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_gamename">Name in Game(Avatar)</label>
                                <input data-validation id="memberCreateForm-member_gamename" placeholder="Don't put prefix! i.e. Yaksha" class="form-control" name="member_gamename" />
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_shirt_number">Shirt Number</label>
                                <select data-validation type="text" class="form-control" style="width:100%;" name="member_shirt_number" id="memberCreateForm-member_shirt_number" ></select>
                            </div>
						</div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_status">Status</label>
                                <select data-validation type="text" class="form-control" style="width:100%;" name="member_status" id="memberCreateForm-member_status" value="1" ></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_tagvalue">Medal$Tag Value</label>
                                <input data-validation readonly id="memberCreateForm-member_tagvalue" class="form-control" name="member_tagvalue" />
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_kpi">KPI</label>
                                <input data-validation id="memberCreateForm-member_kpi" class="form-control" name="member_kpi" value="50" />
                            </div>
                        </div>

						<div class="form-row">
                            <div class="form-group col-md-8">
                                <div class="form-group">
                                        <label for="memberCreateForm-member_picture">Picture (recommand size: W:400 x H:400)</label>
                                        <input type="file" data-validation data-not-retrive id="memberCreateForm-member_picture" class="form-control" name="member_picture"/>

                                </div>
                                <div class="form-group">
                                    <label for="memberCreateForm-member_description">Description</label>
                                    <textarea class="form-control" id="memberCreateForm-member_description" name="member_description" rows="5"></textarea>
                                </div>

                            </div>
                            <div class="form-group col-md-4" style="padding-top: 15pt">
                                <img src="" height="240" width="240" id="previewImage"/>
                            </div>
						</div>
					</div>

<!--
====================================Tab container 2====================================
-->
					<div class="tab-pane fade" id="information_p2" role="tabpanel" aria-labelledby="information_p2-tab">
						<div class="alert alert-light" role="alert">
							这里都是可多选的
						</div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="memberCreateForm-member_position">Position</label>
                                <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_position[]" id="memberCreateForm-member_position"></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="memberCreateForm-member_games">Games Playing</label>
                                <select size="3" data-validation type="text" class="form-control" multiple style="width:100%;" name="member_games[]" id="memberCreateForm-member_games"></select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="memberCreateForm-member_perks">Perks</label>
                                <select data-validation type="text" class="form-control select2_big" multiple style="width:100%;" name="member_perks[]" id="memberCreateForm-member_perks"></select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="memberCreateForm-member_medals">Medals</label>
                                <select data-validation type="text" class="form-control select2_big" multiple style="width:100%;" name="member_medals[]" id="memberCreateForm-member_medals"></select>
                            </div>
                        </div>
					</div>

                    <!--
                    ====================================Tab container 3====================================
                    -->
                    <div class="tab-pane fade" id="information_p3" role="tabpanel" aria-labelledby="information_p3-tab">
                        <div class="alert alert-light" role="alert">
                            让队员用邀请链接注册自己的邮箱；然后用邮箱登录
                        </div>

                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="userCreateForm-member_code">Invite Code</label>
                                <input style="padding-right: 40px;" data-validation type="text" class="form-control" name="member_code" id="userCreateForm-member_code" placeholder="邀请码" autocomplete="off" value="">
                            </div>
                            <div class="form-group col-0" style=" width: 40px; padding-top: 40px; margin-left: -40px; z-index: 2000" id="create_button_generateRandomCode">
                                <i class="ico_cache_read material-icons rotating_hover" style=" display: inline-block;cursor: pointer;" title="Random Code">cached</i>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="memberCreateForm-member_code_link">Registration Link</label>
                                <input tabindex="0" data-validation id="memberCreateForm-member_code_link" class="form-control" name="member_code_link" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-user_id">User Id</label>
                                <input tabindex="0" readonly data-validation id="memberCreateForm-user_id" class="form-control" name="user_id" />
                            </div>

                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_start">Join Date</label>
                                <input data-validation id="memberCreateForm-member_start" class="form-control" placeholder="12-31-1900 (Default today)" name="member_start"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memberCreateForm-member_end">Leave Date</label>
                                <input data-validation id="memberCreateForm-member_end" class="form-control" placeholder="12-31-1900" name="member_end" />
                            </div>
                        </div>
                    </div>


				</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="save_button">Create</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_button">Close</button>
			</div>
		</div>
	</div>




<script>

	selector_createModal = '#memberCreateModal';
	selector_createForm = '#memberCreateForm';

	selector_detailForm = '#memberDetailForm';
	selector_detailModal = '#memberDetailModal';

	selector_create_user_id = ' #memberCreateForm-user_id';
	selector_detail_user_id = ' #memberDetailForm-user_id';

	// generate the Datatable and popup forms
	$(document).ready(function() {


		// initialize dropdownlists

		// datepicker
		$('#memberCreateForm-member_start').datepicker(dateFormatSetting());
		$('#memberCreateForm-member_end').datepicker(dateFormatSetting());


		create_member_shirt_number = new read_select2("#memberCreateForm-member_shirt_number", Json_member_shirt_number_pick, "=== No Number ===");
        create_perks = new read_select2("#memberCreateForm-member_perks",Json_member_perks);
        create_position = new read_select2("#memberCreateForm-member_position",Json_member_position);
        create_medals = new read_select2("#memberCreateForm-member_medals",Json_member_medals);
        create_games = new read_select2("#memberCreateForm-member_games",Json_member_games);
        create_status = new read_dropdown("#memberCreateForm-member_status",Json_member_status);


		$('#memberDetailForm-member_start').datepicker(dateFormatSetting());
		$('#memberDetailForm-member_end').datepicker(dateFormatSetting());

    	detail_member_shirt_number = new read_select2("#memberDetailForm-member_shirt_number", Json_member_shirt_number_pick, "=== No Number ===");
		detail_perks = new read_select2("#memberDetailForm-member_perks",Json_member_perks);
        detail_position = new read_select2("#memberDetailForm-member_position",Json_member_position);
        detail_medals = new read_select2("#memberDetailForm-member_medals",Json_member_medals);
        detail_games = new read_select2("#memberDetailForm-member_games",Json_member_games);
        detail_status = new read_dropdown("#memberDetailForm-member_status",Json_member_status);


		// initialize the Ajax submit
		ajax_detailform = new ajax_validation(selector_detailForm, successDetailModal);
		ajax_detailform.modal_selector=selector_detailModal;
		ajax_detailform.isCloseCheck=true;

		ajax_createForm = new ajax_validation(selector_createForm, successCreateModal);
		ajax_createForm.modal_selector = selector_createModal;

	});

	//=============events=================
	// detail: save
	$(document).on('click', selector_detailModal + ' #save_button', function() {
		ajax_detailform.submit();
	});

	// preview
    $(document).on('change', '#memberCreateForm-member_picture', function() {
        changePicture(this, '#memberCreateForm #previewImage');
    });

    // preview
    $(document).on('change', '#memberDetailForm-member_picture', function() {
        changePicture(this, '#memberDetailForm #previewImage');
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

    function clearPicture()
    {
        $("#memberDetailForm #previewImage").attr('src', base_url + '/uploads/error.png');
        $("#memberCreateForm #previewImage").attr('src', base_url + '/uploads/error.png');
    }

	// submit
	$(document).on('click', selector_createModal + ' #save_button', function() {
		ajax_createForm.submit();
	});

    // generate code
    $(document).on('click', '#create_button_generateRandomCode', function() {
        generateNewCode(selector_createForm);
    });

    // generate code
    $(document).on('click', '#detail_button_generateRandomCode', function() {
        generateNewCode(selector_detailForm);
    });

	/**
	 * A callback function to reset all icons for the Create User form
	 */
	function createForm_reset()
	{
		ajax_createForm.reset_form();
		$("#button_generateRandomCode i").removeClass("rotating");
        clearPicture();
	}

	/**
	 * Generate a new random password and fill into the item
	 */
	function generateNewCode(myselector)
	{
	    console.log(myselector);
	    inviteCode = generatePassword();
        url = "<?php echo site_url('users/view_invite_signup')?>/" + inviteCode;

		$(myselector + " input[name='member_code']").val(inviteCode);
        $(myselector + " input[name='member_code_link']").val(url);

        $(myselector + " input[name='member_code_link']").select();
	}

	// from data-render, display the picture
	function preview(item, value)
    {
        targetSelector = $(item).attr("data-target");

        value = value == null? "error.png": value;

        $(targetSelector).attr('src', base_url + "/uploads/" + value);
    }

    /*
    读数据的时候显示邀请链接
     */
    function callback_member_code_link(item, value){

        inviteCode = value;

        url = "<?php echo site_url('users/view_invite_signup')?>/" + inviteCode;
        $(selector_detailForm + " input[name='member_code_link']").val(url);
    }

</script>
