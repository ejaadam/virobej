<?php

Route::any('sign-up', ['as'=>'sign-up', 'middleware'=>'validate', 'uses'=>'SupplierController@signUp']);
Route::post('verify-mobile', ['as'=>'verify-mobile', 'uses'=>'SupplierController@verifyMobile']);
Route::get('mobile-verification', ['as'=>'sign-up.mobile-verification', 'uses'=>'SupplierController@signUpMobileVerification']);
Route::post('check-verification-mobile', ['as'=>'check-verification-mobile', 'middleware'=>'validate', 'uses'=>'SupplierController@checkMobileVerification']);
Route::get('verify-email', ['as'=>'verify-email','middleware'=>'validate','uses'=>'SupplierController@verify_email']);
Route::post('verify-email', ['as'=>'verify-email', 'uses'=>'SupplierController@sent_mailverification']);
Route::post('check-email-verification', ['as'=>'check-email-verification', 'uses'=>'SupplierController@confirm_email_verification']);
//Route::post('verify-new-email', ['as'=>'verify-new-email','middleware'=>'validate', 'uses'=>'SupplierController@new_Email_verification']);
Route::get('verifyEmailLink/{token}', ['as'=>'verifyEmailLink', 'uses'=>'AccountController@verifyEmailLink'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}');
Route::get('verifyNewEmailLink/{token}', ['as'=>'verifyNewEmailLink', 'uses'=>'AccountController@verifyNewEmailLink'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}');
Route::post('chech-user', ['as'=>'check-user', 'uses'=>'APISupplierContoller@check_user']);
//Route::post('send-verify-link', ['as'=>'send-verify-link', 'uses'=>'AccountController@sendEmailverification']);

Route::any('login', ['as'=>'login', 'uses'=>'SupplierController@login']);
//Route::get('forgot-password', 'SupplierController@forgotPassword');
Route::post('forgot-password', ['as'=>'forgot-password', 'middleware'=>'validate', 'uses'=>'SupplierController@forgotpwd']);
//Route::post('forgot_opt', ['as'=>'forgot_opt', 'middleware'=>'validate', 'uses'=>'SupplierController@forgot_opt']);
//Route::post('resetpwd', ['as'=>'resetpwd', 'middleware'=>'validate', 'uses'=>'SupplierController@resetpwd']);
Route::post('pwdreset-link', ['as'=>'pwdreset-link', 'middleware'=>'validate', 'uses'=>'SupplierController@pwdresetLink']);
Route::get('resetpwd-link/{token}', ['as'=>'resetpwd-link', 'uses'=>'SupplierController@verifyForgotpwdLink'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}');
Route::get('remove-resetpwd-sess/{token}', ['as'=>'remove-resetpwd-sess', 'uses'=>'SupplierController@removeForgotpwdSess'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}');

Route::post('change-reg-mobile',['as'=>'change-reg-mobile','middleware'=>'validate','uses'=>'SupplierController@change_reg_mobile']);
Route::post('verify-profile-pin', ['as'=>'verify-profile-pin', 'uses'=>'AccountController@verifyProfilePIN']);
Route::post('get_tags', ['as'=>'get_tags', 'uses'=>'AccountController@get_tags']);
Route::get('verifyMobileLink/{token}', ['as'=>'verifyMobileLink', 'uses'=>'AccountController@verifyMobileLink'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}'); 

Route::group(['middleware'=>['authweb']], function()
{	
	/* Route::get('dashboard', ['as'=>'dashboard', 'uses'=>'SupplierController@dashboard']);	 */
	Route::any('dashboard', ['as'=>'dashboard', 'uses'=>'WidgetController@dashboard']);
	Route::any('account-settings', ['as'=>'account-settings', 'uses'=>'SupplierController@AccountSettings']);
	Route::post('get-notifications', [ 'as'=>'get-notifications', 'uses'=>'AccountController@getNotifications']);
	Route::post('notification-read', ['as'=>'notification-read', 'middleware'=>'validate', 'uses'=>'AccountController@markNotificationRead']);
	Route::post('update-notification-token', ['as'=>'update-notification-token', 'uses'=>'AccountController@updateNotificationToken']);
	
	Route::group(['prefix'=>'account-settings', 'as'=>'account-settings.'], function()
	{	
		/* Route::any('general-details', ['as'=>'general-details', 'uses'=>'AccountController@General_Details']);
		Route::any('business-details', ['as'=>'business-details', 'uses'=>'AccountController@Business_Details']); 
		Route::any('general-details', ['as'=>'general-details', 'uses'=>'AccountController@Seller_Information']);
		Route::any('business-details', ['as'=>'business-details', 'uses'=>'AccountController@Seller_Information']);*/
		
		Route::post('general-details', ['as'=>'general-details', 'uses'=>'AccountController@General_Details']);
		Route::any('profile_info', ['as'=>'profile_info', 'uses'=>'AccountController@seller_general_info']);
		Route::post('business-details', ['as'=>'business-details','middleware'=>'validate', 'uses'=>'AccountController@Business_Details']);
		/* Route::any('seller-information', ['as'=>'seller-information', 'uses'=>'AccountController@Seller_Information']); */
		Route::post('get_bank_details', ['as'=>'bank-info', 'uses'=>'AccountController@get_bank_details']);
		Route::post('bank-details', ['as'=>'bank-details', 'uses'=>'AccountController@Bank_Details']);
		Route::post('get-ifsc-details', ['as'=>'get-ifsc-details', 'uses'=>'AccountController@Get_Ifsc_Bank_Details']);
		Route::post('change-password', ['as'=>'change-password','uses'=>'AccountController@change_password']);		
		Route::post('update-password', ['as'=>'update-password', 'middleware'=>'validate', 'uses'=>'AccountController@update_password']);		
		Route::post('manage-cashback', ['as'=>'manage-cashback', 'uses'=>'AccountController@Manage_cashback']);	
		Route::post('update-cashback', ['as'=>'update-cashback', 'uses'=>'AccountController@update_cashback']);	
		Route::post('commision', ['as'=>'commision', 'uses'=>'AccountController@Commisions']);    
		Route::post('add_profit_sharing', ['as'=>'add_profit_sharing', 'uses'=>'AccountController@add_profit_sharing']);
		Route::post('tax-information', ['as'=>'tax-information','middleware'=>'validate', 'uses'=>'AccountController@tax_information']);
		Route::post('tax-info', ['as'=>'tax-info','uses'=>'AccountController@get_tax_info']);
		Route::any('gst-information', ['as'=>'gst-information','middleware'=>'validate','uses'=>'AccountController@gst_information']);
		Route::any('proof-details', ['as'=>'proof-details', 'uses'=>'AccountController@update_Proof_details']);
		Route::any('shipping-info', ['as'=>'shipping-info', 'uses'=>'AccountController@shipping_information']);		
		Route::post('update_pickup_address', ['as'=>'update_pickup_address','middleware'=>'validate','uses'=>'AccountController@update_pickup_address']);	
		Route::post('pickup-address', ['as'=>'pickup-address', 'uses'=>'AccountController@pickup_address']);	
		Route::post('return-address', ['as'=>'return-address', 'uses'=>'AccountController@get_shipping_address']);
		Route::post('update-return-address', ['as'=>'update-return-address', 'uses'=>'AccountController@update_return_address']);
		Route::post('upload-store-images', ['as'=>'upload-store-images','middleware'=>'validate','uses'=>'AccountController@add_store_images']);
		Route::post('change-security-pin', ['as'=>'change-pin','middleware'=>'validate','uses'=>'AccountController@change_security_pin']);
		Route::get('change-security-pin', ['as'=>'change-pin','uses'=>'AccountController@change_security_pin']);
		Route::get('/','AccountController@account_settings');
	});	

	Route::group(['prefix'=>'security-pin', 'as'=>'security-pin.'], function()
	{
		Route::post('forgot', ['as'=>'forgot','uses'=>'AccountController@forgotProfilePin']);
		Route::post('resend-forgot-otp', ['as'=>'resend-forgot-otp', 'uses'=>'AccountController@forgotProfilePin']);
		Route::post('reset', ['as'=>'reset', 'middleware'=>'validate', 'uses'=>'AccountController@resetProfilePin']);
		Route::post('save', ['as'=>'save', 'middleware'=>'validate', 'uses'=>'AccountController@saveProfilePin']);
	});
	
	Route::group(['prefix'=>'profile-settings', 'as'=>'profile-settings.'], function()
	{
	    Route::group(['prefix'=>'profile-pin', 'as'=>'profile-pin.'], function()
        {
            Route::post('forgot', ['as'=>'forgot', 'uses'=>'AccountController@forgotProfilePin']);
            Route::post('resend-forgot-otp', ['as'=>'resend-forgot-otp', 'uses'=>'AccountController@forgotProfilePin']); 
            Route::post('reset', ['as'=>'reset','uses'=>'AccountController@resetProfilePin']);
        });
	    Route::group(['prefix'=>'change-email', 'as'=>'change-email.'], function()
        {
            Route::post('new-email-otp', ['as'=>'new-email-otp', 'middleware'=>'validate', 'uses'=>'AccountController@changeEmailNewEmailOTP']);
            Route::post('new-email-otp-resend', ['as'=>'new-mob-email-resend', 'uses'=>'AccountController@changeEmailNewEmailOTPResend']); 
            Route::post('confirm', ['as'=>'confirm', 'middleware'=>'validate', 'uses'=>'AccountController@changeEmailIDConfirm']);
        });		
		Route::group(['prefix'=>'change-mobile', 'as'=>'change-mobile.'], function()
        {
		    Route::post('new-mob', ['as'=>'new-mob', 'uses'=>'AccountController@ChangeMobile']);
		    Route::post('new-mob-confirm', ['as'=>'new-mob-confirm', 'middleware'=>'validate', 'uses'=>'AccountController@ChangeMobileConfirm']);
		    Route::post('new-mob-otp', ['as'=>'new-mob-otp', 'middleware'=>'validate','uses'=>'AccountController@ChangeMobileOTP']);
		    Route::post('new-mob-otp-resend', ['as'=>'new-mob-otp-resend', 'middleware'=>'validate','uses'=>'AccountController@ChangeMobileOTPResend']);
			
        });
		
	});
	
	Route::group(['prefix'=>'outlet', 'as'=>'outlet.'], function()
    {
        Route::any('list', ['as'=>'list', 'uses'=>'OutletController@OutletList']);
		Route::post('details/{store_code}', ['as'=>'details', 'uses'=>'OutletController@store_details']);
        Route::post('update-web/{store_code}', ['as'=>'store-update-web','middleware'=>'validate', 'uses'=>'OutletController@store_update_web']);		
		Route::post('update-status/{status}/{code}', ['as'=>'update-status', 'uses'=>'OutletController@update_store_status']);		
		Route::post('view-details/{store_code}', ['as'=>'view-details', 'uses'=>'OutletController@store_view_details']);			
		Route::post('save-web', ['as'=>'store-save-web','middleware'=>'validate','uses'=>'OutletController@store_create_web']);	
		Route::get('images/{store_code}', ['as'=>'images', 'uses'=>'OutletController@store_images']);		
		Route::group(['prefix'=>'photos', 'as'=>'photos.'], function()
        {
			Route::post('list', ['as'=>'list', 'uses'=>'OutletController@list_photos']);
			Route::post('update-status/{status}/{id}', ['as'=>'update-status', 'uses'=>'OutletController@updateStoreImageStatus']);
			Route::post('delete/{id}', ['as'=>'delete', 'uses'=>'OutletController@deleteStoreImage']);			
            Route::post('upload', ['as'=>'upload', 'middleware'=>'validate', 'uses'=>'OutletController@take_photo']);                        
			
            Route::post('remove/{id}', ['as'=>'remove', 'uses'=>'AccountController@removeMerchantPhotos']);            
            Route::post('take', ['as'=>'take_photos', 'uses'=>'AccountController@merchantImageUploadsSettings']);
        });
    });
	
	//Route::get('stores', ['as'=>'stores', 'uses'=>'SupplierController@stores_management']);
	
	/* Route::get('profile-setup', ['as'=>'profile-setup', 'uses'=>'SupplierController@profileSetup']);			
	Route::get('bussiness-info', ['as'=>'bussiness-info', 'uses'=>'SupplierController@BussinessInfo']);
	Route::get('account-info', ['as'=>'account-info', 'uses'=>'SupplierController@AccountInfo']);	
	Route::get('store-info', ['as'=>'store-info', 'uses'=>'SupplierController@storeInfo']); 	
	Route::get('store-banking', ['as'=>'store-banking', 'uses'=>'SupplierController@storeBanking']);	*/
	//Route::get('kyc-update', ['as'=>'kyc-verification', 'uses'=>'SupplierController@kycUpdate']);
	//Route::get('verification', ['as'=>'verification', 'uses'=>'SupplierController@verification']);
	Route::get('logout', ['as'=>'logout', 'uses'=>'SupplierController@logout']);		
	Route::get('change-email', ['as'=>'change-email', 'uses'=>'SupplierController@change_email']);					
	
	/* Route::group(['prefix'=>'order', 'as'=>'order.'], function()
	{		
		Route::get('{status}', ['as'=>'list', 'uses'=>'ProductOrderController@subOrderList'])->where(['status'=>'[a-z]+']);
		Route::get('details/{code}', ['as'=>'order.details', 'uses'=>'ProductOrderController@subOrderDetails']);
	});	 */	
	Route::group(['prefix'=>'reports', 'as'=>'reports.'], function()
	{		
		Route::group(['prefix'=>'instore', 'as'=>'instore.'], function()
		{		
			Route::any('orders', ['as'=>'orders', 'uses'=>'SupplierReportController@OrdersList']);				
			Route::any('orders/details/{order_code}', ['as'=>'orders.details', 'uses'=>'SupplierReportController@orderDetail']);	
			Route::any('transactions', ['as'=>'transactions', 'uses'=>'SupplierReportController@TransactionList']);						
			Route::any('transaction/details/{order_code}', ['as'=>'transaction.details', 'uses'=>'SupplierReportController@TransactionDetails']);					
		});		
	});	
	//Route::get('wallet-balance', ['as'=>'wallet-balance', 'uses'=>'SupplierReportController@wallet_balance']);	
	//Route::get('bank-accounts', ['as'=>'bank-accounts', 'uses'=>'SupplierReportController@bank_accounts']);	
	//Route::get('offer-cashback', ['as'=>'offer-cashback', 'uses'=>'CashbackController@offer_cashback']);		
	Route::group(['prefix'=>'wallet', 'as'=>'wallet.'], function()
    {
		/* Route::get('add-money', ['as'=>'add-money', 'uses'=>'WalletController@addMoney']);
        Route::get('history', ['as'=>'transHistory', 'uses'=>'WalletController@wallet_history']); */
		Route::any('balance', ['as'=>'balance', 'uses'=>'SupplierReportController@wallet_balance']);        
        /* Route::get('history/{id}', ['as'=>'transHistory-details', 'uses'=>'WithdrawalController@wallet_history_details']);         */
    });	
	/* Route::group(['prefix'=>'catalog', 'as'=>'catalog.'], function()
	{
		Route::get('categories', ['as'=>'categories.list', 'uses'=>'ProductController@productCategories']);
		Route::get('brands', ['as'=>'brands.list', 'uses'=>'ProductController@brand_list']);
	});	  */
	
	
	
	  Route::group(['prefix'=>'manage_users', 'as'=>'manage_users.'], function()
	  {
		  Route::any('user_list', [ 'as'=>'user_list', 'uses'=>'ManageUsersController@manage_user_list']); 
		  Route::match(['get','post'],'login_block/{uname}/{status}',['as'=>'login_block','uses'=>'ManageUsersController@login_block']);
		  Route::post('edit_user/{id}',['as'=>'edit_user','uses'=>'ManageUsersController@edit_user_acc']);
		  Route::post('reset-password',['as'=>'reset-password','middleware'=>'validate','uses'=>'ManageUsersController@reset_password']);
		  Route::post('get_stores/{id}',['as'=>'get_stores','middleware'=>'validate','uses'=>'ManageUsersController@get_stores']);
		  Route::post('save_allocation/{id}',['as'=>'save_allocation','middleware'=>'validate','uses'=>'ManageUsersController@save_allocation']);
		  Route::get('add', ['as'=>'add_user','uses'=>'ManageUsersController@add_user']);
		  Route::post('save_user',['as'=>'save_user','middleware'=>'validate','uses'=>'ManageUsersController@save_user']);
		  Route::post('update_user_details',['as'=>'update_user_details','uses'=>'ManageUsersController@save_user']);
		  Route::post('update-status/{status}/{id}', ['as'=>'update-status', 'uses'=>'ManageUsersController@updatManageUserStatus']);

	  });
	  
	Route::group(['prefix'=>'products', 'as'=>'products.'], function()
	{
		Route::any('/', [ 'as'=>'list', 'uses'=>'ProductController@supplier_products']);
		Route::post('check-product', [ 'as'=>'check-product', 'uses'=>'ProductController@checkProduct']);
		Route::post('save_payment_types', ['as'=>'save_payment_types', 'uses'=>'ProductController@save_payment_types']);	
		Route::post('payment_list', ['as'=>'payment_list', 'uses'=>'ProductController@product_payment_list']);			
		Route::post('delete_payment', ['as'=>'delete_payment', 'uses'=>'ProductController@delete_payment']);				
		Route::group(['prefix'=>'package_details'], function()
		{
			Route::post('save', 'ProductController@save_package_info');
		});
		Route::group(['prefix'=>'zones', 'as'=>'zones'], function()
		{
			Route::get('/', 'ProductController@supplier_products_zones');
			Route::post('/', 'ProductController@supplier_products_zones');
			Route::post('save', 'ProductController@save_supplier_product_zone');
			Route::post('delete', 'ProductController@delete_supplier_product_zone');
		});
		Route::group(['prefix'=>'stock'], function()
		{
			Route::get('/', ['as'=>'supplier.products.stock.list', 'uses'=>'ProductController@productStockList']);
			Route::post('/', ['as'=>'supplier.products.stock.list-data', 'uses'=>'ProductController@productStockList']);
			Route::get('log', ['as'=>'supplier.products.stock.log', 'uses'=>'ProductController@Product_stock_log_Report']);
			Route::post('log', ['as'=>'supplier.products.stock.log-data', 'uses'=>'ProductController@Product_stock_log_Report']);
		});		
   
		Route::get('add/{product_code?}/{product_cmb_code?}', [ 'as'=>'add', 'uses'=>'ProductController@addProduct']);		
		Route::any('{supplier_product_code}', ['as'=>'config', 'uses'=>'ProductController@configureProduct']); 			
		Route::get('edit/{supplier_product_code?}', ['as'=>'supplier.products.edit', 'uses'=>'ProductController@editProducts']); 
		Route::get('order_list/{id}', 'ProductController@order_list');
		Route::get('product_items', 'ProductController@product_items');
		Route::post('add_new_products', 'ProductController@add_new_products');
		Route::post('add_stock/{id}', 'ProductController@add_stock');
		Route::post('cover_images', 'ProductController@cover_images');
		Route::post('delete/{id}', 'ProductController@delete_product');		
		Route::post('delete_selected_image', 'ProductController@delete_selected_image');
		Route::post('edit/{id}', 'ProductController@edit_product');
		Route::post('edit_stock/{id}', 'ProductController@edit_stock');
		Route::post('get_combinations', 'ProductController@get_combinations');
		Route::post('get_commisions', 'ProductController@get_commisions');
		Route::post('order_list/{id}', 'ProductController@order_list');		
		Route::post('product_items', 'ProductController@product_items');
		Route::post('product_status/{id}', 'ProductController@product_status');
		Route::post('save_association', 'ProductController@save_association');
		Route::post('sku_valid', 'ProductController@check_sku_valid');
		Route::post('tempimg_upload', 'ProductController@tempimg_upload');
		Route::post('update_product', 'ProductController@update_product');
		Route::post('update_stock', 'ProductController@update_stock');	
	});
	
	/* Route::group(['prefix'=>'manage_users','as'=>'manage_users.'],function(){
		Route::get('add', ['as'=>'add_user','uses'=>'ManageUsersController@add_user']);
		Route::post('save_user',['as'=>'save_user','middleware'=>'validate','uses'=>'ManageUsersController@save_user']);
	}); */

	});









