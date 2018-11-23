@extends('affiliate.layout.dashboard')
@section('title',"My Profile")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>My Profile</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Profile</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-3">
				<!-- Profile Image -->
				<div class="box box-primary">
					<div class="box-body box-profile">
						<img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">

						<h3 class="profile-username text-center">{{$userSess->full_name}}</h3>

						<p class="text-center text-danger">({{$userSess->uname}})</p>

						<ul class="list-group list-group-unbordered">
						<li class="list-group-item">
						  <b>My Refferals</b> <a class="pull-right">1,322</a>
						</li>
						<li class="list-group-item">
						  <b>My Downlines</b> <a class="pull-right">543</a>
						</li>
						
						</ul>
						<a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- About Me Box -->
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">About Me</h3>
					</div>
					<div class="box-body">
						<strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
						<p class="text-muted">Malibu, California</p>					
					</div>					
				</div>
				<!-- /.box -->
			</div>
			<!-- ./col -->
			<div class="col-md-9">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
					  <li class="active"><a href="#changepwd" data-toggle="tab">Change Password</a></li>
					  <li><a href="#changespwd" data-toggle="tab">Change Security Password</a></li>
					  <li><a href="#changeemail" data-toggle="tab">Change Email</a></li>
					</ul>
					<div class="tab-content">
						<div class="active tab-pane" id="changepwd">
							<form class="form-horizontal" action="{{route('profile.changepwd')}}" method="post">
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
						<div class="tab-pane" id="changespwd">
							<form class="form-horizontal" action="{{route('profile.changespwd')}}" method="post">
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
						<div class="tab-pane" id="changeemail">
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
				</div>
			</div>
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
