<header id="top_header">
	<div class="container">
		<div class="row">
		    <!--  Site Image -->	
			<div class="col-xs-6 col-sm-2">
				<a href="{{URL::asset('/seller/dashboard')}}" class="logo_main" title="{{$pagesettings->site_name}}"><img src="{{$pagesettings->site_logo_large}}" alt="{{$pagesettings->site_name}}"></a>
			</div>	
			<!--  Navigations -->	
			<div class="col-xs-6 col-sm-8">
			    <!--  Profile Dropdowns -->	
				<!--div class="pull-right dropdown" style="margin-top: 7px;">
					<a  href="#" class="user_info dropdown-toggle" title="{{$userSess->full_name}}" data-toggle="dropdown">                         
						<span class="profile-name font-roboto">{{strtoupper(substr($userSess->full_name,0,1))}}</span>
                    </a>
					<ul class="dropdown-menu">
						<li><a href="{{route('seller.dashboard')}}"><i class="icon-dashboard"></i> Dashboard</a></li>					   
						<li class="dropdown-header"><b> My Account </b></li>
						<li><a  href="#"><i class="icon-user"></i> Profile </a></li>
						<li><a  href="{{route('seller.account-settings')}}"><i class="icon-cog"></i> Account Settings</a></li>
                        <li><a href="{{route('seller.logout')}}"><i class="icon-chevron-sign-right"></i> Log Out</a></li>						
					</ul>
				</div-->
				<!--  Notifications Messages -->				
				<!--div class="pull-right dropdown text-right" style="">
					<div class="notification_dropdown dropdown" id="user-notifications">
						<a href="javascript:void(0)" class="notification_icon dropdown-toggle" data-toggle="dropdown" title="Notifications">
						    <i class="fa fa-2x icon-bell" style="font-size: 20px;"></i>
							<span class="label label-danger count" style="display:'';padding:3px;">0</span>
						</a>
						<ul class="dropdown-menu">
							<li>
								<div class="dropdown_content">
									<ul class="dropdown_items list">
										<li>
											<h3><span class="small_info">12:43</span><a href="javascript:void(0)">Lorem ipsum dolor&hellip;</a></h3>
											<p>Lorem ipsum dolor sit amet&hellip;</p>
										</li>
										<li>
											<h3><span class="small_info">Today</span><a href="javascript:void(0)">Lorem ipsum dolor&hellip;</a></h3>
											<p>Lorem ipsum dolor sit amet&hellip;</p>
										</li>
										<li>
											<h3><span class="small_info">14 Aug</span><a href="javascript:void(0)">Lorem ipsum dolor&hellip;</a></h3>
											<p>Lorem ipsum dolor sit amet&hellip;</p>
										</li>
									</ul>
								</div>
							</li>
						</ul>
					</div>
				</div-->
								
				<!--  Navigations -->		
				@if(!empty($logged_userinfo->is_verified))
				<nav id="top_navigation" class="text_nav">
					<div class="container">
						<ul id="text_nav_h" class="clearfix j_menu top_text_nav jMenu l_tinynav1">
						<li class="jmenu-level-0">
							<a  href="#" class="fNiv isParent isTopParent">Products<i class="icon-angle-down"></i></a>
							<ul>
								<li><a href="{{route('seller.products.list')}}"> Manage Products</a></li>
								<li><a href="#"> Add Products</a></li>
							</ul>
						</li>
						<li class="jmenu-level-0">
							<a  href="#" class="fNiv isParent isTopParent">Online Sales<i class="icon-angle-down"></i></a>
							<ul>
								<li><a href="#"> My Listings</a></li>
								<li><a href="#"> Track Approval Requests</a></li>								
								<li><a href="#"> Active Orders</a></li>								
								<li><a href="#"> Returns</a></li>								
								<li><a href="#"> Cancellations</a></li>								
								<li><a href="#"> Payments Overview</a></li>						
							</ul>
						</li>
						<li class="jmenu-level-0">
							<a  href="#" class="fNiv isParent isTopParent">In-Store Sales<i class="icon-angle-down"></i></a>
							<ul>
								<li><a href="{{route('seller.outlet.list')}}"> Manage Shop</a></li>
								<li>
									<a href="#" data-toggle="dropdown">Manage Users</a>
									<ul class="sub-menu">
										<li><a href="{{route('seller.manage_users.add_user')}}">Add Users</a></li>
										<li><a href="#">Assignment</a></li>
										<li><a href="{{route('seller.manage_users.user_list')}}">Manage Users</a></li>						
									</ul>
								</li>
								<li><a href="{{route('seller.reports.instore.orders')}}" class="dropdown-item">Orders</a>
									<ul class="sub-menu">
										<li><a href="{{route('seller.reports.instore.orders')}}">My Listings</a></li>
										<li><a href="#">Track Approval Requests</a></li>				
										<li><a href="#">Active Orders</a></li>				
										<li><a href="#">Returns</a></li>				
										<li><a href="#">Cancellations</a></li>				
										<li><a href="#">Payments Overview</a></li>
									</ul>
								</li>		
								<li><a href="#">Payments Overview</a></li>		
								<li><a href="#">Messages</a></li>
							</ul>
						</li>
						<li class="jmenu-level-0">
							<a  href="#" class="fNiv isParent isTopParent">Finance<i class="icon-angle-down"></i></a>
							<ul>
								<li><a href="{{route('seller.reports.instore.transactions')}}">Transactions</a></li>
								<li><a href="#">Previous Payments</a></li>
							</ul>
						</li>						
						</ul>
					</div>
				</nav>
				@endif
			</div>
			<!-- Notifications  -->
			<div class="col-xs-6 col-sm-1 text-right hidden-xs">			   
				<div class="notification_dropdown dropdown" id="user-notifications">
					<a href="#" class="notification_icon dropdown-toggle" data-toggle="dropdown">
						<span class="label label-danger count">0</span>
						<i class="icon-envelope-alt icon-2x"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-wide">
						<li>
							<div class="dropdown_heading">Notifications</div>
							<div class="dropdown_content">
								<ul class="dropdown_items list">
								</ul>
							</div>
							<!--div class="dropdown_footer">
								<a href="#" class="btn btn-sm btn-default">Show all</a>
								<div class="pull-right dropdown_actions">
									<a href="#"><i class="icon-refresh"></i></a>
									<a href="#"><i class="icon-cog"></i></a>
								</div>
							</div-->
						</li>
					</ul>
				</div>
			</div>
			<!--  Profile DropDown -->
			<div class="col-xs-6 col-sm-1">
				<div class="pull-right dropdown" style="margin-top: 7px;">
					<a  href="#" class="user_info dropdown-toggle" title="{{$userSess->full_name}}" data-toggle="dropdown">                         
						<span class="profile-name font-roboto">{{strtoupper(substr($userSess->full_name,0,1))}}</span>
                    </a>
					<ul class="dropdown-menu">
						<li><a href="{{route('seller.dashboard')}}"><i class="icon-dashboard"></i> Dashboard</a></li>					   
						<li class="dropdown-header"><b> My Account </b></li>
						<li><a  href="#"><i class="icon-user"></i> Profile </a></li>
						<li><a  href="{{route('seller.account-settings')}}"><i class="icon-cog"></i> Account Settings</a></li>
                        <li><a href="{{route('seller.logout')}}"><i class="icon-chevron-sign-right"></i> Log Out</a></li>						
					</ul>
				</div>
			</div>
		</div>
	</div>
</header>