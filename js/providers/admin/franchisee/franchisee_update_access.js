var err_franchi = 1;
$(document).ready(function(){
	var franchi_type = $("#franchi_type").val();
	var franchi_name = $("#franchi_type option:selected").text();
	var relation_id = '';
	
	switch(franchi_type){
		case '1':							
			relation_id = $("#country option:selected").val();
			$('.country').css('display','block');
			  $('.state').css('display','none');
			   $('.region').css('display','none');
			  $('.district').css('display','none');
			  $('.city').css('display','none');
		break;
		case '2':
			relation_id = $("#region option:selected").val();	
			 $('.country').css('display','block');
			  $('.region').css('display','block');
			   $('.state').css('display','none');
			  $('.district').css('display','none');
			  $('.city').css('display','none');
		break;
		case '3':
			relation_id = $("#state option:selected").val();
			 $('.country').css('display','block');
		   $('.region').css('display','none');
		  $('.state').css('display','block');
		  $('.district').css('display','none');		 
		  $('.city').css('display','none');
		break;
		case '4':
			relation_id = $("#district option:selected").val();	
			$('.country').css('display','block');
		  $('.state').css('display','block');
		  $('.district').css('display','block');
		   $('.region').css('display','none');
		  $('.city').css('display','none');
		break;
		case '5':
			relation_id = $("#city option:selected").val();	
			$('.country').css('display','block');
		  $('.state').css('display','block');
		  $('.district').css('display','block');
		   $('.region').css('display','none');
		  $('.city').css('display','block');
		break;
	}
	//check_existing_frachise_access(franchi_type, relation_id, franchi_name);
	$('#franchi_type').change();
  $(document.body).on('change','#franchi_type',function(e){
	  var franci_type = $(this).val();	  	 
	  if(franci_type ==1){
		  $('.country').css('display','block');
		  $('.state').css('display','none');
		   $('.region').css('display','none');
		  $('.district').css('display','none');
		  $('.city').css('display','none');
	  }else if(franci_type == 2){
		  $('.country').css('display','block');
		  $('.region').css('display','block');
		   $('.state').css('display','none');
		  $('.district').css('display','none');
		  $('.city').css('display','none');
	  }else if(franci_type == 3){
		  $('.country').css('display','block');
		   $('.region').css('display','none');
		  $('.state').css('display','block');
		  $('.district').css('display','none');		 
		  $('.city').css('display','none');
	  }else if(franci_type == 4){
		  $('.country').css('display','block');
		  $('.state').css('display','block');
		  $('.district').css('display','block');
		   $('.region').css('display','none');
		  $('.city').css('display','none');
	  }else if(franci_type == 5){
		  $('.country').css('display','block');
		  $('.state').css('display','block');
		  $('.district').css('display','block');
		   $('.region').css('display','none');
		  $('.city').css('display','block');
	  }else{
		  $('.country').css('display','none');
		  $('.region').css('display','none');
		  $('.state').css('display','none');
		  $('.district').css('display','none');
		  $('.city').css('display','none');
	  }
	  
	    var franchi_type = $("#franchi_type").val();
		var franchi_name = $("#franchi_type option:selected").text();
		var relation_id = '';
		
		switch(franchi_type){
			case '1':							
				relation_id = $("#country option:selected").val();						
			break;
			case '2':
				relation_id = $("#region option:selected").val();	
			break;
			case '3':
				relation_id = $("#state option:selected").val();	
			break;
			case '4':
				relation_id = $("#district option:selected").val();	
			break;
			case '5':
				relation_id = $("#city option:selected").val();	
			break;
		}
		if(relation_id != ''){
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}
  });
  
  /* Franchisee access form validation */
	$("#update_access").click(function () {
		var franchi_type = $('#franchisee_access #franchi_type').val();
	
		var franchi_name = $("#franchi_type option:selected").text();
		var relation_id = '';
		//var i = 0;
		switch(franchi_type){			
			case '1':							
				relation_id = $("#country option:selected").val();						
			break;
			case '2':
				relation_id = $("#region option:selected").val();	
			break;
			case '3':				
				relation_id = $("#state option:selected").val();	
				/*if($("#union_territory") != undefined){				
					
					$('#union_territory :selected').each(function(i, selected){ 
						i++;						
						relation_id[$i] = $(selected).val();						
					});					
				}*/
				
			break;
			case '4':
				relation_id = $("#district option:selected").val();	
			break;
			case '5':
				relation_id = $("#city option:selected").val();	
			break;
		}
		check_existing_frachise_access(franchi_type, relation_id, franchi_name);
	    $("#franchisee_access").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
	        // Specify the validation rules
            rules: {
                country: "required",
                region: {
                    required:function (element){
						return (franchi_type != '1' && franchi_type != '5' && franchi_type != '3' &&franchi_type != '4')
					}
                },
                state: {
                       required:function (element){
						  return (franchi_type != '1' && franchi_type != '2')
					}
                },
                 district: {
                       required:function (element){
						  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3')
					}
                },
                 city: {
                       required:function (element){
						  return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3' &&franchi_type != '4' )
					}
                }
              },
            // Specify the validation error messages
            messages: {
                country: "Please select Country",
                region: "Please select Region",
                state: "Please select State",
                district: "Please select District",
                city: "Please select city"
	        },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
					
					if(!err_franchi){
                   var datastring = $(form).serialize();				 
                    $.ajax({
                        url: $(form).attr('action'), 
                        type: "POST", 
                        data: datastring, 
                        dataType: "json",
						beforeSend:function(){
							$('#access_edit #update_access').val("Processing..");
						},
                        success: function (data) 	
                        {
                            if(data.status =='ok'){								
								var franchi_name = $("#franchi_type option:selected").text()
								$('#msg').html('<div class="alert alert-success">'+data.msg+'</div>');
								$('#access_edit').hide();
								$("#view_user_profile .modal-body").html('<div class="alert alert-success">'+data.msg+'</div>');
								
								$('td.franch_type'+data.user_id).text(franchi_name);								
							}
                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
					}
                }
            }
        });
	});	
	
	
	$("#country").change(function () {
        var country_id = $("#country").val();
       if(country_id !=''){	  	
	   		$(".union_territory").css('display','none');		
			$("#union_territory").html('');
			$("#region").val('');
			$("#state").val('');	
			var regionOpt = "<option value=''>--Select Region--</option>";
			var stateOpt = "<option value=''>--Select State--</option>";
			var franchi_type = $("#franchi_type").val();		
						
			$.post('get_franchisee_state_phonecode',{country_id : country_id},function(data){													
			 	
				if(data.region_list != '' && data.region_list != null){
						var regions = data.region_list;
						$.each(regions,function(key, elements){
							regionOpt += "<option value='"+elements.region_id+"'>"+elements.region_name+"</option>";				   	
					   });
				}
																
			 	if(data.state_list != '' && data.state_list != null){
						var states = data.state_list;
						$.each(states,function(key, elements){
							stateOpt += "<option value='"+elements.state_id+"'>"+elements.name+"</option>";				   	
					   });
				}
			  $("#region").html(regionOpt); 	
			  $("#state").html(stateOpt);					  
			
		   },'json');			
			
			//if(franchi_type == 1){				
				
				var relation_id =  $("#country").val();
				var franchi_name = $("#franchi_type option:selected").text();		
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
			
		}else{
			
			$("#region").html("<option value=''>--Select Region--</option>");	
			$("#state").html("<option value=''>--Select State--</option>");
		}
    });	

	// district 
	 $("#state").change(function () {
        var state_id = $("#state").val();
		$("#union_territory").html('');
		$(".union_territory").css('display','none');		
	    if(state_id !=''  && state_id != null){
			$("#district").html('');			
			var districtOpt = "<option value=''>--Select District--</option>";	
			var franchi_type = $("#franchi_type").val();
			var territoryOpt = '';
			$.post('get_franchisee_district',{state_id : state_id},function(data){
				if(data.territory_list != '' && data.territory_list != null){
						var territory = data.territory_list;
						$.each(territory,function(key, elements){
							territoryOpt += "<option value='"+elements.state_id+"' selected='selected'>"+elements.state_name+"</option>";							
							
					   });
				}		
				if(data.district_list != '' && data.district_list != null){
						var districts = data.district_list;
						$.each(districts,function(key, elements){
							districtOpt += "<option value='"+elements.district_id+"'>"+elements.district_name+"</option>";				   	
					   });
				}
				
				//districtOpt += "<option value='0'>Others</option>";
				if(territoryOpt != ''){					
					$(".union_territory").css('display','block');
					$("#union_territory").html(territoryOpt);							
				}else{
					$("#union_territory").html('');
					$(".union_territory").css('display','none');
				}
			 	$("#district").html(districtOpt);			 			 	
							   
		   },'json');
						 
			//if(franchi_type == 3){
				var franchi_name = $("#franchi_type option:selected").text();
				var relation_id =  $("#state").val();							
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
			  
		}else{
			$("#district").html("<option value=''>--Select City--</option>");			
		}
		$("#district").change();
		$("#city").change();
    });
	
	// city 
	$("#district").change(function () {
        var state_id = $("#state").val();
		var district_id = $("#district").val();	
		
		if(state_id !='' && district_id != '' && district_id != 0 && district_id != null){
			$("#district_others").css('display','none');
			$("#city").html('');
			
			var franchi_type = $("#franchi_type").val();
			
			
			var cityOpt = "<option value=''>--Select City--</option>";			
			$.post('get_city',{state_id : state_id, district_id : district_id},function(data){					
			
			 	if(district_id != '' && data.city_list != '' && data.city_list != null){
						var cities = data.city_list;
						$.each(cities,function(key, elements){
							cityOpt += "<option value='"+elements.city_id+"'>"+elements.city_name+"</option>";				   	
					   });
				}	
				
				//cityOpt += "<option value='0'>Others</option>";				
			 	$("#city").html(cityOpt);			 			 	
							   
		   },'json');
			// if(franchi_type == 4){
				var franchi_name = $("#franchi_type option:selected").text();
				var relation_id =  $("#district").val();
						
				check_existing_frachise_access(franchi_type, relation_id, franchi_name);
			//}
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
					
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}
		
		if($("#city option:selected").text() == "Others"){
				$("#city_others").css('display','block');
		}else{
			$("#city_others").css('display','none');	
		}
	});
	
	$("#region").on('change',function(){	
														  
		var franchi_type = $("#franchi_type").val();
		if(franchi_type == 2){
			var franchi_name = $("#franchi_type option:selected").text();
			var relation_id =  $("#region").val();
					
			check_existing_frachise_access(franchi_type, relation_id, franchi_name);
		}
	
	});
  
});

function check_existing_frachise_access(franchise_type, relation_id, franchi_name){		
		$.post('check_franchise_access',{franchise_type : franchise_type, relation_id : relation_id, franchi_name : franchi_name},function(data){
				if(data.status =='ok'){
					err_franchi = 0;	
					$('#franchisee_status').html('');
					$('#update_access').show();
					
					var country_id = ''; 
					var state_id = '';
					var district_id = '';
									
					
					if($("#country") !== undefined && $("#country option:selected").val() !== undefined){
						country_id = $("#country option:selected").val();						
					}
					if($("#state") !== undefined && $("#state option:selected").val() !== undefined)
						state_id = $("#state option:selected").val();
					if($("#district") !== undefined && $("#district option:selected").val() !== undefined)
						district_id = $("#district option:selected").val();	
					
					
					$.post('check_franchise_mapped',{franchise_type : franchise_type, country_id : country_id, state_id : state_id, district_id : district_id},function(data){
							var franchisee_mapped_users = '';
							
							if(data.country_franchisee != '')
								franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Country Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.country_franchisee+' </div></div></div></div>';
							if(data.region_franchisee != '')
								franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Regional Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.region_franchisee+' </div></div></div></div>';
							if(data.state_franchisee != '')
								franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">State Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.state_franchisee+' </div></div></div></div>';
							if(data.district_franchisee != '')
								franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">District Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">'+data.district_franchisee+' </div></div></div></div>';
							
							if(	franchisee_mapped_users === undefined)
								franchisee_mapped_users = '';
								
							$("#franchisee_mapped_user").html(franchisee_mapped_users);																														   
					});
					
				 }else if(data.status =='error'){
					 err_franchi = 1;
					$('#franchisee_status').html(data.msg);
					$('#update_access').hide();					
				 }			
	   },'json')
}