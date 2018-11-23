<div class="row" id="paymodes" style="display:none">
    <!-- ./col -->
    <div class="col-md-3">	
        <div class="box box-primary">
            <div class="box-header with-border">
                <i class="fa fa-edit"></i>
                <h3 class="box-title">{{trans('affiliate/package/purchase.package_page_title')}}</h3>    
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-default btn-sm backto_packagebtn"><i class="fa fa-arrow-left"></i> {{trans('affiliate/package/purchase.backbtn')}}</button>
                </div>	                    			
            </div>
            <div class="box-body">            
            	<ul class="list-group list-group-bordered"  id="packInfo">
                <li class="list-group-item">
                  <b>{{trans('affiliate/package/purchase.package_name_label')}}</b> <span class="pull-right text-info pkname">1,322</span>
                </li>
                <li class="list-group-item">
                  <b>{{trans('affiliate/package/purchase.package_price')}}</b> <span class="pull-right text-success pkamt">543</span>
                </li>                
              </ul>                 
            </div>
        </div>
    </div>
    <div class="col-md-9" id="paymentprocess">	
        <div class="box box-primary">
            <div class="box-header with-border">
                <i class="fa fa-edit"></i>
                <h3 class="box-title">{{trans('affiliate/package/purchase.paymodes')}}</h3>                        			
            </div>
            <div class="box-body">
                <div class="form-group">
                <ul class="selpaymode">                   
                </ul>
                </div>                
            </div>
        </div>
    </div>
</div>   