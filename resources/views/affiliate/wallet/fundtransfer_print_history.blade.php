<?php include('assets/user/css/print_style.css');?>
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
//foreach ($data as $key => $val)
//    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Fund Transfer History List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                  
								<th>{{trans('affiliate/fund_transfer_history.transfered_on')}}</th>  
                                <th>{{trans('affiliate/fund_transfer_history.transaction_id')}}</th>
                                <th>{{trans('affiliate/fund_transfer_history.from_account')}}</th>
                                <th>{{trans('affiliate/fund_transfer_history.to_account')}}</th>                                
                                <th>{{trans('affiliate/fund_transfer_history.wallet_name')}}</th>                                
                                <th>{{trans('affiliate/general.amount')}}</th>
                                <th>{{trans('affiliate/fund_transfer_history.paidamt')}}</th>    
                                <th>{{trans('affiliate/general.status')}}</th>	
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($print_data != '')
                {
                    foreach ($print_data as $row)
                    {
                        ?>
                        <tr>
                        	
                            <td class="text-center"><?php echo date("d-M-Y H:i:s", strtotime($row->transfered_on));?></td>
                            <td class="text-left"><?php echo $row->transaction_id;?></td>
                            <td class="text-left"><?php echo $row->from_fullname.' ('.$row->from_uname.')'; ?></td>
                            <td class="text-left"><?php echo $row->to_fullname.' ('.$row->to_uname.')';?></td>
                            <td class="text-left"><?php echo $row->wallet_name;?></td>
                            <td class="text-right"><?php echo $row->amount.' '. $row->currency_code;?></td>
                            <td class="text-right"><?php echo $row->paidamt.'  '.$row->currency_code;?></td>
                            <td class="text-center"><?php echo $row->status_name;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
					echo "<tr><td colspan='8'>No commission Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>
