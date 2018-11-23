<nav id="side_fixed_nav">
    <div class="slim_scroll">
        <div class="side_nav_actions"> <a href="javascript:void(0)" id="side_fixed_nav_toggle"><span class="icon-align-justify"></span></a>
            <div id="toogle_nav_visible" class="make-switch switch-mini" data-on="success" data-on-label="<i class='icon-lock'></i>" data-off-label="<i class='icon-unlock-alt'></i>">
                <input id="nav_visible_input" type="checkbox">
            </div>
        </div>
        <ul id="text_nav_side_fixed">
            <li><a href="{{URL::to('admin/dashboard')}}"><span class="icon-dashboard"></span>Dashboard</a> </li>           
            <li><a href="#"><span class="icon-exchange"></span>In-Store Seller</a>
                <ul>
                    <!--li><a href="{{URL::to('admin/suppliers/add')}}">Add Supplier</a></li-->
					<!--li><a href="{{URL::to('admin/seller/stores/management')}}">Stores Management</a></li-->
                    <li><a href="{{URL::to('admin/seller')}}">Seller Management</a></li>
                    <li><a href="{{URL::to('admin/seller/new')}}">Seller New Signup</a></li>					                    
                    <li><a href="{{URL::to('admin/seller/verification')}}">Seller KYC</a></li>
                    <li><a href="{{URL::to('admin/seller/commission')}}">Seller Commission Settings</a></li>
                    <li><a href="{{URL::to('admin/catalog/products/seller/brands')}}">Seller Brands</a></li>
                    <li><a href="{{route('admin.seller.tax-info')}}">Seller Verification</a></li>
                    <li><a href="{{route('admin.seller.in_store.category-list')}}">In-Store category</a></li>
   
                </ul>
            </li>
            <li><a href="#"><span class="icon-exchange"></span>Online Retailers</a>
                <ul>
                    <li><a href="{{URL::to('admin/online/store-add')}}">Add New Retailers</a></li>
                    <li><a href="{{URL::to('admin/online/store-list')}}">Manage Retailers</a></li>
                    <li><a href="{{URL::to('admin/online/category/list')}}">Online Store Category</a></li>
					
				</ul>
            </li>
			<li><a href="#"><span class="icon-exchange"></span>Products</a>
                <ul>
                    <li><a href="#">Add New Products</a></li>
                    <li><a href="#">Manage Products</a></li>
                    <li><a href="{{route('admin.category.products.category-list')}}">Products Category</a></li>
					
				</ul>
            </li>
			<li><a href="#"><span class="icon-exchange"></span> Management Affiliate</a>
                <ul>
                    <!--li><a href="{{URL::to('admin/suppliers/add')}}">Add Supplier</a></li-->
				
                    <li><a href="{{route('admin.aff.root-account.create')}}">Create Root Affiliate</a></li>
                    <li><a href="{{route('admin.aff.root-account.view')}}">View Affiliate</a></li>
                </ul>
            </li>
			<li><a href="#"><span class="icon-exchange"></span> Management Franchisee</a>
                <ul>                    
                    <li><a href="{{route('admin.franchisee.create')}}">Create Franchisee</a></li>       
                    <li><a href="{{route('admin.franchisee.manage')}}">manage Franchisee</a></li>       					
                </ul>
            </li>
			<li class="treeview">
                <a href="#"><span class="icon-currency"></span> Finance Management</a>
                <ul class="treeview-menu">
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer.to_merchant')}}">Merchant credit & debit</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer.to_member')}}">Member credit & debit</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer.dsa')}}">DSA credit & debit</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer-history')}}">Member Fund Transfer history</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.admin-credit-debit-history')}}">Admin Credit & Debits log</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.transaction-log')}}">Transaction Log</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.order-payments')}}">Order Payments</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
