<!doctype html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="{{$pagesettings->site_name}}">	
<meta name="csrf-token" content="{{ csrf_token() }}" />
<base href="{{url('/')}}/" >
<title>@yield('page-title')</title>

<link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/css/foundation.css')}}" />
<link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/css/register.css')}}" />		        
<script src="{{asset('resources/assets/themes/affiliate/js/modernizr.js')}}"></script>
<script src="{{asset('resources/assets/themes/affiliate/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<link href='https://fonts.googleapis.com/css?family=Lato:100,400,900' rel='stylesheet' type='text/css'>    
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>  
<body>
	@yield('contents')	
	<script src="{{asset('resources/assets/themes/affiliate/plugins/jQueryUI/jquery-ui.js')}}"></script>
	<script src="{{asset('resources/assets/themes/affiliate/js/bootstrap.min.js')}}"></script>	
	<script src="{{asset('resources/supports/app.js')}}"></script>
	@yield('scripts')
</body>
</html>
