<?php 
include('assets/user/css/print_style.css');?>
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
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/bonus/team_bonus.t_bonus')}} - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>            <th class="text-center">{{trans('affiliate/bonus/team_bonus.period')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.1g_qv')}}</th>
							     <th>{{trans('affiliate/bonus/team_bonus.2g_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.1g_new')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.2g_new')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.total_1g')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.total_2g')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.matching_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.Tax')}}</th>
								 <th>{{trans('affiliate/bonus/team_bonus.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/team_bonus.net_pay')}}</th>
                                  <th>{{trans('affiliate/bonus/team_bonus.bonus_status')}}</th>
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
                            <td class="text-left"><?php echo $row->tax;?></td>
                            <td class="text-left"><?php echo $row->ngo_wallet;?></td>
                            <td class="text-left"><?php  echo $row->paidinc;?></td>
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
<button class="noprint" onClick="myFunction()">{{trans('affiliate/general.print_btn')}}</button>