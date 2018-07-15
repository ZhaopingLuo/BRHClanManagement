<!--
====================================heading of page====================================
-->
<div class="row page-heading bg-light">
    <div class="col page-title"><h2><?php echo $title; ?></h2></div>

    <div class="col tool-bar">

        <!--
        ====================================advanced search====================================
        -->
        <div class="btn-group dropdown dropleft">
            <button class="btn btn-info dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuAdvancedSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Advanced Search
            </button>
            <div class="dropdown-menu search-panel" aria-labelledby="dropdownMenuAdvancedSearch">
                <form class="px-4 py-3" id="advanced_search">

                    <!--search conditions here-->
                    <div class="form-group">
                        <label for="advanced_keyword">Keyword:</label>
                        <input type="text" class="form-control" id="advanced_keyword" name="advanced_keyword" />
                    </div>
                    <!--search conditions here-->

                </form>
                <div class="form-group_advanced_search">
                    <button form="advanced_search" class="btn btn-primary" id="button_search"><i class="material-icons">search</i> Search</button>
                    <button form="advanced_search" class="btn btn-light " id="button_refresh"><i class="material-icons">refresh</i> All</button>
                </div>
            </div>
        </div>
        <!--end: advanced search panel-->
        <button  data-toggle="modal" data-target=".modal" data-backdrop="static" class="btn btn-warning" id="button_modalUserCreate"><i class="material-icons ico_person_add">person_add</i>  New User</button>
    </div>
</div>

<!--
====================================Datatable====================================
-->
<table id="list_demo" class="table table-striped table-bordered table-hover" cellspacing="0" style="width:100%">
    <thead>
    <tr>
        <th data-source="demo_column" data-filter style="width:10px;">#</th>
    </tr>
    </thead>
</table>



<!--
====================================scripts:====================================
-->
<script>

    // modal size could be set when its open
    selector_dataTable = "#list_demo";

    // generate the Datatable and popup forms
    $(document).ready(function() {

        // initialize the Datatable
        // Advanced search
        searchInitialize = <?=$initSearchData?>;
        for(var obj in searchInitialize)
        {
            $("[name = " +obj +"]").val(searchInitialize[obj]);
        }

        // initialize the Datatable
        ajaxTarget = "<?php echo site_url('Demos/ajax_getRemoteJson'); ?>";

        advancedSearchFormSelector = "#advanced_search";

        oTable = new create_dataTable(
            selector_dataTable,
            ajaxTarget,
            advancedSearchFormSelector
        );
    } );

</script>
