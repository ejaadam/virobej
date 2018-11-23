@extends('affiliate.layout.dashboard')
@section('title',"Sponsor Geneology")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Sponsor Geneology</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li >Referrals</li>
		<li class="active">Sponsor Geneology</li>
      </ol>
    </section>
  
    <section class="content">
		<div class="wrapper">
			<div class="row">
				<div class="col-md-12">
					<div class="panel">
						<div class="panel-body">
							<div class="tree col-md-6">
								<?php
								if($my_treeinfo != '')
								{?>
								<ul>
									<li>
									<span data-value="{{$my_treeinfo->account_id}}">
									<i class="icon-minus-sign"></i>
									<?php echo '<b' . (($my_treeinfo->block == 1) ? 'class="text-danger" title="Your Account Freezed"' : '') . '>' . 
									$my_treeinfo->full_name .'('.$my_treeinfo->uname.')' ;?>									
									</span> 
										<ul  id="ch_{{$my_treeinfo->account_id}}">
											<li style="display:none"><span>{{trans('affiliate/referrels/general.loading')}}</span></li>
										</ul>
									</li>
								</ul>	
								<?php } ?>
							</div>
							<div class="col-md-4 pull-right" id="user_info">
								<div class="col-md-6 ">{{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.user_name')}}</div> <div class="col-md-6" id="tree_acfullname"></div><br>
								<div class="col-md-6 ">{{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.invited_by')}}</div><div class="col-md-6" id="tree_acinvby"></div><br>
								<div class="col-md-6">{{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.group_id')}}</div> <div class="col-md-6" id="tree_acginv_cnts"></div><br>
								<div class="col-md-6">{{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.signed_up_on')}}</div>  <div class="col-md-6" id="tree_actsinby"></div><br>
								<div class="col-md-6 ">{{ trans('affiliate/referrels/generation_viewer_my_genelogy_dir.act_date')}}</div> <div class="col-md-6" id="tree_actby"></div><br>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<link rel="stylesheet" href="<?php echo URL::asset('assets/css/treeview.css');?>" />
</section>
    <!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/referrals/sponsor_geneology.js')}}"></script>
@stop



	