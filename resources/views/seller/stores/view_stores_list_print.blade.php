<?php
foreach ($get_stores_list as $key => $val)
    $$key = $val;
?>
<div class="rightbox">
<button class="noprint" onclick="myFunction()">Print</button>
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>View Stores - <?php echo date("d-M-Y");?></h1><br/>
          <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr> 
                      <th>Store Name</th>
                      <th>Company Name</th>
                      <th>Amount Value</th>
                      <th>Store Code</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>City</th>
                      <th>Updated On</th>

                </tr>
            </thead>
		<tbody>
                <?php
                $i = 1;
                if (!empty($get_stores_list))
                {
				
                    foreach ($get_stores_list as $val)
                    {
                        ?>
                        <tr>
                     
                            <td class="uname"><?php echo $val->store_name; ?> </td>  
                            <td><?php echo $val->company_name; ?> </td>                           
                            <td><?php echo $val->store_code; ?> </td>                           
                            <td><?php echo $val->mobile_no; ?> </td>                           
                            <td><?php echo $val->address; ?> </td>                           
                            <td><?php echo $val->city; ?> </td>                           
                            <td width="5%" nowrap="nowrap" align="center"><?php echo date('d-M-Y H:i:s', strtotime($val->updated_on));?></td>

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
