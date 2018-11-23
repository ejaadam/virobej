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
                if ($export_data != '' && isset($export_data))
                {
                    foreach ($export_data as $row)
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