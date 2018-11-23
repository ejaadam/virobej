<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/general.my_directs_list')}} - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					<th>{{trans('affiliate/referrels/my_referrels.username')}}</th>  
					<th>{{trans('affiliate/referrels/my_referrels.full_name')}}</th>
					<th>{{trans('affiliate/referrels/my_referrels.invited_by')}}</th>
					<th>{{trans('affiliate/referrels/my_referrels.user_level')}}</th>
					<th>{{trans('affiliate/referrels/my_referrels.recent_package_purchased')}}</th>
					<th>{{trans('affiliate/referrels/my_referrels.recent_package_purchased_on')}}</th>
					<th>{{trans('affiliate/referrels/my_referrels.signed_up_on')}}</th> 
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
                            <td class="text-left"><?php echo $row->uname;?></td>
                            <td class="text-left"><?php echo $row->full_name;?></td>
                            <td class="text-left"><?php echo $row->direct_sponser_uname;?></td>
                            <td class="text-left"><?php echo $row->level;?></td>
                            <td class="text-left"><?php echo $row->package_name;?></td>
                            <td class="text-left"><?php echo $row->recent_package_purchased_on;?></td>
                            <td class="text-left"><?php echo $row->signedup_on;?></td>
                            <td class="text-left"><?php echo $row->status_name;?></td>
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