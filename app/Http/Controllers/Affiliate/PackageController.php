<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Package;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Payments;
use App\Models\Affiliate\AffModel;


class PackageController extends AffBaseController
{
	private $packageObj = '';
	private $purchase_steps = '';
    public function __construct ()
    {
        parent::__construct();
		$this->packageObj = new Package;
		$this->walletObj = new Wallet;
		$this->paymentObj = new Payments;
		$this->affObj = new AffModel();
		
    }
	
	public function packages_browse($token='',$package=0){
		/*echo '<pre>';
		$userSess = '{"account_id":179,"uname":"dsvbdirect","full_name":"dsvb direct","email":"dsvbdirect@virob.com","is_affiliate":1,"can_sponsor":0,"account_type_name":"Customer","account_type_id":2,"currency_id":2,"language_id":0,"country_id":77,"currency_code":"INR","is_mobile_verified":0,"is_email_verified":0,"is_verified":0,"mobile":null,"phonecode":"+91","profile_image":"profile_image_blank.jpg"}';
		$pack_details = '{"package_id":2,"package_code":"1002","package_level":1,"is_refundable":1,"refundable_days":10,"expire_days":30,"package_image":"1002.jpg","is_upgradable":1,"is_adjustment_package":0,"instant_benefit_credit":0,"currency_id":2,"price":750,"package_qv":250,"weekly_capping_qv":1000,"shopping_points":5000,"currency_code":"INR","package_name":"Basic","description":null,"package_image_url":"https:\/\/localhost\/dsvb_portal\/1002.jpg"}';
		$userSess = json_decode($userSess);
		$pack_details = json_decode($pack_details);
		$this->affObj->updateLineage($userSess,$pack_details);
		die;*/		
		$data = [];
	   	$data['packages'] = $this->packageObj->get_packages(['currency_id'=>$this->userSess->currency_id]);
		$data['purchase_paymodes'] = $this->packageObj->purchase_paymodes();
		return view('affiliate.package.package_browse',$data);
	}
	
	public function paymode_select($type=''){				
		$op = [];		
		$data = [];
		if($type==''){
		 	$this->statusCode = $this->config->get('httperr.SUCCESS');			
			$purchase_paymodes = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE')]);	
			if(!empty($purchase_paymodes)){
				array_walk($purchase_paymodes,function(&$pg,$key){
					$pg->url = route('aff.package.paymodeinfo',['type'=>$pg->payment_type_id]);
					$pg->icon = asset($pg->image_name);
				});				
				$op['purchase_paymodes'] = $purchase_paymodes;			
				$op['status'] = $this->config->get('httperr.SUCCESS');					
			} else {
				$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op = ['msg'=>'Service not available','msgtype'=>'danger','status'=>$this->statusCode];
			}
		}			
		else if($type>0){							
			if($type==$this->config->get('constants.PAYMENT_TYPES.WALLET')) {
				$data['walletbal'] = $this->walletObj->get_user_balance(0,['account_id'=>$this->userSess->account_id],$this->config->get('constants.WALLETS.PW'),$this->userSess->currency_id,$this->config->get('constants.WALLET_PURPOSE.PURCHASE'));
				$op['uwdata'] = is_array($data['walletbal'])? $data['walletbal']: array($data['walletbal']);						
				$op['template'] = view('affiliate.package.package_by_wallet',$data)->render();
				$op['status'] = $this->config->get('httperr.SUCCESS');
				$this->statusCode = $this->config->get('httperr.SUCCESS');
			}				
		}
		else {
			$this->statusCode = $this->config->get('httperr.NOT_FOUND');
			$op = ['msg'=>'Package not found','msgtype'=>'danger','status'=>200];					
		}	
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	public function purchase_confirm(){			
		$data = array();
		$postdata = $this->request->all(); 
		$postdata['userSess'] = $this->userSess;
		$op = $this->packageObj->doPurchase($postdata);		
		if($op['status']==200){
			$this->statusCode = 200;
		}
		return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}
	
	
	public function my_packages(){
	
		$data = $filter = array();
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();        
        if (\Request::ajax())
        { 
			if (!empty($post))
			{			
				$filter['from'] = !empty($post['from']) ? $post['from'] : '';
				$filter['to'] = !empty($post['to']) ? $post['to'] : '';
				$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
				$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
				$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
				$submit = isset($post['submit']) ? $post['submit'] : '';
			}
			$data['count'] = true;
			$data = array_merge($data, $filter);
            $ajaxdata['recordsTotal'] = $this->packageObj->get_mypackage($data);
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
				$ajaxdata['data'] = $this->packageObj->get_mypackage($data);
            }
            $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
        }
		return view('affiliate.package.my_packages',$data);
	}
	
	public function upgrade_history(){
		$data = array();
		return view('affiliate.package.upgrade_history',$data);
	}
}