<?php
   Route::get('reset-profile-pin/{token}', ['as'=>'reset-profile-pin', 'uses'=>'FronController@sample'])->where('token', '[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}');