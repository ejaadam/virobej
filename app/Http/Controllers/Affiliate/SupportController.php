<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Support;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class SupportController extends AffBaseController {
    
    public $userObj = '';
	public $supObj ='';
	
    public function __construct ()
    {
        parent::__construct();
		 $this->supObj = new Support();
         $this->affObj = new AffModel();
    }
	
	public function tickets($status = false){ 
		$data = $wdata = $filter = array(); 
		$post= $this->request->all();	
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['search_term'] = $this->request->has('search_term') ? $this->request->get('search_term') : '';
		$filter['ticket_status_id'] = $this->request->has('ticket_status_id') ? $this->request->get('ticket_status_id') : '';
		$data['ticket_id'] = '';
        $data['category'] = '';
		$data['ticket_code'] = '';
		$data['account_id'] = $this->userSess->account_id;
        if ($this->request->ajax())
	    {  
			$wdata['count'] = true;
			if (isset($post['order']))
			{
				$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
				$wdata['order'] = $post['order'][0]['dir'];
			}
			$ajaxdata['recordsTotal'] = $this->supObj->ticket_entries(array_merge($wdata,$data));
			$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
			$ajaxdata['recordsFiltered'] = 0;
			$ajaxdata['data'] = array();
			if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
			{
				$ajaxdata['recordsFiltered'] = $this->supObj->ticket_entries(array_merge($wdata,$filter,$data));
				$wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->supObj->ticket_entries(array_merge($wdata,$filter,$data)); 
				
				if(!empty($ajaxdata['data']))
					{
						array_walk($ajaxdata['data'], function(&$ticket)
						{
							$ticket->created= date('d-M-Y H:i:s', strtotime($ticket->created));
							$ticket->solved_at= date('d-M-Y H:i:s', strtotime($ticket->solved_at));
							$ticket->priority_name= trans("user/support/support.priority");
							$ticket->status_name= trans("user/support/support.status");
							$ticket->category= trans("user/support/support.category");
							$ticket->view_details= trans("user/general.view_details");
							$ticket->reopen= trans("user/general.reopen");
							$ticket->in_progress= trans("user/general.in_progress");
							$ticket->already_close= trans("user/general.already_close");
							$ticket->ticket_label= trans("user/general.id");
						});
					}
			}
			return $this->response->json($ajaxdata);
        }
		else
		{
			$data['status_details']= $this->supObj->check_status();
			$data['category_name']= $this->supObj->check_category_name();
			$data['priority_info']= $this->supObj->check_priority_info();
			
			return view('affiliate.support.tickets',$data);
		}
	}
	public function save_tickets()
	{
		$op = $data = $postdata = $result= array();
		$op = array('status' => trans('affiliate/general.error'), 'msg' => trans('affiliate/general.something_wrong'));
		$postdata = $this->request->except('submit_btn');
		$attachment = $this->request->file('file_attachment');
		$errs = [];
		if (!empty($postdata))
        {
			$rules =  [            
				'ticket_category_id' => 'required',
				'ticket_priority_id' => 'required',
				'ticket_subject' => 'required',
				'ticket_message' => 'required',
			];
			$messages = [
			  'ticket_category_id.required' => trans('affiliate/support/support.select_category'),
			  'ticket_priority_id.required' => trans('affiliate/support/support.select_priority'), 
			  'ticket_subject.required' => trans('affiliate/support/support.enter_subject') ,
			  'ticket_message.required' => trans('affiliate/support/support.enter_message'),
			];		
			$validator = Validator::make($postdata, $rules,$messages);
			
			if ($validator->fails())
			{	
				$errs = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}
			
			if ($attachment != '') 
			{
				$destinationPath = base_path() . '/assets/img/tickets/';
				$filename['file_attachment'] = rand(100000, 999999) . $this->userSess->account_id .'_' . $attachment->getClientOriginalName();
				$attachment->move($destinationPath, $filename['file_attachment']);
				$postdata['file_attachment'] = $filename['file_attachment'];
			}
			$data['priority'] = $postdata['ticket_priority_id'];
			if ($postdata['ticket_category_id'] == 1 || $postdata['ticket_category_id'] == 2) 
			{
				$data['category'] = $postdata['ticket_category_id'];
			}
			$data['account_id'] = $this->userSess->account_id;
			$data['description'] = strip_tags($postdata['ticket_message']);
			$data['subject'] = strip_tags($postdata['ticket_subject']);
			$data['attachment'] = isset($postdata['file_attachment']) ? $postdata['file_attachment'] : '';
			$data['account_type'] = $this->config->get('constants.TICKET_REPLAY_ACCOUNT_TYPE');
			$data['created'] = date("Y-m-d H:i:s");
			$data['status'] = $this->config->get('constants.TICKET.OPEN');
			$result= $this->supObj->save_tickets($data);
			if ($result) 
			{
				$op['status'] = 200;
				$op['msg'] = trans("user/support/support.success_alert");
				$op['ticket_id'] = $result;
			} 
			else 
			{
				$op['status'] =trans('affiliate/general.error');
				$op['msg'] = trans("user/support/support.ticket_not_send");
			}
		}
		return $this->response->json($op);
	}
	
	public function view_ticket_detail()
	{   
        $op = array();
		$op = array('status' => trans('affiliate/general.error'), 'msg' => trans('affiliate/general.something_wrong'));
		$postdata = $this->request->all();
        $data['id'] = '';
        $data['search_status'] = '';
        $data['from'] = '';
        $data['to'] = '';
        $data['uname'] = '';
        $data['category'] = '';
        if ($postdata['id'] != '') 
		{
            $data['ticket_id'] = $postdata['id'];
            $data['account_id'] = $this->userSess->account_id;
            $vdata['detail'] = $this->supObj->ticket_entries($data);
			$vdata['detail'][0]->tic_code='T'.str_pad($vdata['detail'][0]->ticket_id,10,"0",STR_PAD_LEFT);
            $vdata['replies'] = $this->supObj->ticket_replies($data);
            if ($this->request->ajax()) {
                if (count($vdata['detail']) > 0) { 
				
					if(!empty($vdata['detail']))
					{
						array_walk($vdata['detail'], function(&$ticket)
						{
							$ticket->created= date('d-M-Y H:i:s', strtotime($ticket->created));
							$ticket->solved_at= date('d-M-Y H:i:s', strtotime($ticket->solved_at));
							$ticket->priority_name= trans("user/support/support.priority");
							$ticket->status_name= trans("user/support/support.status");
							$ticket->category= trans("user/support/support.category");
							$ticket->view_details= trans("user/general.view_details");
							$ticket->reopen= trans("user/general.reopen");
							$ticket->in_progress= trans("user/general.in_progress");
							$ticket->already_close= trans("user/general.already_close");
							$ticket->ticket_label= trans("user/general.id");
						});
					}
				    if(!empty($vdata['replies']))
					{
						array_walk($vdata['replies'],function(&$wtdata){
							$wtdata->create_date = date('d-M-Y H:i:s',strtotime($wtdata->create_date));
							$wtdata->download_attachment = trans('affiliate/support/support.download_attachment');
						});
					}
				    $op['status'] = 200;
                    $op['contents'] = $vdata;
                    $op['msg'] = trans('affiliate/support/support.ticket_detail_collect');
					//print_r( $op);exit;
                }
            }		
        } 
		return $this->response->json($op);	
    } 
	public function save_ticket_replies() 
	{
        $op = array('status' => trans('affiliate/general.error'), 'msg' => trans('affiliate/general.something_wrong'));
		$data= array();
        $postdata = $this->request->all();
		if (!empty($postdata))
        { 
			$rules =  [            
				'replay_comments' => 'required',
			];
			$messages = [
			  'replay_comments.required' => trans('affiliate/support/support.replay_comments'),
			];		
			$validator = Validator::make($postdata, $rules,$messages);			
			if ($validator->fails())
			{	 
				$errs = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}
            $postdata['account_id'] = $this->userSess->account_id;
		    $postdata['status_id']='';
            $attachment = $this->request->file('file_attachment_comment');		
			if ($attachment != '') 
			{	
				$destinationPath = base_path() . '/assets/img/tickets/comments';
				$filename['file_attachment_comment'] = rand(100000, 999999) . $this->userSess->account_id . '_' . $attachment->getClientOriginalName();
				$attachment->move($destinationPath, $filename['file_attachment_comment']);
				$postdata['file_attachment_comment'] = $filename['file_attachment_comment'];
			}
		    $result=$this->supObj->update_ticket_replies($postdata);
            if (!empty($result)) 
			{
				$status_cls = $this->config->get('constants.TICKET_STATUS_CLASS');
			    $data['attachment'] = $postdata['file_attachment_comment'];
			    $data['create_date'] = date('d-M-Y H:i:s');
			    $data['download_attachment'] = trans('affiliate/support/support.download_attachment');
			    $data['status_label'] = trans('affiliate/support/support.ticket_status.2');
			    $data['status_class'] = $status_cls[$this->config->get('constants.TICKET.IN_PROGRESS')];
                $op['status'] = 200;
				$op['data']= $data;
                $op['msg'] = trans('affiliate/support/support.reply_submit_msg');
            }
        } 
        return $this->response->json($op);
    }
	
	 public function close_ticket() {
        $op = array('status' => trans('affiliate/general.error'), 'msg' => trans('affiliate/general.something_wrong'));
        $postdata = $this->request->all();
		//print_r($postdata['']);exit();
		if (!empty($postdata))
        {
			$rules =  [            
				'comment' => 'required',
				'rating' => 'required',
			];
			$messages = [
			  'comment.required' => trans('affiliate/support/support.comment'),
			  'rating.required' => trans('affiliate/support/support.rating'),
			];		
			$validator = Validator::make($postdata, $rules,$messages);			
			if ($validator->fails())
			{	 
				$errs = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}
			$postdata['account_id'] = $this->userSess->account_id;
            $result = $this->supObj->close_ticket($postdata);
			
            if (!empty($result)) {
			    $status_cls = $this->config->get('constants.TICKET_STATUS_CLASS');
				$result['solved_date'] = date('d-M-Y H:i:s');
				$result['status_label'] = trans('affiliate/support/support.ticket_status.3');
				$result['status_class'] = $status_cls[$this->config->get('constants.TICKET.CLOSED')];
				$op['status']= 200;
				$op['data']= $result;
                $op['msg'] = trans('affiliate/support/support.rating_submit_msg');
            }
        } 
        return $this->response->json($op);
    }
	
	public function tickets_status() 
	{   
		$postdata = $this->request->all();
		$status = $this->supObj->change_ticket_status($postdata);
        if ($status) {
		    $op['status'] = 200;
            $op['msg'] = trans('affiliate/support/support.ticket_reopen');
        } else {
		    $op['status'] = trans('affiliate/general.error');
            $op['msg'] = trans('affiliate/general.something_went_wrong');
        }
        return $this->response->json($op);
    }

	public function tickets_details()
	{
	  $data =array();
		return view('affiliate.support.tickets_details',$data);
	}
	public function faqs(){
	  
	 $data['faq_categories'] = $this->supObj->faq_categories();
		
		
		return view('affiliate.support.faqs',$data);
	}
	public function get_faqs($catlink) {
	    $op = [];
		$postdata = Input::all();
        if (!empty($catlink) && !empty($catlink)) {
			$wdata['catlink'] = $catlink;
			$wdata['term'] = Input::has('term')? Input::get('term'):'';
            $get_faqu = $this->supObj->getFaqs($wdata);
            if (!empty($get_faqu)) {
                 $op['list'] = $get_faqu;
            }
             return response()->json($op,200);
        }
    }
	
	/* 
	public function search_faq() {
	 	$op = [];
        $postdata = Input::all();
        $data['title'] = "Search FAQ";
        $data['term'] = Input::has('term')? Input::has('term'):'';
        $get_faqu = $this->supObj->get_faqs(array('faq_word' => $postdata['faq_word']));
        if (!empty($get_faqu)) {
            $op['list'] = $get_faqu;
        }
        return response()->json($op,200);
    }
	*/
	
	public function downloads(){
		$data = array();
		return view('affiliate.support.downloads',$data);
	}
	
	public function announcements(){
		$data = array();
		return view('affiliate.support.announcements',$data);
	}
}