@if(!empty($particular_details))
<div class="panel panel-default">
    <div class='panel-heading'>
        <div class="col-sm-4 pull-right text-right">
            <?php
            switch ($shippinginfo->order_status_id)
            {
                case Config::get('constants.ORDER_STATUS.PLACED'):
                    echo "<span id='c_order'><button type='button' class='btn btn-primary btn-sm mr' data-sub_order_id='$sub_order_id' data-account_id='$account_id' id='cancelOrd' name='$order_account_id'>Cancel Order</button></span>";
                    break;
                case Config::get('constants.ORDER_STATUS.PACKED'):
                    echo "<span id='c_order'><button type='button' class='btn btn-primary btn-sm mr' data-sub_order_id='$sub_order_id' data-account_id='$account_id' id='cancelOrd' name='$order_account_id'>Cancel Order</button></span>";
                    break;
                case Config::get('constants.ORDER_STATUS.DISPATCHED'):
                    echo "<span class='btn btn-success btn-sm mr' >Dispatched</span>";
                    break;
                case Config::get('constants.ORDER_STATUS.COMPLETED'):
                    echo "<span class='btn btn-info btn-sm mr' >Completed</span>";
                    break;
                case Config::get('constants.ORDER_STATUS.DELIVERED'):
                    echo "<span class='btn btn-success btn-sm mr' >Delivered</span>";
                    break;
                case Config::get('constants.ORDER_STATUS.CANCELLED'):
                    echo "<span ><button type='button' class='btn btn-danger btn-sm mr'>Cancelled</button></span>";
                    break;
            }
            ?>
            <button type="button" class="btn btn-danger btn-sm" name="close" id="close">X</button>
        </div>
        <h4 class='panel-title'>Order: #{{$shippinginfo->order_code}}
            <!--?php
              switch($shippinginfo->order_status_id){
          case Config::get('constants.ORDER_STATUS.PLACED'):
            echo "<span class='label label-sm label-warning' id='cancel'>$shippinginfo->status</span>";
          break;
             case Config::get('constants.ORDER_STATUS.PACKED'):
             echo "<span class='label label-sm label-success' id='cancel'>$shippinginfo->status</span>";
          break;
               case Config::get('constants.ORDER_STATUS.DISPATCHED'):
            echo "<span class='label label-sm label-info' id='cancel'>$shippinginfo->status</span>";
          break;
           case Config::get('constants.ORDER_STATUS.CANCELLED'):
            echo "<span class='label label-sm label-danger' id='cancel' >$shippinginfo->status</span>";
          break;
              }
      ?--></h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4">
                <table class="table table-bordered table-default">
                    <tbody>
                        <tr>
                            <th> Order Date</th>
                            <td>{{date('d-M-Y H:i:s', strtotime($shippinginfo->created_on))}}</td>
                        </tr>
                        <tr>
                            <th>Dispatch Date </th>
                            <td>
                                @if ($shippinginfo->dispatch_date != NULL)
                                {{date('d-M-Y H:i:s', strtotime($shippinginfo->dispatch_date))}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Delivery Date </th>
                            <td>
                                @if ($shippinginfo->delivery_date != NULL)
                                {{date('d-M-Y H:i:s', strtotime($shippinginfo->delivery_date))}}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-8">
                <table class="table table-bordered">
                    <tbody>
                    <td>
                        <!-- col-sm-4 -->
                        <p class="heading_a"><strong> Billing Details</strong></p>
                        <p><strong>Order By: </strong> {{$billing_details->full_name}}
                        <p> <strong>Email: </strong> {{$billing_details->email}}</p>
                        <p> <strong>Mobile: </strong> {{$billing_details->mobile}}</p>
                    </td>
                    <td>
                        <p class="heading_a"><strong>Shipping Address</strong></p>
                        <?php
                        echo "<p><strong>Contact Person: </strong>".$shippinginfo->full_name."</p>";
                        $city = $shippinginfo->city.'-'.$shippinginfo->postal_code;
                        $shippingcont = $shippinginfo->address1.'<br>'.$shippinginfo->address2.'<br>'.$shippinginfo->state.'<br>'.$city.'. '.$shippinginfo->country_name;
                        if (!empty($shippingcont))
                        {
                            ?>
                            <p>{{$shippingcont}}.</p>
                            <p><strong>Mobile: </strong>{{$shippinginfo->mobile_no}}.</p>
                        <?php }?>
                    </td>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- col-sm-6 -->
        <br />
        <table class="table table-bordered col-sm-6 table-default" data-filter="#table_search" data-page-size="40">
            <thead >
                <tr>
                    <th  width="10%"></th>
                    <th class="text-left">Product</th>
                    <th  width="5%"class="text-center" >Qty</th>
                    <th width="10%"class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($particular_details as $detail)
                <tr>
                    <td><div class="fileupload-new img-thumbnail" style="width: 128px; height: 120px;"><img height="100%" width="100%" src="{{$detail->file_path.$detail->img_path}}" /></div>  <b></b></td>
                    <td class="text-left">{{$detail->product_name}} <br>({{$detail->brand_name}} - {{$detail->category_name}})</td>
                    <td class="text-center">{{$detail->qty}}</td>
                    <td class="text-right">{{$detail->net_pay}}</td>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right"> Sub Total </th>
                    <td class="text-right">{{number_format($shippinginfo->net_pay,0,'.',',')}}</td>
                </tr>
                <!--tr>
                 <th colspan="3" class="text-right"> Discount </th>
                 <td class="text-right">{{number_format($shippinginfo->discount,0,'.',',')}}</td>
               </tr>
                <tr>
                 <th colspan="3" class="text-right"> Tax </th>
                 <td class="text-right">{{number_format($shippinginfo->tax,0,'.',',')}}</td>
               </tr-->
                <tr>
                    <th colspan="3" class="text-right"> Net Pay </th>
                    <td class="text-right">{{number_format($shippinginfo->net_pay,0,'.',',')}}</td>
                </tr>
            </tfoot>
        </table>
        <table class="table table-bordered pull-right" style="width:165px; float:right;">
        </table>
        @endif
    </div>
</div>
