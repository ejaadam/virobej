<nav id="top_navigation">
    <div class="container">
        <ul id="icon_nav_h" class="top_ico_nav clearfix">
            <li>
                <a href="{{URL::to('admin/suppliers/approvals')}}">
                    <span class="label label-danger">{{$approvals_cnts or ''}}</span>
                    <i class="icon-group icon-2x"></i>
                    <span class="menu_label">Approvals</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/suppliers')}}">
                    <span class="label label-success">{{$active_cnts or ''}}</span>
                    <i class="icon-eye-open icon-2x"></i>
                    <span class="menu_label">Active</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/suppliers/inactive')}}">
                    <span class="label label-danger">{{$inactive_cnts or ''}}</span>
                    <i class="icon-eye-close icon-2x"></i>
                    <span class="menu_label">Inactive</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('admin/suppliers/closed')}}">
                    <span class="label label-danger">{{$closed_cnts or ''}}</span>
                    <i class="icon-remove icon-2x"></i>
                    <span class="menu_label">Closed</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
