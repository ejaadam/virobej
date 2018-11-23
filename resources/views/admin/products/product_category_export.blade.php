
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1 style="text-align:center">Product Categories - <?php echo date("d-M-Y");?></h1><br/>
        
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
								<tr>                                                    
                                <th>{{trans('general.label.created_on')}}</th>
								<th>{{trans('admin/in_store_category.category')}} </th>
								<th>{{trans('admin/in_store_category.parent_category')}} </th>
								<th>{{trans('admin/in_store_category.status')}} </th>
                            </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($manage_category_details != '' && isset($manage_category_details))
                {
                    foreach ($manage_category_details as $row)
                    {
                        ?>
                        <tr>                        
                            <td class="text-left">{{$row->created_on}}</td>
                            <td class="text-left">{{$row->bcategory_name}}</td>
                            <td class="text-left">{{$row->parent_name}}</td>
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