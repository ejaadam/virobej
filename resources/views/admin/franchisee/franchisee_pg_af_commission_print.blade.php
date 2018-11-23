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
<?php
foreach ($data as $key => $val)
    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1> SC PG Add Fund Commission - <?php echo date("d-M-Y");?></h1><br/>
        <?php
        $err = Session::get('err');
        $msgClass = Session::get('msgClass');
        if (isset($err))
        {
            if ($err != '')
            {
                echo '<div class="' . $msgClass . '">' . $err . '</div>';
            }
        }
        ?>
        <table id="example" border="1" class="table table-bordered table-striped display" cellspacing="0" width="100%">
            <thead>
               <tr>
               					<th>S.No</th>
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
                <?php
                $i = 1;
                if ($fundtransfer_commission != '')
                {
                    foreach ($fundtransfer_commission as $row)
                    {
                        ?>
                        <tr>
                            <td nowrap><?php echo $i;?></td>
                             <td>{{date('d-M-Y H:i:s',strtotime($row->created_date))}}</td>  
                            <td>{{$row->from_uname}}</td>
                            <td>{{$row->to_uname}}</td>                           
                            <td>{{$row->remark}}</td>
                            <td>{{$row->code}}</td>
                            <td>{{number_format($row->amount,2)}}</td>
                            <td>{{number_format($row->commission_amount,2)}}</td>
                            <td>{{$status_arr[$row->status]}}</td>
                            <td>{{ ($row->confirmed_date != '' && $row->confirmed_date !== '0000-00-00') ? date('d-M-Y H:i:s',strtotime($row->confirmed_date)):''}}</td>                  
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onclick="myFunction()">Print</button>
