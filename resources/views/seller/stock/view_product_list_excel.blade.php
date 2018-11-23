<?php
foreach ($product_stock as $key => $val)
    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>View Product Stock - <?php echo date("d-M-Y");?></h1><br/>
          <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr> 
                      <th>Created On</th>
                      <th>Product</th>
                      <th>Amount Value</th>
                      <th>Stock</th>
                      <th>Available</th>
                      <th>Sold</th>
                      <th>In-Progress</th>
                </tr>
            </thead>
		<tbody>
                <?php
                $i = 1;
                if (!empty($product_stock))
                {
				
                    foreach ($product_stock as $val)
                    {
                        ?>
                        <tr>
                     
                            <td width="5%" nowrap="nowrap" align="center"><?php echo date('d-M-Y H:i:s', strtotime($val->created_date));?></td>
                            <td class="uname"><?php echo $val->product_name; ?> <br />
                            Code:<?php echo $val->product_code; ?><br /> 
							Brand:<?php echo $val->brand_name; ?><br />
                            Category:<?php echo $val->category_name; ?>
                            </td>                           
                            <td> {{number_format($val->price,2,'.',',')}} {{($val->price_type==1)?'Points':'Coins'}} </td>
                            <td align="center">
                             <?php
                            if ($val->in_stock == 1)
                            {
                                echo '<span class="label label-success">In Stock</span>';
                            }
                            else
                            {
                                echo '<span class="label label-danger">Out of Stock</span>';
                            }
                          
							?>
                            </td>
                           <td><?php echo $val->current_stock; ?> </td>
                           <td><?php echo $val->sold_items; ?> </td>
                           <td><?php echo $val->commited_stock; ?> </td>
                              
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
       
    </div>
</div>