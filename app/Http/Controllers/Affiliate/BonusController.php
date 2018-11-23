<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;

use App\Models\Affiliate\Package;
use App\Models\Affiliate\Bonus;
use App\Models\Affiliate\AffModel;

class BonusController extends AffBaseController {
    
    //private $userObj = '';
    
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->packageObj = new Package();
		$this->bonusObj = new Bonus();
    }	
	public function referral_bonus()
	{
		$data = $wdata = $filter = array();
		$data['from'] = '';
        $data['to'] = '';
        $data['step'] = $data['level'] = $data['type_of_package'] = '';
		$data['account_id']=$this->userSess->account_id;
		$postdata = $this->request->all();	
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['type_of_package'] = $this->request->has('type_of_package') ? $this->request->get('type_of_package') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
        if (\Request::ajax())
        {  
			
			$wdata = array_merge($data, $filter); 
			$wdata['count'] = true;
			$ajaxdata['recordsTotal'] = $this->bonusObj->referral_bonus_details($data['account_id'],array_merge($wdata,$filter));
			//print_r($ajaxdata['recordsTotal'] ); exit;
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
			$ajaxdata['recordsFiltered'] = 0;
			$ajaxdata['data'] = array();
			if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
			{
				$ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal']; 
				$wdata['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;	
				$wdata['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
				if (isset($postdata['order']))
					{
					$wdata['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
					$wdata['order'] = $postdata['order'][0]['dir'];
					}
				unset($wdata['count']);   
				$ajaxdata['data'] = $this->bonusObj->referral_bonus_details($data['account_id'],$wdata);
				if(!empty($ajaxdata['data'])){  
					array_walk($ajaxdata['data'],function(&$refData){
						$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
						switch($refData->status)
						{
							case 0:
									$refData->status_label="<label class='label label-danger'>".trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 1:
									$refData->status_label="<label class='label label-success'>".trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 4:
									$refData->status_label="<label class='label label-info'>".trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 5:
									$refData->status_label="<label class='label label-info'>".trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
						}
					});
				}
		    }
			return $this->response->json($ajaxdata);
        }
		else if(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
			$pdata['print_data']  = $this->bonusObj->referral_bonus_details($data['account_id'],array_merge($wdata,$filter));
			if(!empty($pdata['print_data'])){
				array_walk($pdata['print_data'],function(&$refData){
					$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
					$refData->status_name = trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status);
				});
			}
            return view('affiliate.bonus.referral_bonus_print', $pdata);
		}
		else if(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
		{
			$epdata['export_data']=  $this->bonusObj->referral_bonus_details($data['account_id'],array_merge($wdata,$filter));
			if(!empty($epdata['export_data'])){
				array_walk($epdata['export_data'],function(&$refData){
					$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
					$refData->status_name = trans('affiliate/bonus/referral_bonus.status.lang_'.$refData->status);
				});
				}
            $output = view('affiliate.bonus.referral_bonus_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=Bonus_referrals_list_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return \Response::make($output, 200, $headers);
		}
		else
		{
			$data['package_list']=$this->packageObj->package_list();
			return view('affiliate.bonus.referral_bonus',$data);
		}
	}
	
	public function leadership_bonus(){
		$data = array();
		return view('affiliate.bonus.leadership_bonus',$data);
	}
	
	public function team_bonus(){
		$data = array();
		return view('affiliate.bonus.team_bonus',$data);
	}
	
	public function faststart_bonus(){
		$data = array();
		return view('affiliate.bonus.faststart_bonus',$data);
	}	

	public function leadership_depth(){                          
		$data = array();
		return view('affiliate.bonus.leadership_depth_bonus',$data);
	}
	
	public function car_bonus(){
		$data = array();
		return view('affiliate.bonus.car_bonus',$data);
	}
	
	public function ambassador_bonus(){
		$data = array();
		return view('affiliate.bonus.ambassador_bonus',$data);
	}
}