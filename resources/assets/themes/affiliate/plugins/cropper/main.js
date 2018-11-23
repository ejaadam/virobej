window.onload = function () {
    'use strict';
    var Cropper = window.Cropper;
    var file_name;
    var container = document.querySelector('.img-container');
    var image = container.getElementsByTagName('img').item(0);
    var actions = document.getElementById('actions');
    var options = {
        autoCropArea: 1,
        autoCrop: true,
        preview: '.img-preview',
        minContainerWidth: 664,
        minContainerHeight: 366,
        viewMode: 1,
        aspectRatio: (4, 1),
        dragCrop: false,
        movable: false,
        resizable: false,
        zoom: function (data) {
            console.log('zoom', data.ratio);
        }
    };
    var resizable_options = {
        autoCropArea: 1,
        autoCrop: true,
        preview: '.img-preview',
        viewMode: 1,
        minContainerWidth: 664,
        minContainerHeight: 366,
        aspectRatio: 2.156,
        dragCrop: false,
        movable: false,
        resizable: false,
        zoom: function (data) {
            console.log('zoom', data.ratio);
        }
    };
    if (($('#prof_image').hasClass('resizable'))) {
        var cropper = new Cropper(image, resizable_options);
    }
    else {
        var cropper = new Cropper(image, options);
    }
    function isUndefined(obj) {
        return typeof obj === 'undefined';
    }
    function preventDefault(e) {
        if (e) {
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
        }
    }
    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();
    // Buttons
    if (!document.createElement('canvas').getContext) {
        $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
    }
    if (typeof document.createElement('cropper').style.transition === 'undefined') {
        $('button[data-method="rotate"]').prop('disabled', true);
        $('button[data-method="scale"]').prop('disabled', true);
    }
    // Methods
    actions.querySelector('.docs-buttons').onclick = function (event) {
        var e = event || window.event;
        var target = e.target || e.srcElement;
        var result;
        var input;
        var data;
        if (!cropper) {
            return;
        }
        while (target !== this) {

            if (target.getAttribute('data-method')) {
                break;
            }
            target = target.parentNode;
        }
        if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
            return;
        }
        data = {
            method: target.getAttribute('data-method'),
            target: target.getAttribute('data-target'),
            option: target.getAttribute('data-option'),
            secondOption: target.getAttribute('data-second-option')
        };
        console.log(data.method);
        if (data.method) {
            if (typeof data.target !== 'undefined') {
                input = document.querySelector(data.target);

                if (!target.hasAttribute('data-option') && data.target && input) {
                    try {

                        data.option = JSON.parse(input.value);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
            if (data.method === 'getCroppedCanvas') {
                data.option = JSON.parse(data.option);
            }
            result = cropper[data.method](data.option, data.secondOption);

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    target.setAttribute('data-option', -data.option);
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        $('#image_resize').modal('hide');
                        uploadimage(result.toDataURL());
                    }
                    break;
                case 'destroy':
                    cropper = null;
                    break;
                
            }
            if (typeof result === 'object' && result !== cropper && input) {
                try {
                    input.value = JSON.stringify(result);
                } catch (e) {
                    console.log(e.message);
                }
            }
        }
    };
	
	var inputImage = document.getElementById('prof_image');
    var URL = window.URL || window.webkitURL;
    var blobURL;
    $('.uneditable-input').hide();
    if (URL) {
        inputImage.onchange = function () {
            var files = this.files;
            var file;
            if (cropper && files && files.length) {
                file = files[0];
                if (/^image\/\w+/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    cropper.reset().replace(blobURL);
                    inputImage.value = null;
                    $('#image_resize').modal();
                } else {
                    window.alert($choose_image);
                }
            }
        };
    } else {
        inputImage.disabled = true;
        inputImage.parentNode.className += ' disabled';
    }

    $('#remove_tmp_image').click(function (e) {
        e.preventDefault();
		if($('#image-holder').data('original')!=''){
			$('#image_upload_show1').attr('src',$('.profile-user-img').attr('src'));
		}
        $('#profile_image_name').val('');
        $('.uneditable-input').hide();
        $('.uneditable-input span').text('');
        $('#update_profile_image').attr('disabled', 'disabled');
		$('.fileupload-exists').attr('disabled',true);
    });
};


function uploadimage(dataURI) 
{    
    var data = new FormData();
    var byteString = atob(dataURI.split(',')[1]);
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    var arrayBuffer = new ArrayBuffer(byteString.length);
    var _ia = new Uint8Array(arrayBuffer);
    for (var i = 0; i < byteString.length; i++) {
        _ia[i] = byteString.charCodeAt(i);
    }
    var dataView = new DataView(arrayBuffer);
    var file = new Blob([dataView.buffer], {type: mimeString});
    data.append('file', file);
    $.ajax({
        url: $('#prof_image').data('url'),
        data: data,
        dataType: "json",
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: function () {
            $("#update_profile_image").wrap('<b></b>');
        },
        success: function (data) {
		    $('.fileupload-exists').removeAttr('disabled');
            $('#profile_image_name').val(data.img_name);
            $('.uneditable-input').show();
            $('.uneditable-input span').text(data.orginal_name);
            $('#image_upload_show1').attr('src', data.img_path + "?" + new Date().getTime());
            $("#update_profile_image").html('Upload');
            $('#update_profile_image').removeAttr('disabled');
        }
    });
}


