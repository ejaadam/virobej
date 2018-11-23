<?php
Route::get('login', ['as'=>'login', 'uses'=>'AdminController@login']);
Route::post('forgot-password', ['as'=>'forgot-password',  'uses'=>'AdminController@forgotPassword']);
Route::post('check-account', ['as'=>'check-account',  'uses'=>'AdminController@checkAccount']);
Route::post('login', ['as'=>'login',  'uses'=>'AdminController@loginCheck']);
Route::match(['get','post'],'logout', ['as'=>'login',  'uses'=>'AdminController@logout']);
Route::post('check-login', ['as'=>'check-login',  'uses'=>'AdminController@loginCheck']);
Route::get('sample', ['as'=>'sample', 'uses'=>'FranchiseeController@sample']);



Route::group(['namespace'=>'Admin'], function()
 {  
	Route::get('validate/lang/{langkey}', ['uses'=>'LangController@langLoad']);	
	Route::group(['middleware'=>['adauth']], function()
	{
		Route::get('dashboard', ['as'=>'dashboard', 'uses'=>'AdminDashboard_Controller@dashboard']);
		
		Route::group(['prefix'=>'catalog', 'as'=>'catalog.'], function()
		{
			Route::group(['prefix'=>'products', 'as'=>'products.'], function()
			{
				Route::group(['prefix'=>'seller', 'as'=>'seller.'], function()
				{
					//Route::post('/', 'AdminProductController@productSuppliers');
					Route::group(['prefix'=>'brands'], function()
					{						
						Route::any('/', ['as'=>'list', 'uses'=>'SuppliersController@brandList']);
						Route::post('save', ['as'=>'save', 'uses'=>'SuppliersController@saveBrand']);
						Route::post('update-status/{id}', ['as'=>'update-status', 'uses'=>'SuppliersController@updatebrandStatus']);			
						Route::post('update-verification', 'SuppliersController@updateBrandVerification');
						Route::post('details', 'SuppliersController@brand_details');						
						Route::post('delete/{brand_id}', 'SuppliersController@deleteBrand');
					});
				});
			});
		});
		Route::group(['prefix'=>'seller', 'as'=>'seller.'], function()
		{		
			Route::any('tax-info', ['as'=>'tax-info', 'uses'=>'SuppliersController@get_tax_information']);		
			Route::any('edit-profile', ['as'=>'edit-profile', 'uses'=>'SuppliersController@edit_profile']);	
			Route::any('update-proof-status', ['as'=>'update-proof-status', 'uses'=>'SuppliersController@update_status']);	
			Route::any('update_premium', ['as'=>'update_premium', 'uses'=>'SuppliersController@update_premium']);	
			
			Route::post('admin-info', ['as'=>'admin-info', 'uses'=>'SuppliersController@admin_details']);
			Route::post('reset_pwd/{account_id}', ['as'=>'reset-pwd', 'uses'=>'SuppliersController@suppliers_reset_pwd']);
			Route::post('meta-info', ['as'=>'meta-info', 'uses'=>'SuppliersController@meta_info']);
			Route::post('meta-info/save', ['as'=>'meta-info.save', 'uses'=>'SuppliersController@save_meta_info']);		
			Route::post('preferences/save', ['as'=>'preferences.save', 'uses'=>'SuppliersController@supplierSavePerferences']);
			Route::get('preferences/{uname}', 'SuppliersController@supplierPerferences');
			Route::post('delete_doc', ['as'=>'delete_doc', 'uses'=>'SuppliersController@delete_doc']);
			Route::post('update', ['as'=>'update', 'uses'=>'SuppliersController@update_suppliers']);
			Route::get('edit/{uname}', ['as'=>'edit', 'uses'=>'SuppliersController@edit_suppliers']);
			Route::post('change_status', ['as'=>'change-status', 'uses'=>'SuppliersController@change_status']);
			Route::post('verify-step', ['as'=>'verify-step', 'uses'=>'SuppliersController@verifyStep']);			
			Route::get('details/{code}', ['as'=>'details', 'uses'=>'SuppliersController@get_suppliers_details']);				
			Route::any('verification/{uname?}', ['as'=>'verification', 'uses'=>'SuppliersController@verification']);		
			Route::post('doc-list', ['as'=>'doc-list', 'uses'=>'SuppliersController@doc_list']);		
			Route::any('/', ['as'=>'list', 'uses'=>'SuppliersController@suppliers_list']);
			
			Route::post('update-block/{block}/{mrcode}', ['as'=>'update-block', 'uses'=>'SuppliersController@updateRetailerBlock']);
			Route::post('change-status/{status}/{mrcode}', ['as'=>'update-status', 'uses'=>'SuppliersController@updateRetailerStatus']);
			Route::post('change-verify-status/{is_verified}/{mrcode}', ['as'=>'verify-status', 'uses'=>'SuppliersController@verify_status']);
			Route::group(['prefix'=>'stores', 'as'=>'stores.'], function()
			{
				Route::match(['get', 'post'], '/{mrcode?}', ['as'=>'list', 'uses'=>'SuppliersController@store_list']);				
			});
			Route::group(['prefix'=>'commission', 'as'=>'commission.'], function()
			{
				Route::post('details/{for}/{id}', ['as'=>'details', 'uses'=>'SuppliersController@profitSharingDetails'])->where(['for'=>'edit|view']);
				Route::post('update-status/{id}/{stauts}', ['as'=>'update-status', 'uses'=>'SuppliersController@profitSharingStatusUpdate']);
				Route::post('delete/{id}', ['as'=>'delete', 'uses'=>'SuppliersController@profitSharingDelete']);
				Route::post('save/{id}', ['as'=>'save', 'uses'=>'SuppliersController@profitSharingSave']);
				Route::match(['get', 'post'], '/', ['as'=>'list', 'uses'=>'SuppliersController@profitSharingList']);
			});		
			Route::any('{status?}', ['as'=>'list', 'uses'=>'SuppliersController@suppliers_list']);	
        
			Route::group(['prefix'=>'in_store', 'as'=>'in_store.'], function()
			{
				Route::any('category/list', ['as'=>'category-list', 'uses'=>'CategoryManagementController@getInStoreCategory_list']);
				Route::match(['get', 'post'], 'getcategory', ['as'=>'getcategory', 'uses'=>'CategoryManagementController@getInStoreCategories']);
				Route::match(['get', 'post'], 'check-slug', ['as'=>'check-slug', 'uses'=>'CategoryManagementController@check_InStoreCategory_slug']);
				Route::match(['get', 'post'], 'update', ['as'=>'update','middleware'=>['validate'],'uses'=>'CategoryManagementController@saveInStoreCategory']);
				Route::match(['get', 'post'], 'edit', ['as'=>'edit', 'uses'=>'CategoryManagementController@editInStoreCategory']);
				Route::match(['get', 'post'], 'change-status', ['as'=>'status', 'uses'=>'CategoryManagementController@change_InStoreCategory_status']);
			});			
		});		
		Route::group(['prefix'=>'affiliate', 'as'=>'aff.'], function()
		{ 
			Route::get('create', ['as'=>'root-account.create','middleware'=>['validate'],'uses'=>'AffiliateController@create_root_user']);
			Route::post('save', ['as'=>'root-account.save', 'uses'=>'AffiliateController@save_root_user']);
			Route::match(['GET','POST'],'view', ['as'=>'root-account.view', 'uses'=>'AffiliateController@view_root_user']);
			Route::match(['get','post'],'check-uname', ['as'=>'root-account.check-uname', 'uses'=>'AffiliateController@checkUnameAvaliable']);
			Route::match(['get','post'],'user_email_check', ['as'=>'root-account.user_email_check', 'uses'=>'AffiliateController@checkEmailAvaliable']);
			Route::match(['get','post'],'user_mobile_check', ['as'=>'root-account.user_email_check', 'uses'=>'AffiliateController@CheckMobileAvailable']);
		});
		Route::group(['prefix'=>'account'], function() {
		  Route::match(['get','post'],'view/{uname}', ['as'=>'account.view-details', 'uses'=>'AffiliateController@view_details']);
		  Route::match(['get','post'],'change-password', ['as'=>'account.change-password', 'uses'=>'AffiliateController@change_password']);
		  Route::match(['get','post'],'reset-pin', ['as'=>'account.reset-pin', 'uses'=>'AffiliateController@reset_security_pin']);
		  Route::post('update_pin', ['as'=>'account.updatepin','uses'=>'AffiliateController@updatepin']);	
		  Route::post('update_pwd', ['as'=>'account.updatepwd','uses'=>'AffiliateController@updatepwd']);	
		  Route::match(['get','post'],'update_details', ['as'=>'account.update_details', 'uses'=>'AffiliateController@update_details']);	
		  Route::match(['get','post'],'edit/{uname}', ['as'=>'account.edit-details', 'uses'=>'AffiliateController@edit_detail']);
		  Route::match(['get','post'],'email', ['as'=>'account.email','uses'=>'AffiliateController@updateding_email']);
		  Route::match(['get','post'],'update_mobile', ['as'=>'account.update_mobile','uses'=>'AffiliateController@update_mobile']);
		  Route::match(['get', 'post'], 'block_status/{uname}/{status}', ['as'=>'account.block_status', 'uses'=>'AffiliateController@user_block_status']);
		  Route::match(['get','post'],'active_status', ['as'=>'account.active_status', 'uses'=>'AdminController@active_status']);
		});
		
		Route::post('country-list/{status?}', ['as'=>'country-list', 'uses'=>'AffiliateController@country_list']);
		
		Route::group(['prefix'=>'franchisee','as'=>'franchisee.'], function() {
			
	
      Route::match(['get','post'],'change_block_users/{uname}/{status}',['as'=>'change_block_users','uses'=>'FranchiseeController@change_block_users']);
      Route::match(['get','post'],'change_password',['as'=>'change_password','uses'=>'FranchiseeController@change_password']);
      Route::match(['get','post'],'login_block/{uname}/{status}',['as'=>'login_block','uses'=>'FranchiseeController@login_block']);
      Route::match(['get','post'],'change_pin',['as'=>'change_pin','uses'=>'FranchiseeController@change_pin']);
      Route::match(['get','post'],'edit_profile',['as'=>'edit_profile','uses'=>'FranchiseeController@franchisee_edit_profile']);
      Route::match(['get','post'],'update_profile',['as'=>'update_profile','uses'=>'FranchiseeController@update_franchisee_profile']);
      Route::match(['get','post'],'franchisee_check_mobile',['as'=>'franchisee_check_mobile','uses'=>'FranchiseeController@franchisee_check_mobile']);
      Route::match(['get','post'],'franchisee_check_email',['as'=>'franchisee_check_email','uses'=>'FranchiseeController@franchisee_check_email']);
	
			Route::get('create',['as'=>'create','uses'=>'FranchiseeController@create_franchise']);
			Route::post('save', ['as'=>'savenew','uses'=>'FranchiseeController@save_franchise']);
		   Route::match(['get','post'],'manage', ['as'=>'manage','uses'=>'FranchiseeController@manage_franchisee']);
			
			Route::post('states', ['as'=>'states','uses'=>'FranchiseeController@get_states']);
			Route::post('districts', ['as'=>'districts','uses'=>'FranchiseeController@get_districts']);
			Route::post('cities', ['as'=>'cities','uses'=>'FranchiseeController@get_cities']);
			Route::post('zipcode', ['as'=>'zipcode','uses'=>'FranchiseeController@get_zipcode']);
			
			Route::get('/', ['as'=>'list','uses'=>'FranchiseeController@view_franchisee']);
			Route::post('list', ['as'=>'list.json','uses'=>'FranchiseeController@view_franchisee']);

			Route::get('kyc', ['as'=>'kyc','uses'=>'FranchiseeController@kyc']);
			Route::post('kyc', ['as'=>'kyc.json','uses'=>'FranchiseeController@kyc']);			
			
			Route::post('kyc/change-status/{uv_id}', ['as'=>'','uses'=>'FranchiseeController@kyc_change_status']);
			
			Route::post('kyc/delete/{uv_id}', ['as'=>'','uses'=>'FranchiseeController@kyc_delete']);
			Route::post('check-email', ['as'=>'validate.email','uses'=>'FranchiseeController@check_email']);
			Route::post('validate/username', ['as'=>'validate.username','uses'=>'FranchiseeController@check_username']);
			
			Route::get('add', ['as'=>'add','uses'=>'FranchiseeController@add_franchisee']);
			Route::post('save', ['as'=>'save','uses'=>'FranchiseeController@save_franchisee']);
			
	        Route::group(['prefix'=>'acc','as'=>'acc.'], function() {
				
			Route::post('state', ['as'=>'state','uses'=>'FranchiseeController@get_franchisee_state_phonecode']);
			Route::post('district', ['as'=>'district','uses'=>'FranchiseeController@get_franchisee_district']);
			Route::post('city', ['as'=>'city','uses'=>'FranchiseeController@franchisee_get_cities']);
			Route::post('check_franchise_access', ['as'=>'check_franchise_access','uses'=>'FranchiseeController@check_franchise_access']);
			Route::post('check_franchise_mapped', ['as'=>'check_franchise_mapped','uses'=>'FranchiseeController@check_franchise_mapped']);
	      });
			
			Route::get('access-control/edit', ['as'=>'access.edit','uses'=>'FranchiseeController@edit_franchisee_access']);
			Route::post('access-control/update', ['as'=>'access.update','uses'=>'FranchiseeController@save_franchisee_access']);			
			Route::post('access', ['as'=>'access.save','uses'=>'FranchiseeController@add_newaccess']);
			
			
			Route::get('edit', ['as'=>'edit','uses'=>'FranchiseeController@edit_profile']);
			Route::post('edit/save', ['as'=>'edit.save','uses'=>'FranchiseeController@update_profile']);			
			
			Route::post('block/{userid}', ['as'=>'block','uses'=>'FranchiseeController@change_block_franchisee']);
			Route::post('loginblock/{userid}', ['as'=>'loginblock','uses'=>'FranchiseeController@change_franchisee_loginblock']);
			
			Route::post('check', ['as'=>'check','uses'=>'FranchiseeController@check_franchisee']);
			Route::post('mapping/check', ['as'=>'mapping.check','uses'=>'FranchiseeController@check_franchise_mapped']);			
			
			Route::get('addfund', ['as'=>'addfund','uses'=>'FinanceController@add_fund_to_frnachisee']);
			Route::post('details/check', ['as'=>'details.check','uses'=>'FinanceController@check_franchisee_details']);
			Route::post('addfund/save', ['as'=>'addfund.save','uses'=>'FinanceController@save_fund_to_frnachisee']);
			
			Route::get('fund-credits', ['as'=>'fund-credits','uses'=>'FinanceController@supportCenterFundCredits']);
			Route::post('fund-credits', ['as'=>'fund-credits.json','uses'=>'FinanceController@supportCenterFundCredits']);
			Route::post('change_fund_status', ['as'=>'change_fund_status','uses'=>'FinanceController@change_franchisee_fund_status']);
			
			Route::get('fund-transfer-commission', ['as'=>'fundtransfer-commission','uses'=>'TransferController@franchiseeFundTransferCommission']);
			Route::post('fund-transfer-commission', ['as'=>'fundtransfer-commission.json','uses'=>'TransferController@franchiseeFundTransferCommission']);
		});		
	
		Route::group(['prefix'=>'finance', 'as'=>'finance.'], function()
		{
			Route::group(['prefix'=>'fund-transfer', 'as'=>'fund-transfer.'], function()
			{
				Route::match(['get', 'post'], 'merchant/{type?}/{mrcode?}', ['as'=>'to_merchant', 'middleware'=>'validate', 'uses'=>'AdminFinanceController@merchant_finance']);
				Route::match(['get', 'post'], 'find-merchant', ['as'=>'find_merchant', 'uses'=>'AdminFinanceController@find_merchant']);
				Route::match(['get', 'post'], 'member/{type?}/{member?}', ['as'=>'to_member', 'middleware'=>'validate', 'uses'=>'AdminFinanceController@member_finance']);
				Route::post('find-member', ['as'=>'find_member', 'uses'=>'AdminFinanceController@find_member']);
				Route::match(['get', 'post'], 'dsa', ['as'=>'dsa', 'uses'=>'AdminFinanceController@dsa_finance']);
				Route::match(['get', 'post'], 'find-dsa', ['as'=>'find_dsa', 'uses'=>'AdminFinanceController@find_dsa_acc']);
			});
			Route::match(['get', 'post'], 'fund-transfer-history', ['as'=>'fund-transfer-history', 'uses'=>'AdminFinanceController@fund_transfer_history']);
			Route::match(['get', 'post'], 'admin-transfer-history', ['as'=>'admin-transfer-history', 'uses'=>'AdminFinanceController@admin_fund_transfer_history']);
			Route::get('transaction-log/{for?}', ['as'=>'transaction-log', 'uses'=>'AdminFinanceController@transactionLog']);
			Route::post('transaction-log', ['as'=>'transaction-log-list', 'uses'=>'AdminFinanceController@transactionLog']);
			Route::match(['get', 'post'], 'admin-credit-debit-history', ['as'=>'admin-credit-debit-history', 'uses'=>'AdminFinanceController@admin_credit_debit_history']);
			Route::match(['get', 'post'], 'order-payments', ['as'=>'order-payments', 'uses'=>'AdminFinanceController@online_payments']);
			Route::match(['get', 'post'], 'order-payments-details/{id}', ['as'=>'order-payments-details', 'uses'=>'AdminFinanceController@online_payments_details']);
			Route::match('post', 'pay-confirm/{id}', ['as'=>'pay-confirm', 'uses'=>'AdminFinanceController@confirmPayment']);
			Route::match(['get', 'post'], 'payment-paid/{id}', ['as'=>'payment-paid', 'uses'=>'AdminFinanceController@updateStatus']);
			Route::match('post', 'paymen-refund/{id}', ['as'=>'payment-refund', 'uses'=>'AdminFinanceController@refundPayment']);
		});
		
	    Route::group(['prefix'=>'online', 'as'=>'online.'], function()
        {
		    Route::any('store-list', ['as'=>'store-list', 'uses'=>'OnlineStoreController@affiliateList']); 
		    Route::post('signup-categories', ['as'=>'options-list', 'uses'=>'OnlineStoreController@affiliateSignupCategories']);
		    Route::any('store-add', ['as'=>'store-add', 'uses'=>'OnlineStoreController@affiliateAdd']);
		    Route::post('store-save/{id?}', ['as'=>'store-save', 'middleware'=>'validate','uses'=>'OnlineStoreController@saveAffiliate']);
		    Route::match(['get', 'post'], 'countries/list', ['as'=>'countries.list', 'uses'=>'OnlineStoreController@country_list']);
		    Route::match(['get', 'post'], 'details/{id}', ['as'=>'details', 'uses'=>'OnlineStoreController@affiliateDetails']);
		    Route::post('update-status/{status}/{id}', ['as'=>'update-status', 'uses'=>'OnlineStoreController@updateAffiliateStatus']);
		    Route::post('delete/{id}', ['as'=>'delete', 'uses'=>'OnlineStoreController@affiliateDelete']);
		    Route::any('category/list', ['as'=>'category.list', 'uses'=>'OnlineStoreController@getOnlineCategory_list']);
        });
	 
	    Route::group(['prefix'=>'category', 'as'=>'category.'], function()
        {
	        Route::group(['prefix'=>'online-store', 'as'=>'online_store.'], function()
            {
			    Route::match(['get', 'post'], 'getcategory', ['as'=>'getcategory', 'uses'=>'OnlineStoreController@getOnlineCategories']);
			    Route::match(['get', 'post'], 'check-slug', ['as'=>'check-slug', 'uses'=>'OnlineStoreController@check_onlineCategory_slug']);
			    Route::match(['get', 'post'], 'update', ['as'=>'update','middleware'=>['validate'],'uses'=>'OnlineStoreController@saveOnlineCategory']);
		        Route::match(['get', 'post'], 'edit', ['as'=>'edit', 'uses'=>'OnlineStoreController@editOnlineCategory']);
			    Route::match(['get', 'post'], 'change-status', ['as'=>'status', 'uses'=>'OnlineStoreController@change_onlineCategory_status']);
		    });
		    Route::group(['prefix'=>'products', 'as'=>'products.'], function()
			{
				Route::any('category/list', ['as'=>'category-list', 'uses'=>'CategoryManagementController@getProductCategory_list']);
				Route::match(['get', 'post'], 'getcategory', ['as'=>'getcategory', 'uses'=>'CategoryManagementController@getProductCategories']);
				Route::match(['get', 'post'], 'check-slug', ['as'=>'check-slug', 'uses'=>'CategoryManagementController@check_ProductCategory_slug']);
				Route::match(['get', 'post'], 'update', ['as'=>'update','middleware'=>['validate'],'uses'=>'CategoryManagementController@saveProductCategory']);
				Route::match(['get', 'post'], 'edit', ['as'=>'edit', 'uses'=>'CategoryManagementController@editProductCategory']);
				Route::match(['get', 'post'], 'change-status', ['as'=>'status', 'uses'=>'CategoryManagementController@change_ProductCategory_status']);
			}); 
        });	
    });	
});