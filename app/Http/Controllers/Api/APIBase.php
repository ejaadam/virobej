<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseController;
use Config;
use ShoppingPortal;
use Input;
use Response;

class APIBase extends BaseController
{

    public function __construct ()
    {
        parent::__construct();
        if (Config::has('data.user'))
        {
            $user_details = Config::get('data.user');
            if (!empty($user_details))
            {
                $this->acc_type_id = $user_details->account_type_id;
                $this->account_id = $user_details->account_id;
                $this->uname = $user_details->uname;
                $this->full_name = $user_details->full_name;
                $this->email = $user_details->email;
                $this->mobile = $user_details->mobile;
                $this->token = $user_details->token;
                $this->language_id = $user_details->language_id;
                //$this->locale_id = $user_details->locale_id;
                //$this->time_zone_id = $user_details->time_zone_id;
                $this->currency_id = $user_details->currency_id;
                $this->send_email = $user_details->send_email;
                $this->send_sms = $user_details->send_sms;
                $this->send_notification = $user_details->send_notification;
                $this->is_mobile_verified = $user_details->is_mobile_verified;
                $this->is_email_verified = $user_details->is_email_verified;
                if (isset($user_details->supplier_id))
                {
                    $this->supplier_id = $user_details->supplier_id;
                }                
                $user_details->address = $this->commonObj->getUserAddress($this->account_id, $this->acc_type_id);
                $this->user_details = $user_details;
                $this->op['UserDetails'] = $user_details;
            }
        }
    }

    public function loginCheck ()
    {
        $postdata = Input::all();
        $this->memberObj = new MemberAuth($this->commonObj);
        $res = $this->memberObj->validateUser($postdata);
        $this->response = array_merge($this->response, $res->response);
        $this->statusCode = $res->statusCode;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function checkAccount ()
    {
        $this->response = [];
        $post = Input::all();
        if ($account = $this->commonObj->checkAccount($post['username'], Config::get('constants.ACCOUNT_TYPE.USER')))
        {
            if ($code = $this->commonObj->updateAccountVerificationCode($this->device_log_id))
            {
                SMS::send($account->mobile, Lang::get('customer.sms.reset_pwd_verification', ['code'=>$code, 'name'=>$account->full_name, 'uname'=>$account->uname]));
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

    public function forgotPassword ()
    {
        $this->response = [];
        $post = Input::all();
        $post['device_log_id'] = $this->device_log_id;
        $status = $this->commonObj->updatePassword($post, Config::get('constants.ACCOUNT_TYPE.USER'));
        if ($status == 1)
        {
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

    public function changePassword ()
    {
        $this->response = [];
        $post = Input::all();
        $post['account_id'] = $this->account_id;
        if ($this->commonObj->changePassword($post))
        {
            $this->statusCode = 200;
            $this->response['msg'] = Lang::get('general.your_password_has_been_updated_successfully');
        }
        else
        {
            $this->response['msg'] = Lang::get('general.incorrect_old_new_password_are_same');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function checkEmailVerification ($activation_key)
    {
        $this->response = [];
        if ($this->commonObj->updateEmailVerification($activation_key))
        {
            $this->statusCode = 200;
            $this->response['msg'] = Lang::get('general.your_email_id_is_verified');
        }
        else
        {
            $this->response['msg'] = Lang::get('general.invalid_verification_code');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
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
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

	/* NOTIFICATIONS  */
    public function getNotifications ()
    {
        $this->response = $data = [];
        $this->response['notifications'] = [];
        $this->response['count'] = 0;
        if (isset($this->account_id))
        {
            $data['account_id'] = $this->account_id;
            $this->response['notifications'] = $this->commonObj->getNotifications($data);
            $this->response['count'] = $this->commonObj->getNotifications($data, true);
        }
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function markNotificationRead ()
    {
        $post = Input::all();
        $data = [];
        if (isset($post['id']) && !empty($post['id']))
        {
            $data['account_id'] = $this->account_id;
            $data['notification_id'] = $data['id'];
            if ($this->commonObj->markNotificationRead($data))
            {
                $this->response['notifications'] = $this->commonObj->getNotifications($data);
                $this->response['count'] = $this->commonObj->getNotifications($data, true);
                $this->statusCode = 200;
            }
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function updateNotificationToken ()
    {
        $this->response = [];
        $data = Input::all();
        if (!empty($data))
        {
            $data['device_log_id'] = $this->device_log_id;
            $this->commonObj->updateNotificationToken($data);
            $this->statusCode = 200;
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function getAvaliablePaymentModes ()
    {
        $this->response = [];
        $this->response['payment_modes'] = $this->commonObj->getAvaliablePaymentModes();
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function getAvaliablePaymentTypes ()
    {
        $this->response = [];
        $this->response = $this->commonObj->getAvaliablePaymentTypes();
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function getSliders ()
    {
        $this->response = $data = [];
        $data = Input::all();
        $data['currency_id'] = $this->currency_id;
        $data['country_id'] = $this->country_id;
        $this->response = $this->commonObj->getSliders($data);
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function subscribe ()
    {
        $this->response = $data = [];
        $data = Input::all();
        if ($this->commonObj->subscribe($data))
        {
            $this->response['msg'] = Lang::get('general.subscribe_successfully');
            $this->statusCode = 200;
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function unsubscribe ($id)
    {
        $this->response = $data = [];
        $data = ['id'=>$id];
        if ($this->commonObj->unsubscribe($data))
        {
            $this->response['msg'] = Lang::get('general.unsubscribe_successfully');
            $this->statusCode = 200;
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function getCategory ()
    {
        $this->response = $data = [];
        $data = Input::all();
        $this->response['category'] = $this->commonObj->get_category_childs(1);
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function save_contact_us ()
    {
        $this->response = [];
        $postdata = Input::all();
        if ($this->page_settings->enquiry_receive_type == 1)
        {
            if ($this->frontendObj->contact_us($postdata))
            {
                $this->response['msg'] = Lang::get('general.thanks_for_submitting_your_enquires');
                $this->statusCode = 200;
            }
        }
        else if ($this->page_settings->enquiry_receive_type == 2)
        {
            $setting = json_decode(stripslashes($this->page_settings->outbound_email_api));
            $receiver_email = $setting->receiver_email;
            foreach ($receiver_email as $to_email)
            {
                Mailer::send($to_email, 'emails.site_contact_us', Lang::get('general.customer_enquiry'), $postdata, $postdata['useremail'], $postdata['username']);
            }
            $this->response['msg'] = Lang::get('general.thanks_for_submitting_your_enquires');
            $this->statusCode = 200;
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function groupRequest ()
    {
        $post = Input::all();
        $this->response = [];
        $this->response['response'] = [];
        if (isset($post['requests']) && !empty($post['requests']))
        {
            foreach ($post['requests'] as $request)
            {
                if (!empty($request))
                {
                    $request = (object) $request;
                    $this->response['response'][$request->id] = ShoppingPortal::getResponse($request->url, 'POST', (isset($request->data) ? $request->data : []), true);
                }
            }
        }
        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function pageData ()
    {
        $post = Input::all();
        $this->response = $data = $requests = [];
        $post['page'] = isset($post['page']) && !empty($post['page']) ? $post['page'] : 'home';
        switch ($post['page'])
        {
            case 'home':
                $this->response['sliders'] = ShoppingPortal::getResponse('api/v1/customer/get-sliders', 'POST', ['page'=>'home']);
                $this->response['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []);
                $this->response['menus'] = ShoppingPortal::getResponse('api/v1/customer/get-menus', 'POST', []);
                $this->response['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []);
                $this->response['main_categories'] = ShoppingPortal::getResponse('api/v1/customer/main-categories', 'POST', []);
                $this->response['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []);
                break;
            case 'browse-products':
                $this->response['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []);
                $this->response['menus'] = ShoppingPortal::getResponse('api/v1/customer/get-menus', 'POST', []);
                $this->response['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []);
                $this->response['main_categories'] = ShoppingPortal::getResponse('api/v1/customer/main-categories', 'POST', []);
                $this->response['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []);
                break;
            case 'product-details':
                $this->response['payment_types'] = ShoppingPortal::getResponse('api/v1/customer/get-payment-types', 'POST', []);
                $this->response['menus'] = ShoppingPortal::getResponse('api/v1/customer/get-menus', 'POST', []);
                $this->response['my_cart'] = ShoppingPortal::getResponse('api/v1/customer/products/my-cart-count', 'POST', []);
                $this->response['main_categories'] = ShoppingPortal::getResponse('api/v1/customer/main-categories', 'POST', []);
                $this->response['notifications'] = ShoppingPortal::getResponse('api/v1/get-notifications', 'POST', []);
                break;
        }

        $this->statusCode = 200;
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

}
