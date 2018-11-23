<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Seller\Report;
use App\Models\Seller\Supplier;
use Config;
use Lang;
use Redirect;
use Input;
use View;
use App\Helpers\ShoppingPortal;

class AccountController extends BaseController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->report = new Report();
		$this->suppObj = new Supplier();
    }

    

    public function login ()
    {
        return View::make('admin.login');
    }	
}
