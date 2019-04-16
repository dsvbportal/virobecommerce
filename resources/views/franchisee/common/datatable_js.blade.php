<link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/css/responsive.bootstrap.min.css')}}">
<script src="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('resources/assets/themes/franchisee/plugins/datatable-responsive/js/responsive.bootstrap.min.js')}}"></script>
<!-- bootstrap datepicker -->
@include('franchisee.common.datepicker')
<script>
  $(function () {
	  
   $('#dob').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    });
	var fromDate = $('#from,#from_date').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    }).on('changeDate',function (evt) {
    	var pkDate = new Date(evt.date);
        pkDate.setDate(pkDate.getDate() + 1);
        toDate.datepicker('setStartDate', pkDate);
    });
    var toDate = $('#to,#to_date').datepicker({
		autoclose:true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function (evt) {
        var pkDate = new Date(evt.date);
        pkDate.setDate(pkDate.getDate() - 1);
        fromDate.datepicker('setEndDate', pkDate);
    });;
});
</script>