<form  class="form-horizontal" >
    <div class="form-group">
        <label class="control-label col-md-2" for="email">Product Name:</label>
        <div class="col-md-8">
            <input type="hidden" name="product_id" id="product_id" value="{{{ $details->product_id or '' }}}">
            @if(isset($details) && !empty($details))
            {{$details->product_name}}
            @endif
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="email">Select an option:</label>
        <div class="col-md-8">
            <div class="col-md-4"><div class="radio">
                    <label><input type="radio" id="type" name="type" value="1" checked>Add Stock</label>
                </div></div>
            <div class="col-md-4"><div class="radio">
                    <label><input type="radio" id="type" name="type" value="2">Reduce Stock</label>
                </div></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="email">Current Stock:</label>
        <div class="col-md-4">
            <input type="text" name="current_value" class="form-control" id="current_value" value="{{{ $details->current_stock or '' }}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="email">Update Stock Value:</label>
        <div class="col-md-4">
            <input type="text" name="stock_value" onkeypress="return isNumberKeydot(event)" class="form-control"  id="stock_value" value="{{{ $details->stock_value or '' }}}">
            <div id="stock_msg"></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="email"></label>
        <div class="col-md-8">
            <input type="button" class="btn  btn-primary" name="update_stock" id="update_stock" value="Update" />
            <button id="cancel" class="btn  btn-danger">Cancel</button>
        </div>
    </div>	
</form>	
