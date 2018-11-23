<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;

use App\Models\Seller\Supplier;
use App\Models\Seller\Cashback;
use App\Models\MemberAuth;
use App\Helpers\ShoppingPortal;
use Config;
use Lang;
use Redirect;
use Session;
use Input;

class CashbackController extends SupplierBaseController
{

    public $data = array();
    public $adminObj = '';

    public function __construct ()
    {
        parent::__construct();
        $this->suppObj = new Supplier();
        $this->cashbackObj = new Cashback();
    }
	

    public function offer_cashback ()
    {
        return View('seller.offer_cashback');
    }
	
	

}
