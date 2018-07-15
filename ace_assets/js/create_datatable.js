/*
*  Used for create datatable
*
*  data-source: --- what column looking for
*  data-orderby-desc
*  data-orderby-asc: --- if has this attribute, will be as default sorting,
*  						 the value is priority when there are more than 1 sorting columns
*  data-filter: ---filter by which columns
*  data-icon: --- what boolean should display
*  data-class: --- change the column's classname
*  data-render: --- change content, render function should be fn(data, type, row, meta)
*  data-multiselect: --- set the column as multi checkbox column
*
* */

// settings of iconlist matches of value
var iconList={
    "boolean":{
        "0" : "block",
        "1" : "check"
    },
    "available":{
        "0" : "event_busy",
        "1" : "event_available"
    }
};

function reload_dataTable(selector)
{
    $(selector).DataTable().ajax.reload(null, false);
}

function create_dataTable(selector, url, searchForm, initialSearch)
{
    count = 0;

    // determine what columns are using filter
    filters = [];

    // binding data with columns
    columns = [];

    // sort
    orderBy = [];

    // datatable
    this.datatable = null;

    //icons = [];
    is_popover = false;

    selector_popover_template = "#popover_contents";

    // fill in extra search
    //=================================pass arguments in
    if (typeof initialSearch !== 'undefined') {
        for(var obj in initialSearch)
        {
            $("[name = " +obj +"]").val(initialSearch[obj]);
            $("[name = " +obj +"]").trigger("change");
        }
    }

    // get data source from columns
    $(selector + " thead tr th").each(
        function () {

            ds_item={};
            ft_item = "";
            order_item = {};

            // get column name from table, passing to remote AJAX
            if($(this).attr("data-source") != undefined)
            {
                ds_item["data"] = $(this).attr("data-source");

                // set this column work with filter
                if($(this).attr("data-filter") != undefined)
                {
                    ft_item = $(this).attr("data-source");
                    filters.push(ft_item);
                }

                if($(this).attr("data-orderby-desc") != undefined)
                {
                    priority = $(this).attr("data-orderby-desc");

                    order_item = [columns.length, "desc", priority];
                    orderBy.push(order_item);
                }else if($(this).attr("data-orderby-asc") != undefined)
                {
                    priority = $(this).attr("data-orderby-asc");

                    order_item = [columns.length, "asc", priority];
                    orderBy.push(order_item);
                }

                // when reading data, it will be an icon
                if($(this).attr("data-icon") != undefined)
                {
                    icontype=$(this).attr("data-icon");

                    ds_item["render"] = function(data, type, row, meta){
                        iconName = iconList[this.icontype][data];
                        return "<i class='material-icons ico_" + iconName + "'>"+iconName+"</i>"
                    };
                }

                if($(this).attr("data-class") != undefined)
                {
                    ds_item["className"] = $(this).attr("data-class");
                }

                if($(this).attr("data-toolbar") != undefined)
                {
                    ds_item["orderable"] = false;
                    //ds_item["defaultContent"] = lastColumn;
                }

                if($(this).attr("data-multiselect") != undefined)
                {
                    ds_item["orderable"] = false;
                    ds_item["checkboxes"] = {'selectRow': true};
                }

                if($(this).attr("data-render") != undefined)
                {
                    renderName = $(this).attr("data-render");

                    if(typeof window[renderName] == "function") {
                        ds_item["render"] = window[renderName];
                    }
                    /*function(data, type, row, meta){
                        //window[$(inputItem).attr("data-render")](inputItem, value);
                        if(typeof window[renderName] == "function") {
                            return window[renderName](data, type, row, meta);
                        }
                    }*/
                }

                columns.push(ds_item);
            }
        }
    );

    orderBy.sort(sortFunction);
    function sortFunction(a, b) {
        if (a[2] === b[2]) {
            return 0;
        }
        else {
            return (a[2] < b[2]) ? -1 : 1;
        }
    }

    if(orderBy.length == 0)
    {
        orderBy = [[ 0, "desc" ]];
    }

    /*
    // / last column, which is tools column
    toolButtons = {
        "data":null,
        "className": "edit_column",
        "orderable": false,
        "defaultContent" : lastColumn,
    };

    // edit column
    columns.push(toolButtons);*/

    // disable the error alert
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.ext.classes.sFilterInput = 'form-control';
    $.fn.dataTable.ext.classes.sLengthSelect = 'select-inline';
//	$.fn.dataTable.ext.classes.sPaging ='pagination pagination-sm ' + $.fn.dataTable.ext.classes.sPaging;

    this.get_datatable = function(){
        return this.datatable;
    }

//===========================================================================
// hover popover

    this.fn_pop_content = "";
    this.pop_url = "";
    var pop_url = "";
    var fn_pop_content = "";

    this.__defineSetter__('fn_pop_content', function (val) {
        fn_pop_content = val;
    });

    this.__defineSetter__('pop_url', function (val) {
        pop_url = val;
    });

    // the ajax used to load data; need a return
    function ajax_popover_getdetail(id, row, url)
    {
        if($(row).attr("data-popover-completed") !== "1")
        {
            $(row).attr("data-popover-completed",1);
            data = {"id": id};
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(data)
                {
                    json = JSON.parse(data);
                    //content = window[fn_pop_content](id,row,json);
                    content = get_popover_detail_from_json(selector_popover_template,id, json );

                    content = content.length === 0?"No Record": content;

                    $(row).attr("data-content",content);
                    // dynamically renew the content of popover
                    var popover = $(row).data('bs.popover');
                    popover.setContent();
                }
            }).fail(function(data){
                $("html").html(data.responseText);
            });
        }

    }

//===========================================================================

    // initialize the datatable
    this.datatable = $(selector).DataTable( {
        "processing": true, // the processing bar
        "serverSide": true,	// run ajax
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 0], [10, 25, 50, "All"]],
        "order": orderBy,
        "rowCallback": function( row, data, index ) {
            //console.log(row);
            if(is_popover)
            {
                $(row).attr("data-animation",true);
                $(row).attr("data-toggle","popover");
                $(row).attr("data-trigger","manual");
                $(row).attr("data-html","true");
                $(row).attr("data-delay", 50);
                $(row).attr("data-container","table");
                $(row).attr("data-placement","auto");
                $(row).attr("data-content","Loading...");
                $(row).attr("data-popover-completed",0);
                $(row).attr("data-template",'<div class="popover" role="tooltip"><div class="popover_inner"><div class="arrow"></div><div class="popover-body personal_information"></div></div></div>');
                $(row).popover(
                    {

                    }
                );
            }
        },
        "ajax": {
            url: url,
            data: function (d) {
                if ($.isFunction(fnGetSearch)) {
                    d.extraSearch = fnGetSearch();
                }else {
                    d.extraSearch = Array(null);
                }

                d.filters = filters;
            }
        },
        "columns": columns,
        'select': {
            'style': 'multi'
        },
        "language": {
            search: "_INPUT_",
            searchPlaceholder: "Filter..."
        }
    } ).on('xhr.dt', function ( e, settings, json, xhr ) { // error handling

        // if get error,
        if(json == null)
        {
            // print all messages on screen
            $("html").html(xhr.responseText);
        }
    } )	;

    //$(".dataTables_filter").hide();
    if (typeof searchForm !== 'undefined') {
        $(searchForm).submit(function( event ) {

            $(selector).DataTable().ajax.reload();
            event.preventDefault();
        });
    }
    // submit the multi select

    this.get_multi_selections = function(column)
    {
        var rows_selected  = this.datatable.column(column).checkboxes.selected();

        var array = [];
        $.each(rows_selected, function(index, rowId){
            array.push(rowId);
        });
        console.log(rows_selected);
        return array;
    }

    this.multi_select = function(column, url, callback){

        var rows_selected  = this.datatable.column(column).checkboxes.selected();

        var form_id = selector + "_form";
        var array = [];
        $.each(rows_selected, function(index, rowId){
            array.push(rowId);
        });

        $.ajax({
            type: "POST",
            url: url,
            data: {"selected": array},
            success: function(data)
            {
                if(typeof window[callback] == "function") {
                    window[callback](data);
                }
            }
        }).fail(function(data){
            $("html").html(data.responseText);
        });
    }

    /**
     * initialize the popover
     * @param selector_popover
     */
    this.popover = function(selector_popover)
    {
        is_popover = true;
        selector_popover_template = selector_popover;
        pop_url = $(selector_popover).attr('data-source');

        // current selected row
        tag_in = "";
        tag_out = "";

        // !! check current_selected = current_out, not trigger leave?

        function show_popover(popover, e)
        {
            if($(selector).attr("data-popover-freeze")!=1){
                $(popover).popover('show');
            }
        }


        function hide_popover(popover, e)
        {
            if($(selector).attr("data-popover-freeze")!=1) {

                if (tag_in != tag_out) {
                    $(popover).popover('hide');
                }
            }
        }

        // hover of popover, except its frozen/cursor is on table
        $(document).on('mouseenter', selector + ' tr[data-id] td:not(.no_popover)', function(e) {

            // 如果鼠标指在面板上，就隐藏，除非lock
            if($(selector).attr("data-popover-freeze")!=1 && ($(selector).is(e.target) || $(selector).has(e.target).length > 0)) {

                tag_in = $(this).parent();
                //show_popover(tag_in, e)
                setTimeout(show_popover, 10, tag_in, e);
            }
        });

        // check the pop-proof td
        /*$(document).on('mouseover', selector + ' tr[data-id] td.no_popover', function(e) {

            current_selected = $(this).parent();
            show_popover(e);
            setTimeout(popover_trigger_done, 100);
        });*/


        // hover of popover, except its frozen/cursor is on table
        $(document).on('mouseleave', selector +' tr[data-id] td:not(.no_popover)', function(e) {

            if($(selector).attr("data-popover-freeze")!=1) {
                tag_out = $(this).parent();
                //hide_popover(tag_out, e)
                setTimeout(hide_popover, 1, tag_out, e);
            }
        });

        // load data when pop
        $(document).on('show.bs.popover', selector + ' tr[data-id]', function() {

            if($(this).attr("data-popover-completed") !== "1")
            {
                // if there is a remote ajax url then callback, get ajax; else just callback
                if(	typeof window[fn_pop_content] == "function")
                {
                    $(this).attr("data-content", window[fn_pop_content]($(this).attr("data-id"),this));
                }
                else if(pop_url.length > 0)
                {
                    $(this).attr("data-content", ajax_popover_getdetail($(this).attr("data-id"),this, pop_url));
                }
            }
        });

        // lock or unlock the pop window

        $(document).mousedown(function(e)
        {
            var container = $(".popover_inner");
            var lock_trigger = $(selector);

            // if the popover is locking, release
            if($(selector).attr("data-popover-freeze") === "1")
            {
                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0)
                {
                    $(selector).attr("data-popover-freeze", 0);
                    $(".popover").removeClass("popover_locked");
                    $(selector + " tr[data-id]").trigger("mouseleave");
                    $(".popover").popover('hide');

                }
            }
            else if( (lock_trigger.is(e.target) || lock_trigger.has(e.target).length > 0))
            {
                if($("i,a,button").is(e.target))
                {
                    $(selector + " tr[data-id]").trigger("mouseleave");
                }
                else if($(".no_popover").is(e.target))
                {
                    //console.log("hi");
                }
                else
                {
                    $(".popover").addClass("popover_locked");
                    $(selector).attr("data-popover-freeze", 1);
                }
            }
        });
    }

    // function of passing extra search's variables
    function fnGetSearch()
    {
        searchInfomation = {};
        formJSON = $(searchForm).serializeArray();

        for(var obj in formJSON)
        {
            searchInfomation[formJSON[obj].name] = formJSON[obj].value;
        }

        return searchInfomation;
    }

    /**
     * used for hover popover, will return what should display in popover
     * @param selector: the selector of template
     * @param id: identifier of the popover
     * @param data: values
     */
    function get_popover_detail_from_json(selector, id, data)
    {
        selector_temporary = selector + "_clone_" + id;
        popover_clone =  $(selector).clone().attr('id', "popover_contents_clone_" + id);
        $(popover_clone).appendTo(selector);

        //=============pull out==========
        if(!jQuery.isEmptyObject(data) )
        {
            // search all the names except those are in the loop
            $(selector_temporary+ " [name]:not([data-loop] [name])" ).each(function(index) {

                inside_value = data[$(this).attr("name")];

                // fill first level json in
                if($(this).attr("name") in data){

                    // if there is value, set value, else remove
                    if(inside_value != null && inside_value.length > 0)
                    {
                        // if its a link
                        if($(this).is("a"))
                        {
                            $(this).attr("href", $(this).attr("href") + inside_value);
                        }
                        else if($(this).is("img"))
                        {
                            $(this).attr("src", $(this).attr("src") + inside_value);
                        }else
                        {
                            $(this).html(inside_value);
                        }
                    }
                    else
                    {
                        if($(this).is("img"))
                        {
                            $(this).attr("src", $(this).attr("src") + "error.png");
                        }else
                        {
                            $(this).remove();
                        }
                    }



                }
            });

            // search data-loop, get matched json
            $(selector_temporary+ " [data-loop]" ).each(function(index) {

                sourceName = $(this).attr("data-loop");
                idx_title = $(this).attr("data-loop-title");
                loop_limit = Number($(this).attr("data-loop-limit"));

                data_list_source = [];

                data_list_source = data[sourceName];
                parent =  $(this).parent();

                //data-loop-limit
                // loop the source
                for (var i = 0; i < data_list_source.length && (i < loop_limit || loop_limit < 0 ); i++)
                    //for(var obj in data_list_source)
                {
                    // clone an element
                    var newEl;
                    newid = "popover_" + sourceName + "_" + data_list_source[i]["id"];
                    newEl = $(this).clone().attr("id", newid);
                    newEl.removeAttr("data-loop");

                    // if data-loop has title
                    newEl.attr("title", data_list_source[i][idx_title]);

                    newEl.find(" [name]").each(function(){
                        if($(this).attr("name") in data_list_source[i]){

                            var inside_value = data_list_source[i][$(this).attr("name")];

                            if( inside_value)
                            {
                                // if its a link
                                if($(this).is("a"))
                                {
                                    $(this).attr("href", $(this).attr("href") + inside_value);
                                }
                                else if($(this).is("img"))
                                {
                                    $(this).attr("src", $(this).attr("src") + inside_value);
                                }
                                else
                                //if($(this).is("span"))
                                {
                                    $(this).append(inside_value);
                                }
                            }
                            else
                            {
                                $(this).remove();
                            }
                        }
                    });

                    parent.append(newEl);
                }

                if(data_list_source.length > loop_limit && loop_limit >= 0)
                {
                    newEl = $(this).clone();
                    newEl.removeAttr("data-loop");
                    newEl.html("...");
                    parent.append(newEl);
                }
            });

            $(selector_temporary+ " [data-loop]" ).hide();
            returnValue = $(selector_temporary).html();
            $(selector_temporary).remove();
        }

        return returnValue;
    }

}


