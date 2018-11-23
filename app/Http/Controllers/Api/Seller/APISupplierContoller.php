<?php
namespace App\Http\Controllers\Api\Seller;
//use App\Http\Controllers\Api\APIBase;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Api\Seller\APISupplier;
use App\Models\Memberauth;
use App\Models\Seller\Supplier;
use App\Models\Commonsettings;
use Input;
use Config;
use Response;
use App\Helpers\CommonNotifSettings;
use App\Helpers\SMS;
use Lang;
use URL;
use Session;
use Illuminate\Support\Facades\Validator;

class APISupplierContoller extends SupplierBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->suppierObj = new APISupplier($this->commonObj);
		$this->memberObj = new MemberAuth($this->commonObj);
		$this->suppObj = new Supplier();
		$this->commonstObj = new Commonsettings();
    }    

    public function logout ()
    {
        $this->response = [];
        if (Config::has('data.user'))
        {
            $this->memberObj = new MemberAuth($this->commonObj);
            if ($this->memberObj->logoutUser($this->account_id, $this->device_log_id))
            {
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.logout_success');
            }
            else
            {
                $this->response['msg'] = Lang::get('general.something_went_wrong');
            }
        }
        return Redirect::to('supplier/login');
    }

    public function checkAccount ()
    {
        $this->response = [];
        $post = Input::all();
		$account = $this->commonstObj->checkAccount($post['username'], Config::get('constants.ACCOUNT_TYPE.SELLER'));			
        if ($account)
        {			
            if ($code = $this->commonstObj->updateAccountVerificationCode($this->device_log_id))
            {					
                $this->response['code'] = $code;
                $res = SMS::send($account->mobile, Lang::get('supplier.sms.reset_pwd_verification', ['name'=>$account->full_name, 'uname'=>$account->uname, 'code'=>"$code"]));
				$this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.verification_code_has_been_sent_to_yo_mobile_no');
            }
        }
        else
        {
            $this->response['msg'] = Lang::get('general.mobile_no_email_not_registered');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function checkVerificationCode ()
    {
        $this->response = [];
        $post = Input::all();
        if ($this->commonstObj->checkAccountVerificationCode($this->device_log_id, $post['verification_code'], false))
        {
            $this->statusCode = 200;
        }
        else
        {
            $this->response['msg'] = Lang::get('general.invalid_verification_code_for_mobile');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function forgotPassword ()
    {
        $this->response = [];
        $post = Input::all();			
        $post['device_log_id'] = $this->device_log_id;
        $status = $this->commonstObj->updatePassword($post, Config::get('constants.ACCOUNT_TYPE.SUPPLIER'));
        if ($status == 1)
        {
			$account = $this->commonstObj->checkAccount($post['username'], Config::get('constants.ACCOUNT_TYPE.SELLER'));	
			$res = CommonNotifSettings::notify('FORGOT_PASSWORD', $account->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>"$account->full_name", 'uname'=>"$account->full_name", 'pwd'=>$post['password']], true, true, false, true, false);				
            $this->statusCode = 200;
            $this->response['msg'] = Lang::get('general.your_password_has_been_updated_successfully');
        }
        elseif ($status == 2)
        {
            $this->statusCode = 208;
            $this->response['msg'] = Lang::get('general.some_password');
        }
        else
        {
            $this->response['msg'] = Lang::get('general.invalid_verification_code_for_mobile');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function change_password ()
    {
        $postdata = Input::all();		
        $op['result'] = 'ERR';
        $data['password_check'] = $this->suppObj->old_password_check($this->account_id);
		
        if ($data['password_check']->pass_key == md5($postdata['oldpassword']))
        {
            $data['update'] = $this->suppObj->update_new_password($postdata['newpassword'], $this->account_id);
            if ($data['update'])
            {
				$res = CommonNotifSettings::notify('CHANGE_PASSWORD', $this->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>"$this->full_name", 'uname'=>"$this->full_name", 'pwd'=>$postdata['newpassword']], true, true, false, true, false);	
                $op['result'] = 'OK';
                $op['msg'] = Lang::get('general.new_password_has_been_updated_successfully');
            }
        }
        else
        {
            $op['result'] = 'ERR';
            $op['msg'] = Lang::get('general.old_password_doest_matched_with_the_new_password');
        }
        return Response::Json($op);
    }
	
	public function change_email ()
    {
        $postdata = Input::all();				
        $op['result'] = 'ERR';     
		$op['msg'] = Lang::get('general.new_email_different');
		$res = $this->suppObj->update_new_email($postdata['new_email'], $this->account_id);		
		if ($res)
		{
			CommonNotifSettings::notify('CHANGE_EMAIL', $this->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>"$this->full_name", 'uname'=>"$this->full_name", 'email'=>$postdata['new_email']], true, true, false, true, false);	
			$op['result'] = 'OK';
			$op['msg'] = Lang::get('general.new_email_success');			
		}        
        return Response::Json($op);
    }
	
	public function check_user ()
    {
        $this->response = [];
		$post = Input::all();		
        if (!empty($post))
        {
            $user = $this->commonstObj->checkAccount($post['search_user']);			
            if (!empty($user))
            {
				$this->response['data'] = (array) $user;
                $this->statusCode = 200;
                $this->response['msg'] = 'Account Exists.';               
            } else {
				$this->response['data'] = [];
				$this->statusCode = 404;
				$this->response['msg'] = 'Account Not Found.';      
			}
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function signUp_bk ()
    {
        $data = Input::all();		
        $this->response = [];				
        $res = $this->suppierObj->saveSupplierRegister($data);			
		$this->response = array_merge($this->response, $res->response);
		$this->response['msg'] = 'Congratulations! Your account has been created';
		$this->statusCode = $res->statusCode;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function verifyMobile_bk ()
    {		
        $this->response = [];			
        if (!empty($this->mobile) && !$this->is_mobile_verified)
        {
            if ($code = $this->commonstObj->updateAccountVerificationCode($this->device_log_id))
            {				
                CommonNotifSettings::notify('VERIFY_MOBILE', $this->supplier_id, $this->account_type_id, ['code'=>"$code", 'name'=>"$this->full_name", 'uname'=>"$this->uname"], false, true, false, true);			
                $this->statusCode = 200;
                $this->response['code'] = $code;
                $this->response['msg'] = Lang::get('general.verification_code_has_been_sent_to_yo_mobile_no');
            }
        }
        else
        {
            $this->response['msg'] = Lang::get('general.mobile_is_already_verified');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function verifyEmail_bk ()
    {
        $this->response = [];
        if (!empty($this->email) && !$this->is_email_verified)
        {
            $activation_key = $this->commonstObj->getAccountActivationKey($this->account_id);			
            if ($activation_key)
            {						
				CommonNotifSettings::notify('SUPPLIER_VERIFY_EMAIL', $this->supplier_id, $this->account_type_id, ['url'=>route('seller.check-email-link', ['activation_key'=>$activation_key])], true, false, false, true);						
				$this->response['path'] = route('seller.check-email-link', ['activation_key'=>$activation_key]);					
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.verification_code_has_been_sent_to_your_email_id');                
            }
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function checkMobileVerification_bk ()
    {
        $this->response = [];
        $post = Input::all();
        if ($res = $this->commonstObj->updateMobileVerification($this->account_id, $this->device_log_id, $post['verification_code']))
        {			
            $this->statusCode = 200;
            $this->response['msg'] = Lang::get('general.your_mobile_no_is_verified');
            $this->response['url'] = 'seller/login';
        }
        else
        {			
            $this->response['msg'] = Lang::get('general.invalid_verification_code_for_mobile');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function stores ()
    {
        $data = $filter = $ajaxdata = array();
        $data['supplier_id'] = $this->supplier_id;
        $post = Input::all();
        if (!empty($post))
        {
            $filter['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : null;
            $filter['filterTerms'] = (isset($post['filterTerms']) && !empty($post['filterTerms'])) ? $post['filterTerms'] : null;
            $filter['from'] = (isset($post['from']) && !empty($post['from'])) ? $post['from'] : null;
            $filter['to'] = (isset($post['to']) && !empty($post['to'])) ? $post['to'] : null;
        }
        if (isset($post['draw']))
        {
            $ajaxdata['draw'] = $post['draw'];
            $ajaxdata['data'] = array();
        }
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppierObj->get_stores_list($data, true);
        if ($ajaxdata['recordsTotal'] > 0)
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->suppierObj->get_stores_list($data, true);
            }
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
                $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                $data['orderby'] = isset($post['order'][0]['column']) ? $post['columns'][$post['order'][0]['column']]['name'] : (isset($post['orderby']) ? $post['orderby'] : NULL);
                $data['order'] = isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : (isset($post['order']) ? $post['order'] : NULL);
                $ajaxdata['data'] = $this->suppierObj->get_stores_list($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function saveStores ($store_code = 0)
    {
        $post = Input::all();
        $op = [];
        $post['supplier_id'] = $this->supplier_id;
        $post['account_id'] = $this->account_id;
        if ($store_code && !empty($store_code))
        {
            $post['store_code'] = $store_code;
        }
        $result = $this->suppierObj->update_stores($post);
        if ($result)
        {
            $this->statusCode = 200;
            if ($result == 1)
            {
                $op['msg'] = Lang::get('general.actions.added', ['label'=>Lang::get('general.store')]);
            }
            if ($result == 2)
            {
                $op['msg'] = Lang::get('general.actions.updated', ['label'=>Lang::get('general.store')]);
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changeStoreStatus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppierObj->changeStoreStatus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.actions.status.'.$postdata['status'], ['label'=>Lang::get('general.store')]);
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function verification ()
    {
        $data = $ajaxdata = $filter = array();
        $data['account_id'] = $this->account_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['supplier_id'] = $this->supplier_id;
        $post = Input::all();
        $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : 10;
        $ajaxdata['data'] = [];
        $filter['search_term'] = isset($post['search_term']) ? $post['search_term'] : NULL;
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppierObj->verificationList($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
            $data['order'] = $post['order'][0]['dir'];
            $ajaxdata['data'] = $this->suppierObj->verificationList($data);
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function uploadVerificationDocument ()
    {
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();		
        $settings = Commonsettings::getDocumentDetails(Input::get('document_type_id'), 'other_fields');
        $rules = $messages = [];
        if (!empty($settings))
        {
            $m = ['document_type_id'=>'required'];
            $rules = array_column($settings, 'rules');
            array_walk($rules, function($v) use(&$m)
            {
                $m = array_merge($m, $v);
            });
            $rules = $m;
            $m = ['document_type_id.required'=>trans('supplier.verification.document_type_id')];
            $messages = array_column($settings, 'message');
            array_walk($messages, function($v) use(&$m)
            {
                $m = array_merge($m, $v);
            });
            $messages = $m;
        }
        $validator = Validator::make($postdata, $rules, $messages);
        if (!$validator->fails())
        {
            $upload = array();
            $upload['account_id'] = $this->account_id;
            $upload['path'] = $postdata['document']->getClientOriginalName();
            $upload['document_type_id'] = $postdata['document_type_id'];
            $upload['created_on'] = date(Config::get('constants.DB_DATE_TIME_FORMAT'));
            $upload['status'] = Config::get('constants.OFF');
            $upload['other_fields'] = !empty($postdata['other_fields']) ? json_encode($postdata['other_fields']) : null;
            $extension = $postdata['document']->getClientOriginalExtension();
            $file_name = str_replace('.'.$extension, ' ', $upload['path']);
            $upload['path'] = $file_name.'_'.$upload['account_id'].'.'.$extension;
            $image = array('jpg', 'jpeg', 'png');
            if (in_array($extension, $image))
            {
                $mimetype = 'Image';
            }
            else
            {
                $mimetype = 'File';
            }
            $upload['content_type'] = $mimetype;
            $path = 'assets/uploads/supplier_verify_doc';
            if ($postdata['document']->move($path, $file_name.'_'.$upload['account_id'].'.'.$extension))
            {
                if ($this->suppierObj->uploadVerificationDocument($upload))
                {
                    $this->statusCode = 200;
                    $op['msg'] = Lang::get('general.document.uploaded');
                }
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteVerification ()
    {
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $data = $this->suppObj->delete_doc($postdata);
            if (!empty($data))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.actions.deleted', ['label'=>'general.store'], true);
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function customerReviews ()
    {
        $data = $ajaxdata = $filter = array();
        $data['account_id'] = $this->account_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['supplier_id'] = $this->supplier_id;
        $post = Input::all();
        $ajaxdata['draw'] = $post['draw'];
        $ajaxdata['data'] = [];
        $filter['search_term'] = isset($post['search_term']) ? $post['search_term'] : NULL;
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppierObj->getCustomerReviews($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
            $data['order'] = $post['order'][0]['dir'];
            $ajaxdata['data'] = $this->suppierObj->getCustomerReviews($data);
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function changeCustomerReviewsStatus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppierObj->changeCustomerReviewsStatus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changeCustomerReviewsVerificationStatus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->suppierObj->changeCustomerReviewsVerificationStatus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function saveBussinessInfo ()
    {
        $op = array();
        $post = Input::all();
        $post['supplier_id'] = isset($this->supplier_id) ? $this->supplier_id : $post['supplier_id'];
        $post['account_id'] = isset($this->account_id) ? $this->account_id : $post['account_id'];
        $res = $this->suppierObj->saveBussinessInfo($post);				
        if ($res && !empty($res))
        {                
            $op['url'] = $this->suppObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.BUSSINESS_INFO'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
			 											        'account_id'=>$this->account_id]);				
            if (!isset($this->supplier_id))
            {
                $op['msg'] = Lang::get('general.account.created');
                unset($op['url']);
            }
            else
            {
                $op['msg'] = Lang::get('general.account.updated');
            }
            Session::forget('supplierSignUp');
            Session::flash('signUpSuccess', $op['msg']);
            $this->statusCode = 308;
        }
        else
        {            
            $op['msg'] = 'No Changes';
			$this->statusCode = 422;
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function saveAccountInfo ()
    {
        $op = array();
        $post = Input::all();
        $post['supplier_id'] = isset($this->supplier_id) ? $this->supplier_id : $post['supplier_id'];
        $post['account_id'] = isset($this->account_id) ? $this->account_id : $post['account_id'];
        $res = $this->suppierObj->saveAccountInfo($post);			
        if ($res && !empty($res))
        {                
            $op['url'] = $this->suppObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_INFO'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
																'account_id'=>$this->account_id]);																		
            if (!isset($this->supplier_id))
            {
                $op['msg'] = Lang::get('general.account.created');
                unset($op['url']);
            }
            else
            {
                $op['msg'] = Lang::get('general.account.updated');
            }           
            $this->statusCode = 308;
        }
        else
        {            
            $op['msg'] = 'No Changes';
			$this->statusCode = 422;
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function saveStoreBanking ()
    {
        $op = array();
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;		
        $res = $this->suppierObj->saveStoreBanking($data);		
        if ($res)
        {
            $op['msg'] = Lang::get('general.updated_successfully');
            //$data['current_step'] = Config::get('constants.ACCOUNT_CREATION_STEPS.STORE_BANK');
            //$op['url'] = $this->suppObj->updateNextStep($data);
			$op['url'] = $this->suppObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.STORE_BANK'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
																'account_id'=>$this->account_id]);		
           
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function save_bank_info ()
    {
        $op = array();
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;		
        $res = $this->suppierObj->save_bank_info($data);		
        if ($res)
        {
            $op['msg'] = 'Account info added successfully';    
			$op['url'] = Route('seller.bank-accounts');
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function delete_bank_info ()
    {
        $op = array();
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;		
        $res = $this->suppierObj->delete_bank_info($data);			
        if ($res)
        {
            $op['msg'] = 'Account deleted successfully';    
			$op['url'] = Route('seller.bank-accounts');
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function get_bank_info ()
    {
        $op = array();
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;		
        $res = $this->suppierObj->get_bank_info($data);			
        if ($res)
        {
            $op['data'] = $res;			
            $op['status'] = $this->statusCode = 200;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function update_bank_info ()
    {
        $op = array();
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;		
        $res = $this->suppierObj->update_bank_info($data);				
        if ($res)
        {
            $op['msg'] = 'Account info updated successfully';    
			$op['url'] = Route('seller.bank-accounts');
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }	

    public function saveStoreInfo ()
    {
        $op = array();
        $data = Input::all();
        //$data['account_mst'] = [];
        $data['account_id'] = $this->account_id;
        $data['supplier_id'] = $this->supplier_id;
        $data['country_id'] = !empty($this->country_id) ? $this->country_id : 0;		
        $res = $this->suppierObj->updateAccountDetails($data);			
        if ($res)
        {
            $op['msg'] = Lang::get('general.updated_successfully');
			$op['url'] = $this->suppObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.STORE_INFO'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
																'account_id'=>$this->account_id]);																		            
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function saveKycUpdatee ()
    {
        $op = array();
        $data = Input::all();		
        $data['kyc_verifiacation']['relative_post_id'] = $this->supplier_id;
        $data['verification_doc']['account_id'] = $this->account_id;
        $data['kyc_verifiacation']['post_type'] = $this->acc_type_id;		
        if (Input::hasFile('pan_card_image'))
        {
            $file = Input::file('pan_card_image');
            $filename = 'PANCARD_'.$this->supplier_id.'_'.$file->getClientOriginalName();
            $destinationPath = Config::get('path.SUPPLIER_PANCARD');
            if (Input::file('pan_card_image')->move($destinationPath, $filename))
            {
                if (isset($data['kyc_verifiacation']['pan_card_image']) && !empty($data['kyc_verifiacation']['pan_card_image']) && file_exists($data['kyc_verifiacation']['pan_card_image']))
                {
                    unlink($data['kyc_verifiacation']['pan_card_image']);
                }
                $data['kyc_verifiacation']['pan_card_image'] = $destinationPath.$filename;
            }
        }
        if (Input::hasFile('auth_person_id_proof'))
        {
            $file = Input::file('auth_person_id_proof');
            $filename = 'IDPROOF_'.$this->supplier_id.'_'.$file->getClientOriginalName();
            $destinationPath = Config::get('path.SUPPLIER_IDPROOF');
            if (Input::file('auth_person_id_proof')->move($destinationPath, $filename))
            {
                if (isset($data['kyc_verifiacation']['auth_person_id_proof']) && !empty($data['kyc_verifiacation']['auth_person_id_proof']) && file_exists($data['kyc_verifiacation']['auth_person_id_proof']))
                {
                    unlink($data['kyc_verifiacation']['auth_person_id_proof']);
                }
                $data['kyc_verifiacation']['auth_person_id_proof'] = $destinationPath.$filename;
            }
        }

        if ($res = $this->suppierObj->saveKycUpdate($data))
        {			
            Session::forget('supplierSignUp');
            Session::flash('signUpSuccess', 'Updated Successfully');
            $op['msg'] = Lang::get('general.updated_successfully');            
			$op['url'] = $this->suppObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.VERIFY_KYC'),
																'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																'supplier_id'=>$this->supplier_id,
																'account_id'=>$this->account_id]);																	
            $this->statusCode = 308;
        }
        else
        {
            $op['msg'] = Lang::get('general.something_went_wrong');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function categoriesList ()
    {
        $data = array();
        $data['parent_category_id'] = Input::has('parent_category_id') ? Input::get('parent_category_id') : '';
        $data['search_term'] = Input::has('search_term') ? Input::get('search_term') : '';
        if (Input::has('withUrl'))
        {
            $data['withUrl'] = true;
        }
        $data['supplier_id'] = $this->supplier_id;
        $categories = $this->commonstObj->get_categories_list($data);
        $this->statusCode = 200;
        return Response::json(!empty($categories) ? $categories : array(), $this->statusCode, $this->headers, $this->options);
    }

    public function brandsList ()
    {
        $data = array();
        $data['supplier_id'] = $this->supplier_id;
        $brands = $this->commonstObj->get_brands_list($data);
        $this->statusCode = 200;
        return Response::json(!empty($brands) ? $brands : array(), $this->statusCode, $this->headers, $this->options);
    }	
	
	
	

}
