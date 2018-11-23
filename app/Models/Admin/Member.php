<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use DB;
use CommonLib;

class Member extends BaseModel
{

    public function __construct ()
    {
        parent::__construct();
    }

    /* Country List */

    public function country_list ()
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                ->join($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'am.account_id')
                    ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                    ->where('adm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.PERMANENT'));
                })
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as lcu', 'lcu.country_id', ' = ', 'adm.country_id')
                ->where('am.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->select('adm.country_id', 'lcu.country')
                ->distinct()
                ->orderBy('lcu.country', 'asc')
                ->get();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return null;
    }

    /* Gender List */

    public function gender_list ()
    {
        $result = DB::table($this->config->get('tables.GENDER_LANG'))
                ->where('lang_id', '=', $this->config->get('app.locale_id'))
                ->select('gender_id', 'gender')
                ->get();
        return (!empty($result)) ? $result : NULL;
    }

    /* Gio Location */

    public function get_coutry_by_iso ($country_iso)
    {
        return DB::table($this->config->get('tables.LOCATION_COUNTRIES'))
                        ->where('iso2', $country_iso)
                        ->select('country_id')->first();
    }

    public function get_stateid_by_sname ($state_code)
    {
        return DB::table($this->config->get('tables.LOCATION_STATES'))
                        ->where('state_code', $state_code)
                        ->select('state_id')->first();
    }

    public function get_cityid_by_cname ($city_name)
    {
        return DB::table($this->config->get('tables.LOCATION_DISTRICTS'))
                        ->where('district', $city_name)
                        ->select('district_id')->first();
    }

    public function get_localityid_by_lname ($locality)
    {
        return DB::table($this->config->get('tables.LOCATION_LOCALITIES'))
                        ->where('locality', $locality)
                        ->select('locality_id')->first();
    }

    public function member_list ($data = array(), $count = false)
    {
        extract($data);
        $query = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
                ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'am.account_id')
                    ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                    ->where('adm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.PERMANENT'));
                })
                ->leftjoin($this->config->get('tables.LOCATION_COUNTRIES').' as lcu', 'lcu.country_id', ' = ', 'adm.country_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
                ->where('am.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('am.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'));

        if (isset($from) && !empty($from))
        {
            $query->whereDate('am.signedup_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $query->whereDate('am.signedup_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            if (isset($filterTerms) && !empty($filterTerms))
            {
                $search_text = '%'.$search_text.'%';
                $search_field = ['UserName'=>'am.uname', 'FullName'=>'concat_ws(" ",ad.firstname,ad.lastname)', 'Email'=>'am.email', 'Mobile'=>'concat_ws(" ",lcu.phonecode,am.mobile)'];
                $query->where(function($sub) use($filterTerms, $search_text, $search_field)
                {
                    foreach ($filterTerms as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $sub->orWhere(DB::raw($search_field[$search]), 'like', $search_text);
                        }
                    }
                });
            }
            else
            {
                $query->where(function($wcond) use($search_text)
                {
                    $wcond->Where('am.uname', 'like', $search_text)
                            ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', $search_text)
                            ->orwhere('am.email', 'like', $search_text)
                            ->orwhere(DB::Raw('concat_ws(" ",lcu.phonecode,am.mobile)'), 'like', $search_text);
                });
            }
        }
        if (isset($orderby) && !empty($orderby) && isset($order) && !empty($order))
        {
            if ($orderby == 'signedup_on')
            {
                $query->orderBy('am.signedup_on', $order);
            }
            elseif ($orderby == 'uname')
            {
                $query->orderBy('am.uname', $order);
            }
            elseif ($orderby == 'email')
            {
                $query->orderBy('am.email', $order);
            }
            elseif ($orderby == 'country')
            {
                $query->orderBy('lcu.country', $order);
            }
            elseif ($orderby == 'activated_on')
            {
                $query->orderBy('am.activated_on', $order);
            }
            elseif ($orderby == 'status')
            {
                $query->orderBy('am.status', $order);
            }
        }
        if (isset($country) && !empty($country))
        {
            $query->where('adm.country_id', $country);
        }
        if (isset($start) && !empty($length))
        {
            $query->skip($start)->take($length);
        }
        if (isset($count) && !empty($count))
        {
            return $query->count();
        }
        else
        {
            $result = $query
                    ->selectRaw('am.account_id,am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,am.block_login,ast.is_account_verified,concat_ws(" ",ad.firstname,ad.lastname) as fullname,concat_ws(" ",lcu.phonecode,am.mobile) as mobile,lcu.country')
                    ->orderBy('am.signedup_on', 'DESC')
                    ->get();
            array_walk($result, function(&$data)
            {
                $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y H:i:s');
                $data->activated_on = showUTZ($data->activated_on, 'd-M-Y H:i:s');
                $data->actions = [];
                $data->actions[] = ['url'=>route('admin.member.view-details', ['uname'=>$data->uname]), 'redirect'=>false, 'label'=>trans('admin/member/member_list.view_details')];
                $data->actions[] = ['url'=>route('admin.member.edit-details', ['uname'=>$data->uname]), 'redirect'=>false, 'label'=>trans('admin/member/member_list.edit')];
				$data->actions[] = ['url'=>route('admin.qlogin.user-qlogin'), 'class'=>'quick_login', 'data'=>['uname'=>$data->uname], 'redirect'=>false, 'label'=>trans('admin/member/member_list.quick_login')];
                $data->actions[] = ['url'=>route('admin.member.update_pwd', ['uname'=>$data->uname]), 'class'=>'change_password', 'data'=>[
                        'uname'=>$data->uname,
                        'fullname'=>$data->fullname
                    ], 'redirect'=>false, 'label'=>trans('admin/member/member_list.change_password')];
                if ($data->block_login)
                {
                    $data->actions[] = ['url'=>route('admin.member.login_block', ['uname'=>$data->uname, 'status'=>'unblock']), 'label'=>trans('admin/member/member_list.unblock_login_btn')];
					$data->login_block_status = trans('general.account.loginblock.1');;
					$data->login_block_class = 'danger';
                }
                else if (!$data->block_login)
                {
                    $data->actions[] = ['url'=>route('admin.member.login_block', ['uname'=>$data->uname, 'status'=>'block']), 'label'=>trans('admin/member/member_list.block_login_btn')];
					$data->login_block_status = '';
					$data->login_block_class = '';
                }
                if ($data->is_account_verified == $this->config->get('constants.OFF'))
                {
                    $data->actions[] = ['url'=>route('admin.member.verify_status', ['uname'=>$data->uname, 'status'=>'verify']), 'label'=>trans('general.account.verify.'.$this->config->get('constants.ACCOUNT.VERIFIED.VERIFY'))];
                }
                else
                {
                    $data->actions[] = ['url'=>route('admin.member.verify_status', ['uname'=>$data->uname, 'status'=>'unverify']), 'label'=>trans('general.account.verify.'.$this->config->get('constants.ACCOUNT.VERIFIED.UNVERIFY'))];
                }
                $data->verify_disp_class = $this->config->get('dispclass.member_verify_status.'.$data->is_account_verified);
                $data->verify_status = trans('general.account.verify.'.$data->is_account_verified);
                if ($data->block == $this->config->get('constants.OFF'))
                {
                    $data->actions[] = ['url'=>route('admin.member.block_status', ['uname'=>$data->uname, 'status'=>'block']), 'label'=>trans('general.account.block.1')];
                    if ($data->status == $this->config->get('constants.ACCOUNT.STATUS.ACTIVE'))
                    {
                        $data->actions[] = ['url'=>route('admin.member.active_status', ['uname'=>$data->uname, 'status'=>'inactive']), 'label'=>trans('general.account.status.2')];
                    }
                    elseif ($data->status == $this->config->get('constants.ACCOUNT.STATUS.INACTIVE'))
                    {
                        $data->actions[] = ['url'=>route('admin.member.active_status', ['uname'=>$data->uname, 'status'=>'active']), 'label'=>trans('general.account.status.1')];
                    }
					$data->block_status = '';
					$data->block_status_cls = '';
                }
                else
                {
					$data->block_status = trans('general.account.block.1');
					$data->block_status_cls = 'danger';
                    $data->actions[] = ['url'=>route('admin.member.block_status', ['uname'=>$data->uname, 'status'=>'unblock']), 'label'=>trans('general.account.block.0')];
                }
                $data->actions[] = ['url'=>route('admin.finance.fund-transfer.to_member', ['member'=>$data->email, 'type'=>'credit']), 'redirect'=>true, 'target'=>'_blank', 'label'=>trans('general.credit_fund')];
                $data->actions[] = ['url'=>route('admin.finance.fund-transfer.to_member', ['member'=>$data->email, 'type'=>'debit']), 'redirect'=>true, 'target'=>'_blank', 'label'=>trans('general.debit_fund')];
                $data->actions[] = ['url'=>route('admin.member.transaction-log.list', ['for'=>'list', 'account_id'=>$data->account_id]), 'redirect'=>true, 'target'=>'_blank', 'label'=>trans('general.label.transaction_log')];
                $data->actions[] = ['url'=>route('admin.in_store.cashback.list', ['account_id'=>$data->account_id]), 'redirect'=>true, 'target'=>'_blank', 'label'=>trans('general.label.cashbacks')];
                $data->actions[] = ['url'=>route('admin.member.delete', ['uname'=>$data->uname]), 'label'=>trans('general.btn.delete')];
                $data->status_name = trans('general.account.status.'.$data->status);
                $data->status_disp_class = $this->config->get('dispclass.member_account_status.'.$data->status);
            });
            return $result;
        }
        return null;
    }

    /* View Member Details */

    public function member_view ($uname)
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
                ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'am.account_id')
                    ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                    ->where('adm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.RESIDENCE'));
                })
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as lcu', 'lcu.country_id', ' = ', 'adm.country_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
                ->leftjoin($this->config->get('tables.GENDER_LANG').' as agl', 'agl.gender_id', ' = ', 'ad.gender')
                ->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,ast.is_account_verified,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,ad.profile_image,concat_ws(" ",lcu.phonecode,am.mobile) as mobile,lcu.country,agl.gender,adm.formated_address as address')
                ->where('am.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('am.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                ->where('am.uname', $uname)
                ->first();

        if (!empty($result))
        {
            $result->signedup_on = showUTZ($result->signedup_on, 'd-M-Y H:i:s');
            $result->activated_on = showUTZ($result->activated_on, 'd-M-Y H:i:s');
            $result->dob = showUTZ($result->dob, 'd-M-Y H:i:s');
            $result->profile_image = $this->config->get('constants.ACCOUNT.PROFILE_IMG.WEB.160x160').$result->profile_image;
            if ($result->is_account_verified == $this->config->get('constants.OFF'))
            {
                $result->is_verified = trans('general.account.verify.'.$result->is_account_verified);
                $result->verify_disp_class = $this->config->get('dispclass.member_verify_status.'.$result->is_account_verified);
            }
            if ($result->block == $this->config->get('constants.OFF'))
            {
                $result->status_name = trans('admin/member/member_list.member_account_status.'.$result->status);
                $result->status_disp_class = $this->config->get('dispclass.member_account_status.'.$result->status);
            }
            else
            {
                $result->status_name = trans('admin/member/member_list.member_block_status.'.$result->block);
                $result->status_disp_class = $this->config->get('dispclass.member_block_status.'.$result->block);
            }
        }
        return $result;
    }

    /* Edit Memeber Details */

    public function member_edit ($uname)
    {
        return DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
                        ->join($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                        {
                            $subquery->on('adm.relative_post_id', '=', 'am.account_id')
                            ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                            ->where('adm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.PERMANENT'));
                        })
                        ->join($this->config->get('tables.LOCATION_COUNTRIES').' as lcu', 'lcu.country_id', ' = ', 'adm.country_id')
                        ->selectRaw('am.uname,am.email,am.mobile,ad.firstname,ad.lastname,ad.gender,ad.dob,adm.flatno_street,adm.landmark,adm.address,adm.city_id,adm.district_id,adm.state_id,adm.country_id,adm.postcode')
                        ->where('am.is_deleted', $this->config->get('constants.NOT_DELETED'))
                        ->where('am.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                        ->where('am.uname', $uname)
                        ->first();
    }

    /* Update Memeber Details */

    public function member_update (array $data = array())
    {

        extract($data);
        if (isset($uname) && !empty($uname))
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                    ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                    ->where('uname', $uname)
                    ->select('account_id')
                    ->first();
            if (isset($result->account_id) && $result->account_id > 0)
            {
                $result1 = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $result->account_id)
                        ->update(array(
                    'email'=>$email,
                    'mobile'=>$mobile));

                $result2 = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                        ->where('account_id', $result->account_id)
                        ->update(array(
                    'firstname'=>$firstname,
                    'lastname'=>$lastname,
                    'dob'=>getGTZ($dob, 'Y-m-d'),
                    'gender'=>$gender,
                    'updated_on'=>getGTZ()));
                $result3 = DB::table($this->config->get('tables.ADDRESS_MST'))
                        ->where('relative_post_id', $result->account_id)
                        ->where('post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                        ->where('address_type', '=', $this->config->get('constants.ADDRESS_TYPE.PERMANENT'))
                        ->update(array(
                    'flatno_street'=>$street,
                    'landmark'=>$landmark,
                    'address'=>$location,
                    'city_id'=>$locality_id,
                    'district_id'=>$district_id,
                    'state_id'=>$state_id,
                    'country_id'=>$country_id,
                    'postcode'=>$postcode,
                    'geolatitute'=>$latitude,
                    'geolongitute'=>$longitude));

                return $result1 || $result2 || $result3;
            }
            return NULL;
        }
        return NULL;
    }

    /* Member Block Unblock */

    public function member_block_status (array $data = array())
    {
        $op = array();
        extract($data);
        if (isset($status) && $status == 1)
        {
            return DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                            ->where('uname', $uname)
                            ->update(['block'=>$this->config->get('constants.ON')]);
        }
        else
        {
            return DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                            ->where('uname', $uname)
                            ->update(['block'=>$this->config->get('constants.OFF')]);
        }
    }

    /* Member Active InActive */

    public function member_active_status (array $data = array())
    {
        $op = array();
        extract($data);

        if (isset($status) && $status == 1)
        {
            return DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                            ->where('uname', $uname)
                            ->update(['status'=>$this->config->get('constants.MEMBER_STATUS.ACTIVE')]);
        }
        else
        {
            return DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                            ->where('uname', $uname)
                            ->update(['status'=>$this->config->get('constants.MEMBER_STATUS.INACTIVE')]);
        }
    }

    /* Member Is Verify Not Verify */

    public function member_verify_status (array $data = array())
    {
        $op = array();
        extract($data);
        $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                ->where('uname', $uname)
                ->select('account_id')
                ->first();
        if (isset($result->account_id) && $result->account_id > 0)
        {
            if (isset($status) && $status == 1)
            {
                return DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
                                ->where('account_id', $result->account_id)
                                ->update(['is_account_verified'=>$this->config->get('constants.ON')]);
            }
            else
            {
                return DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
                                ->where('account_id', $result->account_id)
                                ->update(['is_account_verified'=>$this->config->get('constants.OFF')]);
            }
        }
        return false;
    }

    /* Member Login Block */

    public function member_login_block (array $data = array())
    {
        $op = array();
        extract($data);
        $upd = [];
        $upd['block_login'] = $status;
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                        ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                        ->where('block_login', !$status)
                        ->where('uname', $uname)
                        ->update($upd);
    }

    /* Change Member Password */

    public function member_pwd_change ($uname)
    {
        if (isset($uname) && !empty($uname))
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
                    ->selectRaw('am.uname,concat_ws(" ",ad.firstname,ad.lastname) as fullname')
                    ->where('am.is_deleted', $this->config->get('constants.NOT_DELETED'))
                    ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                    ->where('am.uname', $uname)
                    ->first();
            return (!empty($result)) ? $result : NULL;
        }
        return NULL;
    }

    /* Update Member Password  */

    public function member_pwd_update (array $data = array())
    {
        extract($data);
        if (isset($uname) && !empty($uname) && isset($new_pwd) && !empty($new_pwd))
        {
            return DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                            ->where('uname', $uname)
                            ->update(['pass_key'=>md5($new_pwd)]);
        }
    }

    public function get_member_details (array $arr)
    {
        extract($arr);
        $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', '=', 'am.account_id')
                ->where('am.account_type_id', config('constants.ACCOUNT_TYPE.USER'))
                ->where('am.status', config('constants.ON'))
                ->where('am.is_deleted', config('constants.OFF'))
                ->where('am.block', config('constants.OFF'));
        if (isset($member) && !empty($member))
        {
            if (strpos($member, '@') > 0)
            {
				
                $qry->where('am.email', 'like', $member);
                //->where('ast.is_email_verified', config('constants.ON'));
            }
            else if (is_numeric($member))
            {
                $qry->where('am.mobile', $member);
                //->where('ast.is_mobile_verified', config('constants.ON'));
            }
            else
            {
                $qry->where('am.uname', $member);
            }
        }
        elseif (isset($account_id) && !empty($account_id))
        {
            $qry->where('am.account_id', $account_id);
        }
        return  $qry->select('am.uname', 'am.email', 'am.account_id', 'ast.currency_id', 'am.mobile', DB::raw('CONCAT_WS(\' \',ad.firstname,ad.lastname) as full_name', 'ast.currency_id', 'ast.country_id'))
                        ->first();
						
    }

    public function getKYCList (array $arr = [], $count = false)
    {
        //print_r($arr);exit;
        extract($arr);
        $query = DB::table($this->config->get('tables.ACCOUNT_KYC').' as kyc')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acc', function($subquery)
                {
                    $subquery->on('acc.account_id', '=', 'kyc.relative_post_id')
                    ->where('acc.is_deleted', '=', $this->config->get('constants.NOT_DELETED'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'acc.account_id')
                ->join($this->config->get('tables.KYC_DOCUMENT_TYPE').' as dt', 'dt.document_type_id', '=', 'kyc.document_type_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as act', 'act.account_id', '=', 'kyc.created_by')
                ->where('kyc.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('kyc.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'))
                ->where('dt.is_deleted', $this->config->get('constants.NOT_DELETED'));
        if (isset($from) && !empty($from))
        {
            $query->whereDate('kyc.created_date', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $query->whereDate('kyc.created_date', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            $query->where(function($wcond) use($search_text)
            {
                $wcond->Where('acc.uname', 'like', '%'.$search_text.'%')
                        ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', '%'.$search_text.'%');
            });
        }
        if (isset($doc_type) && !empty($doc_type))
        {
            $query->where('kyc.document_type_id', 'like', $doc_type);
        }
        if (isset($status) && !is_null($status) && $status != '')
        {
            $query->where('kyc.is_verified', $status);
        }

        if (isset($count) && !empty($count))
        {
            return $query->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $query->skip($start)->take($length);
            }
            $kycs = $query->select('kyc.kyc_id as id', 'kyc.docfile as doc_name', 'kyc.docpath', 'kyc.created_date', 'kyc.is_verified', 'kyc.post_type', 'kyc.remarks', 'kyc.relative_post_id as relative_id', 'acc.uname', DB::raw('CONCAT_WS(" ",ad.firstname,ad.lastname) as full_name'), 'dt.document_type_id', 'dt.type as doc_type_name', DB::raw('CONCAT_WS(" ",act.firstname,act.lastname) as uploaded_by'))->get();
            array_walk($kycs, function(&$kyc)
            {
                $kyc->created_date = showUTZ($kyc->created_date, 'd-M-Y H:i:s');
                $kyc->document = asset($this->config->get('constants.ACCOUNT.KYC_PATH.WEB').$kyc->docpath);
                $kyc->actions = [];
                if (in_array($kyc->is_verified, [ $this->config->get('constants.KYC.IS_VERIFIED.PENDING'), $this->config->get('constants.KYC.IS_VERIFIED.REJECTED')]))
                {
                    $kyc->actions[] = ['url'=>route('admin.member.kyc.verify', [ 'id'=>$kyc->id, 'is_verified'=>strtolower('VERIFIED')]), 'label'=>trans('general.kyc_list.is_verified.'.$this->config->get('constants.KYC.IS_VERIFIED.VERIFIED'))];
                }
                if (in_array($kyc->is_verified, [ $this->config->get('constants.KYC.IS_VERIFIED.PENDING'), $this->config->get('constants.KYC.IS_VERIFIED.VERIFIED')]))
                {
                    $kyc->actions[] = ['url'=>route('admin.member.kyc.verify', [ 'id'=>$kyc->id, 'is_verified'=>strtolower('REJECTED')]), 'label'=>trans('general.kyc_list.is_verified.'.$this->config->get('constants.KYC.IS_VERIFIED.REJECTED'))];
                }
                $kyc->actions[] = ['url'=>route('admin.member.kyc.delete', [ 'id'=>$kyc->id]), 'label'=>trans('general.btn.delete')];
                $kyc->verify_disp_class = $this->config->get('dispclass.dsa_kyc_verify_status.'.$kyc->is_verified);
                $kyc->is_verified = trans('admin/dsa/kyc.kyc_verify_status.'.$kyc->is_verified);
            });
            return $kycs;
        }
    }

    public function updateKYCVerification (array $arr = array())
    {
        extract($arr);
        $data = [];
        $data['is_verified'] = $is_verified;
        $data['updated_date'] = getGTZ();
        $qry = DB::table($this->config->get('tables.ACCOUNT_KYC'))
                ->where('kyc_id', $id);
        if ($is_verified == $this->config->get('constants.KYC.IS_VERIFIED.VERIFIED'))
        {
            $qry->whereIn('is_verified', [$this->config->get('constants.KYC.IS_VERIFIED.PENDING'), $this->config->get('constants.KYC.IS_VERIFIED.REJECTED')]);
            $data['verified_by'] = $account_id;
        }
        else if ($is_verified == $this->config->get('constants.KYC.IS_VERIFIED.REJECTED'))
        {
            $qry->whereIn('is_verified', [$this->config->get('constants.KYC.IS_VERIFIED.PENDING'), $this->config->get('constants.KYC.IS_VERIFIED.VERIFIED')]);
        }
        return $qry->update($data);
    }

    public function deleteKYC (array $arr = array())
    {
        extract($arr);
        return DB::table($this->config->get('tables.ACCOUNT_KYC'))
                        ->where('kyc_id', $id)
                        ->update([
                            'updated_date'=>getGTZ(),
                            'is_deleted'=>$this->config->get('constants.ON')
        ]);
    }

    public function getKYCDocTypes ()
    {
        return DB::table($this->config->get('tables.KYC_DOCUMENT_TYPE'))
                        ->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                        ->where('status', $this->config->get('constants.ON'))
                        ->lists('type', 'document_type_id');
    }

    public function getReviewsList (array $arr = array(), $count = false)
    {
        extract($arr);
        $reviews = DB::table($this->config->get('tables.RATINGS_INFO').' as ri')
                ->join($this->config->get('tables.POST_TYPES').' as pt', 'pt.post_type', '=', 'ri.post_type')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acc', function($subquery)
                {
                    $subquery->on('acc.account_id', '=', 'ri.account_id')
                    ->where('acc.is_deleted', '=', $this->config->get('constants.NOT_DELETED'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'acc.account_id')
                ->join($this->config->get('tables.MERCHANT_STORE_MST').' as sm', 'sm.store_id', '=', 'ri.post_id');
        if (isset($from) && !empty($from))
        {
            $reviews->whereDate('kyc.created_date', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $reviews->whereDate('kyc.created_date', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            $reviews->where(function($wcond) use($search_text)
            {
                $wcond->Where('acc.uname', 'like', '%'.$search_text.'%')
                        ->orwhere(DB::Raw('concat_ws(" ",ad.firstname,ad.lastname)'), 'like', '%'.$search_text.'%');
            });
        }
        if ($count)
        {
            return $reviews->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $reviews->skip($start)->take($length);
            }
            $reviews = $reviews->selectRaw('ri.id,ri.rating,ri.feedback,ri.status_id as status,ri.is_abused,ri.is_verified,ri.created_date,pt.post,concat(sm.store_name,\' (\',sm.store_code,\')\') as post_data,concat_ws(\' \',ad.firstname,ad.lastname) as full_name')->get();
            array_walk($reviews, function($review)
            {
                $review->created_date = showUTZ($review->created_date);
                $review->actions = [];
                if ($review->status == $this->config->get('constants.REVIEWS_STATUS.UPUBLLISHED'))
                {
                    $review->actions[] = ['url'=>route('admin.member.reviews.update-status', [ 'id'=>$review->id, 'status'=>strtolower('PUBLISHED')]), 'label'=>trans('general.rating.status.'.$this->config->get('constants.REVIEWS_STATUS.PUBLISHED'))];
                }
                else
                {
                    $review->actions[] = ['url'=>route('admin.member.reviews.update-status', [ 'id'=>$review->id, 'status'=>strtolower('UPUBLLISHED')]), 'label'=>trans('general.rating.status.'.$this->config->get('constants.REVIEWS_STATUS.UPUBLLISHED'))];
                }
                if (in_array($review->is_verified, [ $this->config->get('constants.REVIEWS_IS_VERIFIED.PENDING'), $this->config->get('constants.REVIEWS_IS_VERIFIED.REJECTED')]))
                {
                    $review->actions[] = ['url'=>route('admin.member.reviews.verify', [ 'id'=>$review->id, 'is_verified'=>strtolower('VERIFIED')]), 'label'=>trans('general.rating.is_verified.'.$this->config->get('constants.REVIEWS_IS_VERIFIED.VERIFIED'))];
                }
                if (in_array($review->is_verified, [ $this->config->get('constants.REVIEWS_IS_VERIFIED.PENDING'), $this->config->get('constants.REVIEWS_IS_VERIFIED.VERIFIED')]))
                {
                    $review->actions[] = ['url'=>route('admin.member.reviews.verify', [ 'id'=>$review->id, 'is_verified'=>strtolower('REJECTED')]), 'label'=>trans('general.rating.is_verified.'.$this->config->get('constants.REVIEWS_IS_VERIFIED.REJECTED'))];
                }
                $review->status_class = $this->config->get('dispclass.rating.status.'.$review->status);
                $review->status = trans('general.rating.status.'.$review->status);
                $review->is_verified_class = $this->config->get('dispclass.rating.is_verified.'.$review->is_verified);
                $review->is_verified = trans('general.rating.is_verified.'.$review->is_verified);
            });
            return $reviews;
        }
    }

    public function updateReviewVerification (array $arr = array())
    {
        extract($arr);
        $data = [];
        $data['is_verified'] = $is_verified;
        $data['updated_by'] = $account_id;
        $data['updated_on'] = getGTZ();
        $qry = DB::table($this->config->get('tables.RATINGS_INFO'))
                ->where('id', $id);
        if ($is_verified == $this->config->get('constants.REVIEWS_IS_VERIFIED.VERIFIED'))
        {
            $qry->whereIn('is_verified', [$this->config->get('constants.REVIEWS_IS_VERIFIED.PENDING'), $this->config->get('constants.REVIEWS_IS_VERIFIED.REJECTED')]);
        }
        else if ($is_verified == $this->config->get('constants.REVIEWS_IS_VERIFIED.REJECTED'))
        {
            $qry->whereIn('is_verified', [$this->config->get('constants.REVIEWS_IS_VERIFIED.PENDING'), $this->config->get('constants.REVIEWS_IS_VERIFIED.VERIFIED')]);
        }
        return $qry->update($data);
    }

    public function updateReviewStatus (array $arr = array())
    {
        extract($arr);
        $data = [];
        $data['status_id'] = $status;
        $data['updated_by'] = $account_id;
        $data['updated_on'] = getGTZ();
        $qry = DB::table($this->config->get('tables.RATINGS_INFO'))
                ->where('id', $id);
        if ($status == $this->config->get('constants.REVIEWS_STATUS.PUBLISHED'))
        {
            $qry->where('status_id', $this->config->get('constants.REVIEWS_STATUS.UPUBLLISHED'));
        }
        else if ($status == $this->config->get('constants.REVIEWS_STATUS.UPUBLLISHED'))
        {
            $qry->where('status_id', $this->config->get('constants.REVIEWS_STATUS.PUBLISHED'));
        }
        return $qry->update($data);
    }

    public function getTopEarners (array $arr = array(), $count = false, $countries_list = false, $currency_list = false)
    {
        extract($arr);
        DB::select('SET @curRank = 0;');
        $qry = DB::table($this->config->get('tables.REFERRAL_EARNINGS').' as s')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acc', function($subquery)
                {
                    $subquery->on('acc.account_id', '=', 's.to_account_id')
                    ->where('acc.is_deleted', '=', $this->config->get('constants.NOT_DELETED'))
                    ->where('acc.account_type_id', '=', $this->config->get('constants.ACCOUNT.TYPE.USER'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'acc.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 's.currency_id')
                ->join($this->config->get('tables.WALLET_LANG').' as w', function($w)
                {
                    $w->on('w.wallet_id', '=', 's.wallet_id')
                    ->where('w.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->leftJoin($this->config->get('tables.ADDRESS_MST').' as addm', function($join)
                {
                    $join->on('addm.relative_post_id', '=', 'acc.account_id');
                    $join->where('addm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'));
                    $join->where('addm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.RESIDENCE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_LOCALITIES').' as ll', function($lc)
                {
                    $lc->on('ll.locality_id', '=', 'addm.city_id')
                    ->where('ll.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', function($ld)
                {
                    $ld->on('ld.district_id', '=', 'addm.district_id')
                    ->where('ld.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_COUNTRIES').' as lc', function($lc)
                {
                    $lc->on('lc.country_id', '=', 'addm.country_id')
                    ->where('lc.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->where('s.status', $this->config->get('constants.EARNINGS.STATUS.RELEASED'))
                ->where('s.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->groupby('s.to_account_id', 's.wallet_id', 's.currency_id');
        if ($countries_list)
        {
            return $qry->selectRaw('lc.country_id as id,lc.country')
                            ->orderby('country', 'ASC')
                            ->groupby('lc.country_id')
                            ->distinct('lc.country_id')
                            ->lists('country', 'id');
        }
        if ($currency_list)
        {
            return $qry->selectRaw('c.id,c.code')
                            ->orderby('code', 'ASC')
                            ->groupby('c.id')
                            ->distinct('c.id')
                            ->lists('c.code', 'c.id');
        }
        if (isset($currency) && !empty($currency))
        {
            $qry->whereIn('c.id', $currency);
        }
        if (isset($country) && !empty($country))
        {
            $qry->whereIn('addm.country_id', $country);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('s.updated_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('s.updated_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            $search_text = '%'.$search_text.'%';
            $qry->where(DB::raw('concat_ws(\' \',ad.firstname,ad.lastname)'), 'like', $search_text)
                    ->orWhere('uname', 'like', $search_text)
                    ->orWhere('mobile', 'like', $search_text)
                    ->orWhere('email', 'like', $search_text);
        }
        if ($count)
        {
            return $qry->count(DB::raw('DISTINCT(s.to_account_id)'));
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $qry->skip($start)->take($length);
            }
            if (isset($orderby) && !empty($orderby) && isset($order) && !empty($order))
            {
                $qry->orderby($orderby, $order);
            }
            else
            {
                $qry->orderby('earned', 'DESC');
            }
            $accounts = $qry->selectRaw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name,uname,mobile,email,sum(s.amount) as earned,w.wallet,c.code,c.decimal_places,c.currency_symbol,lc.country,coalesce(ld.district,ll.locality) as city,@curRank := @curRank + 1 AS rank')
                    ->get();
            array_walk($accounts, function(&$account)
            {
                $account->earned = CommonLib::currency_format($account->earned, ['currency_symbol'=>$account->currency_symbol, 'currency_code'=>$account->code, 'decimal_places'=>$account->decimal_places], true, true);
                unset($account->code);
                unset($account->currency_symbol);
                unset($account->decimal_places);
            });
            return $accounts;
        }
    }

    public function getTopReferrers (array $arr = array(), $count = false, $countries_list = false)
    {
        extract($arr);
        DB::select('SET @curRank = 0;');
        $qry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as s')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acc', function($subquery)
                {
                    $subquery->on('acc.account_id', '=', 's.referred_account_id')
                    ->where('acc.is_deleted', '=', $this->config->get('constants.NOT_DELETED'))
                    ->where('acc.account_type_id', '=', $this->config->get('constants.ACCOUNT.TYPE.USER'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'acc.account_id')
                ->leftJoin($this->config->get('tables.ADDRESS_MST').' as addm', function($join)
                {
                    $join->on('addm.relative_post_id', '=', 'acc.account_id');
                    $join->where('addm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'));
                    $join->where('addm.address_type', '=', $this->config->get('constants.ADDRESS_TYPE.RESIDENCE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_LOCALITIES').' as ll', function($lc)
                {
                    $lc->on('ll.locality_id', '=', 'addm.city_id')
                    ->where('ll.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', function($ld)
                {
                    $ld->on('ld.district_id', '=', 'addm.district_id')
                    ->where('ld.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->leftJoin($this->config->get('tables.LOCATION_COUNTRIES').' as lc', function($lc)
                {
                    $lc->on('lc.country_id', '=', 'addm.country_id')
                    ->where('lc.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->whereNotNull('s.referred_account_id')
                ->groupby('s.referred_account_id');
        if ($countries_list)
        {
            return $qry->selectRaw('lc.country_id as id,lc.country')
                            ->orderby('country', 'ASC')
                            ->groupby('lc.country_id')
                            ->distinct('lc.country_id')
                            ->lists('country', 'id');
        }
        if (isset($country) && !empty($country))
        {
            $qry->whereIn('addm.country_id', $country);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('acc.signedup_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('acc.signedup_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            $search_text = '%'.$search_text.'%';
            $qry->where(function($s) use($search_text)
            {
                $s->where(DB::raw('concat_ws(\' \',ad.firstname,ad.lastname)'), 'like', $search_text)
                        ->orWhere('uname', 'like', $search_text)
                        ->orWhere('mobile', 'like', $search_text)
                        ->orWhere('email', 'like', $search_text);
            });
        }
        if ($count)
        {
            return $qry->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $qry->skip($start)->take($length);
            }
            if (isset($orderby) && !empty($orderby) && isset($order) && !empty($order))
            {
                $qry->orderby($orderby, $order);
            }
            else
            {
                $qry->orderby('reffered_count', 'DESC');
            }
            $accounts = $qry->selectRaw('concat_ws(\' \',ad.firstname,ad.lastname) as full_name,uname,mobile,email,count(s.account_id) as reffered_count,lc.country,coalesce(ld.district,ll.locality) as city,@curRank := @curRank + 1 AS rank')
                    ->get();
            return $accounts;
        }
    }

    public function deleteMember (array $arr = array())
    {
        extract($arr);
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('uname', $uname)
                        ->update(['is_deleted'=>$this->config->get('constants.ON')]);
    }

}
