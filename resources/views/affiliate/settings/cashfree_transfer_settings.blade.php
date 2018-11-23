<form action="<?php echo URL('account/settings/account_payout_settings_update');?>" method="post" class="cashfree_transferfrmad" id="form_{{$acpayout_setting_id or ''}}" data-id="{{$acpayout_setting_id or ''}}">
	<input type="hidden" name="payment_type_id" value="{{ isset($payment_type_id)? $payment_type_id:'' }}"  />
	<input type="hidden" name="acpayout_setting_id" value="{{ $acpayout_setting_id or '' }}"  />
	<div class="form-group col-sm-12">
		@if(isset($acpayout_setting_id) && !empty($acpayout_setting_id))
			<label class="col-sm-6 control-label"  for="is_approved">{{trans('affiliate/settings/payout_settings.is_approve_label')}}</label>
			<div class="col-sm-6">
				@if(isset($is_approved) && $is_approved == 1)
					<label class="col-sm-6 control-label"  for="is_approved">Approved</label>
					@else
						<label class="col-sm-6 control-label"  for="is_approved">Processing</label>
				@endif
			</div>
		@endif
	</div>	
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="account_nickname"> {{trans('affiliate/settings/payout_settings.handling_currency_label')}}</label>
		<div class="col-sm-6">
			<?php
			if (isset($currency_list))
			{
				echo "<select name='currency_id' id=''  class='form-control' >";
				echo "<option value=''>" . trans('affiliate/settings/payout_settings.select_currency') . "</option>";
				foreach ($currency_list as $curifo)
				{
					echo "<option value='" . $curifo->currency_id . "'"; 
					if (isset($currency_id) && $currency_id == $curifo->currency_id)
					{
						echo "selected=selected";
						
					}
					echo ">" . $curifo->currency_code . "</option>";
				}
				echo '</select>';
			}
			?>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#form_<?php echo (isset($acpayout_setting_id) ? $acpayout_setting_id : '');?> #currency_id').change(function () {
				var currency_id = $(this).val();
				$('#form_<?php echo (isset($acpayout_setting_id) ? $acpayout_setting_id : '');?> #ifsccode').parents('.form-group').addClass('hidden');
				if (currency_id =={{config('constants.DEFAULT_CURRENCY_ID')}}) {
					$('#form_{{(isset($acpayout_setting_id) ? $acpayout_setting_id : '')}} #ifsccode').parents('.form-group').removeClass('hidden');
				}
			});
		});
	</script>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="cashfree_account_id">{{trans('affiliate/settings/payout_settings.cashfree_account_id_label')}}</label>
		<div class="col-sm-6">
			<input name="cashfree_account_id" class="form-control" type="text" id="cashfree_account_id" placeholder="{{trans('affiliate/settings/payout_settings.cashfree_account_id_place')}}" value="{{ isset($acinfo)? $acinfo->cashfree_account_id:'' }}" />
		</div>
	</div>
    <div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="account_name">{{trans('affiliate/settings/payout_settings.account_holder_name_label')}}</label>
		<div class="col-sm-6">
			@if(isset($acpayout_setting_id) && $acpayout_setting_id == false)
			<input name="account_name" class="form-control" type="hidden" id="account_name" value="{{ isset($acinfo)? $acinfo->account_name:'' }}"/>
			<p class="form-control-static">{{ isset($acinfo)? $acinfo->account_name:'' }}</p>
			@else
			<input name="account_name" class="form-control" type="text" id="account_name" placeholder="{{trans('affiliate/settings/payout_settings.account_holder_name_place')}}" value="{{ isset($acinfo)? $acinfo->account_name:'' }}" />
			@endif
		</div>
	</div>
	
    <div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="status">{{trans('affiliate/settings/payout_settings.status_label')}}</label>
		<div class="col-sm-6">
			<select name="status" id="status_ws" class="form-control" >
				echo "<option value=''>{{ trans('affiliate/settings/payout_settings.select_status')}}</option>";
				<option value="0" <?php echo (isset($status) && $status == 0) ? "selected=selected" : '';?>>{{trans('affiliate/settings/payout_settings.in_active')}}</option>
				<option value="1" <?php echo (isset($status) && $status == 1) ? "selected=selected" : '';?>>{{trans('affiliate/settings/payout_settings.active')}}</option>
			</select>
		</div>
	</div>  

	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label"  for="tpin">{{trans('affiliate/settings/payout_settings.security_pin')}} *</label>
		<div class="col-sm-6">
			<input type="password" name="tpin" id="form_{{$acpayout_setting_id or ''}}_tpin" placeholder="{{trans('affiliate/settings/payout_settings.security_pin_tbin')}}" class="form-control" />
			<span id="errmsg" class="help-block"></span>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="update">&nbsp;</label>
		<div class="col-sm-6">
			<button type="submit" class="btn btn-sm bg-olive cashfree"  value="1"> {{ ( !isset($acpayout_setting_id) ? trans('affiliate/settings/payout_settings.add_now') : trans('affiliate/settings/payout_settings.update_now') ) }} </button>
			
		</div>
	</div>
</form>