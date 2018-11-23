<?php
Route::get('login',['as'=>'login','uses'=>'AffiliateController@login']);
Route::post('checklogin',['as'=>'checklogin','uses'=>'AffAuthcontroller@login_check']);
Route::post('forgotpwd',['as'=>'forgotpwd','uses'=>'AffAuthcontroller@forgotpwd']);	
Route::post('signup-save',['as'=>'signup.save','middleware'=>['validate'],'uses'=>'AffiliateController@save_account']);
Route::post('logout', ['as'=>'logout','middleware'=>['affauth'],'uses'=>'AffAuthcontroller@logout']);
Route::get('recovery-pwd', ['as'=>'recoverpwd', 'uses'=>'AffAuthcontroller@recoverpwd']);
Route::get('validate/lang/{langkey}', 'LangController@langLoad');

Route::group(['middleware'=>['affauth','validate']], function()
{     
	Route::get('dashboard',['as'=>'dashboard','uses'=>'AffiliateController@dashboard']);	
  /*Profile */
    Route::group(['prefix'=>'profile'], function()
    {
        Route::get('/', ['as'=>'profile', 'uses'=>'AffiliateController@myprofile']);
        Route::post('profileimage_save', ['as'=>'profile.profileimage_save', 'uses'=>'AffiliateController@profileimage_withcrop_save']);
        Route::get('remove_profile_image', ['as'=>'profile.remove_profile_image', 'uses'=>'AffiliateController@remove_profile_image']);
        Route::post('tempimg_upload', ['as'=>'profile.tempimg_upload', 'uses'=>'AffiliateController@tempimg_upload']);
        Route::get('kyc', ['as'=>'profile.kyc', 'uses'=>'AffiliateController@kyc']);
        Route::match(['get', 'post'], 'kyc-upload', ['as'=>'profile.kyc_upload', 'uses'=>'AffiliateController@kyc_upload']);
    });
	
     /* Settings */	
	Route::group(['prefix'=>'settings'],function(){
		
		Route::match(['get'],'change-email', ['as'=>'settings.change_email','uses'=>'SettingsController@security_settings']);
		/* change_password */
		Route::match(['get','post'],'change-pwd', ['as'=>'settings.change_pwd','uses'=>'SettingsController@change_pwd']);
		Route::match(['get','post'],'password_check', ['as'=>'settings.password_check','uses'=>'SettingsController@password_check']);
        Route::match(['post'],'update_password', ['as'=>'settings.updatepwd','uses'=>'SettingsController@updatepwd']);
		/* change_security password */
	    Route::match(['get','post'],'change-securitypin', ['as'=>'settings.change_securitypin','uses'=>'SettingsController@change_securitypin']);
	    Route::match(['get','post'],'check_securitypwd', ['as'=>'settings.check_securitypin','uses'=>'SettingsController@check_securitypwd']);
	    Route::match(['post'],'update_securitypwd',['as'=>'settings.check_securitypin','uses'=>'SettingsController@update_securitypwd']);
	    Route::match(['post'],'forgot_security_pin', ['as'=>'settings.forgot_security_pin','uses'=>'SettingsController@forgot_security_pwd']);		
        Route::match(['get','post'],'otp_check',['as'=>'settings.otp_check','uses'=>'SettingsController@otp_check']); 
        Route::match(['get','post'],'reset_security_pwd/{activation_key}',['as'=>'settings.reset_security_pwd','uses'=>'SettingsController@reset_security_pwd']); 
	    Route::match(['get','post'],'reset_update_pwd', ['as'=>'settings.reset_update_pwd','uses'=>'SettingsController@updatesecuritypwd']);
	   /* change_email */
	    Route::match(['post'],'send-update-email-verification', ['as'=>'settings.updateemailverification','uses'=>'SettingsController@sendUpdate_emailVerification']);		
        Route::match(['post'],'update_email', ['as'=>'settings.update_email','uses'=>'SettingsController@update_email']);
		Route::match(['post'],'send-update-mobile-verification', ['as'=>'settings.updatemobileverification','uses'=>'SettingsController@sendUpdate_mobileVerification']);
        Route::match(['get','post'],'update-mobile', ['as'=>'settings.update_mobile','uses'=>'SettingsController@update_mobile']);
		Route::match(['get','post'],'checkTpin', ['as'=>'settings.checkTpin','uses'=>'PayoutsettingsController@checkTpin']);
		Route::match(['get','post'],'payouts', ['as'=>'settings.payouts','uses'=>'PayoutsettingsController@payouts_settings']);
		Route::match(['get','post'],'account_payout_settings_update', ['as'=>'settings.bank_transfer_update','uses'=>'PayoutsettingsController@account_payout_settings_update']);
	});
	
	 /*Package */
	Route::group(['prefix'=>'package'],function(){
		Route::match(['get','post'],'browse', ['as'=>'package.browse','uses'=>'PackageController@packages_browse']);
		Route::post('paymodes', ['as'=>'package.paymodes','uses'=>'PackageController@paymode_select']);
		Route::post('paymode/{type}/info', ['as'=>'package.paymodeinfo','uses'=>'PackageController@paymode_select'])->where(array('type' => '[0-9]+'));	
		Route::post('purchase-confirm', ['as'=>'package.purchaseconfirm','uses'=>'PackageController@purchase_confirm']);
		Route::match(['get','post'],'my-packages', ['as'=>'package.my_packages','uses'=>'PackageController@my_packages']);
		Route::match(['get','post'],'upgrade-history', ['as'=>'package.upgrade_history','uses'=>'PackageController@upgrade_history']);
	});
	
    /* Refferals */
     Route::group(['prefix'=>'referrals'],function(){
		Route::match(['get','post'],'my-directs', ['as'=>'referrals.my_directs','uses'=>'ReferralsController@my_directs']);
		Route::match(['get','post'],'my_team', ['as'=>'referrals.my_team','uses'=>'ReferralsController@my_team']);
		Route::match(['get','post'],'my-referrals', ['as'=>'referrals.my_referrals','uses'=>'ReferralsController@my_referrals']);
		Route::match(['get','post'],'sponsor-geneology', ['as'=>'referrals.sponsor_geneology','uses'=>'ReferralsController@sponsor_geneology']);
		Route::match(['get','post'],'get-sponsor-geneology/{account_id}', ['as'=>'referrals.getsponsor','uses'=>'ReferralsController@get_sponsor_geonology'])
		->where('account_id', '([0-9]+)?');
		Route::match(['get','post'],'my-geneology', ['as'=>'referrals.my_geneology','uses'=>'ReferralsController@my_geneology']);
		Route::match(['get','post'],'get-direct-geneology/{account_id}', ['as'=>'referrals.getdirect','uses'=>'ReferralsController@get_direct_geneology'])
		->where('account_id', '([0-9]+)?');
	});
    
	/* Wallet */
	Route::group(['prefix'=>'wallet'],function(){
		Route::match(['get'],'/', ['as'=>'wallet.balance','uses'=>'WalletController@my_wallet']);	
        /* fund_transfer */		
		Route::match(['get','post'],'fund-transfer', ['as'=>'wallet.fundtransfer','uses'=>'TranferController@fundtransfer']);
	    Route::match(['get','post'],'searchacc', ['as'=>'wallet.fundtransfer.usrsearch','uses'=>'TranferController@searchacc']);
		Route::match(['get','post'],'fund_transfer_confirm', ['as'=>'wallet.fund_transfer_confirm','uses'=>'TranferController@fund_transfer_to_user_confirm']);
		Route::match(['get','post'],'get_tac_code', ['as'=>'wallet.fundtransfer.get_tac_code','uses'=>'TranferController@get_tac_code']);
		/*fund_transferhistory */
		Route::match(['get','post'],'fund-transfer/history', ['as'=>'wallet.fundtransfer.history','uses'=>'TranferController@fundtransfer_history']);
		Route::match(['get','post'],'transactions', ['as'=>'wallet.transactions','uses'=>'WalletController@transactions']);
	});
	/* Withdrawl */
	Route::group(['prefix'=>'withdrawal'],function(){
		Route::match(['get','post'],'create', ['as'=>'withdrawal.create','uses'=>'WithdrawalController@new_withdrawal']);
		Route::match(['get','post'],'payout', ['as'=>'withdrawal.payouts', 'uses'=>'WithdrawalController@payoutTypesList']);
		Route::match(['get','post'],'payout-details', ['as'=>'withdrawal.payout-details', 'uses'=>'WithdrawalController@payoutDetails']);
		Route::match(['get','post'],'save', ['as'=>'withdrawal.save', 'uses'=>'WithdrawalController@saveWithdraw']);
		Route::match(['get','post'],'{status}/list', ['as'=>'withdrawal.list','uses'=>'WithdrawalController@withdrawal_list']);
		Route::match(['get','post'],'{status}', ['as'=>'withdrawal.history','uses'=>'WithdrawalController@history']);
	
	});		
	
	Route::group(['prefix'=>'reports'],function(){
		
        Route::match(['get','post'],'fast_start', ['as'=>'reports.fast_start', 'uses'=>'AffiliatereportsController@faststart_bonus']);
		Route::match(['get','post'],'team_bonus', ['as'=>'reports.team_bonus', 'uses'=>'AffiliatereportsController@team_bonus']);
	    Route::match(['get','post'],'leadership', ['as'=>'reports.leadership','uses'=>'AffiliatereportsController@leadership_bonus']); 
	    Route::match(['get','post'],'personal_commission', ['as'=>'reports.personal_commission','uses'=>'AffiliatereportsController@personal_commission']); 
	    Route::match(['get','post'],'ambassador_bonus', ['as'=>'reports.ambassador_bonus','uses'=>'AffiliatereportsController@ambassador_bonus']); 
		
	/* 	Route::match(['get','post'],'leadership_bonus', ['as'=>'report.leadership_bonus', 'uses'=>'AdminreportsController@leadership_bonus']);
		Route::match(['get','post'],'direct_refferal', ['as'=>'report.direct_refferal', 'uses'=>'AdminreportsController@direct_refferal']);
		Route::match(['get','post'],'user_direct_referrals', ['as'=>'report.user_direct_referrals', 'uses'=>'AdminreportsController@user_direct_referrals']);
		Route::match(['get','post'],'package_purchase_report', ['as'=>'report.package_purchase_report', 'uses'=>'AdminreportsController@package_purchase_report']); */
		
	});
	/* Ranks */
	Route::group(['prefix'=>'ranks'],function(){
		Route::match(['get','post'],'myrank', ['as'=>'ranks.myrank','uses'=>'RanksController@myrank']);
		Route::match(['get','post'],'history', ['as'=>'ranks.history','uses'=>'RanksController@myrank_history']);
		Route::match(['get','post'],'eligibilities', ['as'=>'ranks.eligibilities','uses'=>'RanksController@eligibilities']);
	});   

    Route::group(['prefix'=>'support'], function()
    {
        Route::match(['get', 'post'], 'tickets', ['as'=>'support.tickets', 'uses'=>'SupportController@tickets']);
        Route::post('save-tickets', ['as'=>'support.tickets_save', 'uses'=>'SupportController@save_tickets']);
        Route::post('view_ticket_detail', ['as'=>'support.tickets_detail', 'uses'=>'SupportController@view_ticket_detail']);
        Route::post('tickets_comment', ['as'=>'support.tickets_comment', 'uses'=>'SupportController@save_ticket_replies']);
        Route::post('tickets_close', ['as'=>'support.tickets_close', 'uses'=>'SupportController@close_ticket']);
        Route::post('tickets_status', ['as'=>'support.tickets_status', 'uses'=>'SupportController@tickets_status']);
        Route::match(['get', 'post'], 'tickets_details', ['as'=>'support.tickets_details', 'uses'=>'SupportController@tickets_details']);
        Route::match(['get', 'post'], 'faqs', ['as'=>'support.faqs', 'uses'=>'SupportController@faqs']);
        Route::post('faqs/{code?}', 'SupportController@get_faqs')->where('code', '([A-z-_]+)?');
        Route::post('faqs/search-term', 'SupportController@search_faq');
        Route::get('faqs/search_faq', 'SupportController@faq');
        Route::match(['get', 'post'], 'downloads', ['as'=>'support.downloads', 'uses'=>'SupportController@downloads']);
        Route::match(['get', 'post'], 'announcements', ['as'=>'support.announcements', 'uses'=>'SupportController@announcements']);
    });
		Route::match(['get','post'],'team_bonus', ['as'=>'commission','uses'=>'AffiliatereportsController@team_commission']);
		Route::match(['get','post'],'leadership_bonus', ['as'=>'Leadership-Bonus','uses'=>'AffiliatereportsController@leadership_bonus_commission']);
		Route::match(['get','post'],'Car-Bonus', ['as'=>'Car-Bonus','uses'=>'AffiliatereportsController@car_bonus_commision']);
});