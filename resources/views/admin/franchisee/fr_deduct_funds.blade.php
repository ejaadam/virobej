@extends('admin.common.layout')
@section('title','Debit Funds')
@section('layoutContent')
<section class="content">
    <!--Main row -->
    <div class = "row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Debit Funds from Support Center</h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form method="POST" class='form-horizontal form-validate' name="deduct_funds" id="deduct_funds"  enctype="multipart/form-data" >
                <div class="form-group">
                    <label for="username" class="control-label col-sm-2">Support Center User Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="username" id="username" class="form-control"  placeholder="Enter User name" data-rule-required="true" value="" >
                        <input type="hidden" name="user_id" id="user_id"/>
                        <span id="user_avail_status"></span>
                    </div>
                </div>
                <div class="form-group" id="uname_check">
                    <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                    <div class="col-sm-6">
                        <input type="button" name="uname_check" id="fr_uname_check" class="btn btn-primary" value="CHECK" data-url="{{URL::to('admin/check_franchisee_details')}}" />
                    </div>
                </div>
                <div id="user_details" style="display:none">
                    <div class="form-group">
                        <label for="username" class="control-label col-sm-2">Full Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="rec_name" id="rec_name" class="form-control" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
                            <input type="text" name="rec_email" id="rec_email" class="form-control" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Support Center Type:</label>
                        <div class="col-sm-6">
                            <input type="text" name="franchisee_type_name" id="franchisee_type_name" class="form-control" disabled/>
                            <input type="hidden" name="franchisee_type" id="franchisee_type"/>
                        </div>
                    </div>
                </div>

                <div id="deduct_details" style="display:none">
                    <div class="form-group">
                        <label for="ewallet_id" class="control-label col-sm-2">EWallet:</label>
                        <div class="col-sm-6">
                            <select name="ewallet_id" id="ewallet_id" class="form-control">
                                <option value="">Select Wallet</option>
                                <?php
                                foreach ($wallet_list as $value)
                                {
                                    ?>
                                    <option value="<?php echo $value->wallet_id;?>"><?php echo $value->wallet_name;?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="currency_id" class="control-label col-sm-2">Currency:</label>
                        <div class="col-sm-6">
                            <select name="currency_id" id="currency_id" class="form-control" required >
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                <option value="{{$currency->id}}">{{$currency->code}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailfield" class="control-label col-sm-2"></label>
                        <div class="col-sm-6">
                            <span id="balTxt"></span>
                            <input type="hidden" id="avi_bal">
                        </div>
                    </div>
                    <div id="amount_div" style="display:none;">
                        <div class="form-group">
                            <label for="emailfield" class="control-label col-sm-2">Amount :</label>
                            <div class="col-sm-6">
                                <input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" data-rule-required="true" value="" >
                                <span id="amount_err" class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailfield" class="control-label col-sm-2">Admin Comments :</label>
                            <div class="col-sm-6">
                                <textarea cols="66" class="form-control" rows="5" id="comment" name="comment"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">&nbsp;</label>
                            <div class="col-sm-6">
                                <input type="submit" id="Submit" class="btn btn-primary" value="Submit">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>var balArray = new Array();
    balArray["1"] = "0";
    balArray["2"] = "0";
    balArray["3"] = "0";</script>
<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>
{{HTML::script('js/providers/admin/franchisee/franchisee_deduct_funds.js')}}
{{HTML::script('js/providers/admin/franchisee/other_functionalities.js')}}
@stop
