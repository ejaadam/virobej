$(document).ready(function () {
    $('#treeview-container').treeview({
        debug: true,
    });
    $('#tags').select2({
        multiple: true,
        tags: true,
        //placeholder: '',
        //minimumInputLength: 1,
        triggerChange: true,
        allowClear: true,
        tags: true,
                //tokenSeparators: [',', ' '],
                separator: ',',
        ajax: {
            type: 'POST',
            url: window.location.BASE + 'get-tags',
            data: function (params) {
                return {
                    search_term: params
                };
            },
            results: function (data) {
                return {
                    results: data
                };
            }
        },
        createSearchChoice: function (term, data) {
            if ($(data).filter(function () {
                return this.text.localeCompare(term) === 0;
            }).length === 0) {
                return {id: term, text: term};
            }
        }
    });
    $('#add_new #info #product_visibility').loadSelect({
        firstOption: {key: '', value: '--Select--'},
        firstOptionSelectable: false,
        url: window.location.BASE + 'product-visibility-list',
        key: 'visiblity_id',
        value: 'visiblity_desc'
    });
    $('#add_new #info #condition').loadSelect({
        firstOption: {key: '', value: '--Select--'},
        firstOptionSelectable: false,
        url: window.location.BASE + 'product-condition-list',
        key: 'condition_id',
        value: 'condition_desc'
    });
   /*  $('#add-new-product-form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            data: form.serialize(),
            url: form.attr('action'),
            success: function (data) {
                window.document.location.href = data.url;
            }
        });
    }); */
    $('#add_new').on('click', '#assoc_tab, #asso_tab', function (event) {
        event.preventDefault();
		$(".ass_tab").addClass('active');		
		$("#assoc").addClass('active');		
		$(".info_tab, .seo_tab").removeClass('active');	
		$("#seo, #info").removeClass('active');	
        $('#add_new #assoc #category_id').loadSelect({
            firstOption: {key: '', value: 'All'},
            firstOptionSelectable: true,
            url: window.location.BASE + 'product-categories-list',
            key: 'id',
            value: 'name'
        });
        $('#add_new #assoc #brand_id').loadSelect({
            firstOption: {key: '', value: 'All'},
            firstOptionSelectable: true,
            url: window.location.BASE + 'product-brands-list',
            key: 'brand_id',
            value: 'brand_name'
        });
    });
    var AN = $('#add_new form');
    $('#add_new form').on('submit', function (e) {
        e.preventDefault();
        var data = '';
        $('#add_new #editor1').val(CKEDITOR.instances.editor1.getData());
        $('#add_new form').each(function () {
            data += ((data != '') ? '&' : '') + $(this).serialize();
        });
        CURFORM = $(this).attr('id');		
        $.ajax({
            data: data,
            url: window.location.API.SELLER + 'products/save-product',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {
                if (data.status == 'OK') {
                    window.location = window.location.BASE + 'seller/products/' + data.product_code;                    
                }                
            },
            error: function (jqXhr) {
                if (jqXhr.status == 422) {
                    var data = jqXhr.responseJSON;
                    AN.appendLaravelError(data.error);
                }
                else
                {
                    alert('Something went wrong');
                }
            }
        });
    });
	
	$('#add_new').on('click', '#seo_tab', function (event) {
        event.preventDefault();
		$(".seo_tab").addClass('active');		
		$(".info_tab, .ass_tab").removeClass('active');		
	});
});
