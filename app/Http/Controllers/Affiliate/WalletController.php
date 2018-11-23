<?php

namespace App\Http\Controllers\Affiliate;

use App\Models\Affiliate\Wallet;

class WalletController extends AffBaseController
{

    public function __construct ()
    {
        parent::__construct();
		$this->walletObj = new Wallet;
    }
	
	public function my_wallet(){
		$data = array();
		$filter['account_id'] = $this->userSess->account_id;
		$filter['currency_id'] = $this->userSess->currency_id;
		$balInfo = $this->walletObj->my_wallets($filter);
		 
		 if($balInfo){			
				array_walk($balInfo, function(&$balInfos)
				{
					$balInfos->current_balance =$balInfos->currency_symbol .' '.number_format($balInfos->current_balance, \AppService::decimal_places($balInfos->current_balance), '.', ',').' '.$balInfos->currency_code;
					$balInfos->tot_credit =$balInfos->currency_symbol .' '.number_format($balInfos->tot_credit, \AppService::decimal_places($balInfos->tot_credit), '.', ',').' '.$balInfos->currency_code;
					$balInfos->tot_debit =$balInfos->currency_symbol .' '.number_format($balInfos->tot_debit, \AppService::decimal_places($balInfos->tot_debit), '.', ',').' '.$balInfos->currency_code;
				});
			}
		 $data['balInfo']=$balInfo;
		//echo '<pre>';print_r($data);exit;
		return view('affiliate.wallet.mywallet',$data);
	}
	
	public function transactions(){
		$data = $filter = array(); 
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();
		$filter['account_id'] = $this->userSess->account_id;   //variable creation //
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = !empty($post['from']) ? $post['from'] : '';
			$filter['to'] = !empty($post['to']) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
			$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
			$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
			
        }
        if (\Request::ajax())         //checks if call in ajax
        {          
			$data['count'] = true;
			$data = array_merge($data, $filter);  
            $ajaxdata['recordsTotal'] = $this->walletObj->transactions($data);
			//print_r($data); exit;
			$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {	
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal']; 
                $data['start'] = !empty($post['start']) ? $post['start'] : 0;
				$data['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
				{
					$data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$data['order'] = $post['order'][0]['dir'];
				}
				unset($data['count']);                    				
				$ajaxdata['data'] = $this->walletObj->transactions($data);
				//print_r($ajaxdata['data']); exit;
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);    //json data call from table 
        }
        $data['wallet_list'] = $this->walletObj->get_all_wallet_list();	
		return view('affiliate.wallet.transactions',$data);
	}
	
	public function fundtransfer(){
		$data = array();
		return view('affiliate.wallet.fundtransfer',$data);
	}
	
	public function fundtransfer_history(){
		$data = array();
		return view('affiliate.wallet.fundtransfer_history',$data);
	}
}