<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\SupplierWithdrawals;
use Config;
use Lang;
use Redirect;

class SupplierWithdrawalController extends SupplierBaseController
{

    public $withdrawalObj = '';

    public function __construct ()
    {
        parent::__construct();
        $this->withdrawalObj = new SupplierWithdrawals();
    }

    public function withdrawal_payment_list ()
    {
        $op = array();
        $data = array();
        $postdata = Input::all();
        $data['account_id'] = $this->account_id;
        $data1['balnce_list'] = $this->withdrawalObj->withdrawal_wallet_balance_list($data);
        return View::make('supplier.withdrawal.withdrawal', $data1);
    }

    public function withdrawal_list ($status)
    {
        $data = array();
        $data['status_array'] = array('pending'=>0, 'transferred'=>1, 'processing'=>2, 'cancelled'=>3, 0=>'pending', 1=>'transferred', 2=>'processing', 3=>'cancelled');
        if (in_array($status, $data['status_array']))
        {
            $data['status_key'] = $status;
            $data['pg_title'] = ucwords($status).' Withdrawals';
            $data['status'] = $data['status_array'][$status];
            $data['status_label_array'] = array('label label-warning', 'label label-success', 'label label-info', 'label label-danger');
        }
        $data['wallet_list'] = $this->commonObj->get_wallet_list(array('withdrawal_status'=>Config::get('constants.ON')));
        return View::make('supplier.withdrawal.withdrawal_list', $data);
    }

}

?>
