$(function () {	
var mytree = [];	
       $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
        $('.tree li.parent_li').on('click', 'span', function (e) {
		
            var children = $(this).parent('li.parent_li').find(' > ul > li');
			var accountid = $(this).data('value');			
            if (children.is(":visible")) {  
                children.hide('fast');
                $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');				
            } else {		
                var accountid = $(this).data('value');
                var id = "ch_" + accountid;
                var stateOpt = '';                  
                $.post('account/referrals/get-sponsor-geneology/'+accountid, function (data) {  	
                    if (data.status == "ok") {  
                        $.each(data.sponsor, function (key, elements) {
						console.log(elements);
							mytree['ch_'+elements.account_id] = elements;
                            stateOpt += "<li class='parent_li'><span data-value='" + elements.account_id+ "' title='Expand this branch'><b>" + elements.username + "</b>";		
							stateOpt += " ( " +elements.fullname+ " )</span>";
							stateOpt += "<ul id='ch_" + elements.account_id + "'> <li style='display:none'><span>Loading....</span></li></ul>";
					        stateOpt += "</li>";			
                        });						
                    }
                    $("#" + id).html(stateOpt);
					showUserinfo(id,mytree)
					
                }, 'json');
                children.show('fast');
                $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
            }
            e.stopPropagation();
        });		
		//$('.tree ul:first li:first.parent_li span:first').trigger('click');	
		
    });
	
	function showUserinfo(id,list){
	
		if(list[id]!= undefined && list[id]!='' ){
			//console.log(list[id]);return false;
			document.getElementById("tree_acfullname").innerHTML = list[id].fullname+' & '+list[id].username ;
			document.getElementById("tree_acinvby").innerHTML = list[id].sponser_uname;
			document.getElementById("tree_acginv_cnts").innerHTML = list[id].upline_uname;
			document.getElementById("tree_actsinby").innerHTML = list[id].signedup_on;
			document.getElementById("tree_actby").innerHTML = list[id].activated_on;
			
		}
	}
	

	
	
	