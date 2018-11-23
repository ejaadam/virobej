
<!-- Profile Image -->
<div class="box box-primary">
	<div class="box-body box-profile">
		<div class="container-fluid">
			<div id="err_msg">
			</div>
			<form method="post" class="form-horizontal form-bordered" id="profile_image_update" action="{{route('aff.profile.profileimage_save')}}" autocomplete="off"  enctype="multipart/form-data">
			   
				<!-- profile image upload -->
				<div class="form-group">
					<label class="col-sm-3 control-label">{{trans('affiliate/profile.upload_profile_image')}}<span class="danger" style="color:red;">*</span></label>
					<div class="col-sm-9">
						@if(isset($userSess->profile_image) && !empty($userSess->profile_image)) 
						<div id="image-holder" class="fileupload-new img-thumbnail" style="width:108px; height:85px;" 
						data-original="{{url(config('constants.PROFILE_IMAGE_PATH').$userSess->profile_image)}}">
							<img id="image_upload_show1" src="{{ url('' .config('constants.PROFILE_IMAGE_PATH').$userSess->profile_image) }}" width="100%" height="100%"/>
						</div>
						@else
						<div id="image-holder" class="fileupload-new img-thumbnail" style="width:108px; height:85px;" data-defaultimage="{{asset(config('constants.PROFILE_IMAGE_PATH').''.config('constants.DEFAULT_IMAGE'))}}">
							<img id="image_upload_show1" src="{{asset(config('constants.PROFILE_IMAGE_PATH').''.config('constants.DEFAULT_IMAGE'))}}" width="100%" height="100%"/>
						</div>
						@endif	
					</div>

					<div class="pull-right col-sm-9 fileupload fileupload-new" data-provides="fileupload">
						<div class="input-append">
						<!-- fileupload-preview -->
							<div class="uneditable-input" >  <i class="glyphicon glyphicon-file fileupload-exists"></i> <span class="fileupload-preview"></span> </div>
							<span class="btn btn-default btn-file"> <span class="fileupload-new">{{trans('affiliate/general.select_file')}}</span>
								<input type="file" name="prof_image" id="prof_image"  class="form-control"  data-url="<?php echo url('account/profile/tempimg_upload')?>" />
								<input type="hidden" name="profile_image_name" id="profile_image_name" value="" class="form-control" />
							</span>&nbsp;
							<button href="#" class="btn btn-link red fileupload-exists" id="remove_tmp_image" data-dismiss="fileupload">{{trans('affiliate/general.remove')}}</button>
							<div>{{trans('affiliate/general.file_format')}}</div>
						</div>
					</div>
				</div>
				<!-- profile image upload -->
				<div class="form-group form-actions">
					<div class="col-md-9 col-md-offset-3">
						<button name ="Send" type="submit" class="btn btn-sm btn-primary" id="update_profile_image"><span>{{trans('affiliate/profile.update_profile_image')}}</span></button>&nbsp;
						<a href="{{url('account/profile')}}" class="btn btn-sm btn-primary" id="back_btn">{{trans('affiliate/general.back_btn')}}</a>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="modal fade" id="image_resize" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:700px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">{{trans('affiliate/general.crop_image')}}</h4>
				</div>
				<div class="modal-body">
					<div class="block">
						<div class=" form-group row">
							<div class="col-md-12">
								<div class="img-container">
									<img src="{{asset(config('constants.PROFILE_IMAGE_PATH').''.config('constants.DEFAULT_IMAGE'))}}" class="img img-thumbnail col-lg-12" alt="Picture">
								</div>
							</div>
							<div class="col-lg-12 text-center" style="margin-top: 10px;" id="actions">
								<div class="col-md-12 docs-buttons">
									<!-- <h3 class="page-header">Toolbar:</h3> -->
									<div class="btn-group">
										<button type="button" class="btn btn-primary" data-method="setDragMode" data-option="move" title="Move">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;move&quot;)">
												<span class="fa fa-arrows"></span>
											</span>
										</button>
										<button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
												<span class="fa fa-crop"></span>
											</span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
												<span class="fa fa-search-plus"></span>
											</span>
										</button>
										<button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
												<span class="fa fa-search-minus"></span>
											</span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-primary" data-method="rotate" data-option="-5" title="Rotate Left">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-5)">
												<span class="fa fa-rotate-left"></span>
											</span>
										</button>
										<button type="button" class="btn btn-primary" data-method="rotate" data-option="5" title="Rotate Right">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(5)">
												<span class="fa fa-rotate-right"></span>
											</span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
												<span class="fa fa-arrows-h"></span>
											</span>
										</button>
										<button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
												<span class="fa fa-arrows-v"></span>
											</span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-primary" data-method="reset" title="Reset">
											<span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
												<span class="fa fa-refresh"></span>
											</span>
										</button>
									</div>
									<div class="btn-group btn-group-crop">
										<button type="button" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 200, &quot;height&quot;: 200 }">
											<span class="docs-tooltip" data-toggle="tooltip" title="Save">
											   {{trans('affiliate/general.save')}}
											</span>
										</button>
									</div>
								</div><!-- /.docs-buttons -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.box-body -->

<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>