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
        <h1 style="text-align:center">My Bonus Referrals List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
								<th>{{trans('affiliate/general.create_date')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.to_uname')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.from_uname')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.referrer')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.package')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.pay_mode')}}</th> 
                                <th>{{trans('affiliate/bonus/referral_bonus.amt')}}</th> 
                                <th>{{trans('affiliate/general.status')}}</th>  
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($print_data != '' && isset($print_data))
                {
                    foreach ($print_data as $row)
                    {
                        ?>
                        <tr>                                           
                            <td class="text-left"><?php echo date("d-M-Y H:i:s", strtotime($row->created_date));?></td>
                            <td class="text-left"><?php echo $row->to_full_name;?></td>
                            <td class="text-left"><?php echo $row->from_uname;?></td> 
                            <td class="text-left"><?php echo $row->sponser_uname.'('.$row->sponser_full_name.')';?></td>
                            <td class="text-left"><?php echo $row->package_name;?></td> 
                            <td class="text-left"><?php echo $row->pay_mode;?></td> 
                            <td class="text-right"><?php echo $row->amount.' '.$row->currency;?></td>
                            <td class="text-left"><?php echo $row->status_name;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='9'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>