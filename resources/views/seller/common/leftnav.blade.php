<nav id="sidebar">
	<!--div class="sepH_b">
		<a href="javascript:void(0)" id="text_nav_v_collapse" class="btn btn-default btn-xs">Collapse All</a>
		<a href="javascript:void(0)" id="text_nav_v_expand" class="btn btn-default btn-xs">Expand All</a>
	</div-->
	<ul id="text_nav_v" class="side_text_nav">
		<li class="parent_active">
			<a href="{{route('seller.dashboard')}}"><span class="icon-dashboard"></span>Dashboard</a>			
		</li>
		@if(empty($logged_userinfo->is_verified))
		<a href="javascript:void(0)"><span class="icon-th-list"></span>Account Settings</a>
			<ul>		
				<li><a href="{{route('seller.account-settings')}}">Seller Information</a></li>
			</ul>	
		@endif	
		@if(!empty($logged_userinfo->is_verified))
		<li>
			<a href="javascript:void(0)"><span class="icon-th-list"></span>Account Settings</a>
			<ul>		
				<li><a href="{{route('seller.account-settings')}}">Seller Information</a></li>
				<!--<li><a href="{{route('seller.account-settings.manage-cashback')}}">Manage Cashback</a></li>
				<li><a href="{{route('seller.account-settings.tax-information')}}">Tax Information</a></li>				
				
				<li><a href="{{route('seller.account-settings.pickup-address')}}">Pick-Up Address</a></li>					
				<li><a href="{{route('seller.account-settings.return-address')}}">Return Address</a></li>					
				<li><a href="{{route('seller.account-settings.change-password')}}">Change Password</a></li>				
				<li><a href="{{route('seller.account-settings.shipping-info')}}">Shipping Information</a></li>-->			
				<li><a href="{{route('seller.account-settings.bank-details')}}">Bank Details</a></li>
			</ul>
		</li>
		<li>
			<a href="javascript:void(0)"><span class="icon-puzzle-piece"></span>Products</a>
			<ul>
				<li><a href="{{route('seller.products.list')}}">Manage Products</a></li>
				<li><a href="javascript:void(0)">Add Products</a></li>
				<!--li><a href="{{route('seller.products.list')}}">List Products</a></li-->					
			</ul>
		</li>
		<li>
			<a href="javascript:void(0)"><span class="icon-beaker"></span>Manage In-Store</a>
			<ul>
				<li>
					<a href="{{route('seller.outlet.list')}}">Manage Shop</a>					
				</li>
				<li>
					<a href="javascript:void(0)">Manage Users</a>
					<ul>
						<li><a href="javascript:void(0)">Add Users</a></li>
						<li><a href="javascript:void(0)">Assignment</a></li>
						<li><a href="{{route('seller.manage_users.user_list')}}">Mansage Users</a></li>						
					</ul>
				</li>
				<li><a href="{{route('seller.reports.instore.orders')}}">Orders</a></li>		
				<li><a href="javascript:void(0)">Payments Overview</a></li>		
				<li><a href="javascript:void(0)">Messages</a></li>		
			</ul>
		</li>	
		<li>
			<a href="javascript:void(0)"><span class="icon-puzzle-piece"></span>Manage Orders</a>
			<ul>
				<li><a href="javascript:void(0)">My Listings</a></li>
				<li><a href="javascript:void(0)">Track Approval Requests</a></li>				
				<li><a href="javascript:void(0)">Active Orders</a></li>				
				<li><a href="javascript:void(0)">Returns</a></li>				
				<li><a href="javascript:void(0)">Cancellations</a></li>				
				<li><a href="javascript:void(0)">Payments Overview</a></li>				
			</ul>
		</li>
		<li>
			<a href="javascript:void(0)"><span class="icon-puzzle-piece"></span>Payments</a>
			<ul>
				<li><a href="{{route('seller.reports.instore.transactions')}}">Transactions</a></li>
				<li><a href="javascript:void(0)">Previous Payments</a></li>				
			</ul>
		</li>
		<li class="">
			<a href="{{route('seller.logout')}}"><span class="icon-dashboard"></span>Logout</a>			
		</li>
		@endif
		<!--li> 
			<a href="javascript:void(0)">	<span class="icon-list"></span>Reports</a>
			<ul>
				<li><a href="{{route('seller.reports.instore.orders')}}">Orders</a></li>                                
				<li><a href="{{route('seller.reports.instore.transactions')}}">Transactions</a></li>                   
			</ul>
		</li-->
	</ul>				
</nav>