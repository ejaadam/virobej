<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Admin Credit & Debit Report  - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					  <th>{{trans('admin/finance.report.date')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>User Name</th>
                        <th>{{trans('admin/finance.report.wallet')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.hdl_amt')}}</th>
                        <th>{{trans('admin/finance.report.paid_amt')}}</th>
                        <th>{{trans('admin/finance.report.trans_by')}}</th>
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
                            <td class="text-left"><?php echo $row->transfered_on;?></td>
                            <td class="text-left"><?php echo $row->transaction_id;?></td>
                            <td class="text-left"><?php echo $row->username;?></td>
                            <td class="text-left"><?php echo $row->wallet_name;?></td>
                            <td class="text-left"><?php echo $row->amount;?></td>
                            <td class="text-left"><?php echo $row->handleamt;?></td>
                            <td class="text-left"><?php echo $row->paidamt;?></td>
                            <td class="text-left"><?php echo $row->added_by;?></td>
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