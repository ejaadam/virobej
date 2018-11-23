<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('admin/bonus/leadership_bonus.leadership_bonus')}} - <?php echo getGTZ(null,'Y-m-d'); ?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					            <!--<th>{{trans('admin/bonus/leadership_bonus.account_id')}}</th>-->
								 <th class="text-center">{{trans('affiliate/bonus/leadership_bonus.period')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.1g_2g_qv_cf')}}</th>
							     <th>{{trans('affiliate/bonus/leadership_bonus.3g_qv_cf')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.1g_2g_newqv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.3g_newqv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.total_1g_2g')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.total_3g_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.matching_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.tax')}}</th>
								 <th>{{trans('affiliate/bonus/leadership_bonus.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.net_pay')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.status')}}</th>
                                                                               
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
                            <!--<td class="text-left"><?php //echo $row->account_id;?></td>-->
							<td class="text-left"><?php echo $row->created_date;?></td>
                            <td class="text-left"><?php echo $row->leftcarryfwd;?></td>
                            <td class="text-left"><?php echo $row->rightcarryfwd;?></td>
                            <td class="text-left"><?php echo $row->leftbinpnt;?></td>
                            <td class="text-left"><?php echo $row->rightbinpnt;?></td>
                            <td class="text-left"><?php echo $row->totleftbinpnt;?></td>
                            <td class="text-left"><?php echo $row->totrightbinpnt;?></td>
                            <td class="text-left"><?php echo $row->bonus_value;?></td>
                            <td class="text-left"><?php echo '';?></td>
                            <td class="text-left"><?php echo $row->income;?></td>
                            <td class="text-left"><?php echo '';?></td>
                            <td class="text-left"><?php echo '';?></td>
                            <td class="text-left"><?php echo $row->Fpaidamt;?></td>
                            <td class="text-left"><?php echo $row->status;?></td>
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