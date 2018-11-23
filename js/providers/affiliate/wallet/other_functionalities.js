	 
	function isNumberKey(evt){
	  var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
				return false;
			}
		  return true;
	}

	function isNumberKeydot(evt){
	  var charCode = (evt.which) ? evt.which : evt.keyCode;
	  if (charCode != 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57) || charCode == 46)
		 return false;
	  return true;

	}
	function RestrictSpace(evt) {
		if (event.keyCode == 32) {
			return false;
		}
	}
	
	function alpha_checkwithoutspace(e) {
		var code=e.charCode? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}
	
	function alpha_checkwithspace(e) {
		var code=e.charCode? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 32 || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}
	
	function address_checkwithspace(e) {
		var code=e.charCode? e.charCode : e.keyCode;		
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 45 || code == 32 || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}
	
	function uname_validate(e) {
		var code=e.charCode? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}

	$("#id").keypress(function (e) {
		 //if the letter is not digit then display error and don't type anything
		 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			$("#errmsg").html("Enter Digits Only").show().fadeOut("slow");
				   return false;
		}
	});
	$(function() {
		$("#amount").blur(function() {
			var amt = parseFloat(this.value);
			if(isNaN($(this).val())){
				$(this).val(amt.toFixed(2));
		 	}
		});
	});



