<?php

Route::post('sign-up', ['as'=>'sign-up', 'middleware'=>['validate'], 'uses'=>'ApiAccountController@SignUp']);
Route::post('login', ['as'=>'login', 'middleware'=>['validate'], 'uses'=>'ApiUserAuthcontroller@postLoginCheck']);
Route::post('check-login-status',['as'=>'check-login-status', 'middleware'=>'auth', 'uses'=>'ApiUserAuthcontroller@checkLoginStatus']);


Route::group(['middleware'=>['validate', 'auth']], function()
{ 	
  Route::post('change-pwd', ['as'=>'change-pwd', 'uses'=>'ApiUserAuthcontroller@changepwd']);
 
   Route::post('forgot_pwd',['as'=>'forgot_pwd','uses'=>'ApiAffAuthcontroller@forgot_password']);
    Route::post('reset_pwd', ['as'=>'reset_pwd','uses'=>'ApiAffAuthcontroller@reset_pwd']);   
  
	 Route::group(['prefix'=>'profile_settings', 'as'=>'profile_settings.'], function()
           { 
	        Route::post('update_profile', ['as'=>'update', 'uses'=>'ApiAffAuthcontroller@updateProfile']);
			
	        Route::post('edit_profile', ['as'=>'edit', 'uses'=>'ApiAffAuthcontroller@edit_profile']);

		
            Route::group(['prefix'=>'change-email', 'as'=>'change-email.'], function()
            {
              Route::post('new-email-sendotp', ['as'=>'new-email-sendotp','uses'=>'ApiUserAuthcontroller@changeEmailNewEmailOTP']);
			  
			  Route::post('new-email-otp-resend', ['as'=>'new-mob-email-resend', 'uses'=>'ApiUserAuthcontroller@changeEmailNewEmailOTPResend']);
			  
			   Route::post('verify-otp', ['as'=>'verify-otp','uses'=>'ApiUserAuthcontroller@changeEmailIDConfirm']); 
		    
           });
         Route::group(['prefix'=>'change-mobile', 'as'=>'change-mobile.'], function()
			{
			  Route::post('req_otp', ['as'=>'req_otp','uses'=>'ApiUserAuthcontroller@changeMobileNewMobileOTP']);
			
			   Route::post('new-mob-otp-resend', ['as'=>'new-mob-otp-resend', 'uses'=>'ApiUserAuthcontroller@changeMobileNewMobileOTPResend']);
			
			    Route::post('verify-otp', ['as'=>'verify-otp','uses'=>'ApiUserAuthcontroller@changeMobileNoConfirm']);
	         });

		    Route::group(['prefix'=>'profile-pin', 'as'=>'profile-pin.'], function()
		     { 
				Route::post('save', ['as'=>'save', 'uses'=>'ApiUserAuthcontroller@saveProfilePin']);
				Route::post('verify', ['as'=>'verify','uses'=>'ApiUserAuthcontroller@verifyProfilePIN']);
		  }); 
       }); 
});