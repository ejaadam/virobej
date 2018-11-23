
 <p class="line_b">
  
  	
	@if($users_info)
	 {{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.full_name').' &amp; '.trans('affiliate/referrels/generation_viewer_my_genelogy_dir.user_name).': '.$direct->full_name. ' ('.$direct->uname.')'}}<br />  
	 {{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.signed_up_on') .': '.date('d-m-Y' ,strtotime($direct->signedup_on))}}<br />
	 {{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.act_date').': '}} {{ (!empty($direct->activated_on) && $direct->activated_on != '0000-00-00 00:00:00') ? date('d-m-Y' ,strtotime($direct->activated_on)) : '-';}}<br />
					
	@endif              
 </p>
               