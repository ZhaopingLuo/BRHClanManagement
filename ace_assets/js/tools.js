$.fn.deserialize = function (serializedString)
{
	var $form = $(this);
	$form[0].reset();
	serializedString = serializedString.replace(/\+/g, '%20');
	var formFieldArray = serializedString.split("&");

	$.each(formFieldArray, function(i, pair){
		var nameValue = pair.split("=");
		var name = decodeURIComponent(nameValue[0]);
		var value = decodeURIComponent(nameValue[1]);
		// Find one or more fields
		var $field = $form.find('[name=' + name + ']');


		if ($field[0].type == "radio"
			|| $field[0].type == "checkbox")
		{
			var $fieldWithValue = $field.filter('[value="' + value + '"]');
			var isFound = ($fieldWithValue.length > 0);
			if (!isFound && value == "on") {
				$field.first().prop("checked", true);
			} else {
				$fieldWithValue.prop("checked", isFound);
			}
		} else {
			$field.val(value);
		}
	});
}

function generatePassword()
{
	var length = 12,
		charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
		retVal = "";
	for (var i = 0, n = charset.length; i < length; ++i) {
		retVal += charset.charAt(Math.floor(Math.random() * n));
	}
	return retVal;
}

/*
*  example: read_dropdown('#organization_id', <?php echo $organizations?>)
* */
function read_dropdown(selector, list)
{
	for(var obj in list)
	{
		$(selector).append($('<option>', {
			value: list[obj]["id"],
			text: list[obj]["value"],
            'data-picture': list[obj]["picture"],
            'data-rank': list[obj]["rank"],
			disabled: list[obj]["readonly"]==1?true:false
		}));
	}
}

function format(state, container) {

    if (!state.id) return state.text; // optgroup

    picture = "<img height='25' class='flag' src='" +base_url + "/uploads/"+ $(state.element).attr('data-picture') +"'/>";

    if (!$(state.element).attr('data-picture')) picture = ""; // optgroup

    rank = (!$(state.element).attr('data-rank')) ? "" : "<span class='tag-rank'>" + $(state.element).attr('data-rank') + "</span>";

    return rank + picture+ state.text;
}

function read_select2(selector, data, defaultValue)
{
	var select2_data;

	if(data.length>0)
	{
		select2_data= data.slice(0);
		
		if(typeof defaultValue !== 'undefined')
		{
			select2_data.unshift( {"id":"","value":defaultValue} );
		}
	}

	read_dropdown(selector, select2_data);

	return new $(selector).select2({
		width : 'resolve',
		dropdownAutoWidth : true,
        templateResult: format,
        templateSelection: format,
        escapeMarkup: function(m) { return m; }
	});
}

function thumb(picName)
{

    return "<p class='thumb_image'><img src= '" + base_url + "/uploads/" + picName +  "'/></p>";
}

function thumb_small(picObj)
{
    // tag_picture, tag_name
    return "<p class='thumb_image' title=' " + picObj.tag_name+" '><img src= '" + base_url + "/uploads/" + picObj.tag_picture +  "'/></p>";
}
/**
 * set the date format for Datepicker (only used on Datepicker, to prevent repeat settings for pages)
 * @returns {{format: string, calendarWeeks: boolean, todayHighlight: boolean, autoclose: boolean, todayBtn: string}}
 */
function dateFormatSetting()
{
	return {
		format: "MM dd, yyyy",
			//endDate: "today",
			calendarWeeks: true,
		todayHighlight: true,
		autoclose: true,
		todayBtn: "linked"
	}
}


/**
 * generate anchors for list pages
 * @param array
 * @returns {string}
 */
function anchor_list(array)
{
	var returnValue = ""
	returnValue += "<div class=\"dropdown show dropleft anchor_toolbar\">";
	returnValue += "\t<i class=\"material-icons\" role=\"button\" id=\"anchor_dropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" title=\"Anchors\">link</i>";
	returnValue += "<div class=\"dropdown-menu\" aria-labelledby=\"anchor_dropdownMenuLink\">";

	for(var obj in array)
	{
		returnValue += "<a target='_blank' class='dropdown-item' href='" + array[obj] + "'>"+ obj +"</a>";
	}

	returnValue +="</div></div>";

	return returnValue;
}


/**
 *  Pre-settings when page loaded
 */
$(document).ready(function() {

	//method_convertDateFormats();


	// refresh the advance search results
	$(document).on('click', '#button_refresh', function () {
		$('#advanced_search')[0].reset();
		reload_dataTable(selector_dataTable);
	});

});

