<?php
$request = request();
Route::get('/', function () {
    return view('welcome');
});
Route::post('init', function()
{
    if (Request::ajax())
    {
        return Response::json(['Constants'=>Config::get('constants')]);
    }
});
if ($request->is('api/v1/*'))
{
	Route::group(['prefix'=>'api/v1/', 'namespace'=>'Api' ], function() use($request)
    {
		if ($request->is('api/v1/seller/*'))
        {
           /*  Route::group(['prefix'=>'seller', 'as'=>'seller.', 'namespace'=>'Seller'], function()
            {
                include('routes/seller_api.php');
            }); */
        } 
		elseif ($request->is('user/*'))
        {			
            echo "AsaS"; die;
        }  
		elseif ($request->is('api/v1/user/*'))
        {			
			include('routes/payment_gateway.php');			
            Route::group(['prefix'=>'user', 'as'=>'api.v1.user.', 'middleware'=>'api' ,'namespace'=>'User'], function()
            {				
                include('routes/user_api.php');
            });
        }
	});	
	Route::group(['prefix'=>'seller', 'as'=>'seller.', 'namespace'=>'Seller'], function()
    {       		
            include('routes/seller.php');       
    });
	Route::group(['prefix'=>'affiliate', 'as'=>'aff.', 'namespace'=>'Affiliate'], function()
	{ 
		include('routes/affiliate.php');    
	});
	
}
elseif ($request->is('seller/*'))
{	
	Route::group(['prefix'=>'seller', 'as'=>'seller.',  'namespace'=>'Seller'], function()
    {               
			//print_r(123);exit;
            include('routes/seller.php');       
    });
	/* Route::group(['prefix'=>'api/v1/seller', 'as'=>'api.v1.seller.', 'namespace'=>'Api\Seller'], function()
    {        
            include('routes/seller_api.php');          
    }); */
} 
elseif ($request->is('admin/*'))
{	
	Route::group(['prefix'=>'admin', 'as'=>'admin.'], function()
    {               
            include('routes/admin.php');       
    });	
} 
elseif ($request->is('affjoin/*') || $request->is('affiliate/*'))
{	
	Route::group(['prefix'=>'affjoin', 'as'=>'aff.', 'namespace'=>'Affiliate'], function()
    {               
		Route::get('{referralname?}',['as'=>'signup','uses'=>'AffiliateController@signup']);     		
    });	
	Route::group(['prefix'=>'affiliate', 'as'=>'aff.',  'namespace'=>'Affiliate'], function()
    {               
            include('routes/affiliate.php');       
    });	
} 

Route::post('check-pincode', 'DataController@checkPincode');
Route::post('currencies-list', 'DataController@currencies_list');
Route::post('product-visibility-list', 'DataController@product_visibility_list');
Route::any('get-tags', 'DataController@get_tags');
Route::post('product-brands-list', 'DataController@product_brands_list');
Route::post('product-categories-list', 'DataController@product_categories_list');
Route::post('payment-mode-list', 'DataController@payment_mode_list');
Route::post('product-condition-list', 'DataController@product_condition_list');
Route::post('get_available_payment_gateway', 'DataController@get_available_payment_gateway');
Route::post('property-values-for-checktree', 'DataController@properies_values_for_checktree');
Route::post('zone-list', 'DataController@zone_list');
Route::post('courier-mode-list', 'DataController@courier_mode_list');
Route::any('countries-list', 'DataController@countries_list');  
Route::post('seller-list', 'DataController@supplier_list');
Route::match(['get', 'post'], 'seller-categories', ['as'=>'seller-categories', 'uses'=>'DataController@sellerCategories']);
Route::match(['get', 'post'], 'seller-allcategories', ['as'=>'seller-categories', 'uses'=>'DataController@sellerAllCategories']);
Route::get('reset-profile-pin/{token}', ['as'=>'reset-profile-pin', 'uses'=>'BaseController@profilePinVerifyLink']);
