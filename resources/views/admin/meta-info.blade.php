<div class="panel panel-default" id="meta_form" style="display: none;">
    <div class="panel-heading">
        <h4 class="panel-title col-sm-6"></h4>
    </div>
    <div class="panel-body">
        <form class="form" id="meta_info_form" action="{{URL::to('admin/seller/meta-info/save')}}">
            <input type="hidden" name="meta_info[post_type_id]" id="post_type_id" value="">
            <input type="hidden" name="meta_info[relative_post_id]" id="relative_post_id" value="">

            <div class="row">
                <label class="control-label col-md-4" for="description">Description:</label>
                <div class="col-md-8">
                    <textarea class="form-control" name="meta_info[description]" id="description"></textarea>
                </div>
            </div>
            <div class="row">
                <label class="control-label col-md-4" for="meta_keys">Meta Keys:</label>
                <div class="col-md-8">
                    <input type="text" class="" name="meta_info[meta_keys]" value="" id="meta_keys"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-offset-4 col-md-8">
                    <input type="submit" class="btn btn-success" value="Save">
                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                </div>
            </div>
        </form>
    </div>
</div>
