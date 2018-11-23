<?php
namespace App\Http\Controllers\Affiliate;
use App\Http\Controllers\AffBaseController;
use Request;
use Response;
use App\Models\Affiliate\AffiliateReports;
class AffiliatereportsController extends AffBaseController
{
    public function __construct()
	{
        parent::__construct();
		$this->affiliatereportObj    = new AffiliateReports();
    }
   public function faststart_bonus(){
		$data = $wdata = $filter = array();
		$data['from'] = '';
        $data['to'] = '';
        $data['step'] = $data['level'] = $data['type_of_package'] = '';
		$data['account_id']=$this->userSess->account_id;
		$postdata = $this->request->all();	
	
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	
        if (\Request::ajax())
        {  
			$wdata = array_merge($data, $filter); 
			$wdata['count'] = true;
			$ajaxdata['recordsTotal'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'],array_merge($wdata,$filter));
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
				$ajaxdata['data'] = $this->affiliatereportObj->faststart_bonus_details($data['account_id'],$wdata);
				if(!empty($ajaxdata['data'])){  
					array_walk($ajaxdata['data'],function(&$refData){
						$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
						switch($refData->status)
						{
							case 0:
									$refData->status_label="<label class='label label-danger'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 1:
									$refData->status_label="<label class='label label-success'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 4:
									$refData->status_label="<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
							case 5:
									$refData->status_label="<label class='label label-info'>".trans('user/bonus/referral_bonus.status.lang_'.$refData->status)."</label>";
									break;
						}
					});
				}
		    }
			return \Response::json($ajaxdata);
        }
		else if(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
	
			$pdata['print_data']  = $this->affiliatereportObj->faststart_bonus_details($data['account_id'],array_merge($wdata,$filter));
			if(!empty($pdata['print_data'])){
				array_walk($pdata['print_data'],function(&$refData){
					$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
					$refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
				});
			}
            return view('affiliate.bonus.faststart_print', $pdata);
		}
		else if(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
		{
			$epdata['export_data']=  $this->affiliatereportObj->faststart_bonus_details($data['account_id'],array_merge($wdata,$filter));
			if(!empty($epdata['export_data'])){
				array_walk($epdata['export_data'],function(&$refData){
					$refData->created_date = date('d-M-Y H:i:s', strtotime($refData->created_date));
					$refData->status_name = trans('affiliate/bonus/faststart.status.lang_'.$refData->status);
				});
				}
            $output = view('affiliate.bonus.faststart_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=FastStart_Bonus_list_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return \Response::make($output, 200, $headers);
		}
		else
		{
			//$data['package_list']=$this->packageObj->package_list();
		    return view('affiliate.bonus.faststart_bonus'); 
		}		
	}	
   	public function team_bonus() {
	
		$data = $wdata = $filter = array();
		$data['account_id'] = $this->userSess->account_id;
		$data['currency_id'] = $this->userSess->currency_id;
		$post = $this->request->all();
	
       if (!empty($post))   
        {			
	    $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		//$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
	    }  
        if (\Request::ajax())
        {   
		    $wdata['count'] = true;
		    $data = array_merge($data, $filter);
		   //print_r($data); die;
		    $ajaxdata['recordsTotal'] = $this->affiliatereportObj->get_teambonus_details($data['account_id'],$data);//total records//
			
		    $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
		    $ajaxdata['recordsFiltered'] = 0;
		    $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
		    {
			    $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->get_teambonus_details($data['account_id'],$data); 
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
			    $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
					{
					$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$wdata['order'] = $post['order'][0]['dir'];
					}
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->affiliatereportObj->get_teambonus_details($data['account_id'],$data);
				}
			
		 
        return \Response::json($ajaxdata);
           }
	 elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$pdata['print_data']= $this->affiliatereportObj->get_teambonus_details($data['account_id'],array_merge($wdata,$filter));
			if($pdata['print_data'])
			{
				array_walk($pdata['print_data'], function(&$data)
				{
					/* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
				});
			}	
            return view('affiliate.bonus.team_bonus_print', $pdata);
		}
	     elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$epdata['export_data']= $this->affiliatereportObj->get_teambonus_details($data['account_id'],array_merge($wdata,$filter));
			if($epdata['export_data'])
			{
				array_walk($epdata['export_data'], function(&$data)
				{
					/* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
				});
			}	
            $output = view('affiliate.bonus.team_bonus_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=team_bonus' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
		}  
        else
		{  
          return view('affiliate.bonus.team_bonus',$data);
		}
		
	}
		public function personal_commission(){
	
        $data = $wdata = $filter = array();
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();
        if (!empty($post))   
        {			
	    $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		//$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
	    }  
        if (\Request::ajax()){
		    $wdata['count'] = true;
		    $data = array_merge($data, $filter);
		    $ajaxdata['recordsTotal'] = $this->affiliatereportObj->personal_commission($this->userSess->account_id,$data);//total records//
			
		    $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
		    $ajaxdata['recordsFiltered'] = 0;
		    $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
		    {
			    $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->personal_commission($this->userSess->account_id,$data); 
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
			    $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
					{
					$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$wdata['order'] = $post['order'][0]['dir'];
					}
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->affiliatereportObj->personal_commission($data['account_id'],array_merge($wdata,$filter)); }
			
		 
        return \Response::json($ajaxdata);
           }
		  elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$pdata['print_data']= $this->affiliatereportObj->personal_commission($data['account_id'],array_merge($wdata,$filter));
			if($pdata['print_data'])
			{
				array_walk($pdata['print_data'], function(&$data)
				{ 
				 /* $data->confirmed_date = showUTZ($data->confirm_date, 'd-M-Y H:i:s');
				 $data->created_on = showUTZ($data->created_on, 'd-M-Y H:i:s'); */
				});
			}	
            return view('affiliate.bonus.personal_commission_print', $pdata);
		}
	 elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$epdata['export_data']= $this->affiliatereportObj->personal_commission($data['account_id'],array_merge($wdata,$filter));
			if($epdata['export_data'])
			{
				array_walk($epdata['export_data'], function(&$data)
				{
					/* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
				});
			}	
            $output = view('affiliate.bonus.personal_commission_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=leadership_bonus' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
		}
	 else{  
          return view('affiliate.bonus.personal_commission_bonus',$data);
		}
	}
	public function leadership_bonus(){
        $data = $wdata = $filter = array();
		$data['account_id'] = $this->userSess->account_id;
		$data['currency_id'] = $this->userSess->currency_id;
		$post = $this->request->all();
	
         if (!empty($post))   
        {			
	    $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		//$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
	    }  
        if (\Request::ajax()){
		    $wdata['count'] = true;
		    $data = array_merge($data, $filter);
		    $ajaxdata['recordsTotal'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'],$data);//total records//
			
		    $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
		    $ajaxdata['recordsFiltered'] = 0;
		    $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
		    {
			    $ajaxdata['recordsFiltered'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'],$data); 
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
			    $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
					{
					$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$wdata['order'] = $post['order'][0]['dir'];
					}
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->affiliatereportObj->get_leadership_bonus($data['account_id'],array_merge($wdata,$filter)); }
			
		 
        return \Response::json($ajaxdata);
           }
		  elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$pdata['print_data']= $this->affiliatereportObj->get_teambonus_details($data['account_id'],array_merge($wdata,$filter));
			if($pdata['print_data'])
			{
				array_walk($pdata['print_data'], function(&$data)
				{ 
				 /* $data->confirmed_date = showUTZ($data->confirm_date, 'd-M-Y H:i:s');
				 $data->created_on = showUTZ($data->created_on, 'd-M-Y H:i:s'); */
				});
			}	
            return view('affiliate.bonus.leadership_bonus_print', $pdata);
		}
	 elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$epdata['export_data']= $this->affiliatereportObj->get_teambonus_details($data['account_id'],array_merge($wdata,$filter));
			if($epdata['export_data'])
			{
				array_walk($epdata['export_data'], function(&$data)
				{
					/* $data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on)); */
				});
			}	
            $output = view('affiliate.bonus.leadership_bonus_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=leadership_bonus' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
		}
	 else{  
          return view('affiliate.bonus.leadership_bonus',$data);
		}
	}
	
	public function team_commission(){
	$account_id = $this->userSess->account_id;
	$currency_id = $this->userSess->currency_id;
     $user_info= $this->affiliatereportObj->save_team_bonus($account_id,$currency_id);
    }
   public function leadership_bonus_commission(){
	$account_id = $this->userSess->account_id;
	$currency_id = $this->userSess->currency_id;
     $user_info= $this->affiliatereportObj->leadership_bonus_commission($account_id,$currency_id);
	}
	
}	    