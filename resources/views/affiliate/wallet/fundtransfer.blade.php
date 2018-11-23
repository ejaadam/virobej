@extends('affiliate.layout.dashboard')
@section('title',"Fund Transfer")
@section('content')
<?php	
if ($userdetails->status == 1)
{
 ?>
<script src="{{asset('js/providers/affiliate/wallet/fundtransfer.js')}}"></script>  
<script src="{{asset('account/validate/lang/fund_transfer.js')}}"></script>
<script src="{{asset('js/providers/affiliate/wallet/other_functionalities.js')}}"></script>
<script>
        var balArray = new Array();
    <?php
    if (!empty($user_balance_det) && count($user_balance_det) > 0)
    {
        foreach ($user_balance_det as $k=> $v)
        {
            ?>
                balArray['<?php echo $v->wallet_id;?>'] = '<?php echo $v->current_balance;?>';
            <?php
        }
    }
    ?>
    </script>
		
	<section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/wallet/fundtransfer.page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Wallet</li>
        <li class="active">{{\trans('affiliate/wallet/fundtransfer.breadcrumb_title')}}</li>
      </ol>
    </section>
    <section class="content">  
      <div class="row"  id="kycgrid">        
            <div class="col-md-6">
		   <div class="box box-primary">
           <section class="panel">
                    <div class="panel-body">
                     <div class="alert alert-success hidden" id="msg">
					  <button data-dismiss="alert" class="close" type="button">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>
                        </div>
					
                        <?php
                        if (Session::get('success'))
                        {
                        ?>
                            <div class="alert alert-success" >
                                <?php
                                echo Session::get('success');
                                Session::put('success', '');
                                ?>
                                <button data-dismiss="alert" class="close" type="button">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>
                                <br>
                            </div>
                            <?php
                        }
			
						 if ($userdetails->status == 1 && $userdetails->block == 0 && $account_verif_count >= 1	 &&
						  $userdetails->system_role_id == config('constants.USER_ROLE_USER'))
                        {
							
                            ?>
				                    <div class="box-header with-border">			
                                    <div class="form_fields">
				                    <style type="text/css">
					                	.hidefld,.hidefld1,.hidefld2,.hidefld3,#form_data{
						            	<?php
						            	if (empty($show_all))
						             	{
							                  	?>
								                 display:none;
								               <?php
							             }
							            else
							             {
								               ?>
								                 display:block;
								               <?php
						               	}
							            ?>
						}
						.help-block{
							color:#f56954;
						}
					</style>
					 @include('user.wallet.fund_transfer_form')
                	</div>
					</div>
					<?php
					}
					elseif ($account_verif_count == 0)
					{
						echo trans('affiliate/wallet/fundtransfer.kyc_document_msg');
					}
					else
					{
						echo trans('affiliate/wallet/fundtransfer.cant_transfer_fund');
					}
					?>
				</div>
        </section>
		</div>
    </div>
	</div>
	</section>
 @stop
@section('scripts')   

   <script>
   
	 $(document).ready(function () {
		  function get_decimal_value(amt) {
           var amt = amt.toString();
           var decimal_places = 2;
           var decimal_val = amt.split('.');
           if (decimal_val[1] !== undefined) {
        if (decimal_val[1].length > 2) {
            decimal_places = (decimal_val[1].length);
            if (decimal_places > 8) {
                decimal_places = 8;
            }
        }
    }
    return decimal_places;
	};
		  var wcb = JSON.parse('{{$currency}}'.replace(/&quot;/g,'"'));
		  var fts = JSON.parse('{{$fund_trasnfer_settings}}'.replace(/&quot;/g,'"'));
          $(document.body).on('change', '#wallet_id', function () {
			    avi_bal = 0;
                $('#currency_id').html('<option value="">' + $curr_sel + '</option>');
                var wallet_id = $(this).val();
				$('.hidefld1').hide();
				$.each(wcb, function (key, ele) {
                    if (wallet_id == ele.wallet_id) {
                        $('#currency_id').append('<option value="' + ele.id + '">' + ele.code + '</option>');
                    }
                });
				if (wallet_id == '')
                {
                    $('.hidefld').hide();
                    $('.hidefld1').hide();
                    $('.hidefld2').hide();
                    $('.hidefld3').hide();
                }
                else
                {
                    $('.hidefld').show();
                }
                
            });
			$('body').on('change', '#currency_id', function () {
			    $('.hidefld1').hide();
                $('.hidefld2').hide();
                $('.hidefld3').hide();
                var avi_bal = 0;
                var min_amount = 1;
                var max_amount = 1;
				var balcurcy_code = '';
                var wallet_id = $('#wallet_id').val();
			    var currency_id = $(this).val();
                balcurcy_code = $('option:selected', $(this)).text();
                $('#totamount').val('');
				$.each(wcb, function (key, ele) {
					//console.log(ele);
                    if (wallet_id == ele.wallet_id && currency_id == ele.currency_id) {
							avi_bal = ele.current_balance;
                        return;
                    }
                });
              $.each(fts, function (key, ele) {
			   if (currency_id == ele.currency_id) {
                        min_amount = ele.min_amount;
                        max_amount = ((ele.max_amount > parseFloat(avi_bal)) ? avi_bal : ele.max_amount);
                        return;
                    }
                });
                if (max_amount == 1) {
                    max_amount = avi_bal;
                }
             var decimal_places = get_decimal_value(avi_bal);
			 if (avi_bal == '') {
				    $('#user_balance').text(parseFloat(avi_bal).toFixed(decimal_places));
                    $('#user_avail_bal').val(parseFloat(avi_bal).toFixed(decimal_places));
                    $('#min_trans_amount').val(min_amount);
                    $('#max_trans_amount').val(max_amount);
                } else {
                    if (avi_bal >= 0) {
						   $('.hidefld1').show();
                        $('#user_balance').text(parseFloat(avi_bal).toFixed(decimal_places) + ' ' + balcurcy_code);
                        $('#d_user_balance').text(parseFloat(avi_bal).toFixed(decimal_places) + ' ' + balcurcy_code);
                        $('#user_avail_bal').val(parseFloat(avi_bal).toFixed(decimal_places));
                        $('#min_trans_amount').val(min_amount);
                        $('#max_trans_amount').val(max_amount);
                        $(".err_msg3").remove();
                        $(".err_msg2").remove();
                    } else {
                        $('#user_balance').text(0.00);
                        $('#user_avail_bal').val(0.00);
                        $('#max_trans_amount').val(0.00);
                        $('#min_trans_amount').val(0.00);
                        avi_bal = 0;
                    }
                }
            });
	   });
	  
	</script>
	<?php
}
	else
{
    ?>
    <div class="wrapper">
        <!--Main row -->
        <div class="row">
            <div class="col-lg-6">
                <section class="panel">
                    <header class="panel-heading">
                    
                    </header>
                    <div class="panel-body">
                        <div class="alert alert-danger" >
                            <button data-dismiss="alert" class="close" type="button">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                         
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php }?>
@stop

