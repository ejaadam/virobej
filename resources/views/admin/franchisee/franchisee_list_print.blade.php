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
        <h1>Manage Centers - <?php echo date("d-M-Y");?></h1><br/>
        <table id="example6" class="table table-bordered table-striped" border="1">
            <thead>
                <tr>
                    <th>DOR</th>
                    <th>Username</th>
                    <th>Support Center Name</th>
                    <th>Support Center Type</th>
                    <th>Country</th>
                    <th>District Support Center</th>
                    <th>State Support Center</th>
                    <th>Regional Support Center</th>
                    <th>Country Support Center</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($franchisee_list))
                @foreach($franchisee_list as $franchasee)
                <tr>
                    <td>{{date('Y-m-d',strtotime($franchasee->signedup_on))}}</td>
                    <td>{{$franchasee->uname}}</td>
                    <td>{{ $franchasee->company_name.'<br />'.'('.$franchasee->first_name.' '.$franchasee->last_name.')'}}</td>
                    <td align="center">
                        {{$franchasee->franchisee_type_name}}
                        <br />
                        @if(isset($franchasee->access_country_name) && !empty($franchasee->access_country_name))
                        {{ '('.$franchasee->access_country_name.')' }}
                        @elseif(isset($franchasee->access_region_name) && !empty($franchasee->access_region_name))
                        {{ '('.$franchasee->access_region_name.')' }}
                        @elseif(isset($franchasee->access_state_name) && !empty($franchasee->access_state_name))
                        {{ '('.$franchasee->access_state_name.')' }}
                        @elseif(isset($franchasee->access_district_name) && !empty($franchasee->access_district_name))
                        {{ '('.$franchasee->access_district_name.')' }}
                        @elseif(isset($franchasee->access_city_name) && !empty($franchasee->access_city_name))
                        {{ '('.$franchasee->access_city_name.')' }}
                        @endif
                    </td>
                    <td>{{$franchasee->country_name}}</td>
                    <td>
                        @if(isset($franchasee->district_frname) && !empty($franchasee->district_frname) )
                        {{ $franchasee->district_frname }}
                        @else
                        {{ '-' }}
                        @endif
                    </td>
                    <td>
                        @if(isset($franchasee->state_frname) && !empty($franchasee->state_frname) )
                        {{ $franchasee->state_frname }}
                        @elseif(isset($franchasee->state_frname1) && !empty($franchasee->state_frname1) )
                        {{ $franchasee->state_frname1 }}
                        @else
                        {{ '-' }}
                        @endif

                    </td>
                    <td>
                        @if(isset($franchasee->region_frname) && !empty($franchasee->region_frname) )
                        {{ $franchasee->region_frname }}
                        @elseif(isset($franchasee->region_frname1) && !empty($franchasee->region_frname1) )
                        {{ $franchasee->region_frname1 }}
                        @elseif(isset($franchasee->region_frname2) && !empty($franchasee->region_frname2) )
                        {{ $franchasee->region_frname2 }}
                        @else
                        {{ '-' }}
                        @endif
                    </td>
                    <td>
                        @if(isset($franchasee->country_frname) && !empty($franchasee->country_frname) )
                        {{ $franchasee->country_frname }}
                        @elseif(isset($franchasee->country_frname1) && !empty($franchasee->country_frname1))
                        {{$franchasee->country_frname1}}
                        @elseif(isset($franchasee->country_frname3) && !empty($franchasee->country_frname2))
                        {{$franchasee->country_frname2}}
                        @elseif(isset($franchasee->country_frname3) && !empty($franchasee->country_frname3))
                        {{$franchasee->country_frname3}}
                        @else
                        {{ '-' }}
                        @endif
                    </td>
                    <td class="text-center" id="status_{{$franchasee->user_id}}"> <?php
                        if ($franchasee->block == 0)
                        {
                            if ($franchasee->login_block == 1)
                            {
                                echo '<span class="label label-danger">Login Blocked</span>';
                            }
                            else
                            {
                                if ($franchasee->user_status == 1)
                                {
                                    echo '<span class="label label-success">Active</span>';
                                }
                                else if ($franchasee->user_status == 0)
                                {
                                    echo '<span class="label label-warning">Inactive</span>';
                                }
                            }
                        }
                        else
                        {
                            ?>
                            <span class="label label-danger">Blocked</span>
    <?php
}
?>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onclick="myFunction()">Print</button>
