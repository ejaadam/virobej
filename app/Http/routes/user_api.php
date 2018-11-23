<?php
Route::group(['prefix'=>'signup', 'as'=>'signup.'], function()
{ 	
	Route::post('/', ['as'=>'signup', 'middleware'=>['validate'], 'uses'=>'ApiAccountController@Account_Signup']);
	Route::post('verify-mobile', ['as'=>'verify-mobile', 'middleware'=>['validate'], 'uses'=>'ApiAccountController@Verify_Mobile']);
	Route::post('confirm', ['as'=>'confirm', 'middleware'=>['validate'], 'uses'=>'ApiAccountController@confirm_signup']);	
});
Route::post('signup-code-resend', ['as'=>'signup-code-resend', 'uses'=>'ApiAccountController@resendSignUpCode']);
Route::post('countries',['as'=>'countries', 'uses'=>'ApiAccountController@Country_list']);
Route::post('login', ['as'=>'login', 'middleware'=>['validate', 'setLocation:current'], 'uses'=>'ApiUserAuthcontroller@postLoginCheck']);

Route::post('set-location', ['as'=>'set-location', 'middleware'=>['validate', 'setLocation:browse'], 'uses'=>'ApiUserAuthcontroller@setCountry']);

Route::post('forgot-pwd',['as'=>'forgot_pwd', 'middleware'=>['validate'], 'uses'=>'ApiUserAuthcontroller@forgot_password']);
Route::post('reset-pwd', ['as'=>'reset_pwd', 'middleware'=>['validate'], 'uses'=>'ApiUserAuthcontroller@reset_pwd']);
//Route::post('country-update',['as'=>'country-update', 'middleware'=>['validate'], 'uses'=>'ApiAccountController@Country_update']);

Route::post('dashboard', ['as'=>'dashboard', 'uses'=>'ApiAccountController@dashboard']);
Route::post('dashboard/search', ['as'=>'dashboard.search', 'middleware'=>['validate', 'setLocation:browse'], 'uses'=>'StoreController@dashboard_search']);
//Route::post('send-login-otp',['as'=>'send-login-otp', 'middleware'=>['validate'], 'uses'=>'ApiUserAuthcontroller@send_login_otp']);
//Route::post('login-with-otp', ['as'=>'login-with-otp', 'middleware'=>['validate'], 'uses'=>'ApiUserAuthcontroller@login_with_otp']);
Route::group(['prefix'=>'store', 'middleware'=>'setLocation:browse', 'as'=>'store.'], function()
{
	Route::post('search/{category?}', ['as'=>'search', 'uses'=>'StoreController@list_all_store']);
	Route::post('like/{store_code}', ['as'=>'like', 'middleware'=>'auth', 'uses'=>'StoreController@likeStore']);
	Route::post('details', ['as'=>'details', 'middleware'=>'validate', 'uses'=>'StoreController@store_details']);
});
Route::group(['prefix'=>'online-stores', 'as'=>'online-stores.'], function()
{
	Route::post('details', ['as'=>'details', 'middleware'=>['auth', 'validate'], 'uses'=>'StoreController@OnlineStoreDetails']);
	Route::post('/{category?}', ['as'=>'list', 'uses'=>'StoreController@OnlineStoreList']);
});
Route::group(['prefix'=>'product', 'as'=>'product.'], function()
{ 
	Route::post('categories/{cname?}', ['as'=>'categories','uses'=>'ProductController@Product_Categories']);		
	Route::post('details', ['as'=>'details', 'uses'=>'ProductController@Product_Details']);
	Route::post('{category?}', ['as'=>'list', 'uses'=>'ProductController@ProductList_By_Category']);	
});	
Route::post('check-login-status',['as'=>'check-login-status',  'middleware'=>'setLocation:browse', 'uses'=>'ApiUserAuthcontroller@checkLoginStatus']);
Route::post('update-notification-token', ['as'=>'update-notification-token', 'middleware'=>'validate', 'uses'=>'ApiAccountController@updateNotificationToken']);

Route::group(['middleware'=>['auth', 'validate']], function()
{	
	Route::post('my-orders/{type?}', ['as'=>'my-orders', 'uses'=>'ApiAccountController@myOrders'])->where(['type'=>'all|paid|cashback']);	
	Route::post('my-orders/{order_code}', ['as'=>'my-orders.details', 'uses'=>'ApiAccountController@myOrderDetails'])->where(['order_code'=>'[0-9]+']);	
	Route::post('order/save-rating', ['as'=>'order.save-rating', 'uses'=>'ApiAccountController@submitRatingsFeedbacks']);
	Route::post('my-messages', ['as'=>'my-messages', 'uses'=>'ApiAccountController@MyMessages']);	
	Route::post('toggle-app-lock', ['as'=>'toggle-app-lock', 'uses'=>'ApiAccountController@toggleAppLock']);	
	Route::group(array('prefix'=>'pay', 'as'=>'pay.'), function()
	{
		Route::post('store/search', ['as'=>'store.search', 'middleware'=>'setLocation:current', 'uses'=>'PayController@getStoreSearch']);
		Route::post('set-bill-amount', ['as'=>'set-bill-amount', 'uses'=>'PayController@setBillAmount']);
		//Route::post('proceed-to-otp', ['as'=>'proceed-to-otp', 'uses'=>'PayController@proceedPayOTP']);
		//Route::post('proceed-to-otp-resend', ['as'=>'proceed-to-otp-resend', 'uses'=>'PayController@proceedPayOTP']);
		Route::post('get-payment-types', ['as'=>'get-payment-types',  'uses'=>'PayController@getPaymentTypes']);		
		Route::post('get-payment-info', ['as'=>'get-payment-info', 'uses'=>'PayController@getPaymentInfo']);				
	});
	Route::group(array('prefix'=>'redeem', 'as'=>'redeem.'), function()
    {
        Route::post('store/search', ['as'=>'store.search', 'uses'=>'RedeemController@getStoreSearch']);		
        Route::post('set-bill-amount', ['as'=>'set-bill-amount', 'uses'=>'RedeemController@getwallets']);		
        Route::post('wallet-validate', ['as'=>'wallet-validate',  'uses'=>'RedeemController@ValidateWallet']);		
        //Route::post('send-otp', ['as'=>'send-otp', 'uses'=>'RedeemController@proceedRedeemOTP']);		
        //Route::post('resend-otp', ['as'=>'resend-otp', 'uses'=>'RedeemController@proceedRedeemOTP']);		
        //Route::post('confirm', ['as'=>'confirm', 'uses'=>'RedeemController@confirmRedeem']);        
    });
	Route::group(['prefix'=>'favourite', 'as'=>'favourite.'], function()
	{		
		Route::post('store/add', ['as'=>'store.add', 'uses'=>'StoreController@add_store_to_favourite']);		
	});
	Route::post('my-wallet/balance', ['as'=>'wallet.balance', 'uses'=>'WalletController@wallet_balance']);	
	Route::post('my-wallet/transactions', ['as'=>'wallet.transactions', 'uses'=>'WalletController@getTranshistory']);
	Route::post('my-wallet/transaction-details/{id}', ['as'=>'wallet.transhistory-details', 'uses'=>'WalletController@transactionDetails']);
	Route::post('change-pwd', ['as'=>'change-pwd', 'uses'=>'ApiUserAuthcontroller@changepwd']);	
	Route::group(['prefix'=>'profile-settings', 'as'=>'profile-settings.'], function()
    { 	
		Route::post('account-info', ['as'=>'account-info', 'uses'=>'ApiUserAuthcontroller@get_account_info']);
	    Route::group(['prefix'=>'profile', 'as'=>'profile.'], function()
		{
			Route::post('image-upload', ['as'=>'image-upload', 'uses'=>'ApiUserAuthcontroller@profile_image_upload']);
			Route::post('update', ['as'=>'update', 'uses'=>'ApiUserAuthcontroller@updateProfile']);
			//Route::post('edit', ['as'=>'edit', 'uses'=>'ApiUserAuthcontroller@edit_profile']);
		});
	    Route::group(['prefix'=>'change-email', 'as'=>'change-email.'], function()
		{
			Route::post('sendotp', ['as'=>'sendotp','uses'=>'ApiUserAuthcontroller@changeEmailNewEmailOTP']);
			Route::post('resend-otp', ['as'=>'resend-otp', 'uses'=>'ApiUserAuthcontroller@changeEmailNewEmailOTPResend']);
			Route::post('verify-otp', ['as'=>'verify-otp','uses'=>'ApiUserAuthcontroller@changeEmailIDConfirm']); 
	    });
	    Route::group(['prefix'=>'change-mobile', 'as'=>'change-mobile.'], function()
	  	{
			Route::post('sendotp', ['as'=>'sendotp','uses'=>'ApiUserAuthcontroller@changeMobileNewMobileOTP']);
			Route::post('resend-otp', ['as'=>'resend-otp', 'uses'=>'ApiUserAuthcontroller@changeMobileNewMobileOTPResend']);
			Route::post('verify-otp', ['as'=>'verify-otp','uses'=>'ApiUserAuthcontroller@changeMobileNoConfirm']);
		});
		Route::group(['prefix'=>'security-pin', 'as'=>'security-pin.'], function()
		{ 
			Route::post('save', ['as'=>'save', 'uses'=>'ApiUserAuthcontroller@saveProfilePin']);
			Route::post('verify', ['as'=>'verify','uses'=>'ApiUserAuthcontroller@verifyProfilePIN']);
		    Route::post('change', ['as'=>'change','uses'=>'ApiUserAuthcontroller@changeProfilePin']);
			Route::post('forgot', ['as'=>'forgot', 'uses'=>'ApiUserAuthcontroller@forgotProfilePin']);
			Route::post('reset-email', ['as'=>'reset-email', 'uses'=>'ApiUserAuthcontroller@forgotProfilePinEmail']);
			Route::post('reset', ['as'=>'reset','uses'=>'ApiUserAuthcontroller@resetProfilePin']);
		});
    }); 
	Route::group(['prefix'=>'cashback', 'as'=>'cashback.'], function()
	{ 
		Route::post('store/search', ['as'=>'store.search','uses'=>'CashbackController@getStoreSearch']);
		Route::post('set-bill-amount', ['as'=>'set-bill-amount','uses'=>'CashbackController@setBillAmount']);
		Route::post('send-otp', ['as'=>'send-otp', 'uses'=>'CashbackController@confirmCashback']);
		Route::post('resend-otp', ['as'=>'resend-otp', 'uses'=>'CashbackController@confirmCashback']);
		Route::post('confirm', ['as'=>'confirm','uses'=>'CashbackController@verifyCashBack']);
	});
	Route::post('payment/info', ['as'=>'payment.info', 'uses'=>'WithdrawalController@paymentTypeInfo']);
	Route::group(['prefix'=>'withdraw-fund', 'as'=>'withdraw-fund.'], function()
	{ 
		//Route::post('get-bank-details', ['as'=>'get-bank-details','uses'=>'WithdrawalController@Get_Bank_Details']);
		//Route::post('confirm', ['as'=>'confirm', 'uses'=>'WithdrawalController@ConfirmWithdraw']);	
		Route::post('save-bank-details', ['as'=>'save-bank-details','uses'=>'WithdrawalController@Save_Bank_Details']);
		Route::post('list-bank-details', ['as'=>'list-bank-details','uses'=>'WithdrawalController@List_Bank_Details']);
		Route::post('delete-bank-details', ['as'=>'delete-bank-details','uses'=>'WithdrawalController@Delete_Bank_Details']);
		Route::post('get-amount', ['as'=>'get-amount','uses'=>'WithdrawalController@Get_Bank_Details']);		
		Route::post('confirm', ['as'=>'confirm', 'uses'=>'WithdrawalController@ConfirmWithdraw']);	
	});	
	Route::post('reviews/list', ['as'=>'reviews', 'uses'=>'ApiAccountController@reviews_and_rating_list']);
	Route::group(['prefix'=>'merchant', 'as'=>'merchant.'], function()
	{ 
		Route::post('stores', ['as'=>'stores','uses'=>'ApiUserAuthcontroller@merchant_stores_list']);	
		Route::post('store/select', ['as'=>'store.select','uses'=>'ApiUserAuthcontroller@select_store']);	
	});
	/* Get Cashback */
	Route::group(['prefix'=>'offer-cashback', 'as'=>'offer-cashback.'], function()
    {
        Route::post('customer/search', ['as'=>'customer.search', 'middleware'=>'validate', 'uses'=>'OfferCashbackController@searchCustomer']);
        Route::post('get-bill-amount', ['as'=>'get-bill-amount', 'middleware'=>'validate', 'uses'=>'OfferCashbackController@getBillAmount']);
        //Route::post('rating', ['as'=>'rating', 'middleware'=>'validate', 'uses'=>'OfferCashbackController@offerCashbackRating']);
    });
}); 

