@extends('affiliate.layout.dashboard')
@section('title',"Change Email")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>My Profile</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li >Profile</li>
		<li class="active">Change Email</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">				
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Change Email</h3>
					</div>
					<div class="box-body">
						<form class="form-horizontal" action="{{route('profile.changeemail')}}" method="post">
							{!! csrf_field() !!}
							<div class="form-group">
								<label for="inputName" class="col-sm-3 control-label">Old Password</label>
								<div class="col-sm-4">
									<input type="password" class="form-control" id="oldpassword" placeholder="Enter old password">
								</div>
							</div>
							<div class="form-group">
								<label for="inputName" class="col-sm-3 control-label">New Password</label>
								<div class="col-sm-4">
									<input type="password" class="form-control" id="newpassword" placeholder="Enter new password">
								</div>
							</div>
							<div class="form-group">
								<label for="inputName" class="col-sm-3 control-label">Confirm Password</label>
								<div class="col-sm-4">
									<input type="password" class="form-control" id="confirm_newpassword" placeholder="Re-enter your new password">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-4">
									<button type="submit" class="btn btn-danger">Submit</button>
								</div>
							</div>
						</form>					
					</div>					
				</div>
				<!-- /.box -->
			</div>
			<!-- ./col -->				
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
