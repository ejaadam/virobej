<?php

Route::group(['prefix'=>'user/payment-gateway-response/{payment_type}', 'as'=>'payment-gateway-response.', 'middleware'=>['checkPaymentGateWayDomain', 'throttle:60,5']], function()
{	
	Route::post('datafeed/{id?}', ['as'=>'datafeed', 'uses'=>'PaymentGatewayController@dataFeed']);	
	Route::post('check-sum/{id?}', ['as'=>'check-sum', 'uses'=>'PaymentGatewayController@checkSum']);
	//Route::post('notify/{id?}', ['as'=>'notify', 'uses'=>'PaymentGatewayController@dataFeed']);	
	Route::post('return/{id}', ['as'=>'return', 'uses'=>'PaymentGatewayController@_return']);
	//Route::get('return/{id}', ['as'=>'return', 'uses'=>'PaymentGatewayController@_return']);
	//Route::get('success/{id}', ['as'=>'success', 'uses'=>'PaymentGatewayController@success']);
	Route::post('success/{id}', ['as'=>'success', 'uses'=>'PaymentGatewayController@success']);
	//Route::get('failure/{id}', ['as'=>'failure', 'uses'=>'PaymentGatewayController@failure']);
	Route::post('failure/{id}', ['as'=>'failure', 'uses'=>'PaymentGatewayController@failure']);
	//Route::get('cancelled/{id}', ['as'=>'cancelled', 'uses'=>'PaymentGatewayController@cancelled']);
	Route::post('cancelled/{id}', ['as'=>'cancelled', 'uses'=>'PaymentGatewayController@cancelled']);
});
