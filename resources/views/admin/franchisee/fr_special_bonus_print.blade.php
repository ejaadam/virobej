<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css" media="print">
    table tr td{
        border-collapse:collapse;
        padding:5px 5px;
    }
    .noprint{
        display:none;
    }
</style>
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
                    <td>{{date('d-M-Y H:i:s',strtotime($row->created_date))}}</td>
                    <td>{{$row->root_username}}</td>                    
                    <td>{{$row->to_full_name .'<br /> (' . $row->to_uname.' - '.$row->franchisee_location.' '.$row->franchisee_type_name.')'}}</td>                    
                    <td>{{$row->remark}}</td>
                    <td>{{$row->currency}}</td>
                    <td>{{number_format($row->amount,2,'.',',')}}</td>
                    <td><b class="text-success">{{number_format($row->commission_amount,2,'.',',')}}</b></td>
                    <td>{{$status_arr[$row->status]}}</td>
                    <td>{{ ($row->confirmed_date != '' && $row->confirmed_date !== '0000-00-00') ? date('d-M-Y H:i:s',strtotime($row->confirmed_date)):''}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onclick="myFunction()">Print</button>
