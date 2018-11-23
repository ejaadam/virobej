@extends('admin.layouts.login')
@section('contents')
<style>
    /*  form validation styles */
    .form-group span.errmsg_yellow{
        
        bottom:-17px;
        right:0px;
        z-index:10;
        background-color:red;
        color:#fff;
        font-weight:bold;
        font-size:11px;
        line-height:17px;
        padding:3px 8px 4px;
        border-radius: 0 0 5px 5px;
    }

    .form-control.yellow_brd{
        border-top:1px solid red !important;
        border-bottom:1px solid red !important;
        border-right:1px solid red !important;
        border-left:1px solid red !important;        
    }

    .form-control.normal_brd{
        border:1px transparent !important;
    }
    .input-group-addon.yellow_brd1{
        border-top:1px solid red !important;
        border-bottom:1px solid red !important;
        border-right:none;
        border-left:1px solid red !important;
    }

    .input-group-addon.normal_brd1{
        border:1px transparent !important;
    }

    #form-login .form-group{
        margin-bottom:8px;
    }

</style>
<div id="login_mess" style="color: red; text-align: center;">  <?php echo Session::get('msg'); ?>  </div>
<form id="hr_form_register">
 <input type="button" id="hr_register" value="submit">

<table class="form-fields">
<tbody>
    <tr>
        <th>
            <label for="Account_name" class="required">Name <span class="required">*</span></label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="Account_name" name="Account_name" type="text" class="form-control" maxlength="10">
                <span class="errmsg_yellow" style="display:none"></span>
            </div>
        </td>
    </tr>
    <tr>
        <th>
            <label for="Account_officePhone">Office Phone</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="officePhone" name="officePhone" type="number" class="form-control" min="0">
                <span class="errmsg_yellow" style="display:none"></span>
            <div class="form-group">
        </td>
    </tr>
    <tr>
        <th>
            <label for="Account_industry_value">Industry</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                    <select name="industry" id="industry" class="form-control">
                        <option value="">(None)</option>
                        <option value="Automotive">Automotive</option>
                        <option value="Banking">Banking</option>
                        <option value="Business Services">Business Services</option>
                        <option value="Energy">Energy</option>
                        <option value="Financial Services">Financial Services</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Retail">Retail</option>
                        <option value="Technology">Technology</option>
                    </select>
                    <span class="errmsg_yellow" style="display:none"></span>
            </div>
        </td>
    </tr>

     <tr>
        <th>
            <label for="Account_officeFax">Office Fax</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="officeFax" name="officeFax" type="number" class="form-control" min="0">
                <span class="errmsg_yellow" style="display:none"></span>
             </div>
        </td>
    </tr>

     <tr>
        <th>
            <label for="Account_employees">Employees</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="employees" name="employees" type="text" class="form-control">
                <span class="errmsg_yellow" style="display:none"></span>
            </div>
        </td>
    </tr>

    <tr>
        <th>
            <label for="Account_annualRevenue">Annual Revenue</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="annualRevenue" name="annualRevenue" type="text" class="form-control">
                <span class="errmsg_yellow" style="display:none"></span>
            <div class="form-group">
        </td>
    </tr>

    <tr>
        <th>
            <label for="Account_type_value">Type</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <select name="Account_type" id="Account_type" class="form-control">
                    <option value="">(None)</option>
                    <option value="Prospect">Prospect</option>
                    <option value="Customer">Customer</option>
                    <option value="Vendor">Vendor</option>
                </select>
                <span class="errmsg_yellow" style="display:none"></span>
            </div>
        </td>
    </tr>

    <tr>
        <th>
            <label for="Account_website">Website</label>
        </th>
        <td colspan="1">
            <div class="form-group">
                <input id="Account_website" name="Account_website" type="text" class="form-control">
                <span class="errmsg_yellow" style="display:none"></span>
            </div>
        </td>
    </tr>

    <tr>
        <th>
            <label>Billing Address</label>
        </th>
        <td colspan="1">
            <div class="address-fields form-group">
                <div class="overlay-label-field">
                    <label for="Account_billingAddress_street1">Street 1</label>
                    <input name="billing_street1" id="billing_street1" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_billingAddress_street2">Street 2</label>
                    <input name="billing_street2" id="billing_street2" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_billingAddress_city">City</label>
                    <input name="billing_city" id="billing_city" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="hasHalfs">
                    <div class="overlay-label-field half">
                        <label for="Account_billingAddress_state">State</label>
                        <input name="billing_state" id="billing_state" type="text" class="form-control">
                        <span class="errmsg_yellow" style="display:none"></span>
                    </div>
                    <div class="overlay-label-field half">
                        <label for="Account_billingAddress_postalCode">Postal Code</label>
                        <input name="billing_code" id="billing_code" type="text" class="form-control">
                        <span class="errmsg_yellow" style="display:none"></span>
                    </div>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_billingAddress_country">Country</label>
                    <input name="billing_country" id="billing_country" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                    
                </div>
                 <div class="overlay-label-field">
                    <label for="Account_billingAddress_country">Shipping Address is same as Billing Address</label>                    
                    <input type="checkbox" id="checkid" value="">
                </div>
            </div>
        </td>
    </tr>
  <tr>
    
    </tr>
    <tr>
        <th>
            <label>Shipping Address</label>
        </th>
        <td colspan="1">
            <div class="address-fields form-group">
                <div class="overlay-label-field">
                    <label for="Account_shippingAddress_street1">Street 1</label>
                    <input name="shipping_street1" id="shipping_street1" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_shippingAddress_street2">Street 2</label>
                    <input name="shipping_street2" id="shipping_street2" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_shippingAddress_city">City</label>
                    <input name="shipping_city" id="shipping_city" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
                <div class="hasHalfs">
                    <div class="overlay-label-field half">
                        <label for="Account_shippingAddress_state">State</label>
                        <input name="shipping_state" id="shipping_state" type="text" class="form-control">
                        <span class="errmsg_yellow" style="display:none"></span>
                    </div>
                    <div class="overlay-label-field half">
                        <label for="Account_shippingAddress_postalCode">Postal Code</label>
                        <input name="shipping_code" id="shipping_code" type="text" class="form-control">
                        <span class="errmsg_yellow" style="display:none"></span>
                    </div>
                </div>
                <div class="overlay-label-field">
                    <label for="Account_shippingAddress_country">Country</label>
                    <input name="shipping_country" id="shipping_country" type="text" class="form-control">
                    <span class="errmsg_yellow" style="display:none"></span>
                </div>
            </div>
        </td>
    </tr>

    <tr>
        <th>
            <label for="Account_description">Description</label>
        </th>
        <td colspan="1">
            <div class="form-group">
            <textarea id="Account_description" name="Account_description" class="form-control" rows="6" cols="50" style="height: 106px;"></textarea> 
            <span class="errmsg_yellow" style="display:none"></span> 
            </div>    
        </td>
    </tr>
</tbody>
</table>
</form>


       
    



<!--div class="login_wrapper"-->
 

</div>
   
@stop
@section('scripts')
{{ HTML::script('supports/supplier/hr_create.js') }}
@stop

