<footer class="main-footer">
    <strong>Copyright &copy; 2014-{{date('Y')}} <a href="{{url('/')}}">{{$siteConfig->site_name}}</a>.</strong> All rights reserved.
</footer>
<div id="retailer-qlogin-model" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">User Quick Login</h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
                <form action="" id="retailerForm" autocomplete="off">
                    <div class="form-group">
                        <label for="email">User Email/Mobile:</label>
                        <input type="text" name="uname" id="retailerUname" class="form-control" id="email">
                        <span id="unameErr"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="qloginBtn" class="btn btn-info">Submit</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>