/*
*  Author: Zhaoping Luo
*  The ajax validation working with codeigniter and bootstrap
*
*  start, tie an ajax form with this class
*  ajax_validation(form_selector, fn)
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
function ajax_validation(form_selector_val, fn) {

	var form_selector;

	// whole body of the modal (if it is a modal)
	var modal_selector;

	// if need confirmation when closing the modal
	var isCloseCheck;

	var errorMessageIds = [];
	var passedMessageIds = [];

	// private values: if data not matches, popup confirmation when closing, otherwise just close
	var modalCloseCheckDataMatching = false;
	var modalCloseCheckPassed = false;

	// set default value
	this.form_selector = "form";
	this.modal_selector = "";
	this.isCloseCheck = false;


	// callback function when load
	var fn_load;

	var fn_unload;

	// will store form, be used to check if any change happens
	var originalFormValues = "";

	//================================= getters and setters
	this.__defineSetter__('modal_selector', function (val) {
		modal_selector = val;
	});

	this.__defineSetter__('isCloseCheck', function (val) {
		isCloseCheck = val;
	});

	this.__defineSetter__('form_selector', function (val) {
		form_selector = val;
	});

	this.__defineSetter__('fn_load', function (val) {
		fn_load = val;
	});

	this.__defineSetter__('fn_unload', function (val) {
		fn_unload = val;
	});

	//=================================pass arguments in
	if (typeof form_selector_val !== 'undefined') {
		this.form_selector = form_selector_val;
	}

	// regisiter function to submit event
	$(form_selector).submit(function( event ) {

		// For Ajax version, will be always false to prevent submit(then handle it in AJAX)
		// if going to modify all those to JS version, will be need a boolean, then process
		var isSuccessful = form_AjaxValidation(this);

		if(!isSuccessful)
		{
			event.preventDefault();
		}

		return isSuccessful;
	});


	// AJAX send the form to validate
	function form_AjaxValidation(form)
	{
		postUrl = $(form).attr("action");
		callbackFormId = $(form).attr("id");

		var formData = new FormData( $(form)[0] );
		$.ajax({
			type: "POST",
			url: postUrl,
			data: formData,
			processData:false,
			contentType:false,
			cache:false,
			async:false,
			success: function(data)
			{
				fill_error_from_json(data);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});

		// formId is used to fill error messages when called back
		/*$.ajax({
			type: "POST",
			url: postUrl,
			data: $(form).serialize(),
			success: function(data)
			{
				fill_error_from_json(data);
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});*/
		return false;
	}

	// AJAX callback
	function fill_error_from_json(data)
	{
		// if json parse fails, means there is an parsing error. Show the error page
		try {
			clear_errors();
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

				if(modal_selector!="")
				{
					// bypass the closing confirmation (if its false, will popup a confirmation form)
					modalCloseCheckDataMatching = true;
					$(".modal").modal('hide');
					modalCloseCheckDataMatching = false;
				}

				if(fn != null)
				{
					fn();
				}
			}
			else
			{
				// if need to send error message to a tab
				var tab_errors = [];
				var tab_all = [];

				// loop controls for the form
				$( form_selector + " [data-validation]" ).each(function(index) {

					// if it needs to send an error message to a tab
					var currentTab_selector = $(this).parents(".tab-pane").attr("aria-labelledby");

					if(this.name in errorMessages){

						var message = errorMessages[this.name];
						var obj = document.createElement("div");

						obj.setAttribute("id", this.name + "_errorMessage");
						obj.setAttribute("class", "invalid-feedback");
						obj.textContent= message;

						$(this).after(obj);
						$(this).removeClass("is-valid").addClass("is-invalid", 1000, "easeOutBounce");
						errorMessageIds.push(this.name);

						if(currentTab_selector)
						{tab_errors.push("#" + currentTab_selector);}
					}
					else
					{
						$(this).removeClass("is-invalid").addClass("is-valid", 1000, "easeOutBounce");
						passedMessageIds.push(this.name);
					}

					if(currentTab_selector)
					{tab_all.push("#" + currentTab_selector);}
				});

				// clone array, ready to calculate error number of each tab
				var tab_calculate = tab_errors.slice(0);;

				jQuery.unique( tab_errors );
				jQuery.unique( tab_all );

				var tab_valids = $(tab_all).not(tab_errors).get();

				// calculate error number then display on tab
				for(var obj in tab_errors)
				{
					var errorNumber = 0;
					for (var i = 0; i < tab_calculate.length; i++)
					{
						if(tab_errors[obj] == tab_calculate[i])
						{errorNumber++;}
					}

					$(tab_errors[obj]).removeClass("navValid").addClass("navError");

					var newSpan = document.createElement("span");
					newSpan.innerHTML = " ("+ errorNumber +" errors)";

					$(tab_errors[obj]).append(newSpan);
				}

				// if no error, display the tick
				for(var obj in tab_valids)
				{
					$(tab_valids[obj]).removeClass("navError").addClass("navValid");
				}


			}
		} catch(e){
			$("html").html(data);
		}
	}

	this.form_fill_error_from_json = function(data){
		fill_error_from_json(data);
	}

	// regisiter all events we need for the modal
	this.initializeModal = function(){

		// clear all confirmation dialog messages and error messages when closed.
		$(document).on('hidden.bs.modal', '.modal' , function (e) {
			clear_errors();
			modalCloseCheckPassed = false;
			modalCloseCheckDataMatching = false;

			$(modal_selector).hide();
			$(modal_selector).parent().before($(modal_selector));

			if(typeof window[fn_unload] == "function") {
				window[fn_unload](form_selector);
			}

			$(document).off('hide.bs.modal', '.modal');
			$(document).off('hidden.bs.modal', '.modal');
			$(document).off('shown.bs.modal', '.modal');
		});

		// user detail: close
		$(document).on('hide.bs.modal', '.modal' , function (e) {

			// set e.target === this to prevent conflict
			if(isCloseCheck && !modalCloseCheckDataMatching && e.target === this){

				modalCloseCheckDataMatching = $(form_selector).serialize() == originalFormValues;

				if(!modalCloseCheckPassed && !modalCloseCheckDataMatching)
				{
					e.preventDefault();
					$.confirm({
						title: 'Confirm',
						content: 'You are about to close this window.<br/> All changes will be lost.',
						buttons: {
							confirm: function () {

								modalCloseCheckPassed = true;
								$('.modal').modal("hide");
							},
							cancel: function () {
							}
						}
					});
				}
			}
		});

		$(document).on('shown.bs.modal', '.modal', function() {
			// get smallest tabindex
			var elements = $(modal_selector +" [tabindex]:not([tabindex*=-1])");
			var vals = [];

			// put all tabindex of the form into an array
			for(var i=0;typeof(elements[i])!='undefined';vals.push(parseInt(elements[i++].getAttribute('tabindex'))));

			// set focus on the first tabitem
			$(modal_selector +" [tabindex*="+ Math.min.apply(Math,vals) +"]").focus();
		});

		$(modal_selector).detach().appendTo('.modal');
		$(modal_selector).show();
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

		$(modal_selector + " .nav-link span").remove();
		$(".navError").removeClass("navError");
		$(".navValid").removeClass("navValid");

		$( form_selector + " [data-validation]").removeClass("is-invalid");
		$( form_selector + " [data-validation]" ).removeClass("is-valid");

		// set tab to first
		$( modal_selector + " .nav-tabs .nav-item:first-child a" ).tab("show");
	}

	this.submit = function()
	{
		$(form_selector).submit();
	}

	this.reset_form = function()
	{
		// call a function in "js/tools.js", eset all form values.
		$(form_selector).deserialize(originalFormValues);
	}

	// retriving data from remote when open the form
	this.read_form = function(id, url){

		if(typeof window[fn_load] == "function") {
			window[fn_load](form_selector);
		}

		if ((typeof id === 'undefined') || (typeof url === 'undefined')){
			originalFormValues = $(form_selector).serialize();
		}
		else
		{
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
							inputItem = $(form_selector + ' [name=' + obj + ']' + ',' + form_selector + ' [name="' + obj + '[]"]');

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
                                else if($(inputItem).attr("multiple") !== undefined ) // if it will be keep blank
                                {

                                    //inputItemMulti = $(form_selector + ' [name="' + obj + '[]"]');
                                    var values=value.split(",");
                                    /*
									$.each(values, function (index,mvalue){
										$(form_selector + ' select[name="' + obj + '[]"] option[value="' + mvalue + '"]').prop("selected", true);
									});

									$(inputItemMulti).trigger("change");
									*/

                                    /*
                                    $.each(values.split(","), function(index, value){
                                        $(inputItem + " option[value='" + e + "']").prop("selected", true);
                                    });
                                    */

                                    $(inputItem).val(values).trigger('change');

                                }
								else if($(inputItem).attr("data-render") !== undefined ) // if it will be keep blank
								{
									if(typeof window[$(inputItem).attr("data-render")] == "function") {
										$htmlValue = window[$(inputItem).attr("data-render")](inputItem, value);
									}
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
