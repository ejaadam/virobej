@if(isset($logged_userinfo->is_verified))
<nav id="top_navigation">
    <div class="container">
        <ul id="icon_nav_h" class="top_ico_nav clearfix">
            <li>
                <a href="{{URL::to('seller/dashboard')}}">
                    <i class="icon-home icon-2x"></i>
                    <span class="menu_label">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('seller/stores')}}">
                    <i class="icon-exchange icon-2x"></i>
                    <span class="menu_label">Stores</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('seller/products')}}">
                    <i class="icon-tags icon-2x"></i>
                    <span class="menu_label">Products</span>
                </a>
            </li>
            <!--li>
                <a href="{{URL::to('seller/feedback')}}">
                    <i class="icon-comments-alt icon-2x"></i>
                    <span class="menu_label">FeedBacks</span>
                </a>
            </li-->
            <li>
                <a href="{{URL::to('settings')}}">
                    <i class="icon-wrench icon-2x"></i>
                    <span class="menu_label">Settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
@endif
