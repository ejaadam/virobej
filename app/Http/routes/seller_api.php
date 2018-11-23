<?php

//Route::post('sign-up', ['as'=>'sign-up', 'uses'=>'APISupplierContoller@signUp']);
Route::post('chech-user', ['as'=>'check-user', 'uses'=>'APISupplierContoller@check_user']);
//Route::post('check-verification-mobile', ['as'=>'check-verification-mobile', 'uses'=>'APISupplierContoller@checkMobileVerification']);
//Route::post('verify-mobile', ['as'=>'verify-mobile', 'uses'=>'APISupplierContoller@verifyMobile']);
//Route::post('verify-email', ['as'=>'verify-email', 'uses'=>'APISupplierContoller@verifyEmail']);

Route::post('check-account', ['as'=>'check-account', 'uses'=>'APISupplierContoller@checkAccount']);
Route::post('check-verification-code', ['as'=>'check-verification-code', 'uses'=>'APISupplierContoller@checkVerificationCode']);
Route::post('forgot-password', ['as'=>'forgot-password', 'uses'=>'APISupplierContoller@forgotPassword']);
Route::post('change-email', ['as'=>'change-email', 'uses'=>'APISupplierContoller@change_email']);

Route::group(['prefix'=>'setup', 'as'=>'setup.'], function()
{
    Route::post('bussiness-info', ['as'=>'bussiness-info', 'uses'=>'APISupplierContoller@saveBussinessInfo']);
    Route::post('account-info', ['as'=>'account-info', 'uses'=>'APISupplierContoller@saveAccountInfo']);
    Route::post('store-banking', ['as'=>'store-banking', 'uses'=>'APISupplierContoller@saveStoreBanking']);
    Route::post('store-info', ['as'=>'store-info', 'uses'=>'APISupplierContoller@saveStoreInfo']);
    Route::post('update-kyc', ['as'=>'update-kycc', 'uses'=>'APISupplierContoller@saveKycUpdatee']);
    //Route::post('update-kyc', function() {print_r(123);});
	Route::post('save-bank-info', ['as'=>'save-bank-info', 'uses'=>'APISupplierContoller@save_bank_info']);
	Route::post('delete-bank-info', ['as'=>'delete-bank-info', 'uses'=>'APISupplierContoller@delete_bank_info']);
	Route::post('get-bank-info', ['as'=>'get-bank-info', 'uses'=>'APISupplierContoller@get_bank_info']);
	Route::post('update-bank-info', ['as'=>'update-bank-info', 'uses'=>'APISupplierContoller@update_bank_info']);
});
Route::group(['prefix'=>'order', 'as'=>'order.'], function()
{    
    //Route::post('update-approval-status', ['as'=>'update-approval-status', 'uses'=>'APISupplierOrderController@updateSubOrderApprovalStatus'])->where(['id'=>'[0-9]+', 'status'=>'[a-z]+']);   
    //Route::post('item/update-approval-status', ['as'=>'item.update-approval-status', 'uses'=>'APISupplierOrderController@updateOrderItemApprovalStatus']);
    Route::post('item/update-status', ['as'=>'item.update-status', 'uses'=>'APISupplierOrderController@updateOrderItemStatus']);
    Route::post('{status}', ['as'=>'list-data', 'uses'=>'APISupplierOrderController@subOrderList'])->where(['status'=>'[a-z]+']);
    Route::post('details/{sub_order_code}', ['as'=>'details', 'uses'=>'APISupplierOrderController@subOrderDetails']);
    Route::post('update-status', ['as'=>'update-status', 'uses'=>'APISupplierOrderController@updateSubOrderStatus']);
});
Route::group(['prefix'=>'stores', 'as'=>'stores.'], function()
{
    Route::post('/', ['as'=>'list-data', 'uses'=>'APISupplierContoller@stores']);
    Route::post('save', ['as'=>'stores.save', 'uses'=>'APISupplierContoller@saveStores']);
    Route::post('change-status', ['as'=>'change-status', 'uses'=>'APISupplierContoller@changeStoreStatus']);
    Route::post('delete', ['as'=>'delete', 'uses'=>'APISupplierContoller@deleteVerification']);
});
Route::group(['prefix'=>'verification', 'as'=>'verification.'], function()
{
    Route::post('/', ['as'=>'list-data', 'uses'=>'APISupplierContoller@verification']);
    Route::post('save', ['as'=>'save', 'uses'=>'APISupplierContoller@uploadVerificationDocument']);
    Route::post('delete', ['as'=>'delete', 'uses'=>'APISupplierContoller@deleteVerification']);
});
Route::group(['prefix'=>'reports', 'as'=>'reports'], function()
{
    Route::post('payments', ['as'=>'payments-data', 'uses'=>'APISupplierReportsController@payments']);
    Route::post('transactions', ['as'=>'transactions-data', 'uses'=>'APISupplierReportsController@transaction_log']);
});
Route::group(['prefix'=>'offer-cashback', 'as'=>'offer-cashback.'], function()
    {
        Route::post('customer/search', ['as'=>'customer.search', 'uses'=>'APICashbackController@searchCustomer']);
        Route::post('get-bill-amount', ['as'=>'get-bill-amount', 'uses'=>'APICashbackController@getBillAmount']);
        Route::post('rating', ['as'=>'rating', 'uses'=>'APICashbackController@offerCashbackRating']);
    });
Route::group(['prefix'=>'add-money', 'as'=>'add-money.'], function()
{
	Route::post('set-amount', ['as'=>'set-amount', 'middleware'=>'validate', 'uses'=>'AddMoneyController@setAmount']);
	Route::post('payment-info', ['as'=>'payment-info', 'middleware'=>'validate', 'uses'=>'AddMoneyController@paymentInfo']);
});
Route::group(['prefix'=>'catalog', 'as'=>'catalog.'], function()
{
    Route::group(['prefix'=>'categories', 'as'=>'categories.'], function()
    {
        Route::post('/', ['as'=>'list-data', 'uses'=>'APISupplierCatalogController@productCategories']);
        Route::post('add-category', ['as'=>'add', 'uses'=>'APISupplierCatalogController@addCategories']);
        Route::post('change-status', ['as'=>'change-status', 'uses'=>'APISupplierCatalogController@changeCategoryStaus']);
        Route::post('delete', ['as'=>'delete', 'uses'=>'APISupplierCatalogController@deleteCategory']);
        Route::post('available-categories', ['as'=>'available-categories', 'uses'=>'APISupplierCatalogController@availableCategory']);
    });
    Route::group(['prefix'=>'brands', 'as'=>'brands.'], function()
    {
        Route::post('/', ['as'=>'list', 'uses'=>'APISupplierCatalogController@brandList']);
        Route::post('update-brand', ['as'=>'update', 'uses'=>'APISupplierCatalogController@updateBrand']);
        Route::post('new', ['as'=>'new', 'uses'=>'APISupplierCatalogController@newBrand']);
        Route::post('change-status', ['as'=>'change-status', 'uses'=>'APISupplierCatalogController@changeBrandStatus']);
        Route::post('delete', ['as'=>'delete', 'uses'=>'APISupplierCatalogController@deleteBrand']);
        Route::post('check-brand', ['as'=>'check-brand', 'uses'=>'APISupplierCatalogController@checkBrand']);
    }); 
});
Route::group(['prefix'=>'products', 'as'=>'products.'], function()
{
    Route::any('/', ['as'=>'list', 'uses'=>'APISupplierProductController@productList']);
	Route::post('addables', ['as'=>'addables', 'uses'=>'APISupplierProductController@addableProductsList']);
	Route::post('save-product', ['as'=>'save', 'uses'=>'APISupplierProductController@saveProduct']);
	Route::post('configure', ['as'=>'configure', 'uses'=>'APISupplierProductController@configureProduct']);
	Route::post('properties-values-checked', ['as'=>'properties-values-checked', 'uses'=>'APISupplierProductController@productPropertiesValuesChecked']);
	Route::post('properties-for-checktree', ['as'=>'properties-for-checktree', 'uses'=>'APISupplierProductController@productPropertiesForChecktree']);	
	Route::post('update-stock', ['as'=>'update-stock', 'uses'=>'APISupplierProductController@updateStock']);	
	
	Route::group(['prefix'=>'combinations', 'as'=>'combinations.'], function()
    {
		Route::post('/', ['as'=>'list-data', 'uses'=>'APISupplierProductController@productCombinationsList']);		
        Route::post('select-list', 'APISupplierProductController@combination_list_for_com');
        Route::post('properties-list', 'APISupplierProductController@properties_list_for_com');
        Route::post('values-list', 'APISupplierProductController@values_list_for_com');
        Route::post('list', 'APISupplierProductController@combinationsList');
        Route::post('details', 'APISupplierProductController@get_combination_details');
        Route::post('save', 'APISupplierProductController@save_products_combinations');        
        Route::post('change-status', ['as'=>'api.v1.supplier.products.combinations.change-status', 'uses'=>'APISupplierProductController@changeProductCombinationStatus']);
        Route::post('delete', ['as'=>'api.v1.supplier.products.combinations.delete', 'uses'=>'APISupplierProductController@deleteProductCombination']);
    });	
    Route::post('save', 'APISupplierProductController@saveSupplierProduct');
    Route::post('country/save', 'APISupplierProductController@saveProductCountry');
    Route::post('country/delete', 'APISupplierProductController@deleteProductCountry');    
    Route::post('update-sort-order', 'APISupplierProductController@update_sortorder');
    Route::post('change-status', ['as'=>'api.v1.supplier.products.change-status', 'uses'=>'APISupplierProductController@changeProductStatus']);
    Route::post('delete', ['as'=>'api.v1.supplier.products.delete', 'uses'=>'APISupplierProductController@deleteProduct']);    
    Route::post('stock', ['as'=>'api.v1.supplier.products.stock.list-data', 'uses'=>'APISupplierCatalogController@productStockList']);   
    Route::group(['prefix'=>'price', 'as'=>'price.'], function()
    {
		Route::post('deductions', ['as'=>'deductions', 'uses'=>'APISupplierProductController@priceDeductions']);
		Route::post('save', ['as'=>'save', 'uses'=>'APISupplierProductController@saveProcuctPrice']);		
        Route::post('/', ['as'=>'api.v1.supplier.products.price.list', 'uses'=>'APISupplierProductController@supplierProductPriceList']);        
        Route::post('delete', ['as'=>'api.v1.supplier.products.price.delete', 'uses'=>'APISupplierProductController@supplierProductPriceList']);        
    });    
    Route::group(['prefix'=>'image', 'as'=>'image.'], function()
    {
		Route::post('add', ['as'=>'add', 'uses'=>'APISupplierProductController@add_image']);
        Route::post('details', ['as'=>'details', 'uses'=>'APISupplierProductController@image_details']);     
        Route::post('make-default', ['as'=>'make-default', 'uses'=>'APISupplierProductController@default_pro_img']);     		
        Route::post('combination/add', 'APISupplierProductController@save_combination_images');
        Route::post('delete', 'APISupplierProductController@image_remove');
        Route::post('delete-selected', 'APISupplierProductController@delete_selected_image');        
    });
    Route::post('properties/save', 'APISupplierProductController@save_properties');
});
		

