@if(!$logged_userinfo->is_email_verified)
<div class="row" id="verify-email-id-block">
    <div class="col-sm-12 text-center">
        <h1>E-Mail Verification</h1>
        <p>Your account email has not verified yet. Please verify to receive instant notifications</p>
        <a href="" class="btn btn-sm btn-success" id="send-email-verification-link" data-url="{{route('seller.verify-email')}}">Verify Now</a>  <a href="" class="dismiss btn btn-link">Skip</a>
    </div>
</div>
<script src="{{asset('js/providers/seller/email-id-verification.js')}}"></script>
@endif

