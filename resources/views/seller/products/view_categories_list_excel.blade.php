<div class = "rightbox">
    <div class = "homeMsg" style = "text-align:left; height:auto;">
        <h1>Categories - {{date("d-M-Y")}}</h1><br/>
        <table id = "example1" border = "1" class = "table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Created On</th>
                    <th>Category Name</th>
                    <th>Total Product</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($user_details))
                @foreach ($user_details as $val)
                <tr>
                    <td width="5%" nowrap="nowrap" align="center">{{date('d-M-Y H:i:s', strtotime($val->created_date))}}</td>
                    <td class="uname">{{$val->category_name}}</td>
                    <td  align="right">{{$val->product_count}} </td>
                    <td align="center">{{ ($val->status == 1)?'Active':'Inactive'}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
