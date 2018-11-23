<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminCatalog;
use App\Helpers\ShoppingPortal;
use App\Models\Memberauth;
use App\Models\Commonsettings;
use View;
use Input;
use Config;
use Response;
use Session;
class AdminController extends BaseController
{

    public $adminObj = '';

    public function __construct ()
    {
        parent::__construct();
        $this->adminObj = new Admin();
        $this->adminCatlog = new AdminCatalog();
		$this->memberObj = new Memberauth($this->commonObj);
        if (isset($this->user_details->admin_id))
        {
            $this->admin_id = $this->user_details->admin_id;
        }
    }

    public function login ()
    {
        $data = ['login'=>true];
        $data['lfields'] = ShoppingPortal::getHTMLValidation('admin.login');
        $data['ffields'] = ShoppingPortal::getHTMLValidation('admin.forgot-password');		
        return View::make('admin.login', $data);
    }

    public function loginCheck ()
    {
		$op = [];
        $postdata = Input::all();		        
        $res = $this->memberObj->validateUser($postdata, Config::get('constants.ACCOUNT_TYPE.ADMIN'));		
        $op = array_merge($op, $res->response);
        $this->statusCode = $res->response['status'];
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
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
        return \Redirect::to('admin/login');
    }

    public function checkAccount ()
    {
        $this->response = [];
        $post = Input::all();

        if ($account = $this->commonObj->checkAccount($post['username'], Config::get('constants.ACCOUNT_TYPE.ADMIN')))
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
        if (Request::isMethod('post'))
        {
            $this->response = [];
            $post = Input::all();
            $post['device_log_id'] = $this->device_log_id;
            $status = $this->commonObj->updatePassword($post, Config::get('constants.ACCOUNT_TYPE.ADMIN'));
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
        return View::make('admin.login', ['forgot_password'=>true]);
    }

    public function generate_browse_url ($postdata = array())
    {
        $json = false;
        if (empty($postdata))
        {
            $json = true;
            $postdata = Input::all();
        }

        $op['msg'] = Lang::get('genral.somethink_wrong');
        $op['status'] = 'ERR';
        if (!empty($postdata['category_id']))
        {
            $cat_data = $this->commonObj->get_category_parents($postdata);
            $url = '';
            $spath = '';
            $url_str = '';
            foreach ($cat_data as $data)
            {
                $cat_url[] = $data->url_str;
                $spath[] = $data->category_code;
            }

            if (!empty($postdata['brand_id']))
            {
                $brand_slug = $this->commonObj->get_brand($postdata['brand_id']);

                if (!empty($brand_slug))
                {
                    $cat_url[] = $brand_slug.'~brand';
                    $cat_url[] = 'br';
                    $url_str = implode('/', $cat_url).'?spath='.implode(',', $spath);
                }
            }
            elseif (!empty($postdata['value_id']))
            {
                $property_slug = $this->commonObj->get_property_details($postdata['value_id']);
                if (!empty($property_slug))
                {
                    $cat_url[] = $property_slug->key_value.'~'.$property_slug->property;
                    $cat_url[] = 'br';
                    $url_str = implode('/', $cat_url).'?spath='.implode(',', $spath);
                }
            }
            elseif (!empty($postdata['property_id']))
            {
                $property = $this->commonObj->get_property($postdata['property_id']);
                if (!empty($property))
                {
                    $cat_url[] = '~'.$property;
                    $cat_url[] = 'br';
                    $url_str = implode('/', $cat_url).'?spath='.implode(',', $spath);
                }
            }
            else
            {
                $url_str = implode('/', $cat_url).'?spath='.implode(',', $spath);
            }
        }

        if (!empty($url_str))
        {

            $op['url'] = $url_str;
            $op['status'] = 'OK';
        }
        if ($json != false)
        {
            return Response::json($op);
        }
        else
        {
            return $op;
        }
    }

    public function brands_list_chosen ()
    {
        $data = Input::all();
        if (isset($data['to']) && $data['to'] == 'loadSelect')
        {
            $data = $this->adminObj->brands_list_chosen($data);
        }
        else
        {
            $data['results'] = $this->adminObj->brands_list_chosen($data);
        }
        return Response::json($data);
    }

    public function categories_list_chosen ()
    {
        $data = Input::all();
        if (isset($data['to']) && $data['to'] == 'loadSelect')
        {
            $data = $this->adminObj->categories_list_chosen($data);
        }
        else
        {
            $data['results'] = $this->adminObj->categories_list_chosen($data);
        }
        return Response::json($data);
    }

    public function products_list_chosen ()
    {
        $data = Input::all();
        if (isset($data['to']) && $data['to'] == 'loadSelect')
        {
            $data = $this->adminObj->products_list_chosen($data);
        }
        else
        {
            $data['results'] = $this->adminObj->products_list_chosen($data);
        }

        return Response::json($data);
    }

    public function product_combinations_list_chosen ()
    {
        $data = Input::all();
        if (isset($data['to']) && $data['to'] == 'loadSelect')
        {
            $data = $this->adminObj->product_combinations_list_chosen($data);
        }
        else
        {
            $data['results'] = $this->adminObj->product_combinations_list_chosen($data);
        }
        return Response::json($data);
    }

    public function supplier_list_chosen ()
    {
        $data = Input::all();
        if (isset($data['to']) && $data['to'] == 'loadSelect')
        {
            $data = $this->adminObj->supplier_list_chosen($data);
        }
        else
        {
            $data['results'] = $this->adminObj->supplier_list_chosen($data);
        }
        return Response::json($data);
    }

    public function pages_list_chosen ()
    {
        $data = $this->adminObj->pages_list_chosen();
        return Response::json($data);
    }        

    public function check_product_code ()
    {
        $op = [];
        $postdata = Input::all();
        $postdata['currency_id'] = $this->currency_id;
        $postdata['country_id'] = $this->country_id;
        $op['msg'] = Lang::get('general.product.invalid_code');
        if (!empty($postdata))
        {
            $op = $this->commonObj->supplierProductDetailsSimple($postdata);
            if (!empty($op))
            {
                $this->statusCode = 200;
            }
            else
            {
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
public function sample(){
	
	echo "<pre>";
print_r($this->userSess);die;
}
 }
