// JavaScript Document
$(document).ready(function(){
				
	$("#country").change(function () {
       var country_id = $("#country").val();
	   var state_id = $("#state_id").val();
       if(country_id !=''){	  			
			$("#region").val('');
			$("#state").val('');	
			var regionOpt = "<option value=''>--Select Region--</option>";
			var stateOpt = "<option value=''>--Select State--</option>";
			
			$.post('get_state_phonecode',{country_id : country_id},function(data){													
			 	
				if(data.region_list != '' && data.region_list != null){
						var regions = data.region_list;
						$.each(regions,function(key, elements){
							regionOpt += "<option value='"+elements.region_id+"'>"+elements.region_name+"</option>";				   	
					   });
				}
																
			 	if(data.state_list != '' && data.state_list != null){
						var states = data.state_list;
						$.each(states,function(key, elements){
							stateOpt += "<option value='"+elements.state_id+"'";
							if(elements.state_id == state_id){
								stateOpt +=" selected='selected'";
							}
							stateOpt +=">"+elements.name+"</option>";				   	
					   });
				}
			  $("#region").html(regionOpt); 	
			  $("#state").html(stateOpt);
		   },'json');
			
			
		}else{
			
			$("#region").html("<option value=''>--Select Region--</option>");	
			$("#state").html("<option value=''>--Select State--</option>");
		}
    });	

	// district 
	 $("#state").change(function () {
        var state_id = $(this).val();
		if(state_id == '' &&  $("#state_id").val() !== undefined){
			state_id = $("#state_id").val();
		}
		
		var district_id = $("#district_id").val();
	    if(state_id !=''){
			$("#district").html('');
			var districtOpt = "<option value=''>--Select District--</option>";	
			
			$.post('get_district',{state_id : state_id},function(data){
					
				if(data.district_list != '' && data.district_list != null){
						var districts = data.district_list;
						$.each(districts,function(key, elements){
							districtOpt += "<option value='"+elements.district_id+"'";
							if(elements.district_id == district_id){
								districtOpt +=" selected='selected'";
							}
							districtOpt += ">"+elements.district_name+"</option>";				   	
					   });
				}
				
				//districtOpt += "<option value='0'>Others</option>";				
			 	$("#district").html(districtOpt);			 			 	
							   
		   },'json');
		}else{
			$("#district").html("<option value=''>--Select District--</option>");			
		}
		$("#district").change();
		$("#city").change();
    });
	
	// city 
	$("#district").change(function () {
        var state_id = $("#state").val();
		var district_id = $("#district").val();		
		var city_id = $("#city_id").val();
		if(state_id == '' &&  $("#state_id").val() !== undefined){
			state_id = $("#state_id").val();
		}
		if(district_id == '' &&  $("#district_id").val() !== undefined){
			district_id = $("#district_id").val();
		}
		if(state_id !='' && district_id != '' && district_id != 0){
			$("#district_others").css('display','none');
			$("#city").html('');
			
			var cityOpt = "<option value=''>--Select City--</option>";			
			$.post('get_city',{state_id : state_id, district_id : district_id},function(data){					
			
			 	if(district_id != '' && data.city_list != '' && data.city_list != null){
						var cities = data.city_list;
						$.each(cities,function(key, elements){
							cityOpt += "<option value='"+elements.city_id+"'";
							if(elements.city_id == city_id){
								cityOpt +=" selected='selected'";
							}
							cityOpt += ">"+elements.city_name+"</option>";				   	
					   });
				}	
				
				//cityOpt += "<option value='0'>Others</option>";				
			 	$("#city").html(cityOpt);			 			 	
							   
		   },'json');
		}else if(state_id !='' && district_id == 0){			
			$("#district_others").css('display','block');
			
		}else{
			$("#city").html("<option value=''>--Select City--</option>");			
		}
		$("#city").change();
    });
  	$("#city").change(function(){			
							   
		var franchi_type = $("#franchi_type").val();
		if(franchi_type == 5){
			var franchi_name = $("#franchi_type option:selected").text();
			var relation_id =  $("#city").val();			
		}
		
		if($("#city option:selected").text() == "Others"){
				$("#city_others").css('display','block');
		}else{
			$("#city_others").css('display','none');	
		}
	});	
  
});						   
