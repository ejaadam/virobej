 function cropper_main() {
    'use strict';
	 var Cropper = window.Cropper;
    var container = document.querySelector('.img-container');
    var image = container.getElementsByTagName('img').item(0);
    var actions = document.getElementById('actions');
    var inputImage = document.getElementsByClassName('cropper');
	console.log(inputImage);
	var cropper = new Cropper(image, {
        autoCropArea: 1,
        autoCrop: true,
        preview: '.img-preview',
        minCropBoxWidth: parseInt(inputImage[0].dataset['width']),
        minCropBoxHeight: parseInt(inputImage[0].dataset['height']),
        minContainerWidth: parseInt(inputImage[0].dataset['width']) + 100,
        minContainerHeight: parseInt(inputImage[0].dataset['height']) + 100,
        viewMode: 2,
        movable: true,
		aspectRatio:parseInt(inputImage[0].dataset['width'])/parseInt(inputImage[0].dataset['height']),
        cropBoxResizable: false
    });
	
	$('[data-toggle="tooltip"]').tooltip();
    if (! document.createElement('canvas').getContext) {
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
        if (! cropper) {
            return;
        }
        while (target !== this) {

            if (target.getAttribute('data-method')) {
                break;
            }
            target = target.parentNode;
        }
        if (target === this || target.disabled || target.className.indexOf('disabled') > - 1) {
            return;
        }
        data = {
            method: target.getAttribute('data-method'),
            target: target.getAttribute('data-target'),
            option: target.getAttribute('data-option'),
            secondOption: target.getAttribute('data-second-option')
        };
        if (data.method) {
            if (typeof data.target !== 'undefined') {
                input = document.querySelector(data.target);
                if (data.method == 'getCroppedCanvas') {
                    data.option = {width: parseInt(inputImage[0].dataset['width']), height: parseInt(inputImage[0].dataset['height'])};
					console.log(data.option);
                }
                if (! target.hasAttribute('data-option') && data.target && input) {
                    try {
                        data.option = JSON.parse(input.value);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
            result = cropper[data.method](data.option, data.secondOption);

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    target.setAttribute('data-option', - data.option);
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        //inputImage[0].value = result.toDataURL();
                        $('#' + inputImage[0].id + '-preview').prop('src', result.toDataURL());
                        $(inputImage[0].dataset['hide']).show();
						$('#' + inputImage[0].id).val('');
                        $('#crop-image').hide();
                        CROPPED = true;
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

    var URL = window.URL || window.webkitURL;
    var blobURL;
    $('.uneditable-input').hide();
    if (URL) {
        inputImage[0].onchange = function (e) {
		console.log(e);
            var files = this.files;
            var file;
            if (cropper && files && files.length) {
                file = files[0];
                if (/^image\/\w+/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    cropper.reset().replace(blobURL);
                    $(inputImage[0].dataset['hide']).hide();
                    $('#crop-image').show();
                } else {
                    window.alert('Please select a valid image');
                }
            }
        };
    } else {
        inputImage[0].disabled = true;
        inputImage[0].parentNode.className += ' disabled';
    }
	
	/* Close Cropper */
    $('#crop-image').on('click', '.close-cropper', function () {	
        $(inputImage[0].dataset['hide']).show();
        $('#crop-image').hide();
        setTimeout(function () {
            $('#' + inputImage[0].id + '-preview').prop('src', $('#' + inputImage[0].id + '-preview').data('old-image'));
			$('#' + inputImage[0].id).val('');
        }, 100);
        $('button[type=submit],button[type=reset],input[type=submit],input[type=reset]', $('#' + inputImage[0].id + '-preview').parents('form')).attr('disabled', true);
    });
	
    window.uploadImageFormat = function (dataURI) {
        var data = new FormData();
        var byteString = atob(dataURI.split(',')[1]);
        var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        var arrayBuffer = new ArrayBuffer(byteString.length);
        var _ia = new Uint8Array(arrayBuffer);
        for (var i = 0; i < byteString.length; i ++) {
            _ia[i] = byteString.charCodeAt(i);
        }
        var dataView = new DataView(arrayBuffer);
        return new Blob([dataView.buffer], {type: mimeString});
    };
};





