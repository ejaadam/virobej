<div class="paymode" id="walletinfo">
    <div class="panel panel-default">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-edit"></i> <span>{{trans('affiliate/package/purchase.paymode_wallet')}}</span></h4></div>
        <div class="panel-body">
            <div class="row form-group">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_wallet')}}:</label>
                <div class="col-sm-4">
                    <select name="wallet_id" id="wallet_id" class="form-control">                    	
                    </select>                    
                </div>
            </div>
            <div class="row form-group ">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_curbal')}} :</label>
                <div class="col-md-8 balinfo">                	
                    <span class="usrbal text-success"></span>
                    <span class="usrcur"></span>                    
                </div>
            </div>
            <div class="row form-group dedbalinfo">
                <label class="col-sm-3 control-label">{{trans('affiliate/package/purchase.label_dedbal')}} : </label>
                <div class="col-md-8 ">                	
                    <span class="usrbal text-success"></span>
                    <span class="usrcur"></span>
                    <span class="text-muted clear">{{trans('affiliate/package/purchase.ded_bal_notes')}}</span>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-9 col-sm-offset-3">                    
                   <button class="btn btn-sm btn-primary" name="purchasebtn" id="purchasebyWbtn" data-ptype="wallet" data-url="{{route('aff.package.purchaseconfirm')}}">{{trans('affiliate/package/purchase.paynow_btn')}} <i class="fa fa-angle-right"></i></button>                   
                </div>
            </div>
        </div>
    </div>
</div> 