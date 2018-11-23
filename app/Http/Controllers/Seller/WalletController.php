<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\Report;
use App\Models\Seller\Supplier;
use Config;
use Lang;
use Redirect;
use Input;
use View;
use App\Helpers\ShoppingPortal;

class WalletController extends SupplierBaseController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->report = new Report();
		$this->suppObj = new Supplier();
    }

    

    public function addMoney ()
    {
        return View::make('seller.wallet.add_money');
    }	
	
	
	

}
