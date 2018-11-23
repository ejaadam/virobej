$(function () {
	var t = $('#kyclist');
	t.DataTable({
		"ordering": false,
		"pagingType": 'input_page',
		"sDom": "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		"oLanguage": {
			"sLengthMenu": "_MENU_"
		},	
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data[2].span;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        },
	});
	
	$('#kycgrid').on('click','.uploadkyc',function(evt){
		evt.preventDefault();		
		$('#kycgrid').pageSwapTo('#kycfrm');
	});	
	
	$('#kycfrm').on('click','.backbtn',function(evt){
		evt.preventDefault();		
		$('#kycfrm').pageSwapTo('#kycgrid');
	});		
	$("#document_upload").validate({
        errorElement: 'div',
        errorClass: 'text-danger',
        focusInvalid: false,
        rules: {
            verify_file: {
					required:true
			}
        },
        messages: $document_msg, 
		submitHandler: function (form, event) {
            if ($(form).valid()) {
				$file_uploader = $(form);
               	$file_uploader.ajaxSubmit({
					dataType: 'json',
					beforeSubmit: function(){
						$('.alert').remove();
						var percentVal = '0%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);	
						 $('#submit_button',$file_uploader).hide();		
					},
					uploadProgress: function (event, position, total, percentComplete) {
						$('#submit_button',$file_uploader).val($processing);
						$('.progress',$file_uploader).show();
						var percentVal = percentComplete + '%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);				
					},
					success: function (html) {						
						$('#submit_button',$file_uploader).val($submit_btn);
						$('.progress',$file_uploader).hide();
						var percentVal = '100%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);
						if (html.status == 'ok') {							
							$file_uploader.before('<div class="alert alert-success">'+html.msg+'</div>');
							$("#document_upload").remove();
							$("#document_upload_file_btn").remove();
						}
						else {
						$file_uploader.before('<div class="alert alert-danger">'+html.msg+'</div>');
						}
					},
					complete: function (xhr) {				
						//status.html(xhr.responseText);
					}
				});
            }
        }
    });
	
	$("#photo_id").validate({
        errorElement: 'div',
        errorClass: 'text-danger',
        focusInvalid: false,
        rules: {
			document_types: {
                required: true,
            },
            verify_file: {
					required:true
			}
        },
        messages: $photo_id_msg, 
		submitHandler: function (form, event) {
            if ($(form).valid()) {
				$file_uploader = $(form);
               	$file_uploader.ajaxSubmit({
					dataType: 'json',
					beforeSubmit: function(){
						$('.alert').remove();
						var percentVal = '0%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);	
						$('#submit_button',$file_uploader).hide();
					},
					uploadProgress: function (event, position, total, percentComplete) {
						$('#submit_button',$file_uploader).val($processing);
						$('.progress',$file_uploader).show();
						var percentVal = percentComplete + '%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);				
					},
					success: function (html) {						
						$('#submit_button',$file_uploader).val($submit_btn);
						$('.progress',$file_uploader).hide();
						var percentVal = '100%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);
						if (html.status == 'ok') {
							$file_uploader.before('<div class="alert alert-success">'+html.msg+'</div>');
							$("#photo_id").remove();
							$("#photo_id_file_btn").remove();
						}
						else {
						$file_uploader.before('<div class="alert alert-danger">'+html.msg+'</div>');
						}
					},
					complete: function (xhr) {				
						//status.html(xhr.responseText);
					}
				});
            }
        }
    });
	
	$("#address_id").validate({
        errorElement: 'div',
        errorClass: 'text-danger',
        focusInvalid: false,
        rules: {
			document_types: {
                required: true,
            },
            verify_file: {
					required:true
			}
        },
        messages: $address_proof_msg, 
		submitHandler: function (form, event) {
            if ($(form).valid()) {
				$file_uploader = $(form);
               	$file_uploader.ajaxSubmit({
					dataType: 'json',
					beforeSubmit: function(){
						$('.alert').remove();
						var percentVal = '0%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);	
						$('#submit_button',$file_uploader).hide();
					},
					uploadProgress: function (event, position, total, percentComplete) {
						$('#submit_button',$file_uploader).val($processing);
						$('.progress',$file_uploader).show();
						var percentVal = percentComplete + '%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);				
					},
					success: function (html) {						
						$('#submit_button',$file_uploader).val($submit_btn);
						$('.progress',$file_uploader).hide();
						var percentVal = '100%';
						$('.progress-bar',$file_uploader).width(percentVal)
						$('.percent',$file_uploader).text(percentVal);
						if (html.status == 'ok') {							
							$file_uploader.before('<div class="alert alert-success">'+html.msg+'</div>');
							$("#address_id").remove();
							$("#address_id_file_btn").remove();
						}
						else {
						$file_uploader.before('<div class="alert alert-danger">'+html.msg+'</div>');
						}
					},
					complete: function (xhr) {				
						//status.html(xhr.responseText);
					}
				});
            }
        }
    });
	
	$('#kycfrm').on('change','#document_upload_file,#address_id_file,#photo_id_file',function(){
		var st = $(this).checkFileFormat($(this).data('formatallowd'),$(this).data('error'));
		if(st){
			
			$('button#'+$(this).attr('id')+'_btn').removeClass('hidden');
		}
		else {
			if(!$('button#'+$(this).attr('id')+'_btn').hasClass('hidden')){
				$('button#'+$(this).attr('id')+'_btn').addClass('hidden');
			}		
		}
	})
	
	$('#kycfrm').on('click','#document_upload_file_btn,#address_id_file_btn,#photo_id_file_btn',function(){
		$('#'+$(this).data('form')).submit();
	})
	
	
});