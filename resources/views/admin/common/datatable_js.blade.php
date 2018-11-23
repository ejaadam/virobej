<link rel="stylesheet" href="{{asset('assets/user/plugins/datatable-responsive/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/user/plugins/datatable-responsive/css/responsive.bootstrap.min.css')}}">
<script src="{{asset('assets/user/plugins/datatable-responsive/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/user/plugins/datatable-responsive/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/user/plugins/datatable-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/user/plugins/datatable-responsive/js/responsive.bootstrap.min.js')}}"></script>
<!-- bootstrap datepicker -->

<link rel="stylesheet" href="{{asset('assets/user//plugins/datepicker/datepicker3.css')}}">
<script src="{{asset('assets/user/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script>
  $(function () {
	  $('#dob').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    });
	$('#from,#from_date,#dob').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    }).on('changeDate',function (evt) {
    	var pkDate = new Date(evt.date);
		pkDate.setDate(pkDate.getDate() + 1);
        var pday = ("0" + (pkDate.getDate())).slice(-2);       
        pkDate.setMonth(pkDate.getMonth() + 1);
        var nDate = pkDate.getFullYear() + "-";
        var pMonth = ("0" + pkDate.getMonth()).slice(-2);
        nDate = nDate+pMonth+'-'+pday;  
        nDate = nDate;
        toDate.val(nDate);
        toDate.datepicker('update');		
		toDate.datepicker('setStartDate',pkDate);
    });
    var toDate = $('#to,#to_date').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    });
});
</script>