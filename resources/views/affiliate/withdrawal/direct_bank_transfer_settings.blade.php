<div class="panel panel-success {{!isset($id)?'hidden':''}}" <?php echo !isset($id)? "id='new_bank_panel'":''?> >
    <div class="panel-heading">         
        <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$id or ''}}"><i class="pull-right glyphicon glyphicon-chevron-up"></i> {{isset($nick_name)?$nick_name.' Details':'Details'}}</a></h4>
    </div>
	<div id="collapse{{$id or ''}}" class="panel-collapse collapse {{!isset($id)?'in':''}}">
		<div class="panel-body">
			<form action="<?php echo URL('account/settings/bank_transfer_update');?>" method="post" class="bank_transferfrmad" id="form_{{$id or ''}}" data-id="{{$id or ''}}">
	<input type="hidden" name="payment_type_id" value="{{ isset($payment_type_id)? $payment_type_id:'' }}"  />
	<input type="hidden" name="id" value="{{ $id or '' }}"  />
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
			$('#form_<?php echo (isset($id) ? $id : '');?> #currency_id').change(function () {
				var currency_id = $(this).val();
				$('#form_<?php echo (isset($id) ? $id : '');?> #ifsccode').parents('.form-group').addClass('hidden');
				if (currency_id =={{config('constants.DEFAULT_CURRENCY_ID')}}) {
					$('#form_{{(isset($id) ? $id : '')}} #ifsccode').parents('.form-group').removeClass('hidden');
				}
			});
		});
	</script>
	
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="payout_account_type">{{ trans('affiliate/settings/payout_settings.account_type_label')}}</label>
		<div class="col-sm-6">
			<?php
			if (isset($currency_list))
			{
				echo "<select name='payout_account_type' id='payout_account_type'  class='form-control' >";
				echo "<option value=''>".trans('affiliate/settings/payout_settings.slc_acc_type')."</option>";
				if (isset($payout_account_types))
				{
					foreach ($payout_account_types as $patype)
					{
						echo "<option value='" . $patype->payout_id . "'";
						if (isset($acinfo) && isset($acinfo->payout_account_type_id) && $acinfo->payout_account_type_id == $patype->payout_id)
						{
							echo "selected=selected";
						}
						echo ">" . $patype->desc . "</option>";
					} 
				}
				echo '</select>';
			}
			?>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="nick_name">{{trans('affiliate/settings/payout_settings.nick_name_label')}}</label>
		<div class="col-sm-6">
			<input name="nick_name" class="form-control" type="text" id="nick_name" placeholder="{{trans('affiliate/settings/payout_settings.nick_name_place')}}" value="{{ isset($nick_name)? $nick_name:'' }}" />
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="account_name">{{trans('affiliate/settings/payout_settings.account_holder_name_label')}}</label>
		<div class="col-sm-6">
			@if(isset($editable_banks) && !empty($editable_banks) && in_array($id,$editable_banks))
			<input name="account_name" class="form-control" type="hidden" id="account_name" value="{{ isset($acinfo)? $acinfo->account_name:'' }}"/>
			<p class="form-control-static">{{ isset($acinfo)? $acinfo->account_name:'' }}</p>
			@else
			<input name="account_name" class="form-control" type="text" id="account_name" placeholder="{{trans('affiliate/settings/payout_settings.account_holder_name_place')}}" value="{{ isset($acinfo)? $acinfo->account_name:'' }}" />
			@endif
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="account_no">{{trans('affiliate/settings/payout_settings.account_number_label')}}</label>
		<div class="col-sm-6">
			<input name="account_no" class="form-control" type="text" id="account_no" placeholder="{{trans('affiliate/settings/payout_settings.account_number_place')}}" value="{{ isset($acinfo)? $acinfo->account_no:'' }}" />
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="bank_name">{{trans('affiliate/settings/payout_settings.bank_name_label')}}</label>
		<div class="col-sm-6">
			<input name="bank_name" class="form-control" type="text" id="bank_name" placeholder="{{trans('affiliate/settings/payout_settings.bank_name_place')}}" value="{{ isset($acinfo)? $acinfo->bank_name:'' }}" />
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="bank_branch">{{trans('affiliate/settings/payout_settings.bank_branch_label')}}</label>
		<div class="col-sm-6">
			<input name="bank_branch" class="form-control" type="text" id="bank_branch" placeholder="{{trans('affiliate/settings/payout_settings.bank_branch_place')}}" value="{{ isset($acinfo)? $acinfo->bank_branch:'' }}" />
		</div>
	</div>
	   <div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="ifsccode">{{trans('affiliate/settings/payout_settings.bank_ifsc_code_label')}}</label>
		<div class="col-sm-6">
			 <input name="ifsccode" class="form-control" type="text" id="ifsccode" placeholder="{{trans('affiliate/settings/payout_settings.bank_ifsc_code_place')}}" value="{{ isset($acinfo)? $acinfo->ifsccode:'' }}" />
			 
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="withdrawal_status">{{trans('affiliate/settings/payout_settings.status_label')}}</label>
		<div class="col-sm-6">
			<select name="withdrawal_status" id="withdrawal_status" class="form-control" >
				echo "<option value=''>{{ trans('affiliate/settings/payout_settings.select_status')}}</option>";
				<option value="0" <?php echo (isset($user_payout_status) && $user_payout_status == 0) ? "selected=selected" : '';?>>{{trans('affiliate/settings/payout_settings.in_active')}}</option>
				<option value="1" <?php echo (isset($user_payout_status) && $user_payout_status == 1) ? "selected=selected" : '';?>>{{trans('affiliate/settings/payout_settings.active')}}</option>
			</select>
		</div>
	</div>           
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label"  for="tpin">{{trans('affiliate/settings/payout_settings.security_pin')}} *</label>
		<div class="col-sm-6">
			<input type="password" name="tpin" id="form_{{$id or ''}}_tpin" placeholder="{{trans('affiliate/settings/payout_settings.security_pin_tbin')}}" class="form-control" />
			<span id="errmsg" class="help-block"></span>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<label class="col-sm-6 control-label" for="update">&nbsp;</label>
		<div class="col-sm-6">
			<button type="submit" class="btn btn-sm bg-olive"  value="1"> {{ ( !isset($id) ? trans('affiliate/settings/payout_settings.add_now') : trans('affiliate/settings/payout_settings.update_now') ) }} </button>
		</div>
	</div>
</form>
		</div>
	</div>
</div>


