<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css">
    table {
        border-collapse: collapse;
        border-spacing: 0px;
        width:100%;
    }
    td,th{
        padding: 5px;
    }
    button{
        position: fixed;
        top:2%;
        right:2%;
        background-color: #61adf0;
        background-image: -webkit-linear-gradient(#6ab5f2, #006ca4);
        background-image: linear-gradient(#6ab5f2, #006ca4);
        box-shadow: inset 0 1px 1px #77bdf4, 0 2px 2px -1px rgba(0, 0, 0, 0.2);
        border: 1px solid #4d93d7;
        color: #fff;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
        border-radius: 3px!important;
        padding: 10px;
        cursor:pointer;
    }
    .text-left{
        text-align: left;
    }
    .text-right{
        text-align: right;
    }
    .text-center,h1{
        text-align: center;
    }

</style>
<style type="text/css" media="print">
    table {
        border-collapse: collapse;
        border-spacing: 0px;
    }
    td,th{
        padding: 5px;
    }
    button{
        display:none;
    }
    .text-left{
        text-align: left;
    }
    .text-right{
        text-align: right;
    }
    .text-center{
        text-align: center;
    }
</style>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1> {{$title}} - <?php echo date("d-M-Y");?></h1><br/>
        <table id="example1" class="table table-bordered " border="1">
            <thead>
                <tr>
                     <th>Date</th>            
                    <th>From</th>
                    <th>To</th>
                    <th>Transaction Details</th>
                    <th>Amount</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Verified On</th>

                </tr>
            </thead>
            <tbody>
                @if(!empty($commissions))
                @foreach($commissions as $commission)
                <tr>
                    <td class="text-center">{{date('d-M-Y H:i:s', strtotime($commission->created_date))}}</td>                   
                     <td>{{$commission->from_full_name}}
                    @if($commission->from_uname != '' && !empty($commission->from_uname))
                    {{'<br />('.$commission->from_uname.')'}}
                    @endif
                    @if(isset($commission->district_name) && $commission->district_name != '' && !empty($commission->district_name))
           			{{'<br />District : '.$commission->district_name}}
					@endif
                    </td>
                    <td>{{$commission->to_full_name.'('.$commission->to_uname.')'}}</td>
                    <td>
                    @if(isset($commission->remark) && $commission->remark != '' && !empty($commission->remark))
                     {{$commission->remark.'<br />'}}
                     @endif
                    {{'<b>Transaction Id: '.$commission->transaction_id.'</b>'}}</td>           
                    <td class="text-right">{{number_format($commission->amount,2,'.',',').' '.$commission->currency}}</td>
                    <td class="text-right">{{number_format($commission->commission_amount,2,'.',',').' '.$commission->currency}}</td>
                    <td class="text-center">{{$commission->status_name}}</td>
                    <td class="text-center">{{(!empty($commission->confirmed_date)?date('d-M-Y H:i:s', strtotime($commission->confirmed_date)):'-')}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onclick="myFunction()">Print</button>
