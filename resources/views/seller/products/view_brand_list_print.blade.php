<style><?php include('assets/supplier/css/print_style.css'); ?></style>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Categories -{{date("d-M-Y")}}</h1><br/>
        <h5 align="right"><button class="noprint" onclick="myFunction()">Print</button></h5>
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Created On</th>
                    <th>Brand Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($brands))
                @foreach ($brands as $val)
                <tr>
                    <td width="5%" nowrap="nowrap" align="center">{{date('d-M-Y H:i:s', strtotime($val->created_on))}}</td>
                    <td class="uname">{{$val->brand_name}} </td>
                    <td align="center">{{ ($val->status == 1)?'Active':'Inactive'}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<script>
    function myFunction() {
        window.print();
    }
</script>
