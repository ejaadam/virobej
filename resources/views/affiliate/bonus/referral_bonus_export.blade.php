<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">My Bonus Referrals List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
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
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
                    {
                        ?>
                        <tr>                        
                            <td class="text-left">{{$row->created_date}}</td>
                            <td class="text-left">{{$row->to_uname.' ( '.$row->to_full_name.' )'}}</td>
                            <td class="text-left">{{$row->from_uname}}</td> 
                            <td class="text-left">{{$row->sponser_uname.'('.$row->sponser_full_name.')'}}</td>
                            <td class="text-left">{{$row->package_name}}</td> 
                            <td class="text-left">{{$row->pay_mode}}</td> 
                            <td class="text-right">{{$row->amount.' '.$row->currency}}</td>
                            <td class="text-left">{{$row->status_name}}</td>
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