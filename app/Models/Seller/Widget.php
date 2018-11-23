<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;
use CommonLib;

class Widget extends Model
{

    /**
     * @param array $arr [supplier_id]
     * @param bool $count to get count<i>(Default-false)</i>
     * @param bool $ids_only to get id list<i>(Default-false)</i>
     * @return array|bool return array of categories or false
     */
	 
	/* Order Count & ratio */
    public function order_counts ($arr, $params, $count = false)
    {    
	    extract($arr);		
        $order = DB::table(Config::get('tables.ORDERS').' as ord')  
					->where('ord.supplier_id', $supplier_id)
					->where('ord.status', Config::get('constants.ON'))
					->where('ord.payment_status', Config::get('constants.ON'))
					->where('ord.order_status_id', Config::get('constants.ON'))
					->where('ord.is_deleted', Config::get('constants.OFF'))				
					->selectRaw('convert(ord.order_date,DATE) as ord_date, count(ord.order_id) as ord_count');
				
		if(!empty($params['filter'])){
			if($params['filter']=='day'){
				$order->whereDate('ord.order_date', '<=', getGTZ())
					  ->whereDate('ord.order_date', '>=', getGTZ(date('Y-m-d',strtotime('-'.$params['days'].'day'))));
			}
			else if(!isset($params['filter'])){
				if(isset($params['from'])){
					$order->whereDate('ord.order_date', '>=', $params['from']);
				}
				if(isset($params['to'])){
					$order->whereDate('ord.order_date', '<=',$params['to']);
				}
				if(!isset($params['from']) && !isset($params['to'])){
				    $order->whereDate('ord.order_date', '<=',getGTZ());
				}				
			}
		}  
		if (isset($count) && !empty($count))
        {
            return $order->count();
        }
		else {
		    $order->orderby('ord.order_date', 'ASC')
			      ->groupby('ord_date');
			$res = $order->lists('ord_count','ord_date');	
			
			if(isset($params['days']) && !empty($params['days']))
			{
				$ord_date=[];
				for($i = 0; $i <= $params['days']; $i++){
					$d=date('Y-m-d',strtotime('-'.$i.'day'));
					$ord_date[]  = ['ord_date'=>$d,'count'=>array_key_exists($d,$res)?$res[$d]:0];				
				}		
				return $ord_date;
			}
			return res;
		}
		return NULL;
    }
   
	/* Sales Count & ratio */
    public function sales_counts ($arr, $params, $tot_sale = false)
    {  
	    extract($arr);	
		$currency_symbol = isset($currency_symbol) && !empty($currency_symbol)? $currency_symbol:'';
        $order = DB::table(Config::get('tables.ORDERS').' as ord')  
		            ->join(Config('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'ord.supplier_id')
		            ->join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'sm.account_id')
		            ->leftjoin(Config('tables.CURRENCIES').' as cu', 'cu.currency_id', '=', 'ap.currency_id')
					->where('ord.supplier_id', $supplier_id)
					->where('ord.status', Config::get('constants.ON'))
					->where('ord.payment_status', Config::get('constants.ON'))
					->where('ord.order_status_id', Config::get('constants.ON'))
					->where('ord.is_deleted', Config::get('constants.OFF'))				
					->selectRaw('SUM(ord.bill_amount) as total_sale,convert(ord.order_date,DATE) as ord_date,cu.currency_id');
					
		if(isset($currency_id) && !empty($currency_id)){
		    $order->where('ord.currency_id', '=', $currency_id);
		}
		if(!empty($params['filter'])){
			if($params['filter']=='day')
			{
			    if(empty($params['days'])){
				    $order->whereDate('ord.order_date', '=', getGTZ());
				}else {
				    $order->whereDate('ord.order_date', '<=', getGTZ())
					      ->whereDate('ord.order_date', '>=', getGTZ(date('Y-m-d',strtotime('-'.$params['days'].'day'))));
				}
			}
			else if($params['filter']=='month')
			{
		        if(empty($params['months'])){
			        $order->whereDate('ord.order_date', '<=', getGTZ())
					      ->whereDate('ord.order_date', '>=', getGTZ(date('Y-m-d',strtotime('-30 day'))));
			    }else {
				    $order->whereDate('ord.order_date', '<=', getGTZ(date('Y-m-d',strtotime('-'.$params['months'].'month'))));					    
				}		 
			}
			else if(!isset($params['filter'])){
				if(isset($params['from'])){
					$order->whereDate('ord.order_date', '>=', $params['from']);
				}
				if(isset($params['to'])){
					$order->whereDate('ord.order_date', '<=',$params['to']);
				}
				if(!isset($params['from']) && !isset($params['to'])){
				    $order->whereDate('ord.order_date', '<=',getGTZ());
				}				
			}
		}  		
		if (isset($tot_sale) && !empty($tot_sale))
        {   
            $res = $order->first();			
			$amount = 0;
			if(!empty(array_filter((array)$res))){			
			    $amount = CommonLib::currency_format($res->total_sale, $res->currency_id);
				return $amount;
			}else {
				$amount = $currency_symbol .' '.$amount;
				return $amount;
			}
			//return $order->value('total_sale');
        }
		else {	
		    $order->orderby('ord.order_date', 'ASC')
			      ->groupby('ord_date');
			$res = $order->lists('total_sale','ord_date');	
			
			if(isset($params['days']) && !empty($params['days']))
			{
				$ord_date=[];
				for($i = 0; $i <= $params['days']; $i++){
					$d=date('Y-m-d',strtotime('-'.$i.'day'));
					$ord_date[]  = ['ord_date'=>$d,'amount'=>array_key_exists($d,$res)?$res[$d]:0];				
				}					
				return $ord_date;
			}			
			return $res;
		}
		return NULL;
    }
	
	/* Visitors Count & ratio */
    public function visitors_counts ($arr, $params, $count = false)
    {   
	    extract($arr);		
        $order = DB::table(Config::get('tables.ORDERS').' as ord')  
					->where('ord.supplier_id', $supplier_id)
					->where('ord.status', Config::get('constants.ON'))
					->where('ord.payment_status', Config::get('constants.ON'))
					->where('ord.order_status_id', Config::get('constants.ON'))
					->where('ord.is_deleted', Config::get('constants.OFF')); 
					
		if(!empty($params['filter'])){
			if($params['filter']=='day'){
				$order->whereDate('ord.order_date', '<=', getGTZ())
					  ->whereDate('ord.order_date', '>=', getGTZ(date('Y-m-d',strtotime('-'.$params['days'].'day'))));
			}
			else if(!isset($params['filter'])){
				if(isset($params['from'])){
					$order->whereDate('ord.order_date', '>=', $params['from']);
				}
				if(isset($params['to'])){
					$order->whereDate('ord.order_date', '<=',$params['to']);
				}
				if(!isset($params['from']) && !isset($params['to'])){
				    $order->whereDate('ord.order_date', '<=',getGTZ());
				}				
			}
		}  
		if (isset($count) && !empty($count))
        {
            return $order->selectRaw('count(DISTINCT(ord.account_id)) as visitor_count')->value('visitor_count');
        }
		else {
		  
			$res = $order->orderby('ord.order_date', 'ASC')
			             ->groupby('ord.order_date','ord.account_id')
			             ->selectRaw('convert(ord.order_date,DATE) as ord_date, count(DISTINCT(ord.account_id)) as visitor_count')
			             ->lists('visitor_count','ord_date');	
						
			if(isset($params['days']) && !empty($params['days']))
			{
				$ord_date=[];
				for($i = 0; $i <= $params['days']; $i++){
					$d=date('Y-m-d',strtotime('-'.$i.'day'));
					$ord_date[]  = ['ord_date'=>$d,'count'=>array_key_exists($d,$res)?$res[$d]:0];				
				}		
				return $ord_date;
			}
			return res;
		}
		return NULL;
    }
	
   /*  public function get_product_items ($data, $count = false)
    {
        extract($data);
        $product = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as pi')
                ->leftjoin(Config::get('tables.PRODUCTS').' as p', 'p.product_id', '=', 'pi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'p.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'p.brand_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ssm', 'ssm.product_id', '=', 'pi.product_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as tas', 'tas.supplier_id', '=', 'pi.supplier_id')
                ->where('pi.is_deleted', Config::get('constants.OFF'))
                ->where('pi.supplier_id', $supplier_id)
                ->selectRaw('ssm.*,p.product_name,p.product_code,pi.*, pc.category, pc.category_id, pi.status, pi.pre_order, tas.*, pb.brand_name,pi.created_on');
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $product->where('pi.product_id', $data['product_id']);
        }
        if (isset($data['category']) && !empty($data['category']))
        {
            $product->where('pi.category_id', $data['category']);
        }
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $product->whereRaw('(pi.product_name like \'%'.$data['search_term'].'%\'  OR  pc.category_name like \'%'.$data['search_term'].'%\' OR  pb.brand_name like \'%'.$data['search_term'].'%\' OR pi.product_code like  \'%'.$data['search_term'].'%\')');
        }
        if (isset($data['from']) && !empty($data['from']))
        {
            $product->whereDate('pi.created_on', '>=', date('Y-m-d', strtotime($data['from'])));
        }
        if (isset($data['to']) && !empty($data['to']))
        {
            $product->whereDate('pi.created_on', '<=', date('Y-m-d', strtotime($data['to'])));
        }
        if (isset($data['start']) && isset($data['length']))
        {
            $product->skip($data['start'])->take($data['length']);
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        else
        {
            $product->orderby('pi.created_on', 'desc');
        }
        if ($count)
        {
            return $product->count();
        }
        else
        {
            if (isset($data['product_id']) && !empty($data['product_id']))
            {
                return $product->first();
            }
            else
            {
                return $product->get();
            }
        }
    } */
	
	public function get_supplier_manager($arr)
    {   
			extract($arr);		
			return DB::table(Config('tables.SUPPLIER_PREFERENCE').' as sp')
					->join(Config('tables.ACCOUNT_MST').' as ams','ams.account_id','=','sp.manager_account_id')
					->join(Config('tables.ACCOUNT_DETAILS').' as amd','amd.account_id','=','ams.account_id')
					->where('sp.supplier_id',$supplier_id)
					->select('ams.email','ams.mobile',DB::raw("CONCAT_WS(' ',amd.firstname,amd.lastname) as full_name"))->first();
							
	}
}
