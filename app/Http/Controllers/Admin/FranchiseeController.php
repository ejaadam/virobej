<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Admin\AdminCommon;
use App\Models\Admin\Franchisee;
use App\Models\LocationModel;
use App\Models\Commonsettings;
use App\Helpers\CommonNotifSettings;
use CommonLib;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use Config;
class FranchiseeController extends AdminController
{

    public function __construct ()
    {
        parent::__construct();        
        $this->admincommonObj = new AdminCommon();
        $this->franchiseeObj = new Franchisee();        
		$this->locationObj = new LocationModel(); 
    }

    /**
    * Create new User in Admin panel.
    * name : create_user
    * params:
    * @return Response
    */
		

    public function check_email ()
    {
        $old_email = '';
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $email = $postdata['email'];
            if (isset($postdata['old_email']))
                $old_email = $postdata['old_email'];
            $email_status = $this->franchiseeObj->franchisee_email_check($email, $user_details = 0, $old_email);
            $op = $email_status;
            return $this->response->json($op);
        }
    }
	public function sample(){
		echo '<pre>';
     print_R($this->userSess); die;
	}
    public function check_mobile ()
    {
        $old_mobile = '';
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $mobile = $postdata['mobile'];
            if (isset($postdata['old_mobile']))
                $old_mobile = $postdata['old_mobile'];
            $mobile_status = $this->franchiseeObj->franchisee_mobile_check($mobile, $old_mobile);
            $op = $mobile_status;
            return $this->response->json($op);
        }
    }
    public function check_username ()
    {
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $uname = $postdata['uname'];
            $uname_status = $this->franchiseeObj->franchisee_check_username($uname);
            $op = $uname_status;
            return $this->response->json($op);
        }
    }

    public function create_franchise ()
    {
        $data['franchisee_types'] = $this->franchiseeObj->getFranchiseeTypes();
        $data['country'] = $this->locationObj->getCountries();
        $package_details = $this->franchiseeObj->get_franchisee_package();
        $data['package'] = $package_details;
        return view('admin.franchisee.create_franchisee', $data);
    }

    public function save_franchisee ()
    {
        $postdata = $this->request->all();


        $op['status'] = 'error';
        $op['msg'] = 'Somethig Went Wrong';
        $scope = array(
            '1'=>'.country',
            '2'=>'.country, .region',
            '3'=>'.country, .region, .state',
            '4'=>'.country, .region, .state, .district',
            '5'=>'.country, .region, .state, .district, .city');
        $type = array(
            '1'=>'Master',
            '2'=>'Region',
            '3'=>'State',
            '4'=>'District',
            '5'=>'City');
        if (!empty($postdata))
        {
            $add_user = $this->franchiseeObj->save_franchisee($postdata);
            if (!empty($add_user))
            {
                $op['status'] = 'ok';
                $op['msg'] = 'OnlineSensor Support Center user added Successfully';
                $op['access_type'] = $scope[$postdata['fran_type']];
                $op['franchisee_type'] = $postdata['fran_type'];
                $op['type_name'] = $type[$postdata['fran_type']];
                $op['user_id'] = $add_user;
                $op['pwd'] = $postdata['password'];
                $op['tpin'] = $postdata['tpin'];
                $op['country'] = $postdata['country'];
            }
        }
        return $this->response->json($op);
    }

    public function save_franchisee_access ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'error';
        $op['msg'] = 'Somethig Went Wrong';
        if ($this->request->ajax())
        {
            if (!empty($postdata))
            {
                $add_franchi_access = $this->franchiseeObj->franchisee_access_location($postdata);
                if (!empty($add_franchi_access))
                {
                    $op['status'] = 'ok';
                    $op['msg'] = 'OnlineSensor Support Center user access added successfully';
                }
            }
        }
        else
        {
            return App::abort('404');
        }
        return $this->response->json($op);
    }

    public function manage_centers ()
    {
        $data = [];
        $postdata = $this->request->all();
        $submit = isset($postdata['submit']) ? $postdata['submit'] : '';
        $data['search_feilds'] = ['username'=>'um.uname', 'fullname'=>'concat(ud.first_name," ",ud.last_name)', 'mobile'=>'concat(ud.phonecode," ",ud.mobile)', 'email'=>'um.email'];
        $data['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
        $data['search_feild'] = isset($postdata['search_feild']) ? $postdata['search_feild'] : '';
        $data['franchisee_type'] = isset($postdata['franchisee_type']) ? $postdata['franchisee_type'] : '';
        $data['from'] = isset($postdata['from']) ? $postdata['from'] : '';
        $data['to'] = isset($postdata['to']) ? $postdata['to'] : '';
        $data['franchisee_list'] = $this->franchiseeObj->get_franchisee_list($data);
        if ($submit == "Export")
        {
            $output = view('admin.franchisee.franchisee_list_excel', $data);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Type'=>'application/vnd.ms-excel',
                'Content-Disposition'=>'attachment; filename=UserProfileReport_'.date("Y-m-d").'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return $this->response->json($output, 200, $headers);
        }
        else if ($submit == "Print")
        {
            return view('admin.franchisee.franchisee_list_print', $data);
        }
        $data['franchisee_types'] = $this->franchiseeObj->getFranchiseeTypes();
        return view('admin.franchisee.franchisee_list', $data);
    }

	
	 public function manage_franchisee ()
     {	

        $data = $ajaxdata = $filter = [];
		
		$submit = isset($postdata['submit']) ? $postdata['submit'] : '';
        $data['search_feilds'] = ['username'=>'um.uname', 'fullname'=>'concat(ud.first_name," ",ud.last_name)', 'mobile'=>'concat(ud.phonecode," ",ud.mobile)', 'email'=>'um.email'];
        $data['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
        $data['search_feild'] = isset($postdata['search_feild']) ? $postdata['search_feild'] : '';
        $data['franchisee_type'] = isset($postdata['franchisee_type']) ? $postdata['franchisee_type'] : '';
        $data['from'] = isset($postdata['from']) ? $postdata['from'] : '';
        $data['to'] = isset($postdata['to']) ? $postdata['to'] : '';
		
		
        $postdata = $this->request->except(['from', 'to', 'terms', 'trans_type']);
	
        $filter = $this->request->only(['from', 'to', 'terms', 'trans_type']);
	  
       if ($this->request->ajax())
        {
			
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->franchiseeObj->get_franchisee_list($data, true);
            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['data'] = [];
            if (!empty($ajaxdata['recordsFiltered']))
            {
				$data['trans_type'] = isset($postdata['trans_type'])?$postdata['trans_type']:'';
                $filter = array_filter($filter);
		        if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
				
                    $ajaxdata['recordsFiltered'] = $this->franchiseeObj->get_franchisee_list($data, true);
                }
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                //$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
               //$data['order'] = $postdata['order'][0]['dir'];
		        $ajaxdata['data'] = $this->franchiseeObj->get_franchisee_list($data);
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
	
      elseif(isset($postdata['exportbtn']) && $postdata['exportbtn']=='Export')
        {
		$epdata['export_data']= $this->franchiseeObj->get_franchisee_list($data);
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
			$pdata['print_data']= $this->franchiseeObj->get_franchisee_list($data);
			return view('admin.finance.admin_credit_debit_print', $pdata);
        }  
        else
        {
			
			 $data['franchisee_types'] = $this->franchiseeObj->getFranchiseeTypes();
		
            return view('admin.franchisee.franchisee_list', $data);
        }
    }
	
	
   /*  public function franchisee_edit_profile ()
    {
        $data = array();
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $uname = $postdata['uname'];
            $user_details = $this->franchiseeObj->get_franchisee_list($arr = array(), $uname);
            if (!empty($user_details))
            {
                $op['status'] = "ok";
                $op['msg'] = "";
                $op['user_id'] = $user_details->user_id;
                $op['uname'] = $user_details->uname;
                $op['franchisee_typename'] = $user_details->franchisee_type_name;
                $op['email'] = $user_details->email;
                $data['user_id'] = $op['user_id'];
                $data['countrylist'] = $this->locationObj->getCountries();
                $data['states'] = $this->locationObj->get_states_list();
                $data['districts'] = $this->locationObj->get_district_list();
                $data['citys'] = $this->locationObj->get_city_list();
                $data['user_details'] = $this->franchiseeObj->get_franchisee_details($op['user_id']);
                if (!empty($data['user_details']))
                {
                    $data['franchisee_details'] = $user_details;
                    $op['content'] = view('admin.franchisee.update_profile', $data)->render();
                }
                else
                {
                    $op['status'] = "not_avail";
                    $op['msg'] = "OnlineSensor Support Center user not Available";
                }
            }
            else
            {
                $op['status'] = "not_avail";
                $op['msg'] = "OnlineSensor Support Center user not Available";
            }
            return $this->response->json($op);
        }
        else
        {
            return view('admin.franchisee.franchisee_edit_profile', $data);
        }
    } */

    

    public function edit_franchisee_access ()
    {
        return view('admin.franchisee.edit_franchisee_access');
    }

    public function check_franchisee ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'error';
        $op['msg'] = 'Something Went Wrong';
        if (!empty(!empty($postdata)))
        {
            $uname = $postdata['uname'];
            $data['access_city'] = '';
            $data['access_state'] = '';
            $data['access_district'] = '';
            $data['access_country'] = '';
            $data['user_id'] = '';
            $data['access_region'] = '';
            $data['type'] = '';
            $data['uname'] = '';
            $data['email'] = '';
            $data['franchisee_typename'] = '';
            $access_country = $access_region = $access_state = $access_district = $access_city = '';
            $scope = array(
                '0'=>'.country',
                '1'=>'.country',
                '2'=>'.country, .region',
                '3'=>'.country, .state',
                '4'=>'.country, .state, .district',
                '5'=>'.country, .state, .district, .city');
            $user_details = $this->franchiseeObj->get_franchisee_list($arr = array(), $uname);

            if (!empty($user_details))
            {
                $data['franchisee_types'] = $this->franchiseeObj->franchisee_types();
                $data['country'] = $this->locationObj->getCountries();


                $user_access = $this->franchiseeObj->get_frachisee_access($user_details->user_id);

                if (isset($user_access->access_type) && !empty($user_access->access_type))
                {
                    $type = $user_access->access_type;
                    $location_access = $user_access->location_access;
                }
                else
                {
                    $type = 0;
                    $location_access = 0;
                }

                if ($type == Config::get('constants.MASTER'))
                {
                    //$access_detail = $this->franchiseeObj->get_country_access($location_access);
                    $data['access_country'] = $access_country = $location_access;
                }
                else if ($type == Config::get('constants.REGION'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->franchiseeObj->get_region_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_region'] = $access_region = $location_access;
                    $data['access_country'] = $access_country = $access_detail->country_id; //$access_detail->country_id;
                }
                else if ($type == Config::get('constants.STATE'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->franchiseeObj->get_state_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_state'] = $access_state = $location_access;
                    $data['access_region'] = $access_region = $access_detail->region_id;
                    $data['access_country'] = $access_country = $access_detail->country_id;
                }
                else if ($type == Config::get('constants.DISTRICT'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->franchiseeObj->get_district_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_state'] = $access_state = $access_detail->state_id;
                    $data['access_district'] = $access_district = $location_access;
                    $data['access_country'] = $access_country = $access_detail->country_id;
                    $data['access_region'] = $access_region = $access_detail->region_id;
                }
                else if ($type == Config::get('constants.CITY'))
                {
                    if (empty($user_access->country_id))
                    {
                        $access_detail = $this->franchiseeObj->get_city_access($location_access);
                    }
                    else
                    {
                        $access_detail = $user_access;
                    }
                    $data['access_city'] = $access_city = $location_access;
                    $data['access_state'] = $access_state = $access_detail->state_id;
                    $data['access_district'] = $access_district = $access_detail->district_id;
                    $data['access_country'] = $access_country = $access_detail->country_id;
                    $data['access_region'] = $access_region = $access_detail->region_id;
                }

                $data['regions'] = $this->locationObj->get_region_list($access_country);
                $data['states'] = $this->locationObj->get_states_list($access_country);
                $data['districts'] = $this->locationObj->get_district_list($access_state);
                $data['citys'] = $this->locationObj->get_city_list($access_state, $access_district);

                $data['type'] = $type;
                $data['user_id'] = $user_details->user_id;                
                $op['email'] = $user_details->email;
                $data['uname'] = $user_details->uname;
                $data['franchisee_typename'] = $user_details->franchisee_type_name;
                $data['email'] = $user_details->email;
                $data['franchisee_details'] = $user_details;
                $a = view('admin.franchisee.update_access', $data)->render();
                $op['content'] = $a;
                $op['status'] = "ok";
                $op['msg'] = "";
                $op['scope'] = $scope[$type];
            }
            else
            {
                $op['status'] = "not_avail";
                $op['msg'] = "Franchisee not Available";
            }
        }
        return $this->response->json($op);
    }

    public function add_newaccess ()
    {

       $postdata = $this->request->all();
        $op['status'] = "error";
        $op['msg'] = "Something Went Wrong..!";

        if ($this->request->ajax())
        {
            if (!empty($postdata))
            {
                $status = $this->franchiseeObj->add_access_location($postdata,$this->userSess->account_id);
                if (!empty($status))
                {
                    $op['status'] = "ok";
                    $op['msg'] = "Access updated Successfully";
                    $op['user_id'] = $postdata['user_id'];
                }
            }
        }
        return $this->response->json($op);
    }

    public function get_states ()
    {
        $region_id = '';
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];
        if (isset($postdata['region_id']))
            $region_id = $postdata['region_id'];

        $op['region_list'] = '';
		$op['currency_list']='';
        $op['region_list'] = $this->locationObj->get_region_list($country_id);
        $op['state_list'] = $op['phone_code_list'] = '';

        $op['state_list'] = $this->locationObj->get_states_list($country_id);
        $op['phone_code_list'] = $this->locationObj->getCountries(['country_id'=>$country_id]);
		//$op['currency_list']= $this->locationObj->getCurrencies(['country_id'=>$country_id]);

        return $this->response->json($op);
    }

    public function get_franchisee_state_phonecode ()
    {
        $region_id = '';
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];
        if (isset($postdata['region_id']))
            $region_id = $postdata['region_id'];
        $op['region_list'] = '';
        $op['region_list'] = $this->locationObj->get_region_list($country_id);
        $op['state_list'] = $op['phone_code_list'] = '';

        $op['state_list'] = $this->locationObj->get_states_list(['country_id'=>$country_id]);
        $op['phone_code_list'] = $this->locationObj->getCountries(['country_id'=>$country_id]);
        return $this->response->json($op);
    }

    public function get_cities ()
    {
        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $district_id = $postdata['district_id'];
        $op['city_list'] = '';
        $op['city_list'] = $this->locationObj->get_city_list($state_id, $district_id);
        return $this->response->json($op);
    }
	  public function franchisee_get_cities()
	  {
		  
		$postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $district_id = $postdata['district_id'];
		$pincode_id= $this->locationObj->get_cities_list($district_id);
		
        $op['city_list'] = '';
        $op['city_list'] = $this->locationObj->get_city_list($pincode_id->pincode_id);
        return $this->response->json($op);
		  
	  }

    public function get_districts ()
    {
        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $op['district_list'] = '';
        $op['district_list'] = $this->locationObj->get_district_list($state_id);
        return $this->response->json($op);
    }

    public function get_franchisee_district ()
    {

        $postdata = $this->request->all();
        $state_id = $postdata['state_id'];
        $op['district_list'] = '';
        $op['territory_list'] = '';
        $territory_state_id = '';
        $territory = $this->locationObj->get_territory_list($state_id);
        $op['territory_list'] = $territory;
        if ($territory)
        {
            $territory_state_id = $territory[0]->state_id;
        }
        $op['district_list'] = $this->locationObj->get_district_list($state_id, $territory_state_id);
        return $this->response->json($op);
    }

    public function get_region ()
    {
        $postdata = $this->request->all();
        $country_id = $postdata['country_id'];

        return $this->response->json($op);
    }

    public function check_franchise_access ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'error';
        $op['msg'] = '<div class = "text-danger">Something went wrong</div>';
        extract($postdata);
        if ($franchise_type == 5)
        {
            $op['status'] = 'ok';
            $op['msg'] = '';
        }
        else
        {
            $is_exists_check = $this->franchiseeObj->check_franchise_access($franchise_type, $relation_id);
            if (!empty($is_exists_check))
            {
                $op['status'] = 'error';
                $op['msg'] = '<div class="alert alert-danger">OnlineSensor Support Center user('.$is_exists_check[0].') already exists for this '.$franchi_name.'</div>';
            }
            else
            {
                $op['status'] = 'ok';
                $op['msg'] = '';
            }
        }
	
        return $this->response->json($op);
    }

    public function check_franchise_mapped ()
    {
        $postdata = $this->request->all();
		
		
        $country_id = $state_id = $district_id = $franchisee_type = '';
        $op['country_franchisee'] = $op['state_franchisee'] = $op['district_franchisee'] = $op['region_franchisee'] = '';

        if (isset($postdata['franchise_type']))
            $franchisee_type = $postdata['franchise_type'];

        if (isset($postdata['country_id']))
            $country_id = $postdata['country_id'];
        if (isset($postdata['state_id']))
            $state_id = $postdata['state_id'];
        if (isset($postdata['district_id']))
            $district_id = $postdata['district_id'];

        if (isset($franchisee_type) && !empty($franchisee_type))
        {
            if (!empty($country_id) && $franchisee_type >= 1)
			
               // $op['country_franchisee'] = $this->franchiseeObj->check_franchise_access(Config::get('constants.COUNTRY_FRANCHISEE'), $country_id);
                $op['country_franchisee'] = $this->franchiseeObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'), $country_id);
            if (!empty($state_id))
            {
                if ($franchisee_type > 3)  
                    $op['state_franchisee'] = $this->franchiseeObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.STATE'), $state_id);
                if ($franchisee_type > 2)
                    $op['region_franchisee'] = $this->franchiseeObj->check_franchise_region(Config::get('constants.FRANCHISEE_TYPE.REGION'), $state_id);
            }
            if (!empty($district_id) && $franchisee_type > 4)
                $op['district_franchisee'] = $this->franchiseeObj->check_franchise_access($this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'), $district_id);
        }
	
        return $this->response->json($op);
    }

    public function change_block_franchisee ($userid = 0)
    {
        $postdata['user_id'] = $userid;
        $postdata['status'] = $this->request->get('status');
        $postdata['block'] = $this->request->get('block');
        $response = $this->franchiseeObj->change_block_franchisee($postdata);
        return $this->response->json($response);
    }

    /* Block Login */

    public function change_franchisee_loginblock ($userid = 0)
    {
        $response = '';
        $postdata['user_id'] = $userid;
        $postdata['status'] = $this->request->get('status');
        $postdata['block'] = $this->request->get('login_block');
        $params['user_id'] = $userid;
        $checku_Res = $this->admincommonObj->franchisee_user_check('', $params);
        if ($checku_Res && $checku_Res['status'] == 'ok')
        {
            $response = $this->franchiseeObj->change_franchisee_loginblock($postdata);
        }
        else
        {
            $response['status'] = 'ERR';
            $response['msg'] = $checku_Res['msg'];
        }
        return $this->response->json($response);
    }

    public function release_old_commission ($commission_type = 0)
    {
        /* echo $commission_type;
          exit; */
        if (!empty($commission_type))
        {
            $country_id = "77";
            $month = "11";
            $year = "2016";
            switch ($commission_type)
            {
                case Config::Get('constants.FRANCHISEE_COMMISSION_FIXED_CONTRIBUTION'):
                    $subscription_details = $this->franchiseeObj->update_fixed_commission($country_id, $month, $year);
                    break;
                case Config::Get('constants.FRANCHISEE_COMMISSION_ADD_FUNDS'):
                    $subscription_details = $this->franchiseeObj->update_addfunds_commission($country_id, $month, $year);
                    break;
                case Config::Get('constants.FRANCHISEE_COMMISSION_FLEXIBLE_CONTRIBUTION'):
                    $subscription_details = $this->franchiseeObj->update_flexible_commission($country_id, $month, $year);
                    break;
            }
        }
    }

    public function get_currency_list ()
    {
        $postdata = $this->request->all();
        $currency_ids = $postdata['currency_ids'];
        $current_currency = isset($postdata['current_currency']) ? $postdata['current_currency'] : '';
        $op['currencylist'] = '';
        $currencies = $this->locationObj->get_currencies_list($currency_ids);
        $currency_list = '';
        if ($currencies)
        {
            foreach ($currencies as $row)
            {
                $currency_list .= "<option value='".$row->id."'";
                if (!empty($current_currency) && $current_currency == $row->id)
                    $currency_list .= "selected = 'selected'";
                $currency_list .= ">".$row->code."</option>";
            }
        }
        $op['currencylist'] = $currency_list;
        return $this->response->json($op);
    }
    public function get_zipcode(){
	 $postdata = $this->request->all();
	 $pincode = $this->locationObj->get_pioncode_list($postdata);

	 if(!empty($pincode)){
		  $op['city_list'] = '';
	      $op['city_list'] =$this->locationObj->get_city_list($pincode->pincode_id);
        return $this->response->json($op);
	}
	 else{
		$op['error']='';
		$op['error']='not_found';
		 return $this->response->json($op);
	 }
    }
 public function change_block_users($account_id,$status){
        $op = $postdata = array();
        $postdata['account_id'] = $account_id;
        $postdata['status'] =config('constants.ACCOUNT_USER.'.strtoupper($status));
	return $data=$this->franchiseeObj->user_block_status($postdata);
 }
 public function change_password() {
        $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	  if (!empty($postdata)){
	    $rdata = $this->franchiseeObj->update_password($postdata);
        return $rdata;
        }
	}
public function login_block($account_id,$status){
	
	    $op = $postdata = array();
        $postdata['account_id'] = $account_id;
        $postdata['status'] =config('constants.ACCOUNT_USER.'.strtoupper($status));
	return $data=$this->franchiseeObj->user_block_login($postdata);
}

public function change_pin(){
	    $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	
	  if (!empty($postdata)){
	    $rdata = $this->franchiseeObj->update_pin($postdata);
        return $rdata;
       }
   }
   
   public function franchisee_edit_profile ()
    {
	
        $data = array();
        $postdata =  $this->request->all();
		
		
        if (!empty($postdata))
        {
            $uname = $postdata['uname'];
            $user_details = $this->franchiseeObj->get_franchisee_user_list($arr = array(),$uname);
			
            if (!empty($user_details))
            {
                $op['status'] = "ok";
                $op['msg'] = "";
                $op['account_id'] = $user_details->account_id;
                $op['uname'] = $user_details->uname;
                $op['franchisee_typename'] = $user_details->franchisee_type_name;
                $op['email'] = $user_details->email;
                $data['account_id'] = $op['account_id'];
                $data['countrylist'] = $this->locationObj->getCountries();
				/* print_r($data['countrylist']); die; */
                $data['states'] = $this->locationObj->get_states_list();
	
                $data['districts'] = $this->locationObj->get_district_list();
                $data['citys'] = $this->locationObj->get_city_list();
                $data['user_details'] = $this->franchiseeObj->get_franchisee_details($op['account_id']);
				
				/* print_R($data['user_details']); die;
				 */
                if (!empty($data['user_details']))
                {
                    $data['franchisee_details'] = $user_details;
                    $op['content'] = View::make('admin.franchisee.update_profile', $data)->render();
                }
                else
                {
                    $op['status'] = "not_avail";
                    $op['msg'] = "OnlineSensor Support Center user not Available";
                }
            }
            else
            {
                $op['status'] = "not_avail";
                $op['msg'] = "OnlineSensor Support Center user not Available";
            }
            return Response::json($op);
        }
         else
        {
            return View::make('admin.franchisee.franchisee_edit_profile', $data);
        } 
    }
	

	 public function update_franchisee_profile ()
    {
        $postdata = $this->request->all();
		
		/* print_R($postdata); die; */
		
        if (!empty($postdata))
        {
            $postdata['account_id'] = $postdata['user_id'];
            $data['account_id'] = $postdata['user_id'];
            $user_id = $data['account_id'];
            $response = $this->franchiseeObj->update_franchisee_profile($user_id, $postdata);
            
            if ($response['status'] == 'success')
            {

            }
            return json_encode($response);
        }
    }
	
    public function franchisee_check_mobile ()
    {
        $old_mobile = '';
        $postdata =  $this->request->all();
        if (!empty($postdata))
        {
            $mobile = $postdata['mobile'];
            if (isset($postdata['old_mobile']))
                $old_mobile = $postdata['old_mobile'];
            $mobile_status = $this->franchiseeObj->franchisee_mobile_check($mobile, $old_mobile);
            $op = $mobile_status;
            return json_encode($op);
        }
    }
 public function franchisee_check_email ()
    {
        $old_email = '';
        $postdata =  $this->request->all();
        if (!empty($postdata))
        {
            $email = $postdata['email'];
            if (isset($postdata['old_email']))
                $old_email = $postdata['old_email'];
            $email_status = $this->franchiseeObj->franchisee_email_check($email, $user_details = 0, $old_email);
            $op = $email_status;
            echo json_encode($op);
        }
    }
 }
