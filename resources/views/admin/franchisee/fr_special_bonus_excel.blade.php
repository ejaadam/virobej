<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Special Bonus - {{date("d-M-Y")}}</h1><br/>
        <table id="example" border="1" class="table table-bordered table-striped display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>  
                    <th>Transaction Details</th>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Verified On</th>
                </tr>
            </thead>
            <tbody>
                @if ($fundtransfer_commission != '')
                @foreach ($fundtransfer_commission as $row)
                <tr>
                    <td nowrap="nowrap">{{date('d-M-Y H:i:s',strtotime($row->created_date))}}</td>
                    <td nowrap="nowrap">{{str_replace('<br />','',$row->root_username)}}</td>                    
                    <td nowrap="nowrap">{{$row->to_full_name .'(' . $row->to_uname.' - '.$row->franchisee_location.' '.$row->franchisee_type_name.')'}}</td>
                    <td nowrap="nowrap">{{$row->remark}}</td>
                    <td nowrap="nowrap">{{$row->currency}}</td>
                    <td nowrap="nowrap">{{number_format($row->amount,2,'.',',')}}</td>
                    <td>{{number_format($row->commission_amount,2,'.',',')}}</td>
                    <td nowrap="nowrap">{{$status_arr[$row->status]}}</td>
                    <td nowrap="nowrap">{{ ($row->confirmed_date != '' && $row->confirmed_date !== '0000-00-00') ? date('d-M-Y H:i:s',strtotime($row->confirmed_date)):''}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
