<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Product Item - {{date("d-M-Y")}}</h1><br/>
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Created On</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Amount Value</th>
                    <th>Stock</th>
                    <th>Available</th>
                    <th>Sold</th>
                    <th>In-Progress</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($product_list))
                @foreach ($product_list as $val)
                <tr>
                    <td width="5%" nowrap="nowrap" align="center">{{ date('d-M-Y H:i:s', strtotime($val->created_date))}}</td>
                    <td  align="center"><img height="100" width="100" src="{{ URL::asset($val->file_path.$val->img_path)}}"></td>
                    <td class="uname">{{ $val->product_name}} <br />
                        <b>Code:</b>{{ $val->product_code}}<br />
                        <b>Brand:</b>{{ $val->brand_name}}<br />
                        <b>Category:</b>{{ $val->category_name}}
                    </td>
                    <td> {{number_format($val->price,2,'.',',')}} {{($val->price_type==1)?'Points':'Coins'}} </td>
                    <td align="center">{{($val->in_stock == 1)?'<span class="label label-success">In Stock</span>':'<span class="label label-danger">Out of Stock</span>'}}</td>
                    <td>{{ $val->current_stock}} </td>
                    <td>{{ $val->sold_items}} </td>
                    <td>{{ $val->commited_stock}} </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
