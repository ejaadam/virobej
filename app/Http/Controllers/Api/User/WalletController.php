<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\WalletModel;
use CommonLib;

class WalletController extends UserApiBaseController
{

    private $payObj;
    public function __construct ()
    {
        parent::__construct();
        $this->walletObj = new WalletModel();
    }

    public function wallet_balance ($wallet_code = '', $currency_code = '')
    {
        $data = [];
        $data['account_id'] = $this->userSess->account_id;        
        $data['currency_id'] = $this->userSess->currency_id;        
        $data['is_affiliate'] = $this->userSess->is_affiliate;   	   
        $result = $this->walletObj->wallet_balance($data);		
		//print_r($result);exit;
		$op['withdraw_enable'] = 1;            
		$op['addfund_enable'] = 1;  
		$op['transaction']['filters']['trans_type'] = array_values(trans('user/transactions.filters'));  
        if (!empty($result))
        {                         
            $op['wallets'] = $result['wallets'];                  
            $op['vimoney_balance'] = isset($result['vimoney_bal']) ? $result['vimoney_bal'] : 0;                  
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
			$op['wallets'] = [];
			$op['vimoney_balance'] = CommonLib::currency_format(0, $this->userSess->currency_id, true, false, 2);
            $op['msg'] = 'Data not found';             
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }    	          		 
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function getTranshistory ()
    {
        $postdata = $this->request->all();
		$wdata = [];
        $op['recordsFiltered'] = 0;
        $op['data'] = $filter = [];        
        $wdata['account_id'] = $this->userSess->account_id;        
        $wdata['account_type'] = $this->userSess->account_type;    		
        $wdata['store_code'] = isset($this->userSess->store_code) ? $this->userSess->store_code : '';    		
        if (!empty($postdata))
        {
            $filter['from'] = !empty($postdata['from']) ? $postdata['from'] : '';
            $filter['to'] = !empty($postdata['to']) ? $postdata['to'] : '';
            $filter['month'] = !empty($postdata['month']) ? $postdata['month'] : '';
            $filter['year'] = !empty($postdata['year']) ? $postdata['year'] : '';
            $filter['search_term'] = !empty($postdata['search_term']) ? $postdata['search_term'] : '';
            $filter['currency_id'] = !empty($postdata['currency_id']) ? $postdata['currency_id'] : '';
            $filter['wallet'] = !empty($postdata['wallet']) ? $postdata['wallet'] : '';            
            $submit = isset($postdata['submit']) ? $postdata['submit'] : '';
        }
		if (isset($postdata['transaction_type'])) {
			$filter['transaction_type'] = !empty($postdata['transaction_type']) ? $postdata['transaction_type'] : '';			
		}
		if (isset($postdata['filter'])) {			
			$filter['filter'] = !empty($postdata['filter']) ? $postdata['filter'] : '';
		}
        $wdata = array_merge($wdata, $filter);				
        $op['recordsTotal'] = $this->walletObj->transactions($wdata, true);				
        if (!empty($op['recordsTotal']) && $op['recordsTotal'] > 0)
        {
            $op['recordsFiltered'] = $op['recordsTotal']; //if no records in data in tables 0 records//
            $wdata['length'] = $op['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
            $wdata['page'] = $op['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
            $wdata['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($wdata['page'] - 1) * $wdata['length'];
            $op['move_next'] = (($wdata['start'] + $wdata['length']) < $op['recordsFiltered']) ? true : false;
            $wdata['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
            $wdata['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['data'] = $this->walletObj->transactions($wdata);
        }
        else
        {	           
			$op['status'] = $this->statusCode = $this->config->get('httperr.NO_DATA_FOUND');            
			$op['msg'] = 'Data not found';
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function transactionDetails ($id)
    {
        $op = $data = [];
        $data['id'] = $id;
        $data['account_id'] = $this->userSess->account_id;
		$data['account_type'] = $this->userSess->account_type;  
        $details = $this->walletObj->getTransactionDetail($data);
		//print_r($details);exit;
        if (!empty($details))
        {
            //$op['details'] = $details;
			$op['details'] = $details['res'];
            $op['statementline_id'] = $details['statementline_id'];
            $op['account_type'] = $this->userSess->account_type;
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            //$op['msg'] = trans('general.not_found', ['which'=>trans('general.label.transaction')]);
            $op['msg'] = 'Transactions not found.';
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
}
