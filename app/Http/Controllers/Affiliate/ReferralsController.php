<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\User;

use App\Models\Affiliate\Referrals;

class ReferralsController extends AffBaseController {
    
    public function __construct ()
    {
        parent::__construct();
		$this->referralsObj = new Referrals();
    }
	
	public function my_referrals()
	{
		
		$data = $wdata = $filter = array();
		$data['account_id'] = $this->userSess->account_id;
		$post = $this->request->all();
		//print_r($post); exit;
        if (!empty($post))   
        {			
	    $filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';
	    }  
        if (\Request::ajax())
        {   
		    $wdata['count'] = true;
		    $data = array_merge($data, $filter); 
		    $ajaxdata['recordsTotal'] = $this->referralsObj->my_referrals($data['account_id'],$wdata);//total records//
		    $ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
		    $ajaxdata['recordsFiltered'] = 0;
		    $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
		    {
			    $ajaxdata['recordsFiltered'] = $this->referralsObj->my_referrals($data['account_id'],array_merge($wdata,$filter)); 
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
			    $wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
					{
					$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
					$wdata['order'] = $post['order'][0]['dir'];
					}
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->referralsObj->my_referrals($data['account_id'],array_merge($wdata,$filter));  ///get data all results display//			
		    }
        return $this->response->json($ajaxdata);
        }
		elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
			$pdata['print_data']= $this->referralsObj->my_referrals($data['account_id'],array_merge($wdata,$filter));
			return view('affiliate.referrals.my_referrals_print', $pdata);
		}
	    elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			$epdata['export_data']= $this->referralsObj->my_referrals($data['account_id'],array_merge($wdata,$filter));
			$output = view('affiliate.referrals.my_referrals_export', $epdata);
			$headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=my_referrals_list_' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
	             return \Response::make($output, 200, $headers);
		}   
        else
		{  
          return view('affiliate.referrals.my_referrals',$data);
		}
	}
	
	public function my_directs()
	{
		$data = $wdata = $filter = array();		
	    $data['account_id']= $this->userSess->account_id;
		$post = $this->request->all();	
		$filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$filter['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';

        if ($this->request->ajax())
        { 
			$ajaxdata['recordsTotal'] = $this->referralsObj->my_directs($data['account_id'],$wdata,true);//total records//
			$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
			$ajaxdata['recordsFiltered'] = 0;
			$ajaxdata['data'] = array();
			if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
			{
			if(!empty(array_filter($filter)))
			{
			array_merge($wdata,$filter);
		
			$ajaxdata['recordsFiltered'] = $this->referralsObj->my_directs($data['account_id'],$wdata,true);  
			}	
			if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
			{
				$wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				if (isset($post['order']))
			{
				$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
				$wdata['order'] = $post['order'][0]['dir'];
			} 
                 // print_r(array_merge($wdata,$filter)); die;
				
				$ajaxdata['data'] = $this->referralsObj->my_directs($data['account_id'],array_merge($wdata,$filter)); 
				if($ajaxdata['data'])
				{
					array_walk($ajaxdata['data'], function(&$data)
					{
						$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
						$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
						$data->username= trans('affiliate/referrels/my_referrels.user_name');
					});
				}	
			}
			}
			//print_r($ajaxdata); die;
			
			return $this->response->json($ajaxdata);
        }
		elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
			$pdata['print_data']= $this->referralsObj->my_directs($data['account_id'],array_merge($wdata,$filter));
			if($pdata['print_data'])
			{
				array_walk($pdata['print_data'], function(&$data)
				{
					$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
				});
			}	
            return view('affiliate.referrals.my_directs_print', $pdata);
		}
		elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
		{
			$epdata['export_data']= $this->referralsObj->my_directs($data['account_id'],array_merge($wdata,$filter));
			if($epdata['export_data'])
			{
				array_walk($epdata['export_data'], function(&$data)
				{
					$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
				});
			}	
            $output = view('affiliate.referrals.my_directs_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=my_directs_list_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
		}
		else
		{  
		   return view('affiliate.referrals.my_directs',$data);
		}
	}
	
	public function my_team()
	{
		$data = $wdata = $postdata = array();
		$post = $this->request->all();	
		$data['account_id'] = $this->userSess->account_id; 
		$postdata['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$postdata['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$postdata['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$postdata['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$postdata['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$postdata['filterchk'] = $this->request->has('filterchk')? $this->request->get('filterchk') : '';	

		
        if ($this->request->ajax())
        {   
			$wdata['count'] = true;
			if (isset($post['order']))
			{
				$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
				$wdata['order'] = $post['order'][0]['dir'];
			}			
			$ajaxdata['recordsTotal'] = $this->referralsObj->my_team_details($data['account_id'],$wdata);//total records//
			$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
			$ajaxdata['recordsFiltered'] = 0;
			$ajaxdata['data'] = array();
			if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
			{
				$ajaxdata['recordsFiltered'] = $this->referralsObj->my_team_details($data['account_id'],array_merge($wdata,$postdata)); 
				$wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->referralsObj->my_team_details($data['account_id'],array_merge($wdata,$postdata));
				if($ajaxdata['data'])
                {				
					array_walk($ajaxdata['data'], function(&$data)
					{ 
						$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
						$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
						$data->username= trans('affiliate/referrels/my_referrels.user_name');
					});	
				}	
			}
			return $this->response->json($ajaxdata);
        }
		elseif(isset($postdata['printbtn']) && $postdata['printbtn']=='Print')
		{
			$pdata['print_data']= $this->referralsObj->my_team_details($data['account_id'],array_merge($wdata,$postdata));
			if($pdata['print_data'])
			{				
				array_walk($pdata['print_data'], function(&$data) 
				{ 
					$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
				});	
			}	
            return view('affiliate.referrals.my_team_print', $pdata);
		}
		elseif(isset($postdata['exportbtn']) && $postdata['exportbtn']=='Export')
		{
			$epdata['export_data']= $this->referralsObj->my_team_details($data['account_id'],array_merge($wdata,$postdata));
			if($epdata['export_data'])
			{				
				array_walk($epdata['export_data'], function(&$data) 
				{ 
					$data->recent_package_purchased_on= date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
					$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
				});	
			}	
            $output = view('affiliate.referrals.my_team_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=my_team_list_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
		}
		else
		{  
			return view('affiliate.referrals.my_team',$data);
		}
	}
	
	public function get_sponsor_geonology ($account_id=0)
    {
        $op = array();        
		$wdata['parent_acinfo'] = $this->referralsObj->getUser_treeInfo([$account_id]);	
		$wdata['account_id'] = $this->request->account_id;
        $sponsor = $this->referralsObj->get_sponsor_users($wdata);
		if ($sponsor){
            $op['status'] = "ok";
            $op['sponsor'] = $sponsor;
        }
        else
        {
            $op['status'] = "err";
            $op['sponsor'] = NULL;
        }		
        return $this->response->json($op,200,[],JSON_NUMERIC_CHECK);		
    } 
	
	public function sponsor_geneology()
	{
		$data = array();
			 $account_id = $this->userSess->account_id;
			 $data['my_treeinfo'] = $this->referralsObj->getUser_treeInfo([$account_id]); 
			 $data['sponsor_users'] = $this->referralsObj->get_sponsor_users(['account_id'=>$account_id,'parent_acinfo'=>$data['my_treeinfo']]); 
			
		return view('affiliate.referrals.sponsor_geneology',$data);
	}
	
	public function get_direct_geneology ($account_id=0)
    {
        $op = array();        
		$wdata['parent_acinfo'] = $this->referralsObj->getUser_treeInfo([$account_id]);	
		$wdata['account_id'] = $this->request->account_id;
        $direct = $this->referralsObj->get_direct_users($wdata);
        if ($direct){
            $op['status'] = "ok";
            $op['direct'] = $direct;
        }
        else
        {
            $op['status'] = "err";
            $op['direct'] = NULL;
        }		
        return $this->response->json($op,200,[],JSON_NUMERIC_CHECK);		
    }
	
	public function my_geneology()
	{
		$account_id = $this->userSess->account_id;
        $data = array();
        $data['my_treeinfo'] = $this->referralsObj->getUser_treeInfo([$account_id]); 
		$data['direct_users'] = $this->referralsObj->get_direct_users(['account_id'=>$account_id,'parent_acinfo'=>$data['my_treeinfo']]);
		return view('affiliate.referrals.my_geneology',$data);
	}
}