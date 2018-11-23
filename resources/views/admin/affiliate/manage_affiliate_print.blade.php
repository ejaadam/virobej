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
        <h1 style="text-align:center">View Profile of User - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="referral_bouns_list" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
								<tr>                                                    
                                <th class="text-center">{{trans('admin/affiliate/manage_user.doj')}}</th>
                                <th>{{trans('admin/affiliate/manage_user.uname')}}</th>
                                <th>{{trans('admin/affiliate/manage_user.full_name')}}</th>
						        <th>{{trans('admin/affiliate/manage_user.email')}}</th>
                                <th>{{trans('admin/affiliate/manage_user.mobile')}}</th>
                                <th>{{trans('admin/affiliate/manage_user.country')}}</th> 
                                <th>{{trans('admin/affiliate/manage_user.status')}}</th> 
                              
                            </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($manage_user_details != '' && isset($manage_user_details))
                {
                    foreach ($manage_user_details as $row)
                    {
                        ?>
                         <tr>                        
                            <td class="text-left">{{$row->signedup_on}}</td>
                            <td class="text-left">{{$row->uname}}</td>
                            <td class="text-left">{{$row->fullname}}</td>
                            <td class="text-left">{{$row->email}}</td> 
                            <td class="text-left">{{$row->mobile}}</td>
                            <td class="text-left">{{$row->country_name}}</td> 
                            <td class="text-left">{{$row->status_name}}</td> 
                            
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
<button class="noprint" onClick="myFunction()">Print</button>