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
        <h1 style="text-align:center">Tickets List - <?php echo date("d-M-Y");?></h1><br/>
        
           <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                	  			<th>{{trans('affiliate/general.id')}}</th>
                                <th>{{trans('affiliate/general.subject')}}</th>
                                <th>{{trans('affiliate/general.category')}}</th>                                   
                                <th>{{trans('affiliate/general.priority')}}</th>
                                <th>{{trans('affiliate/general.status')}}</th> 
                                <th>{{trans('affiliate/general.post_date')}}</th> 
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
                            <td class="text-left">{{ $row->ticket_id}}</td>
                            <td class="text-left">{{ $row->subject }}</td>
                            <td class="text-left">{{ $row->category_name}}</td> 
                            <td class="text-left"><?php echo $row->priority_name; ?></td>
                            <td class="text-left"><?php echo $row->status_name; ?></td>    
                            <td class="text-left">{{ $row->created }}</td>             
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