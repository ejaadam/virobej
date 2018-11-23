$(document).ready(function () {
    $('#update_profile_image').attr('disabled',true);
	$('.fileupload-exists').attr('disabled',true);
   	 
    $('#prof_image').change(function () {
        var photo = document.getElementById("prof_image");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif)$");
        if (regex.test(photo.value.toLowerCase())) {
            var file = photo.files[0];
            var sizeKB = Math.round(file.size / 1024);
            if (sizeKB <= 150) {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function () {
                        var height = this.height;
                        var width = this.width;
                        if (height >= 150 && width >= 150) {
						//profile_image_preview
                            $('#fileupload-preview').attr('src', e.target.result);
							return false;
                            $('#update_profile_image').removeAttr('disabled');
                            $('#prof_image').empty();
                        }
                        else {
                            alert($image_dimension_incorrect);
                            $('#prof_image').empty();
                        }
                        return true;
                    };
                }
            }
            else {
                alert($file_size_high);
                $('#prof_image').empty();
            }
        } else {
            alert($valid_image_file);
            $('#prof_image').empty();
            return false;
        }
    });
	
    $('#profile_image_update').submit(function (e) {
        e.preventDefault();
        var formElm = $(this);
        if ($('#profile_image_name').val() != '') {
            $.ajax({
                url: $(this).attr('action'),
                data: $('#profile_image_update').serialize(),
                dataType: "json",
                enctype: 'multipart/form-data',
                type: 'POST',
                beforeSend: function () {
				    $('body').toggleClass('loaded');
                    $('#update_profile_image').attr('disabled', 'disabled');
                },  
                success: function (data) {
				    $('body').toggleClass('loaded');
					if(data.status == 200){
					    $('#remove_prof_image').html('<i class="fa fa-fw fa-times" ></i><a href="#" class="text-danger" >'+data.remove_image+'</a>').show();
					    $('.fileupload-exists').attr('disabled',true);
						$('#image-holder').attr('data-original',data.file_name);
					    $('.profile-user-img').attr('src',data.file_name);
                        $('#err_msg').addClass("alert alert-success").html('<a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+data.msg);
						$('#err_msg').fadeOut(7000);
					}else{
					    $('#err_msg').addClass("alert alert-danger").html('<a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+data.msg);
						$('#err_msg').fadeOut(7000);
					}
                }
            });
        } else {
            alert($select_fileto_upload);
        }
    });
	
	$('#remove_prof_image').on('click',(function (e) {
	    e.preventDefault();
		if (confirm($remove_prof_image)) {
			$.ajax({
		    	url: 'account/profile/remove_profile_image',
				type: 'GET',
				dataType: 'json',
				success: function (data)
				{  
				    if(data.status == 200){
					    $('.fileupload-exists').attr('disabled',true);
					    $('#remove_prof_image').hide();
						$('.uneditable-input').hide();
						$('#image_upload_show1').attr('src', baseUrl +''+ data.file_name );
						$('#image-holder').attr('data-original',baseUrl+''+data.file_name);
					    $('.profile-user-img').parent().before('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+ data.msg +'</div>');
				        $('.profile-user-img').attr('src',data.file_name);
						$('.box-profile .alert').fadeOut(7000);
				   }else{
				       $('.profile-user-img').parent().before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+ data.msg +'</div>');
					   $('.box-profile .alert').fadeOut(7000);
				   }
				},
				error: function () {
				    $('.profile-user-img').parent().before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+ $something_wrong +'</div>');
				}
			});
        }
		else {
			return false;
		}
	})); 
	
	$('#add_prof_image').click(function (e) {
		e.preventDefault();
		$('#my_profile,#location').fadeOut('fast',function(){
		    $('#image_upload_show1').attr('src',$('.profile-user-img').attr('src')); 
			$('#image_upload').fadeIn('slow');
		});
    });
	$('#back_btn').click(function (e) {
		e.preventDefault();
		$('#image_upload').fadeOut('fast',function(){
		    $('#image_upload_show1').attr('src',$('#image-holder').data('original'));
		    $('.uneditable-input').hide();
            $('.uneditable-input span').text('');
            $('#update_profile_image').attr('disabled', 'disabled');
		    $('.fileupload-exists').attr('disabled',true);
		    $('#my_profile,#location').fadeIn('slow');
		});
	});	
});








