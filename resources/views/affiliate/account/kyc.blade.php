@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/kyc.page_title'))
@section('content')
<!-- Content Header (Page header) -->

    <section class="content-header">
      <h1><i class="fa fa fa-files-o"></i>{{\trans('affiliate/kyc.page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li >{{\trans('affiliate/profile.page_title')}}</li>
		<li class="active">{{\trans('affiliate/kyc.breadcrumb_title')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="kycfrm" style="display:none" >        
			<!-- ./col -->
            <!-- kycfrm -->
			<div class="col-md-8">			            	
                <div class="box box-primary">
					<div class="box-header with-border">
                    	<i class="fa fa-edit"></i>
						<h3 class="box-title">{{trans('affiliate/kyc.upload_proof')}}</h3>
                        <div class="box-tools pull-right">
	                        <button type="button" class="btn btn-default btn-sm backbtn"><i class="fa fa-arrow-left"></i> Back to List</button>
                      	</div>
					</div>
					<div class="box-body">
                    	<!-- box-body-->
                        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                            <li><a href="#IDProof" data-toggle="tab">{{trans('affiliate/kyc.id_proof_with_photo')}}</a></li>
                            <li><a href="#addproof" data-toggle="tab">{{trans('affiliate/kyc.proof_of_address')}}</a></li>
                            <li class="active"><a href="#Tinpancard" data-toggle="tab"> {{trans('affiliate/kyc.tin_pancard')}}</a></li>
                        </ul>
                        <div id="my-tab-content" class="tab-content">
                        <div class="tab-pane active" id="Tinpancard">
                            
                                <h3>{{trans('affiliate/kyc.tin_pancard')}}</h3>
                                <p>{!!trans('affiliate/kyc.tax_id_proof_desc')!!}</p>
                                <ul type="disc">
                                    <li>{!!trans('affiliate/kyc.pan_card_notes')!!}</li>
                                </ul>
                                @if(is_array($verification_count) && isset($verification_count[config('constants.VERIFY_DOC_TAX_ID_PROOF')]) && $verification_count[config('constants.VERIFY_DOC_TAX_ID_PROOF')]>0)
                                <div class='alert alert-success'>{{trans('affiliate/kyc.uploade_msg')}}</div>
                                @else
                                <form action="{{route('aff.profile.kyc_upload')}}" class="form" method="post" enctype="multipart/form-data" id="document_upload">
									{!! csrf_field() !!}                                    
									<div class="form-group">
										<input type="file" name="verify_file" id="document_upload_file" data-formatallowd="gif|jpg|jpeg|png|pdf" data-error="Please select valid formet(*.gif, *.jpg, *.jpeg,*.pdf, *.png)" class="file_upload">
									</div>
									<div class="form-group">
										<div class="progress" style="height:20px; display:none;">
											<div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" style="background-color:green;">
												<div class="percent">0%</div >
											</div>
										</div>
									</div>                                    
                                </form>
                                @endif
                                <!-- box-footer -->
                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary pull-right hidden"  name="submit_verification" id="document_upload_file_btn" data-form="document_upload">{{trans('affiliate/kyc.submit_btn')}}</button>
                                </div>
                                <!-- box-footer -->
                            
                        </div>
                        <div class="tab-pane" id="IDProof">
                            <div class="page-heading">
                                <h3>{{trans('affiliate/kyc.id_proof_with_photo')}}</h3>
                                <p>{!!trans('affiliate/kyc.personal_id_proof_desc')!!}</p>
                                <ul type="disc">
                                    {!!trans('affiliate/kyc.document_msg',array('accout'=> config('constants.WEB_SITE')))!!}
                                </ul>
                                @if(is_array($verification_count) && isset($verification_count[config('constants.VERIFY_DOC_ID_PROOF')]) && $verification_count[config('constants.VERIFY_DOC_ID_PROOF')]>0)
                                <div class='alert alert-info'>{{trans('affiliate/kyc.uploade_msg')}}</div>
                                @else
                                <p> {{trans('affiliate/kyc.select_photo_id')}}</p>
                                <form action="{{route('aff.profile.kyc_upload')}}" method="post" enctype="multipart/form-data" id="photo_id">
									{!! csrf_field() !!}
                                    <?php
									if(isset($document_types) && !empty($document_types)){
                                    foreach ($document_types as $docitem){
                                        echo '<div class="radio"><label for="address_types'.$docitem->document_type_id.'"><input type="radio" name="document_types" value="'.$docitem->document_type_id.'" id="address_types'.$docitem->document_type_id.'"><b>'.$docitem->doctype_name.'</b></label></div>';
                                    }
									}
                                    ?>
                                    <div class="form-group">
                                        <input type="file" name="verify_file"  id="photo_id_file" data-formatallowd="gif|jpg|jpeg|png|pdf" data-error="Please select valid formet(*.gif, *.jpg, *.jpeg,*.pdf, *.png)" >
                                    </div>
                                    <div class="form-group">
                                        <div class="progress" style="height:20px; display:none;">
                                            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" style="background-color:green;">
                                                <div class="percent">0%</div >
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <!-- box-footer -->
                            <div class="box-footer">
                            	<button type="button" class="btn btn-primary pull-right hidden"  name="submit_verification" id="photo_id_file_btn" data-form="photo_id">{{trans('affiliate/kyc.submit_btn')}}</button>
                         	</div>
                            <!-- box-footer -->
                        </div>
                        <div class="tab-pane" id="addproof">
                            <div class="page-heading">
                                <h3>{{trans('affiliate/kyc.address_proof')}}</h3>
                                <p>{!!trans('affiliate/kyc.address_id_proof_desc')!!}</p>
                                <ul type="disc">
                                    {!!trans('affiliate/kyc.proof_address_msg')!!}
                                </ul>								
                                @if(is_array($verification_count) && isset($verification_count[config('constants.VERIFY_DOC_ADDRESS_PROOF')]) && $verification_count[config('constants.VERIFY_DOC_ADDRESS_PROOF')]>0)
                                <div class='alert alert-info'>{{trans('affiliate/kyc.uploade_msg')}}</div>
                                @else
                                <p>{{trans('affiliate/kyc.proof_address')}}</p>
                                <form action="{{route('aff.profile.kyc_upload')}}" method="post" enctype="multipart/form-data" id="address_id">
									{!! csrf_field() !!}
                                    <?php
									if(isset($document_types1) && !empty($document_types1)){
                                    foreach ($document_types1 as $docitem) {
                                        echo '<div class="radio"><label for="address_types'.$docitem->document_type_id.'" class="control-label"><input type="radio" name="document_types" value="'.$docitem->document_type_id.'" id="address_types'.$docitem->document_type_id.'"><b>'.$docitem->doctype_name.'</b></label></div>';
                                    }
									}
                                    ?>
                                    <div class="form-group">
                                        <input type="file" name="verify_file" id="address_id_file"  data-formatallowd="gif|jpg|jpeg|png|pdf" data-error="Please select valid formet(*.gif, *.jpg, *.jpeg,*.pdf, *.png)">
                                    </div>
                                    <div class="form-group">
                                        <div class="progress" style="height:20px; display:none;">
                                            <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" style="background-color:green;">
                                                <div class="percent">0%</div >
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <!-- box-footer -->
                            <div class="box-footer">
                            	<button type="button" class="btn btn-primary pull-right hidden"  name="submit_verification" id="address_id_file_btn" data-form="address_id">{{trans('affiliate/kyc.submit_btn')}}</button>
                         	</div>
                            <!-- box-footer -->
                        </div>
                    </div>
                        <!-- box-body -->
                    </div>
                </div>                
            </div>
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-info-circle"></i>
                        <h3 class="box-title">{{trans('affiliate/kyc.upload_guidence')}}</h3>                       
                    </div>
                    <div class="box-body">
                        <!-- box-body-->
                        <ol>
                        {!!trans('affiliate/kyc.document_upload_notes')!!}
                        </ol>
                        {!!trans('affiliate/kyc.document_upload_warning')!!}                        
                        <p>{!!trans('affiliate/kyc.file_submit_format')!!}</p>                        
                        <!-- box-body -->
                    </div>
                </div>
            </div>
        </div>
        <!-- kycfrm -->
            <!-- kycgrid -->
        <div class="row"  id="kycgrid">        
            <div class="col-md-12">
			    <div class="box box-primary">
					<div class="box-header with-border">
	                    <i class="fa fa-list-alt"></i>
						<h3 class="box-title">{{trans('affiliate/kyc.table_title')}}</h3>
                        <div class="box-tools pull-right">	                        
                            <a href="{{route('aff.profile.kyc_upload')}}" class="btn btn-social bg-navy btn-sm uploadkyc"><i class="fa fa-upload"></i> {{trans('affiliate/kyc.upload_proof')}}</a>
                      	</div>
					</div>
					<div class="box-body">
						<table id="kyclist" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                                        
                                <th >{{\trans('affiliate/kyc.document_type')}}</th>  
                                <th width="10%" class="text-center no-sort">{{\trans('affiliate/kyc.verification')}}</th>
                                <th width="15%">{{\trans('affiliate/kyc.create_on')}}</th>        
                            </tr>
                        </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            if (!empty($kycdocs))
                            {
                                foreach ($kycdocs as $row)
                                {
                                    $verification_imgformat = array('jpg','jpeg','gif','png');
                                    ?>
                                    <tr>                                        
                                        <td class="text-left">
                                        <?php 
                                        $fpath = '';
                                        if(in_array(pathinfo($row->path,PATHINFO_EXTENSION),$verification_imgformat)) {
	                                        $fpath = URL::asset(config('constants.USER_VERIFICATION_MIN_VIEWPATH').$row->path);
										}								
										if(!empty($row->proof_type_name)) {
										echo  '<a href="'.$fpath.'" title="Download" target="_blank"><i class="fa fa-download"></i> '.$row->proof_type_name.' </a><i class="fa fa-angle-right"></i> '.$row->doc_type;
                                        }
                                        else {
                               			echo  '<a href="'.$fpath.'" title="Download"  target="_blank"><i class="fa fa-download"></i> '.$row->doc_type.' </a>';         
                                        }
										echo ($row->status == 2 && !empty($row->comments))? '<br /><span class="text-danger">'.$row->comments.'</span>':'';
										?></td> 
                                        <td class="text-center">
                                        <?php
                                        if ($row->status == 1) {
                                            echo '<p><span class="label label-success">'.\trans('affiliate/kyc.verified').'</span>';
                                        }
                                        else if ($row->status == 2) {
                                            echo '<span class="label label-danger">'.\trans('affiliate/kyc.reject').'</span></p>';
                                        }
                                        else {
                                            echo '<span class="label label-warning">'.\trans('affiliate/kyc.pending').'</span>';
                                        }
                                        ?>
                                        </td>   
                                        <td>{{date('d-M-Y H:i:s', strtotime($row->created_on))}}</td>           
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>				
                    </table>
					</div>					
				</div>
				<!-- kycgrid -->
                
			</div>
			<!-- ./col -->				
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
<!-- DataTables -->
@include('user.common.datatable_js')
<script src="{{asset('resources/assets/themes/affiliate/plugins/jQuery/jquery.form.js')}}"></script>
<script src="{{asset('account/validate/lang/user-verification')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('js/providers/affiliate/kyc.js')}}"></script>
@stop
