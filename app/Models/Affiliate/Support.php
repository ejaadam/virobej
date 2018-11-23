<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Support extends BaseModel {

    public function __construct() {
       
    }
    public function faq_categories() {
        return DB::table($this->config->get('tables.FAQ_CATEGORIES'))
                        ->where('status', $this->config->get('constants.ON'))
                        ->where('is_deleted', $this->config->get('constants.OFF'))
                        ->where('is_visible', $this->config->get('constants.ON'))
                        ->where('system_roles', '=', null)
                        ->get();
    }
    public function get_faqcategory($link) {
        if (!empty($link)) {
            return DB::table($this->config->get('tables.FAQ_CATEGORIES'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('is_deleted', $this->config->get('constants.OFF'))
                            ->where('is_visible', $this->config->get('constants.ON'))
                            ->where('link', $link)
                            ->where('system_roles', '=', null)
                            ->select('faq_category', 'faq_category_id')
                            ->first();
        }
    }
    public function getFaqs($arr) {
        $res = DB::table($this->config->get('tables.FAQ_MST') . ' as f')
                ->join($this->config->get('tables.FAQ_CATEGORIES') . ' as fc', 'fc.faq_category_id', '=', 'f.faq_category_id')
                ->where('fc.status', $this->config->get('constants.ON'))
				->where('f.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('fc.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('fc.is_visible', $this->config->get('constants.ON'))
                ->where('fc.system_roles', '=', NULL)
                ->where('f.status', $this->config->get('constants.ON'));

        if (isset($arr['faq_category_id']) && !empty($arr['faq_category_id'])) {
            $res->where('f.faq_category_id', $arr['faq_category_id']);
        }
		
		if (isset($arr['catlink']) && !empty($arr['catlink'])) {
            $res->where('fc.link', $arr['catlink']);
        }		
		
        if (isset($arr['term']) && !empty($arr['term'])) {
			$res->where(function($query) use ($arr){
			  $query->where('f.answers', 'LIKE', '%'.$arr['term'].'%')
			  ->orWhere('f.questions', 'LIKE', '%'.$arr['term'].'%');
		  	});
        }
		
        $res = $res->select(DB::raw('f.id,f.questions,f.answers,fc.faq_category_id,fc.faq_category,fc.link as catlink'))->get();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }
	public function check_status()
	{
		 return DB::table($this->config->get('tables.SUPPORT_TICKET_STATUS') . ' as sts')
						 ->join($this->config->get('tables.SUPPORT_TICKET_STATUS_LANG').' as sptsl', function($subquery)
							{ 
								$subquery->on('sptsl.status_id', ' = ','sts.status_id')
								->where('sptsl.lang_id', '=', $this->config->get('app.locale_id'));
							})
						->select('sptsl.status','sts.status_id')
                        ->get();
	}
	public function check_category_name()
	{
		return DB::table($this->config->get('tables.SUPPORT_CATEGORY') . ' as supc')
		                ->join($this->config->get('tables.SUPPORT_TICKET_CATEGORY_LANG').' as sptcl', function($subquery)
							{ 
								$subquery->on('sptcl.category_id', ' = ', 'supc.category_id')
								->where('sptcl.lang_id', '=', $this->config->get('app.locale_id'));
							})
						->select('supc.category_id','sptcl.category_name')
                        ->get();
	}
	public function check_priority_info()
	{
		return DB::table($this->config->get('tables.SUPPORT_TICKET_PRIORITIES') . ' as suptp')
						->join($this->config->get('tables.SUPPORT_TICKET_PRIORITIES_LANG').' as sptpl', function($subquery)
							{ 
								$subquery->on('sptpl.priority_id', ' = ', 'suptp.priority_id')
								->where('sptpl.lang_id', '=', $this->config->get('app.locale_id'));
							})
						->select('suptp.priority_id','sptpl.priority')
                        ->get();
	}
	public function ticket_entries($data)
	{
	 extract($data);
		$query =DB::table($this->config->get('tables.SUPPORT_TICKETS') . ' as spt ')
				->join($this->config->get('tables.SUPPORT_TICKET_STATUS') . ' as spts', 'spts.status_id', '=', 'spt.status')
				->join($this->config->get('tables.SUPPORT_CATEGORY') . ' as spc', 'spc.category_id', '=', 'spt.category')
				->join($this->config->get('tables.SUPPORT_TICKET_CATEGORY_LANG').' as sptcl', function($subquery)
				  { 
						$subquery->on('sptcl.category_id', ' = ', 'spc.category_id')
						->where('sptcl.lang_id', '=', $this->config->get('app.locale_id'));
				  })
				->join($this->config->get('tables.SUPPORT_TICKET_PRIORITIES') . ' as sptp', 'sptp.priority_id', '=', 'spt.priority')
				->join($this->config->get('tables.SUPPORT_TICKET_PRIORITIES_LANG').' as sptpl', function($subquery)
				  { 
						$subquery->on('sptpl.priority_id', ' = ', 'sptp.priority_id')
						->where('sptpl.lang_id', '=', $this->config->get('app.locale_id'));
				  })
				->join($this->config->get('tables.SUPPORT_TICKET_STATUS_LANG').' as sptsl', function($subquery)
				  { 
						$subquery->on('sptsl.status_id', ' = ', 'spts.status_id')
						->where('sptsl.lang_id', '=', $this->config->get('app.locale_id'));
				  });
		if(isset($ticket_id) && !empty($ticket_id) ){
			$query->leftJoin($this->config->get('tables.SUPPORT_TICKET_RATING_STATUS') . ' as sptrs', 'sptrs.rating_id', '=', 'spt.user_rating')
			      ->leftJoin($this->config->get('tables.SUPPORT_TICKET_RATING_STATUS_LANG').' as sptrsl', function($subquery)
					{ 
						$subquery->on('sptrsl.rating_id', ' = ', 'sptrs.rating_id')
						->where('sptrsl.lang_id', '=', $this->config->get('app.locale_id'));
					}); 
		} 
		if (isset($from_date) && isset($to_date) && !empty($from_date) && !empty($to_date))
		{
			$query->whereRaw("DATE(spt.created) >='".date('Y-m-d', strtotime($from_date))."'");
			$query->whereRaw("DATE(spt.created) <='".date('Y-m-d', strtotime($to_date))."'");
		}
		else if (!empty($from_date) && isset($from_date))
		{
			$query->whereRaw("DATE(spt.created) <='".date('Y-m-d', strtotime($from_date))."'");
		}
		else if (!empty($to_date) && isset($to_date))
		{
			$query->whereRaw("DATE(spt.created) >='".date('Y-m-d', strtotime($to_date))."'");
		}
		if (isset($search_term) && !empty($search_term))
        {		
			$query->where(function($wcond) use($search_term){
				$wcond->orWhereRaw("spt.subject like '%$search_term%'")
					  ->orWhereRaw("spt.ticket_code like '%$search_term%'");
			});				
        }
		if (isset($ticket_status_id) && !empty($ticket_status_id))
        {
            $query->where('spts.status_id',$ticket_status_id);
        }
		if (isset($account_id) && !empty($account_id))
		{
            $query->where('spt.account_id', '=', $account_id);
        }
		if (isset($ticket_id) && !empty($ticket_id))
		{
            $query->where('spt.ticket_id', '=', $ticket_id);
        }
		if (isset($category) && !empty($category)) 
		{
            $query->where('spc.id', '=', $category);
        }
		if (isset($orderby) && isset($order))
		{
			$query->orderBy($orderby, $order);
		}		
		if (isset($length) && !empty($length))
		{
			$query->skip($start)->take($length);
		}
		if (isset($count) && !empty($count))
		{
			return $query->count();
		}
		else
		{
			$query->select('spt.ticket_id','spt.description','sptsl.status','spt.subject','sptcl.category_name','sptpl.priority','spt.created','spts.status_id','spt.attachment','spt.solved_at','spt.user_comments','spt.ticket_code','spc.category_id','spt.account_id','sptp.disp_class','spts.disp_class as status_class');
			
			if(isset($ticket_id) && !empty($ticket_id) ){
			    $query->addSelect('sptrs.disp_class as rating_class','sptrsl.rating');
			}
			$result = $query->orderby('spt.created','DESC')
			               ->get();
		
			return $result;
		}
	}
	public function save_tickets($data)
	{
		$latestUser = DB::table($this->config->get('tables.SUPPORT_TICKETS'))
							->select('ticket_id')
							->orderBy('ticket_id', 'DESC')
							->  first();
		$id = DB::table($this->config->get('tables.SUPPORT_TICKETS'))
				->insertGetId($data);
		//print_r($id);exit;		
		$datas['ticket_code'] ='T'.str_pad($id,10,"0",STR_PAD_LEFT);
		$update=DB::table($this->config->get('tables.SUPPORT_TICKETS'))
				->where('ticket_id', '=', $id)
				->update($datas);		
		return $id;
	}
	public function ticket_replies($data) 
	{ // print_r($data);exit();
	    extract($data);
        $data= DB::table($this->config->get('tables.SUPPORT_TICKET_REPLIES') . ' as sutr')
					->where('sutr.ticket_id', '=', $ticket_id)
					->where('sutr.replay_account_id', '=', $account_id)
					->select('sutr.*');
        $data= $data->get();
		return $data; 
    }
	public function update_ticket_replies($data = '') 
	{
        if (isset($data['replay_comments'])) {
            $arr['status'] = $this->config->get('constants.TICKET.IN_PROGRESS'); 
            $rate['replay_comments'] = $data['replay_comments'];
            $rate['replay_usertype'] = $this->config->get('constants.TICKET_REPLAY_ACCOUNT_TYPE'); // user  Config::get('constants.RIDER_ROLE')
            $rate['ticket_id'] = $data['ticket_id'];
            $rate['replay_account_id'] = $data['account_id'];
            $rate['create_date'] = date('Y-m-d H:i:s');
            $rate['attachment'] = $data['file_attachment_comment'];
            $result = DB::table($this->config->get('tables.SUPPORT_TICKET_REPLIES'))
                    ->insert($rate);
            if ($result) {
                $update = DB::table($this->config->get('tables.SUPPORT_TICKETS'))
                        ->where('ticket_id', $data['ticket_id'])
                        ->update($arr);
				return $result;
            }
        }
        return false;
    }
	public function close_ticket($data = '') 
	{
        if (isset($data['ticket_id']) && $data['ticket_id']>0 && isset($data['rating']) && isset($data['comment'])) {
            $arr['user_comments'] = $data['comment'];
            $arr['user_rating'] = $data['rating'];
            $arr['status'] = $this->config->get('constants.TICKET.CLOSED');
            $arr['solved_at'] = date('Y-m-d H:i:s');
            $result = DB::table($this->config->get('tables.SUPPORT_TICKETS'))
                    ->where('ticket_id', $data['ticket_id'])
					->where('account_id', $data['account_id'])
                    ->update($arr);
				
            if($result){
			    $data =DB::table($this->config->get('tables.SUPPORT_TICKETS') . ' as spt ')
                        ->join($this->config->get('tables.SUPPORT_TICKET_RATING_STATUS') . ' as sptrs', 'sptrs.rating_id', '=', 'spt.user_rating')
						->join($this->config->get('tables.SUPPORT_TICKET_RATING_STATUS_LANG').' as sptrsl', function($subquery)
							{ 
								$subquery->on('sptrsl.rating_id', ' = ', 'sptrs.rating_id')
								->where('sptrsl.lang_id', '=', $this->config->get('app.locale_id'));
							})  
						->select('sptrs.disp_class as rating_class','sptrsl.rating','spt.created','spt.user_comments')
                        ->where('ticket_id', $data['ticket_id'])
					    ->where('account_id', $data['account_id'])						
                        ->get();
                if(!empty($data)){	
					return $data;
				}
			}
        }
        return false;
    }
	public function change_ticket_status($data) {
        $update['status'] = $data['status'];
        return DB::table($this->config->get('tables.SUPPORT_TICKETS'))
                        ->where('ticket_id', $data['ticket_id'])
                        ->update($update);
    }
}
