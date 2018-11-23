<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\Report;
use App\Models\Seller\Supplier;
use Config;
use Lang;
use Redirect;
use Input;
use App\Helpers\ShoppingPortal;

class SupplierReportController extends SupplierBaseController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->report = new Report();
		$this->suppObj = new Supplier();
    }	
	
	public function OrdersList ($type = null)
    {
		$ajaxdata = $data = $filter = array();
		$data['supplier_id'] = $this->userSess->supplier_id;
        $data['account_type_id'] = $this->userSess->account_type_id;
		if(isset($this->userSess->store_id))
        {
            $data['store_id']  = $this->userSess->store_id;
        }	          
		if (\Request::ajax())
        {
			$types = trans('seller/reports.orders_list'); 			
			$post = $this->request->all();
			$post['page'] 		   = !empty($type) && isset($post['page']) ? $post['page'] : 1;
			$filter['from'] 	   = isset($post['from']) ? $post['from'] : null;
			$filter['to'] 		   = isset($post['to']) ? $post['to'] : null;
			$filter['search_term'] = isset($post['search_text']) ? $post['search_text'] : null;			
			$filter['pay_through'] = isset($post['pay_through']) ? $post['pay_through'] : null;			
			
				if (isset($post['draw']))
				{
					$ajaxdata['draw']     = $post['draw'];
				}
				//$data['type']             = $type;				
				$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->report->getOrdersList($data, true);				
				$ajaxdata['data']         = [];
				$ajaxdata['next_page']    = 0;
				if ($ajaxdata['recordsTotal'])
				{
					$filter = array_filter($filter);
					if (!empty($filter))
					{
						$data = array_merge($data, $filter);
						$ajaxdata['recordsFiltered'] = $this->report->getOrdersList($data, true);
					}
					if ($ajaxdata['recordsFiltered'])
					{
						$data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
						$data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : (($post['page'] - 1) * $data['length']);
						//$data['orderby'] = isset($post['columns']) ? $post['columns'][$post['order'][0]['column']]['name'] : (isset($post['orderby']) ? $post['orderby'] : null);
						//$data['order'] = isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : (isset($post['order']) ? $post['order'] : null);
						$ajaxdata['next_page'] = $ajaxdata['recordsFiltered'] > ($data['start'] + $data['length'] + 1) ? $post['page'] + 1 : 0;
						$ajaxdata['data'] = $this->report->getOrdersList($data);
					}
				}
			
            return \Response::json($ajaxdata);
        }
        else
        {			
            return View('seller.reports.instore.orders', $data);
        }
    }
	
	public function orderDetail ($order_code)
    {
        $op = $data = [];
		if (\Request::ajax())
        {			
			$data['supplier_id'] = $this->userSess->supplier_id;
			//$data['refund_days'] = $this->userSess->refund_days;
			//$data['is_refundable'] = $this->userSess->is_refundable;
			$data['refund_days'] = 10;
			$data['is_refundable'] = 1;
			$data['account_type_id'] = $this->userSess->account_type_id;			
			if (isset($this->userSess->store_id))
			{
				$data['store_id'] = $this->userSess->store_id;
			}
			$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$data['order_code'] = $order_code;
			$order = $this->report->getOrderDetails($data);
			if (!empty($order))
			{
				$op['details'] = $order;
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['msg'] = trans('general.not_found', ['which'=>trans('general.order.label')]);
				$op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
			}
			return \Response::make($op, $this->statusCode, $this->headers,  $this->options);			
		}
		else 
		{
			$data['order_code'] = $order_code;
			return view('seller.reports.instore.order_detail', $data);
		}
    }
	
	public function TransactionList ()
    {
        $op = $data = $filter = array();
		if (\Request::ajax())
        {
			$data['supplier_id'] = $this->userSess->supplier_id;
			$data['account_id'] = $this->userSess->account_id;
			$data['currency_id'] = $this->userSess->currency_id;
			$data['account_type_id'] = $this->userSess->account_type_id;
			if (!empty($this->userSess->store_id))
			{
				$data['store_id'] = $this->userSess->store_id;
			} else {
				$data['store_id'] = $this->userSess->primary_store_id;
			}
			//print_r($data);exit;
			$post = $this->request->all();
			$filter['from'] = isset($post['from_date']) ? $post['from_date'] : null;
			$filter['to'] = isset($post['to_date']) ? $post['to_date'] : null;
			$filter['status'] = isset($post['status']) ? $post['status'] : null;
			$filter['search_term'] = isset($post['search_text']) ? $post['search_text'] : null;
			$filter['filterTerms'] = $this->request->has('filterTerms') ? $this->request->get('filterTerms') : '';
			$ajaxdata['draw'] = isset($post['draw']) ? $post['draw'] : 1;
			$data = array_merge($data, $filter);
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->report->getTransactionList($data, true);
			$ajaxdata['data'] = [];
			if ($ajaxdata['recordsTotal'])
			{
				if (!empty($filter))
				{
					$data = array_merge($data, $filter);
					$ajaxdata['recordsFiltered'] = $this->report->getTransactionList($data, true);
				}
				if ($ajaxdata['recordsFiltered'])
				{
					$data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
					$data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
					if ($post['order'])
					{
						$data['orderby'] = isset($post['columns']) ? $post['columns'][$post['order'][0]['column']]['name'] : (isset($post['orderby']) ? $post['orderby'] : null);
						$data['order'] = isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : (isset($post['order']) ? $post['order'] : null);
					}
					$ajaxdata['data'] = $this->report->getTransactionList($data);
					
				}
			}
			$ajaxdata['balance'] = $this->report->wallet_balance($data);
			$this->statusCode = 200;
			return $this->response->json($ajaxdata, $this->statusCode, $this->headers, $this->options);	
		} 
		else 
		{
			return View('seller.reports.instore.transactions', $data);
		}
    }
	
	public function TransactionDetails ($id)
    {
        $op = $data = [];
		if (\Request::ajax())
        {			
			//$data['supplier_id'] = $this->userSess->supplier_id;
			$data['account_id'] = $this->userSess->account_id;
			//$data['refund_days'] = $this->userSess->refund_days;
			//$data['is_refundable'] = $this->userSess->is_refundable;
			//$data['refund_days'] = 10;
			//$data['is_refundable'] = 1;
			//$data['account_type_id'] = $this->userSess->account_type_id;			
			/* if (isset($this->userSess->store_id))
			{
				$data['store_id'] = $this->userSess->store_id;
			}
			$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE'); */
			
			$data['id'] = $id;
			$details = $this->report->getTransactionDetails($data);
			//print_r($details);exit;
			if (!empty($details))
			{
				$op['details'] = $details['details'];
				$op['statementline_id'] = $details['statementline_id'];				
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['msg'] = 'Data not Found.';
				$op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
			}
			return \Response::make($op, $this->statusCode, $this->headers,  $this->options);			
		}
		else 
		{
			$data['order_code'] = $order_code;
			return view('seller.reports.instore.transaction_detail', $data);
		}
    }
	
	public function TransactionDetails_bk111018 ($order_code)
    {
        $op = $data = [];
		if (\Request::ajax())
        {			
			$data['supplier_id'] = $this->userSess->supplier_id;
			//$data['refund_days'] = $this->userSess->refund_days;
			//$data['is_refundable'] = $this->userSess->is_refundable;
			$data['refund_days'] = 10;
			$data['is_refundable'] = 1;
			$data['account_type_id'] = $this->userSess->account_type_id;			
			if (isset($this->userSess->store_id))
			{
				$data['store_id'] = $this->userSess->store_id;
			}
			$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$data['order_code'] = $order_code;
			$order = $this->report->getTransactionDetails($data);
			if (!empty($order))
			{
				$op['details'] = $order;
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['msg'] = trans('general.not_found', ['which'=>trans('general.order.label')]);
				$op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
			}
			return \Response::make($op, $this->statusCode, $this->headers,  $this->options);			
		}
		else 
		{
			$data['order_code'] = $order_code;
			return view('seller.reports.instore.transaction_detail', $data);
		}
    }	
	
	/* Seller Balance */
	public function wallet_balance ()
    {
		$op = $data = [];
		if (\Request::ajax())
        {		
			$data['supplier_id'] = $this->userSess->supplier_id;
			$data['store_id'] = $this->userSess->store_id;
			$data['account_id'] = $this->userSess->account_id;
			$data['account_type_id'] = $this->userSess->account_type_id;
			$data['currency_id'] = $this->userSess->currency_id;
			$currency = $this->report->get_currency($this->userSess->currency_id);
			$data['cur_code'] = '';
			if (!empty($currency))
			{
				$data['cur_code'] = $currency->currency;
			}			
			$result = $this->report->wallet_balance($data);
			$autopayout = $this->report->check_autopay($data);
			$op['autopayout'] = ($autopayout > 0) ? 1 : 0;
			$op['request_money'] = ($autopayout > 0 && $this->userSess->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER')) ? 1 : 0;
			if (!empty($result))
			{
				$op['wallet'] = $result;
				if (empty($wallet_code) && empty($currency_code))
				{
					$data['start'] = 0;
					$data['length'] = 4;
					$op['transactions'] = $this->report->transactions($data);
				}				 
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['msg'] = 'Datas not found';				 
				$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			}
			
			return \Response::make($op, $this->statusCode, $this->headers,  $this->options);			
		} else {
			//$data['account_id'] = $this->account_id;
			//$data['wallets'] = $this->report->get_wallet_balance_details($data);        	
			return View('seller.wallet.balance', $data);      
		}
    }
	
	
	
	
	
	
	

    public function transaction_log ()
    {
        $data = $filter = array();
        $post = Input::all();
        $data['account_id'] = $this->account_id;
        $submit = '';
        $filter['from'] = isset($post['from']) && !empty($post['from']) ? $post['from'] : '';
        $filter['to'] = isset($post['to']) && !empty($post['to']) ? $post['to'] : '';
        $filter['term'] = isset($post['term']) && !empty($post['term']) ? $post['term'] : '';
        $filter['ewallet_id'] = isset($post['ewallet_id']) && !empty($post['ewallet_id']) ? $post['ewallet_id'] : '';
        $filter['wallet_list'] = [];
        if (isset($post['submit']) && !empty($post['submit']))
        {
            $submit = $post['submit'];
        }
        if ($submit == 'Export')
        {
            $data['transaction_log'] = $this->report->getTransactionDetails($data);
            $output = View::make('admin.transaction_log.excel')
                    ->with('data', $data);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=TransactionLog_'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if ($submit == 'Print')
        {
            $data['transaction_log'] = $this->report->getTransactionDetails($data);
            return View('admin.transaction_log.print')
                            ->with('data', $data);
        }
        else
        {			
			$data['wallet_list'] = $this->report->get_wallet_list($data);			
            return View('seller.transaction.transaction_report', $data);
        }
    }

    public function payments ()
    {
        return View('seller.reports.supplier_payment_report');
    }
	
	
	
	public function bank_accounts ()
    {
		$data['account_id'] = $this->account_id;
        $data['wallets'] = $this->report->get_wallet_balance_details($data); 
		$data['account_types'] = $this->commonObj->getBankingAccountTypes();				
        $data['states'] = $this->commonObj->get_state_list(['country_id'=>$this->country_id]);		
        $data['fields'] = ShoppingPortal::getHTMLValidation('api.v1.seller.setup.store-banking');			
		$data['accounts'] = $this->suppObj->get_bank_accounts($this->supplier_id); 		
		//echo '<pre>';print_r($data['accounts']);exit;
		return View('seller.bank_accounts', $data);      
    }

}
