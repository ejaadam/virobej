 $(function () {
	alert("sADA");return false;
 var t = $('#listtbl');
	    var ID = null;
	    var catArr_resource = new Array();
	    var DT = t.dataTable({
        bPagenation: true,
        bProcessing: true,
        bFilter: false,
        bAutoWidth: false,
        oLanguage: {
            sSearch: "<span>Search:</span> ",
            sInfo: "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
            sLengthMenu: "_MENU_ <span>entries per page</span>"
        },
      	
        ajax: {
            url: $('#listfrm').attr('action'),
            type: 'POST',
            data: function (d) {
		
                return $.extend({}, d, $('input,select', '#listfrm').serializeObject());
            },
        },
        columns: [
             {
                name: '',
                class: '',
            },
		
        ],
		
    });
 });