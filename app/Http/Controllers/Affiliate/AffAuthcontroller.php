<?php
namespace App\Http\Controllers\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\MemberAuth;

class AffAuthcontroller extends AffBaseController
{
    
    public function __construct ()
    {
        parent::__construct();
		$this->affObj = new AffModel();
    }	
	
	public function login_check() {
		$postdata = $this->request->all(); 
		$op = array();
		$messages = [
		  'uname.required' => 'Please enter your Account ID / Email ID', // custom message for required rule.
		  'uname.regex' => 'Invalide Member ID / Email ID', // custom message for email rule.
		  'password.min' => 'Password must contain atleast 6 letters long',
		];
		$rules =  [            
            'uname' => 'required|regex:/^[\w]*$/|max:50',
            'password' => 'required|min:6',
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {	
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['errs'][$key] =  $validator->errors()->first($key);			
				}
			return $this->response->json($op,500);
		}
		
        $op = array();
        $op['status'] = 'fail';
        $op['msg'] = 'Invalid Username and Password';
		$postdata = $this->request->all();        
        $validate = '';
        if (!empty($postdata['uname']) && !empty($postdata['password'])) {            
            $validate = $this->affObj->account_validate($postdata);
			
            if ($validate['status']==1) {                
				$op['status'] = 'ok';				
				$op['msg'] = $validate['msg'];
				$op['url'] = \Session::has('a_go_to') ? \Session::get('a_go_to') : route('aff.dashboard');
				return $this->response->json($op,200);                
            } 
			else {                
                $op['status'] = 'fail';
                $op['msg'] = $validate['msg'];
            }
        } else {
			$op['status'] = 'fail';
            $op['msg'] = 'Email (or) Password should not be empty';
		}
		return $this->response->json($op,200);
    }

    public function logout ()
    {
        $op = [];
        if ($this->session->has('userdata'))
        {
            $this->session->forget('userdata');
        }
        $op['url'] = route('aff.login');
		return $this->response->json($op,200);
    }
	
	public function forgotpwd()
    {
        $op = array();
        $postdata = $this->request->all();  
        $op = array();
		$messages = [
		  'uname.required' => 'Please enter your Member ID / Email ID',
		  'uname.email' => 'Invalide Username',
		];
		$rules =  [            
            'uname' => 'required|email|max:50'
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {	
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['errs'][$key] =  $validator->errors()->first($key);			
				}
			return $this->response->json($op,500);
		}		
        $reqStatus = $this->affObj->sendPwd_resetlink($postdata);
		$sdata = \Session::all();
		$op['msg'] = $reqStatus['msg'];
		if ($reqStatus['status']==1) {
			$op['status'] = 'ok';
			$op['res'] = $reqStatus;
		}
		else{
			$op['status'] = 'fail';			
		}
		return $this->response->json($op,200);
    }
	
	
	public function recoverpwd()
    {
        $data = array();
        $postdata = $this->request->all();  
        $op = array();

		$messages = [
		  'usrtoken.required' => 'Please enter your Member ID / Email ID',
		  'usrtoken.min' => 'Invalide Token1',
		  'usrtoken.max' => 'Invalide Token2',
		  'usrtoken.regex' => 'Invalide Token3',		  
		];
		$rules =  [            
            'usrtoken' => 'required|min:32|max:32|regex:/^[\w]*$/',
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {
			$data['errmsg'] = \Lang::get('user/forgotpwd.validate_msg.token_invalid');
		}
		else {
			$res = $this->affObj->check_pwdreset_token($postdata);			
			if(!empty($res)){
				$data['usrtoken'] = $postdata['usrtoken'];
			}		
		}	
		return view('affiliate.recoverpwd',$data);
    }
	
	
	public function update_newpwd()
    {
        $data = array();
        $postdata = $this->request->all();  
        $op = array();

		$messages = [
		  'usrtoken.required' => 'Please enter your Member ID / Email ID',
		  'usrtoken.min' => 'Invalide Token1',
		  'usrtoken.max' => 'Invalide Token2',
		  'usrtoken.regex' => 'Invalide Token3',
		  'newpassword.required' => 'It should not be empty',
		  'newpassword.min' => 'It must be min 6 letters long',
		];
		$rules =  [            
            'usrtoken' => 'required|min:32|max:32|regex:/^[\w]*$/',
			'newpassword' => 'required|min:6'
        ];
		$validator = Validator::make($postdata, $rules,$messages);
		if ($validator->fails()) {
			$ers = $validator->errors();
			foreach($rules  as $key=>$formats){
				$op['errs'][$key] =  $validator->errors()->first($key);			
				}
			return $this->response->json($op,500);			
		}
		else {
			$upStatus = $this->affObj->update_newpassword($postdata);		
			$op['msg'] = $upStatus['msg'];			
			if($upStatus['status']==1){
				$op['status'] = 200;
				$op['msg'] = $upStatus['msg'];
			}		
			else {
				$op['status'] = 'fail';
			}
			return $this->response->json($op,200);
		}
		
    }
}
