$(document).ready(function () {    
	$('.file_upload').change(function(){
		$(this).closest('form').valid();
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
							$("#document_upload").remove();
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
	
	/*$("#address_id").validate({
        errorElement: 'div',
        errorClass: 'text-danger',
        focusInvalid: false,
        rules: {
			document_types: {
                required: true,
            },
            somename: {
					required:true
			}
        },
        messages: {
            document_types: {
                required: "Please Select a document type"
            }
			somename: {
                required: "Please Select your file"
            }
        }, 
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
						if (html.status == 'OK') {
							$file_uploader.prepend('<div class="alert alert-success">'+html.msg+'</div>');
							setTimeout(function () {
								document.location.href = html.url;
							}, 2000);
						}
						else {
						$file_uploader.prepend('<div class="alert alert-danger">'+html.msg+'</div>');
						}
					},
					complete: function (xhr) {				
						//status.html(xhr.responseText);
					}
				});
            }
        }
    });
	*/
	
	

	
	
	
   /* $("#photo_id").validate({
        errorElement: 'div',
        errorClass: 'text-danger',
        focusInvalid: false,
        rules: {
            document_types: {
                required: true,
            },
            somename: {
                required: true
            }
        },
        messages: {
            document_types: {
                required: "Please select your document type",
            },
            somename: {
                required: "Please Select your file"
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                form.submit();
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
            somename: {
                required: true
            }
        },
        messages: {
            document_types: {
                required: "Please select your document type",
            },
            somename: {
                required: "Please select your file"
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                form.submit();
            }
        }
    });*/
	
		
		
		/*function validate_doc12(formData, jqForm, options) { 
			var percentVal = '0%';
			$('.progress-bar',$('#document_upload')).width(percentVal)
			$('.percent',$('#document_upload')).html(percentVal);	
			if($('#document_upload').valid()){
				return true;	
			}
			else {
				return false;
			}
		};
		
		
		function validate_doc123(formData, jqForm, options) { 
			var percentVal = '0%';
			$('.progress-bar',$('#document_upload')).width(percentVal)
			$('.percent',$('#document_upload')).html(percentVal);	
			if($('#document_upload').valid()){
				return true;	
			}
			else {
				return false;
			}
		};

        $('#document_upload23').ajaxForm({
            dataType: 'json',
            beforeSubmit: function(){
				var percentVal = '0%';
				$('.progress-bar',$('#document_upload')).width(percentVal)
				$('.percent',$('#document_upload')).html(percentVal);	
			},
            uploadProgress: function (event, position, total, percentComplete) {
                $('#submit_button',$('#document_upload')).val($processing);
                $('.progress',$('#document_upload')).show();
                var percentVal = percentComplete + '%';
                $('.progress-bar',$('#document_upload')).width(percentVal)
                $('.percent',$('#document_upload')).html(percentVal);				
            },
            success: function (html) {
                $('#document_upload').prepend(html.msg);
                var percentVal = '100%';
                $('.progress-bar',$('#document_upload')).width(percentVal)
                $('.percent',$('#document_upload')).html(percentVal);
                if (html.status == 'OK') {
                    setTimeout(function () {
                        document.location.href = html.url;
                    }, 2000);
                }
            },
            complete: function (xhr) {				
                //status.html(xhr.responseText);
            }
        });
		
		
		 
		 
		 
		  $('#address_id').ajaxForm({
            dataType: 'json',
            beforeSubmit: function (formData, jqForm, options) {
                
                var percentVal = '0%';
                 $('.progress-bar',$('#address_id')).width(percentVal)
                $('.percent',$('#address_id')).html(percentVal);
            },
            uploadProgress: function (event, position, total, percentComplete) {
                $('#submit_button',$('#address_id')).val($processing);
                $('.progress',$('#address_id')).show();
                var percentVal = percentComplete + '%';
                $('.progress-bar',$('#address_id')).width(percentVal)
                $('.percent',$('#address_id')).html(percentVal);				
            },
            success: function (html) {
                $('#address_id').prepend(html.msg);
                var percentVal = '100%';
                $('.progress-bar',$('#address_id')).width(percentVal)
                $('.percent',$('#address_id')).html(percentVal);
                if (html.status == 'OK') {
                    setTimeout(function () {
                        document.location.href = html.url;
                    }, 2000);
                }
            },
            complete: function (xhr) {				
                //status.html(xhr.responseText);
            }
        });*/
	
});
function checkformat(ele, doctypes, str) {
    var fld = $('.file_upload');
    val = $(ele).val();
    txtext = val.split('.')[1];
    txtext = txtext.toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == -1) {
        alert(str);
        val = $('.file_upload').val('');
		return false;
    }
	return true;
}
       
   

