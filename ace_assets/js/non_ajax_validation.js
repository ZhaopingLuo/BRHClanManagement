/*
*  Author: Zhaoping Luo
*  The non ajax validation working with codeigniter and bootstrap
*
*  start, tie an ajax form with this class
*  non_ajax_validation(form_selector, fn)
*
*  items with these type will be processed
*  data-not-retrive : will be keep empty when getting data
*  data-validation : will be validated when submitting data
*  data-render : will call a function, passing the element and the value in, instead of just put a value in
*
*  if need to clear all error messages showing:
*  obj.clear_errors()
* */


/**
 * initialize the ajax form
 * @param fn : callback function after save
 * @param form_selector : selector of the form
 * @returns {ajax_validation}
 */
function non_ajax_validation(form_selector_val, fn) {

	var form_selector;

	// set default value
	this.form_selector = "form";

	this.__defineSetter__('form_selector', function (val) {
		form_selector = val;
	});

	//=================================pass arguments in
	if (typeof form_selector_val !== 'undefined') {
		this.form_selector = form_selector_val;
	}


	this.show_errors = function(data){
		fill_error_from_json(data);
	}

	// AJAX callback
	function fill_error_from_json(data)
	{
		// if json parse fails, means there is an parsing error. Show the error page
		try {

			json = JSON.parse(data);

			// error messages will be attached under inputs
			errorMessages = json.messages;

			// clear all error messages
			$( form_selector +" .invalid-feedback" ).remove();

			// reset all "is-invalid" classes
			$( form_selector +" .is-invalid" ).removeClass( "is-invalid", 1000, "easeInBack" );

			if(json.success == true)
			{
				/* if we need to display successfull items instead of just submit directly:
					$( form_selector +" .form-control" ).addClass("is-valid", 1000, "easeOutBounce")
					*/
				if(fn != null)
				{
					fn();
				}
			}
			else
			{
				// loop controls for the form
				$( form_selector + " [data-validation]" ).each(function(index) {

					if(this.name in errorMessages){

						var message = errorMessages[this.name];
						var obj = document.createElement("div");

						obj.setAttribute("id", this.name + "_errorMessage");
						obj.setAttribute("class", "invalid-feedback");
						obj.textContent= message;

						$(this).after(obj);
						$(this).addClass("is-invalid", 1000, "easeOutBounce");
					}
					else
					{
						$(this).addClass("is-valid", 1000, "easeOutBounce");
					}
				});
			}
		} catch(e){

		}
	}

	this.post_back = function(data){
		fill_post_back(data);
	}

	function fill_post_back(data)
	{
		try {
			json = JSON.parse(data);

			post_backs = json.post_back;

			for (var obj in post_backs)
			{
				inputItem = $(form_selector + ' [name=' + obj + ']');

				// if it exsits in the form, if its not unretrivable column
				if($(inputItem).length) // if element exists
				{
					value = post_backs[obj];

					// if need to be filled in
					if($(inputItem).attr("type") == "checkbox") // if its a check box of boolean
					{
						checkValue = value == "0" ? false:true;
						$(inputItem).val(1);
						$(inputItem).prop('checked', checkValue);
					}
					else
					{
						$(inputItem).val(post_backs[obj]);

						// need this to refresh the select list, display the data
						$(inputItem).trigger("change");
					}
				}
			}
		}catch(e){

		}
	}

	// clear all error messages. will be used when user close the panel
	this.clear_errors =  function()
	{
		clear_errors();
	}

	function clear_errors()
	{
		for(var item in errorMessageIds) {
			$("#" + errorMessageIds[item] + "_errorMessage").remove();
		}
		$( form_selector + " [data-validation]").removeClass("is-invalid");
		$( form_selector + " [data-validation]" ).removeClass("is-valid");
	}

	this.submit = function()
	{
		$(form_selector).submit();
	}

	// retriving data from remote when open the form
	this.read_form = function(id, url){

		$.ajax({
			type:'POST',
			data:{"id": id},
			form_selector : form_selector,
			url: url,
			success:function (result) {

				// get results
				try {
					var resultJSON = JSON.parse(result);
					// loop the results
					for(var obj in resultJSON)
					{
						inputItem = $(form_selector + ' [name=' + obj + ']'+ ',' + form_selector + ' [name="' + obj + '[]"]');

						// if it exsits in the form, if its not unretrivable column
						if($(inputItem).length) // if element exists
						{
							value = resultJSON[obj];

							// if need to be filled in
							if($(inputItem).attr("data-not-retrive") !== undefined ) // if it will be keep blank
							{
								$(inputItem).val("");
							}
							else if($(inputItem).attr("type") == "checkbox") // if its a check box of boolean
							{
								checkValue = value == "0" ? false:true;
								$(inputItem).val(1);
								$(inputItem).prop('checked', checkValue);
							}
							else if($(inputItem).attr("data-render") !== undefined ) // if it will be keep blank
							{
								$htmlValue = window[$(inputItem).attr("data-render")](inputItem, value);
							}

                            else if($(inputItem).attr("multiple") !== undefined ) // if it will be keep blank
                            {
                                var values=value.split(",");
                                $(inputItem).val(values).trigger('change');
                            }
							else
							{
								$(inputItem).val(resultJSON[obj]);

								// need this to refresh the select list, display the data
								$(inputItem).trigger("change");
							}
						}
					}
					originalFormValues = $(form_selector).serialize();
				}catch(err)
				{
					$("html").html(result);
				}
			}
		}).fail(function(result){
			$("html").html(result.responseText);
		});
	}

	// save data when click ctrl+s
	$(window).bind('keydown', function(event) {
		if (event.ctrlKey || event.metaKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 's':
					// prevent browser ctrl+s
					event.preventDefault();

					// if user choosing is part of form
					ifContaints =   $.contains(document.getElementById(form_selector.substr(1)), document.activeElement);

					if(ifContaints)
					{
						$(form_selector).submit();
					}
					break;
			}
		}
	});

	return this;
}
