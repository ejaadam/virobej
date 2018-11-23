<div class="col-md-12" id="crop-image"  style="display:none">
	<div class="panel panel-default">
		<div class="panel-heading">		
			<h4 class="panel-title">Crop Image</h4>
			<div class="box-tools pull-right">
				<button type="button" class="btn bg-red btn-sm close-cropper"><i class="fa fa-arrow-left"></i></button>
			</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<div class="form-group">
					<div class="col-sm-12">
						<div class="img-container">
							<img src="{{asset('imgs/offers/300/300/offers-1.jpg')}}" class="img img-thumbnail col-md-12" alt="Picture">
						</div>
					</div>
					<div class="col-lg-12 text-center" style="margin-top: 10px;" id="actions">
						<div class="col-md-12 docs-buttons">
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="setDragMode" data-option="move" title="Move">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;move&quot;)">
										<span class="icon-arrows"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
										<span class="icon-crop"></span>
									</span>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
										<span class="icon-plus"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
										<span class="icon-minus"></span>
									</span>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="-5" title="Rotate Left">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-5)">
										<span class="icon-rotate-left"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="5" title="Rotate Right">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(5)">
										<span class="icon-rotate-right"></span>
									</span>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
										<span class="icon-arrows-h"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
										<span class="icon-arrows-v"></span>
									</span>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="reset" title="Reset">
									<span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
										<span class="icon-refresh"></span>
									</span>
								</button>
							</div>
							<div class="btn-group btn-group-crop">
								<button type="button" class="btn btn-primary" data-method="getCroppedCanvas">
									<span class="docs-tooltip" data-toggle="tooltip" title="Save">
										Save
									</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
