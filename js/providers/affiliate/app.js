var getUrl = window.location,
 baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]+'/';
$(function () {	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
			
	$.fn.pageSwapTo = function(target){
		$(this).hide( "drop", { direction: "right" }, "fast",function(){
			$(target).show( "fade",'fast');
		});
	};	
	
	$.fn.checkFileFormat=function(doctypes, str){				
		var fname = $(this).val();
		var txtext = fname.split('.')[1];		
		txtext = txtext.toLowerCase();
		fformats = doctypes.split('|');
		$('#'+$(this).attr('id')+'_error').remove();
		if (fformats.indexOf(txtext) == -1) {			
			val = $(this).val('');
			$(this).after('<div class="help-block" id="'+$(this).attr('id')+'_error">'+str+"</div>");
			return false;
		}
		return true;

	}

});

function removePreloader(){
	$('body').toggleClass('loaded');
}

function loadPreloader(){
	$('body').toggleClass('loaded');
}

//loadPage_loader();



$(document).on('click','.logoutBtn',function(e){
	e.preventDefault();
	$.ajax({        
        url: $(this).attr('href'),        
		dataType:'JSON',
        success: function (op) {
            window.location.href = op.url;
        }
    });
});