<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">My Fast Bonus List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
						   <th>{{trans('affiliate/bonus/faststart.username')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.packagename')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.dateofpurchase')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.amount')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.qv')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Earnings')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Commission')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.tax')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.ngo')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.netpay')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.status')}}</th>
						  
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
                            <td class="text-left">{{$row->from_uname}}</td>
                            <td class="text-left">{{$row->package_name}}</td>
                            <td class="text-left">{{$row->created_date}}</td> 
                            <td class="text-left">{{$row->Famount}}</td>
                            <td class="text-left">{{$row->qv}}</td> 
                            <td class="text-left">{{$row->earnings}}</td> 
                            <td class="text-right">{{$row->commission}}</td>
                            <td class="text-left">{{$row->ngo_wallet}}</td>
                            <td class="text-left">{{$row->net_pay}}</td>
                            <td class="text-left">{{$row->Status}}</td>
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