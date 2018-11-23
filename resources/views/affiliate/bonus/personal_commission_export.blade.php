<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/personal_commission.per_commission')}} - <?php echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>                                             
                                 <th class="text-center">{{trans('affiliate/bonus/personal_commission.period')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.directs_cv')}}</th>
							     <th>{{trans('affiliate/bonus/personal_commission.self_cv')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.slab')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.total_cv')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.tax')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.net_pay')}}</th>
								 <th>{{trans('affiliate/bonus/personal_commission.status')}}</th>
                                                                               
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
                        
							<td class="text-left"><?php echo $row->confirm_date; ?></td>
                            <td class="text-left"><?php echo $row->direct_cv;?></td>
                            <td class="text-left"><?php echo $row->self_cv;?></td>
                            <td class="text-left"><?php echo $row->slab;?></td>
                            <td class="text-left"><?php echo $row->total_cv;?></td>
                            <td class="text-left"><?php echo $row->earnings;?></td>
                            <td class="text-left"><?php echo $row->commission;?></td>
                            <td class="text-left"><?php echo $row->tax;?></td>
                            <td class="text-left"><?php echo $row->ngo_wallet;?></td>
                            <td class="text-left"><?php echo $row->net_pay;?></td>
                            <td class="text-left"><?php //echo $row->status;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='6'>{{trans('affiliate/general.no_records_found')}}</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>