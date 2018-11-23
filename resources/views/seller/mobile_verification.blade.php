<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-offset-4 col-sm-4">
				<h3 class="text-center">Mobile Verification</h3>
				<br>
					<p class="text-center"><strong>Please verify your mobile number</strong></p>
				<p>
				<div id="msg"></div>
				<div><a href="#" id="change_mobile">Change Mobile Number</a></div>
				<br>
					<input type="hidden" name="fullname" id="fullname" value="{{$full_name or ""}}">	
					<form id="mobile-verification-form" action="{{route('seller.check-verification-mobile')}}">
						<div class="form-group">
						<span class="text-danger"><strong>One Time Password(OTP)</strong></span>
								<input type="text" name="verification_code" id="verification_code" placeholder="Verification Code" class="form-control "/>
								<a href="" class="btn-link" data-url="{{route('seller.verify-mobile')}}" id="resend-verification-code">Resend OTP</a>
						</div>
						<div class="form-group">
							<input type="submit" class="form-control btn btn-sm btn-success" value="VERIFY OTP"/>
						</div>
					</form>
		</div>
    </div>
</div>
 <div class="modal fade modal-sm" id="mobile-model" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Change Mobile Number</h4>
        </div>
        <div class="modal-body">
			<form id="change-mobile-form" class="col-md-12" action="{{route('seller.change-reg-mobile')}}">
				
				<div class="form-group">
						<input type="text" name="mobile_no" id="mobile_no"  placeholder="Mobile Number" class="form-control input-lg"/>
				</div>
				<div class="row">
					<div class="col-md-6">
					  <button type="button" class="btn btn-default pull-right" data-dismiss="modal">CANCEL</button>
					</div>
					<div class="col-md-6">
					  <button type="button" class="btn btn-success" id="change-mobile-btn">SAVE</button>
					</div>
				</div>
			</form>
        </div>        
      </div>      
    </div>
  </div>
<script src="{{asset('js/providers/seller/mobile-verification.js')}}"></script>
