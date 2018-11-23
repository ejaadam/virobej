<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\AdminController;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminCatalog;
use App\Helpers\ShoppingPortal;
use App\Models\MemberAuth;
use App\Models\Admin\SupplierProductOrder;
use View;
use Input;
use Config;
use Response;

class AdminDashboard_Controller extends AdminController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
    }

    public function dashboard ()
    {
        $data = [];
        $data['products'] = $this->commonObj->getProductsCount($this->currency_id);		
        $data['sales'] = $this->commonObj->getProductItemCount($this->currency_id);		
        $data['orders'] = $this->commonObj->getOrderCount();		
        $data['customers'] = $this->commonObj->getCustomerCount();		
        $sp = new SupplierProductOrder($this->commonObj);
        $data['recent_orders'] = $sp->getOrderList(['start'=>0, 'length'=>10, 'account_type_id'=>$this->acc_type_id]);
		//print_r($data);exit;
        return View('admin.dashboard', $data);
    }

    public function change_password ()
    {
        return View::make('admin.change_password.change_password');
    }

    public function updatePasswrord ()
    {
        $op = [];
        $post = Input::all();
        $post['account_id'] = $this->account_id;
        if ($this->commonObj->changePassword($post))
        {
            $this->statusCode = 200;
            $op['msg'] = Lang::get('general.your_password_has_been_updated_successfully');
        }
        else
        {

            $op['msg'] = Lang::get('general.incorrect_old_new_password_are_same');
        }
        return Response::Json($op, $this->statusCode);
    }

}
