	<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">{{trans('affiliate/general.my_team_list')}}- <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
					
                                <th>{{trans('affiliate/referrels/my_referrels.user')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.full_name')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.sponser')}}</th>
							    <th>{{trans('affiliate/referrels/my_referrels.signed')}}</th> 
                                <th>{{trans('affiliate/referrels/my_referrels.upline')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.rank')}}</th>
								<th>{{trans('affiliate/referrels/my_referrels.placement')}}</th>  
                                <th>{{trans('affiliate/referrels/my_referrels.qv')}}</th>                            
                                <th>{{trans('affiliate/referrels/my_referrels.cv')}}</th>
                               
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
                            <td class="text-left">{{ $row->uname }}</td>
                            <td class="text-left">{{ $row->full_name }}</td>
                            <td class="text-left">{{ $row->direct_sponser_uname }}</td>
                            <td class="text-left">{{ $row->signedup_on }}</td>
                            <td class="text-left">{{ $row->upline_uname }}</td>
                            <td class="text-left">{{ $row->rank }}</td>
                            <td class="text-left">{{ " "}}</td>
                            <td class="text-left">{{ $row->qv }}</td>
                            <td class="text-left">{{ $row->cv }}</td>
                                                                
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