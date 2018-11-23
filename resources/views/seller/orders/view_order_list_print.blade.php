
 <?php
 include('assets/supplier/css/print_style.css');?>
<?php
foreach ($order_list as $key => $val)
    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>View Suppliers Orders - <?php echo date("d-M-Y");?></h1><br/>
    <h5 align="right"><button class="noprint" onclick="myFunction()">Print</button></h5>
          <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                   <th width="20%">Order Date</th>
                   <th width="10%">Order Code</th>
                   <th width="10%">Customer Name</th>
                   <th width="5%">Quantity</th>
                   <th>Discount</th>
                   <th width="15%">Net Pay</th>
                   <th width="5%">Status</th>
                   <th width="20%">Updated On</th>
                </tr>
            </thead>
          <tbody>
                <?php
                $i = 1;
                if (!empty($order_list))
                {
				
                    foreach ($order_list as $val)
                    {
                        ?>
                        <tr>
                     
                            <td width="5%" nowrap="nowrap" align="center"><?php echo date('d-M-Y H:i:s', strtotime($val->order_date));?></td>
                            <td class="uname"><?php echo $val->order_code?> </td>
                      		<td><?php echo $val->fullname?></td>
                            <td><?php echo $val->qty?></td>
                            <td><?php echo number_format($val->discount, 2, '.', ''); ?></td>
                            <td align="right"><?php echo number_format($val->net_pay, 2, '.', ''); ?></td>
                            <td>
                            <?php
							switch($val->order_status_id)
							{
							case 0: echo'<span class="label label-warning">Placed</span>';
							           break;
							case 1: echo '<span class="label label-success">Complete</span>';
							          break;
							case 2: echo '<span class="label label-info">Processing</span>';
							         break;
							case 3:  echo '<span class="label label-info">Dispatch</span>';
							         break;
							case 4: echo '<span class="label label-info">Deliver</span>';
							         break;
							case 4: echo '<span class="label label-info">Cancel</span>';
							         break;
							}
									 
								?>	  
							
                            </td>
                            <td width="5%" nowrap="nowrap" align="center"><?php echo date('d-M-Y H:i:s', strtotime($val->last_updated));?></td>
                          
                       </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
       
    </div>
</div>
<script>
    function myFunction() {
        window.print();
    }
</script>