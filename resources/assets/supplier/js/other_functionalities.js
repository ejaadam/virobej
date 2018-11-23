	function isNumberKey(evt){
	  var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
				return false;
			}
		  return true;
	}
	
	function alphaNumeric_withspace(e) {
		var code=e.charCode? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 32  || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}
	
	function alphaNumeric_withoutspace(e) {
		var code=e.charCode? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57)  || ( code == 37 && e.charCode==0 ) || ( code == 39 && e.charCode==0 ) || code == 9 || code == 8  || ( code == 46  && e.charCode==0) )){
			return true;
		}
		return false;
	}


	function isNumberKeydot(evt){
	  var charCode = (evt.which) ? evt.which : evt.keyCode;
	  if (charCode != 46 && charCode > 31 && (charCode > 57 || charCode < 48 || charCode == 46)){
		 return false;
		}
	  return true;

	}
	function RestrictSpace(evt) {
		if (event.keyCode == 32) {
			return false;
		}
	}
	function selectallchk(evt){
		alert('fg');
		if(evt.checked) { // check select status
			$('.checkbox').each(function() { //loop through each checkbox
				this.checked = true;  //select all checkboxes with class "checkbox1"               
			});
		}else{
			$('.checkbox').each(function() { //loop through each checkbox
				this.checked = false; //deselect all checkboxes with class "checkbox1"                       
			});         
		}
	}
	