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
        <h1>Withdrawal Details List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
               
								<th>{{trans('affiliate/withdrawal/history.requested_on')}}</th>  
                                <th>{{trans('affiliate/withdrawal/history.username')}}</th>
                                <th>{{trans('affiliate/general.country')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.payment_mode')}}</th>
                                <th>{{trans('affiliate/general.amount')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.charges')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.netpay')}}</th>
                                <th>{{trans('affiliate/general.status')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.expected_date_of_credit')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.updated_on')}}</th>
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
                            <td class="text-left"><?php echo date("d-M-Y h:i:s", strtotime($row->created_on));?></td>
                            <td class="text-left"><?php echo $row->uname;?></td>
                            <td class="text-left"><?php echo $row->name; ?></td>
                            <td class="text-left"><?php echo $row->withdrawal_payout_type;?></td>
                            <td class="text-right"><?php echo $row->amount.'  '. $row->currency_code;?></td>
                            <td class="text-right"><?php echo $row->charges;?></td> 
                            <td class="text-right"><?php echo $row->paidamt.'  '.$row->currency_code;?></td>
                            <td class="text-center"><?php 
							switch($row->status){
							case 1:
								$row->status_label = 'Transferred';
							break;
							case 2:
								$row->status_label = 'Processing';
							break;
							case 0:
								$row->status_label = 'Pending';
							break;
							case 3:
								$row->status_label = 'Cancelled';
							break;
							} 
							echo $row->status_label;?></td>
                            <td class="text-left"><?php if($row->expected_date!=NULL){echo date("d-M-Y", strtotime($row->expected_date));}?></td>
                            <td class="text-left"><?php echo date("d-M-Y h:i:s", strtotime($row->timeflag));?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
					echo "<tr><td colspan='10'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>