<header id="top_header">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-2">
                <a href="{{URL::asset('/admin/dashboard/')}}" class="logo_main" title="{{$pagesettings->site_name}}"><img src="{{$pagesettings->site_logo_large}}" alt="{{$pagesettings->site_name}}" height="40"></a>
            </div>
            <div class="col-sm-push-4 col-sm-3 text-right hidden-xs">
                <div class="notification_dropdown dropdown" id="user-notifications">
                    <a href="javascript:void(0)" class="notification_icon dropdown-toggle" data-toggle="dropdown">
                        <span class="label label-danger count" style="display:none;">0</span>
                        <i class="fa fa-2x fa-bell-o"></i>
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
            </div>
            <div class="col-xs-6 col-sm-push-4 col-sm-3">
                <div class="pull-right dropdown">
                    <a href="javascript:void(0)" class="user_info dropdown-toggle" title="{{$logged_userinfo->full_name or ''}}" data-toggle="dropdown">
                        <img src="{{$logged_userinfo->profile_img or ''}}" alt="">
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void(0)">Profile</a></li>
                        <li><a href="javascript:void(0)">Another action</a></li>
                        <li><a href="<?php echo URL::asset('/admin/logout/');?>">Log Out</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 col-sm-pull-6 col-sm-4">
                <form class="main_search">
                    <input type="text" id="search_query" name="search_query" class="typeahead form-control">
                    <button type="submit" class="btn btn-primary btn-xs"><i class="icon-search icon-white"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>
