<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Admin\AdminFinance;
use App\Models\BaseModel;
use App\Models\Admin\Member;

use View;
use Input;
use Config;
use Response;
use Request;
use URL;
use Lang;
use Session;
class AdminFinanceController extends AdminController
{

    public $request;

    public function __construct ()
    {
        parent::__construct();
        $this->financeObj = new AdminFinance();
        $this->baseObj = new BaseModel();
        $this->memberObj = new Member();
    }

	
	public function sample(){
		
		
		
 print_R($this->userSess); die;
		
	}
    public function merchant_finance ($type = null, $mrcode = null)
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->system_role_id;
            $result = $this->financeObj->add_fund_merchant($postdata);
            if (!empty($result))
            {
                $op['status'] = 'ok';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['type'] = $this->config->get('constants.TRANS_TYPE.'.strtoupper($type));
            $data['mrcode'] = $mrcode;
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
	        $data['settings'] = $this->financeObj->fund_transfer_settings();
	        return view('admin.finance.merchant_credit_debit', $data);
        }
    }

    public function find_merchant ()
    {
        $postdata = $this->request->all();
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
            $merchant = $this->baseObj->get_merchant_details($postdata);
            if (!empty($merchant))
            {
                $balance = $this->financeObj->wallet_balance(['account_id'=>$merchant->account_id]);
                foreach ($balance as $bal)
                {
                    if (!empty($bal->currency_id))
                    {
                        $bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
                    }
                }
                $op['status'] = 'ok';
                $op['merchant'] = $merchant;
                $op['balance'] = $bal_arr;
                $this->status_code = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = 'err';
                $this->status_code = $this->config->get('httperr.SUCCESS');
                $op['merchant'] = trans('admin/finance.merchant_not_found');
            }
        }
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }

    public function member_finance ($type = null, $member = null)
    {
		
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {
			/* echo "Dfdsf"; 
			print_r($this->userSess);die; */
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->account_type_id;
            $result = $this->financeObj->add_fund_member($postdata);

            if (!empty($result))
            {
                $op['status'] = 'ok';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
            $data['settings'] = $this->financeObj->fund_transfer_settings();
            $data['type'] = $this->config->get('constants.TRANS_TYPE.'.strtoupper($type));
            $data['member'] = $member;
	        return view('admin.finance.member_credit_debit', $data);
        }
    }

    public function find_member ()
    {   
        $postdata = $this->request->all(); 
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
            $userdetails = $this->memberObj->get_member_details($postdata);
	
			if (!empty($userdetails))
            {
                $balance = $this->financeObj->wallet_balance(['account_id'=>$userdetails->account_id]);
                foreach ($balance as $bal)
                {
                    if (!empty($bal->currency_id))
                    {
                        $bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
                    }
                }
                $op['status'] = 'ok';
                $op['userdetails'] = $userdetails;
                $op['balance'] = $bal_arr;
			
                $this->status_code = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = 'err';
                $this->status_code = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.member_not_found');
            }
        }
        return $this->response->json($op, $this->status_code, $this->headerss,$this->options);
    }

    public function dsa_finance ()
    {
        $postdata = $this->request->all();
        $op = array();
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        if ($this->request->isMethod('post'))
        {
            $postdata['admin_id'] = $this->userSess->account_id;
            $postdata['admin_role_id'] = $this->userSess->system_role_id;
            $result = $this->financeObj->add_fund_dsa($postdata);
            if (!empty($result))
            {
                $op['status'] = 'ok';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = $result;
            }
            else
            {
                $op['status'] = 'err';
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.fund_transfer_fail');
            }
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            $data = array();
            $data['wallets'] = $this->financeObj->get_wallets();
            $data['currencies'] = $this->baseObj->get_currencies();
            $data['settings'] = $this->financeObj->fund_transfer_settings();
            return view('admin.finance.dsa_credit_debit', $data);
        }
    }

    public function find_dsa_acc ()
    {
        $postdata = $this->request->all();
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        $op = array();
        $bal_arr = array();
        if (!empty($postdata))
        {
            $userdetails = $this->baseObj->get_dsa_details($postdata);
            if (!empty($userdetails))
            {
                $balance = $this->financeObj->wallet_balance(['account_id'=>$userdetails->account_id]);
                foreach ($balance as $bal)
                {
                    if (!empty($bal->currency_id))
                    {
                        $bal_arr[$bal->wallet_id][$bal->currency_id] = $bal->current_balance;
                    }
                }
                $op['status'] = 'ok';
                $op['userdetails'] = $userdetails;
                $op['balance'] = $bal_arr;
                $this->status_code = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = 'err';
                $this->status_code = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('admin/finance.dsa_not_found');
            }
        }

        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }

    public function fund_transfer_history ()
    {
        $postdata = $this->request->all();
        $data = $filter = $ajaxdata = [];
        $ajaxdata['data'] = [];
        if ($postdata)
        {
            $filter['from'] = isset($postdata['from_date']) ? $postdata['from_date'] : null;
            $filter['to'] = isset($postdata['to_date']) ? $postdata['to_date'] : null;
            $filter['terms'] = isset($postdata['terms']) ? $postdata['terms'] : null;
           
        }
        if ($this->request->isMethod('post'))
        {
			$data['sysrole'] =  isset($postdata['sysrole']) ? $postdata['sysrole'] : '';
	        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->fund_transfer_history($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                if (!empty(array_filter($filter)))
                {
                    $data = array_merge($data, $filter);
			        $ajaxdata['recordsFiltered'] = $this->financeObj->fund_transfer_history($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['length'] = $ajaxdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                    $data['page'] = $ajaxdata['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                    $data['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($data['page'] - 1) * $data['length'];
                    $ajaxdata['move_next'] = (($data['start'] + $data['length']) < $ajaxdata['recordsFiltered']) ? true : false;
                    $data['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
                    $data['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';
		            $ajaxdata['data'] = $this->financeObj->fund_transfer_history($data);
                }
            }
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['url'] = url('/admin');
            return $this->response->json($ajaxdata, 200, $this->headerss, $this->options);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['tickets'] = $this->financeObj->fund_transfer_history($data);
            $output = View::make($this->view_path.'tickets.member_tickets_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Member_Tickets_List_'.showUTZ("d_M_Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['tickets'] = $this->financeObj->fund_transfer_history($data);
            return View::make($this->view_path.'tickets.member_tickets_print', $res);
        }
        else
        {
			$data['sys_roles'] = $this->financeObj->get_roles();
	        return view('admin.finance.fund_transfer_history', $data);
        }
    }
	
	public function admin_fund_transfer_history(){
		
		$postdata = $this->request->all();
        $data = $filter = $ajaxdata = [];
        $ajaxdata['data'] = [];
        if ($postdata)
        {
            $filter['from'] = isset($postdata['from_date']) ? $postdata['from_date'] : null;
            $filter['to'] = isset($postdata['to_date']) ? $postdata['to_date'] : null;
            $filter['terms'] = isset($postdata['terms']) ? $postdata['terms'] : null;
           
        }
        if ($this->request->isMethod('post'))
        {
			$data['sysrole'] =  isset($postdata['sysrole']) ? $postdata['sysrole'] : '';
	        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->admin_fund_transfer_history($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                if (!empty(array_filter($filter)))
                {
                    $data = array_merge($data, $filter);
			        $ajaxdata['recordsFiltered'] = $this->financeObj->admin_fund_transfer_history($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['length'] = $ajaxdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                    $data['page'] = $ajaxdata['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                    $data['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($data['page'] - 1) * $data['length'];
                    $ajaxdata['move_next'] = (($data['start'] + $data['length']) < $ajaxdata['recordsFiltered']) ? true : false;
                    $data['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
                    $data['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';
                    $ajaxdata['data'] = $this->financeObj->admin_fund_transfer_history($data);
                }
            }
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['url'] = url('/admin');
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['tickets'] = $this->financeObj->admin_fund_transfer_history($data);
            $output = View::make($this->view_path.'tickets.member_tickets_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Member_Tickets_List_'.showUTZ("d_M_Y").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['tickets'] = $this->financeObj->admin_fund_transfer_history($data);
            return View::make($this->view_path.'tickets.member_tickets_print', $res);
        }
        else
        {
			$data['sys_roles'] = $this->financeObj->get_roles();
	        return view('admin.finance.admin_transfer_history', $data);
        }
	}

    public function userTransactionLog ($for = null, $account_id = null)
    {
        return $this->transactionLog($for, $account_id, $this->config->get('constants.ACCOUNT.TYPE.USER'));
    }

    public function retailerTransactionLog ($for = null, $account_id = null)
    {
        return $this->transactionLog($for, $account_id, $this->config->get('constants.ACCOUNT.TYPE.MERCHANT'));
    }

    public function transactionLog ($for = null, $account_id = null, $system_role_id = null)
    {
        $data = $ajaxdata = [];
        $data['breadcrumb'] = [['icon'=>'fa fa-dashboard', 'title'=>trans('admin/finance.management')]];
        $data['title'] = trans('admin/finance.transaction_log');
        $data['account_id'] = $account_id;
		
        if (!is_null($system_role_id))
        {
            $data['system_role_id'] = $system_role_id;
            if ($system_role_id == $this->config->get('constants.ACCOUNT.TYPE.USER'))
            {
                $data['route'] = route('admin.member.transaction-log.list');
                $data['title'] = trans('general.menu.customer_transaction_log');
                $data['breadcrumb'] = [['icon'=>'fa fa-dashboard', 'title'=>trans('general.menu.member_management')]];
            }
        }else{
			$data['system_role_id'] = $this->config->get('constants.ACCOUNT.TYPE.USER');
		}
        $ajaxdata['data'] = [];
        $postdata = $this->request->except(['from_date', 'to_date', 'terms']);
        $filter = $this->request->only(['from_date', 'to_date', 'terms']);
        $for = ($for == null && isset($postdata['submit']) && !empty($postdata['submit'])) ? $postdata['submit'] : $for;
        if ($this->request->isMethod('post'))
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->transaction_log($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            if (!empty($ajaxdata['recordsFiltered']))
            {
                if (!empty(array_filter($filter)))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->financeObj->transaction_log($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    if (isset($postdata['start']))
                    {
                        $data['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                        $data['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                    }
                    $ajaxdata['data'] = $this->financeObj->transaction_log($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if ($for == 'Export' || $for == 'export')
        {
            $coulumns = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'format'=>'long-date', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'amount', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'handleamt', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'paidamt', 'format'=>'currency-colored', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'format'=>'status', 'data'=>['color'=>'statusCls'], 'align'=>'center'],
            ];
            $exp = CommonLib::export(trans('admin/finance.transaction_log'), $coulumns, $this->financeObj->transaction_log($data));
            return $this->response->make($exp->body, 200, $exp->headers);
        }
        else if ($for == 'Print' || $for == 'print')
        {
            $ajaxdata['title'] = trans('admin/finance.transaction_log');
            $ajaxdata['columns'] = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'famount', 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'fhandleamt', 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'fpaidamt', 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'align'=>'center'],
            ];
            $ajaxdata['data'] = $this->financeObj->transaction_log($data);
            return \View::make('print-layout', $ajaxdata);
        }
        else
        {
            return view('admin.finance.transaction_log', $data);
        }
    }

    public function transactionDetails ($id)
    {
        $op = $data = [];
        $data['id'] = $id;
        $details = $this->financeObj->getTransactionDetail($data);
        if (!empty($details))
        {
            $op['details'] = $details;
            $op['stauts'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.label.transaction')]);
            $op['stauts'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function admin_credit_debit_history ()
    {	
        $data = $ajaxdata = $filter = [];
        $postdata = $this->request->except(['from', 'to', 'terms', 'trans_type']);
	
        $filter = $this->request->only(['from', 'to', 'terms', 'trans_type']);
	
       if ($this->request->ajax())
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->admin_credit_debit_history($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['data'] = [];
            if (!empty($ajaxdata['recordsFiltered']))
            {
				$data['trans_type'] = isset($postdata['trans_type'])?$postdata['trans_type']:'';
                $filter = array_filter($filter);
		        if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
				
                    $ajaxdata['recordsFiltered'] = $this->financeObj->admin_credit_debit_history($data, true);
                }
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                //$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
               //$data['order'] = $postdata['order'][0]['dir'];
		        $ajaxdata['data'] = $this->financeObj->admin_credit_debit_history($data);
            }
            return $this->response->json($ajaxdata, 200, $this->headerss, $this->options);
        }
	
      elseif(isset($postdata['exportbtn']) && $postdata['exportbtn']=='Export')
        {
		$epdata['export_data']= $this->financeObj->admin_credit_debit_history($data);
            $output = view('admin.finance.admin_credit_debit_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=admin_debit_report' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
        }
        elseif(isset($postdata['printbtn']) && $postdata['printbtn']=='Print') 
        {
			$pdata['print_data']= $this->financeObj->admin_credit_debit_history($data);
			return view('admin.finance.admin_credit_debit_print', $pdata);
        }  
		
		
		
        else
        {
            return view('admin.finance.admin_credit_debit_trans', $data);
        }
    }

    /* Order payments */

    public function online_payments ()
    {
        $data = $ajaxdata = $filter = [];
        $postdata = $this->request->except(['from_date', 'to_date', 'terms', 'status', 'purpose']);
        $filter = $this->request->only(['from_date', 'to_date', 'terms', 'status', 'purpose']);
        $filter['terms'] = trim($filter['terms']);
        $for = (isset($postdata['submit']) && !empty($postdata['submit'])) ? $postdata['submit'] : '';
        if ($this->request->isMethod('post'))
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->financeObj->online_payments($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['data'] = [];
            if ($ajaxdata['recordsFiltered'])
            {
                $filter = array_filter($filter, function($field)
                {
                    return !($field == "");
                });
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->financeObj->online_payments($data, true);
                }
                if ($ajaxdata['recordsFiltered'])
                {
                    if (isset($postdata['start']))
                    {
                        $data['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
                        $data['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
                    }
                    $ajaxdata['data'] = $this->financeObj->online_payments($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        else if ($for == 'Export' || $for == 'export')
        {
            $coulumns = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'format'=>'long-date', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'amount', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'handleamt', 'format'=>'currency', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'paidamt', 'format'=>'currency-colored', 'data'=>['decimal'=>'decimal_places', 'code'=>'currency_code', 'symbol'=>'currency_symbol'], 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'format'=>'status', 'data'=>['color'=>'statusCls'], 'align'=>'center'],
            ];
            $exp = CommonLib::export(trans('admin/finance.transaction_log'), $coulumns, $this->financeObj->transaction_log($data));
            return $this->response->make($exp->body, 200, $exp->headers);
        }
        else if ($for == 'Print' || $for == 'print')
        {
            $ajaxdata['title'] = trans('admin/finance.transaction_log');
            $ajaxdata['columns'] = [
                ['title'=>trans('admin/finance.report.date'), 'name'=>'created_on', 'align'=>'center'],
                ['title'=>'Full name', 'name'=>'fullname'],
                ['title'=>'Description', 'render'=>['format'=>':statementline (:remark)<br/>#:transaction_id<br/><b>Wallet: </b>:wallet', 'fields'=>['statementline', 'remark', 'transaction_id', 'wallet']]],
                ['title'=>'Amt', 'name'=>'famount', 'align'=>'right'],
                ['title'=>'Handle amt', 'name'=>'fhandleamt', 'align'=>'right'],
                ['title'=>'Paid Amt', 'name'=>'fpaidamt', 'align'=>'right'],
                ['title'=>'Trans type', 'name'=>'trans_type', 'align'=>'center'],
                ['title'=>'Status', 'name'=>'status', 'align'=>'center'],
            ];
            $ajaxdata['data'] = $this->financeObj->online_payments($data);
            return \View::make('print-layout', $ajaxdata);
        }
        else
        {
            $data['status'] = trans('db_trans.payment_gateway_response.status');
            $data['purpose'] = trans('admin/finance.purpose');
            return view('admin.finance.online_payments', $data);
        }
    }

    /* Order payments Details */

    public function online_payments_details ($id = null)
    {
        $postdata = $this->request->all();
        $data['gateway_responce'] = '';
        if ($id != null)
        {
            $postdata['id'] = $id;
        }
        $data['details'] = $this->financeObj->getway_payment_details($postdata);
        if (!empty($data['details']) && !empty($data['details']->response))
        {
            $data['gateway_responce'] = json_decode($data['details']->response);
        }
        $a = view('admin.finance.online_payment_detail', $data)->render();
        $op['content'] = $a;
        $op['status'] = 'ok';
        return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
    }

    public function updateStatus ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->update_payment_status($postdata);

            return $this->response->json(['msg'=>'Payment updated successfully', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
        }
        return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
    }

    public function refundPayment ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->refundPayment($postdata);
            if (!empty($result))
            {
                return $this->response->json(['msg'=>'Payment refunded successfully', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
            }
            else
            {
                return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
            }
        }
    }

    public function confirmPayment ($id = null)
    {
        if ($id != null)
        {
            $postdata['id'] = $id;
            $result = $this->financeObj->confirmPayment($postdata);
            if (!empty($result))
            {
                return $this->response->json(['msg'=>'Payment successfully added', 'status'=>'ok'], $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
            }
            else
            {
                return $this->response->json(['msg'=>'Something went wrong', 'status'=>'err'], $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
            }
        }
    }

}
