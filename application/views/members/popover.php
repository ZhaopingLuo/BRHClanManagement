<!--
====================================Popover content template====================================
The return value of the function will be displayed in the popover as HTML format(need to return a full HTML content)
-->

<!--
how do I make template?
	matches the name attributes from JSON. e.g. {"id" : 10}, so the tag could be <li name="id"> or <span name="id"> or any else
	the data-loop is for array(or hashes); the tag with "data-loop" itself will be loop
-->

<!--count number-->
<div id="popover_contents" class="popover_inner popover_contents"
	data-source="<?php echo site_url('Members/ajax_popover'); ?>">
    <div class="personal_information_header">
        <div class="personal_name" name="member_name"></div>
        <div class="personal_score">
            综合评价：<span name="member_totalvalue"></span>
            <i class="material-icons">
                star
            </i>
        </div>
    </div>
    <div class="personal_information_content">
        <div class="personal_picture">
            <p>
                <img src="<?=base_url("/uploads/")?>" height="200" width="200" name="member_picture"/></p>
        </div>
        <div class="personal_description">
            <p name="member_description">说明文字第一行，灰色底</p>
        </div>
        <div class="personal_games">
            <label>正在玩:
            </label>
            <ul class="personal_tags">
                <li data-loop-title="tag_title" data-loop = "member_games" data-loop-limit="-1">
                    <img src="<?=base_url("/uploads/")?>" name="tag_picture"/>
                    <!--<span name="tag_name"></span>-->
                </li>
            </ul>
        </div>

        <div class="personal_text">
            <div class="personal_perks personal_tags_container">
                <label>
                    <i class="material-icons">
                        flag
                    </i> 特技：
                </label>
                <ul class="personal_tags">
                    <li data-loop-title="tag_title" data-loop = "member_perks" data-loop-limit="-1">
                        <img src="<?=base_url("/uploads/")?>" name="tag_picture"/>
                        <span name="tag_name"></span>
                    </li>
                </ul>
            </div>
            <div class="personal_medals personal_tags_container">
                <label>
                    <i class="material-icons">
                        turned_in
                    </i> 功勋：
                </label>
                <ul class="personal_tags">
                    <li data-loop-title="tag_title" data-loop = "member_medals" data-loop-limit="-1">
                        <img src="<?=base_url("/uploads/")?>" name="tag_picture"/>
                        <span name="tag_name"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>


</div>

<style>
	#popover_contents{
		display:none;
	}
	.popover_inner{

	}
	.contact_title{
		font-size: 16px;
	}

	.popover_inner .address:after{
		content:"\A";
		white-space: pre;
	}

	.popover_inner li{
		line-height: 30px;}
</style>

<!--
    <div class="popover-header">
		<span name="count_tasks">0</span> <b><span>Current arrangement(s)</span></b>
	</div>
	<ul class="list-group list-group-flush">
		<li data-loop = "tasks"
			data-loop-limit =-1
			class="list-group-item">
			<span name="task_description" class="contact_data"></span>
			<br/>
			<span name="task_due" >Due: <i class="material-icons">av_timer</i> </span>
			<br/>
			Des: <a href="" name="location_id" target="_blank">
				<span name="address" class="address"><i class="material-icons">location_on</i> </span>
			</a>

		</li>
	</ul>
    -->