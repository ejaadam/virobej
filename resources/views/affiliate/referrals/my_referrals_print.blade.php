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
        <h1 style="text-align:center">My Referrals List - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                	  		     <th class="text-center">{{trans('affiliate/referrels/my_referrels.username')}}</th>
							    <th>{{trans('affiliate/referrels/my_referrels.name')}} </th>
                                <th>{{trans('affiliate/referrels/my_referrels.mobile')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.email_address')}} </th>
							    <th>{{trans('affiliate/referrels/my_referrels.status')}} </th>
								<th> {{trans('affiliate/referrels/my_referrels.signed_up_on')}}</th>
								<th> {{trans('affiliate/referrels/my_referrels.placement')}}</th>
							    <th> {{trans('affiliate/referrels/my_referrels.package_value')}}</th>
								<th>{{trans('affiliate/referrels/my_referrels.qv')}} </th>
							     <th>{{trans('affiliate/referrels/my_referrels.cv')}} </th>
								<!--<th>{{trans('affiliate/referrels/my_referrels.sponsor_uname')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.last_pack_purchased')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.last_purchased_on')}} </th>-->
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
                            
                              <td class="text-left"><?php echo $row->uname;?></td>
							  <td class="text-left"><?php echo $row->full_name;?></td>
                              <td class="text-left"><?php echo $row->mobile;?></td>
                              <td class="text-left"><?php echo $row->email;?></td> 
							  <td class="text-left"><?php echo $row->status;?></td> 
							  <td class="text-left"><?php echo date("d-M-Y H:i:s", strtotime($row->signedup_on));?></td>
							  <td class="text-left"><?php echo $row->upline_name.'<br>'."level :".$row->level."G";?></td>
							  <td class="text-left"><?php echo $row->package_amount;?></td>
							  <td class="text-left"><?php echo $row->qv;?></td>
							  <td class="text-left"><?php echo $row->cv;?></td> 
                        </tr>
                        <?php
                        $i++;
                    }
                 }
				 else {
					echo "<tr><td colspan='6'>No Records Found.</td></tr>";
				}
                ?>
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onClick="myFunction()">Print</button>