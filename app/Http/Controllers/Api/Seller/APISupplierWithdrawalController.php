<?php

class APISupplierWithdrawalController extends APIBase
{

    public function __construct ()
    {
        parent::__construct();
        $this->withdrawObj = new APISupplierWithdraw($this->commonObj);
    }

    public function paymentDetails ()
    {
        $op = [];
        $this->statusCode = 422;
        $data = Input::all();
        if (Input::has('payment_key') && ($paymentType = $this->withdrawObj->paymentTypeDetails(Input::get('payment_key'))))
        {
            $data['payment_type_id'] = $paymentType->payment_type_id;
            $data['currency_id'] = Input::has('currency_id') ? Input::get('currency_id') : $this->currency_id;
            $data['account_id'] = $this->account_id;
            if ($paymentType && !empty($paymentType) && (empty($paymentType->currency_allowed) || (!empty($paymentType->currency_allowed) && array_key_exists($data['currency_id'], $paymentType->currency_allowed))) && ($paymentType->is_country_based == Config::get('constants.OFF') || ($paymentType->is_country_based == Config::get('constants.ON') && isset($paymentType->countries_allowed[$data['currency_id']]))))
            {
                if ($paymentType->is_user_country_based == Config::get('constants.OFF') || ($paymentType->is_user_country_based == Config::get('constants.ON') && in_array($this->user_details->country_id, $paymentType->countries_allowed[$data['currency_id']])))
                {
                    $settings = $this->withdrawObj->get_balance_bycurrency($data);
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['max'];
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed)
                            {
                                $op = array_merge((array) $op, $settings);
                                $op['amount'] = $data['amount'] = $total_breakdowns;
                                $op['account_details'] = $this->withdrawObj->get_preBank_info($data);
                                $op['currency_code'] = $settings['currency_code'];
                                $op['currency_symbol'] = $settings['currency_symbol'];
                                //   $op['countries'] = ($paymentType->is_country_based == Config::get('constants.ON')) ? $this->commonObj->getCountryList($paymentType->countries_allowed[$data['currency_id']]) : $this->commonObj->getCountryList();
                                unset($paymentType->payment_type_id);
                                unset($paymentType->is_country_based);
                                unset($paymentType->is_user_country_based);
                                unset($paymentType->countries_not_allowed);
                                unset($paymentType->countries_allowed);
                                $op['payment_type_details'] = $paymentType;
                                $this->statusCode = 200;
                            }
                            else
                            {
                                $op['msg'] = Lang::get('withdrawal.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] = Lang::get('withdrawal.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] = Lang::get('general.please_contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] = Lang::get('withdrawal.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] = Lang::get('general.please_contact_administrator');
            }
            return Response::json($op, $this->statusCode, $this->headers, $this->options);
        }
        return App::abort(404);
    }

    public function saveWithdraw ()
    {
        $op = [];
        $this->statusCode = 422;
        $data = Input::all();
        if (Input::has('payment_key') && ($paymentType = $this->withdrawObj->paymentTypeDetails(Input::get('payment_key'))))
        {
            $data['payment_type_id'] = $paymentType->payment_type_id;
            $data['currency_id'] = Input::has('currency_id') ? Input::get('currency_id') : $this->currency_id;
            $data['account_id'] = $this->account_id;
            if ($paymentType && !empty($paymentType) && (empty($paymentType->currency_allowed) || (!empty($paymentType->currency_allowed) && array_key_exists($data['currency_id'], $paymentType->currency_allowed))) && ($paymentType->is_country_based == Config::get('constants.OFF') || ($paymentType->is_country_based == Config::get('constants.ON') && isset($paymentType->countries_allowed[$data['currency_id']]))))
            {
                if ($paymentType->is_user_country_based == Config::get('constants.OFF') || ($paymentType->is_user_country_based == Config::get('constants.ON') && in_array($this->user_details->country_id, $paymentType->countries_allowed[$data['currency_id']])))
                {
                    $settings = $this->withdrawObj->get_balance_bycurrency($data);
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['balance'];
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed && $total_breakdowns == $data['amount'])
                            {
                                $validation = ($paymentType->is_country_based == Config::get('constants.OFF')) ? $paymentType->payment_key : $paymentType->payment_key.'.'.$settings['currency_code'];
                                $validator = Validator::make($data, Config::get('validations.WITHDRAWAL.'.$validation.'.RULES'), Config::get('validations.WITHDRAWAL.'.$validation.'.MESSAGES'));
                                if (!$validator->fails())
                                {
                                    unset($settings['breakdowns']);
                                    $data = array_merge((array) $data, $settings);
                                    $op = array_merge((array) $op, $settings);
                                    $op['account_details'] = $this->withdrawObj->get_preBank_info($data);
                                    $op['currency_code'] = $settings['currency_code'];
                                    $op['currency_symbol'] = $settings['currency_symbol'];
                                    //   $op['countries'] = ($paymentType->is_country_based == Config::get('constants.ON')) ? $this->commonObj->getCountryList($paymentType->countries_allowed[$data['currency_id']]) : $this->commonObj->getCountryList();
                                    $op['payment_type_details'] = $paymentType;
                                    if ($this->withdrawObj->saveWithdrawal($data))
                                    {
                                        $this->statusCode = 200;
                                        $op['msg'] = Lang::get('withdrawal.request_updated_successfully');
                                    }
                                    else
                                    {
                                        $op['msg'] = Lang::get('general.something_went_wrong');
                                    }
                                }
                                else
                                {
                                    $op['error'] = $validator->messages(true);
                                }
                            }
                            else
                            {
                                $op['msg'] = Lang::get('withdrawal.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] = Lang::get('withdrawal.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] = Lang::get('general.please_contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] = Lang::get('withdrawal.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] = Lang::get('general.please_contact_administrator');
            }
            return Response::json($op, $this->statusCode, $this->headers, $this->options);
        }
        return App::abort(404);
    }

    public function paymentTypesList ()
    {
        $data = [];
        $this->statusCode = 200;
        $data['payment_types'] = $this->withdrawObj->withdrawal_payment_list();
        return Response::json($data, $this->statusCode, $this->headers, $this->options);
    }

    public function withdrawal_list ($status)
    {
        $op = $data = $filter = array();
        $data['status_array'] = array('pending'=>0, 'transferred'=>1, 'processing'=>2, 'cancelled'=>3, 0=>'pending', 1=>'transferred', 2=>'processing', 3=>'cancelled');
        $data['account_id'] = $this->account_id;
        if (in_array($status, $data['status_array']))
        {
            $data['pg_title'] = ucwords($status).' Withdrawals';
            $data['status'] = $data['status_array'][$status];
            $data['status_label_array'] = array('label label-warning', 'label label-success', 'label label-info', 'label label-danger');
        }
        $post = Input::all();

        if (Request::ajax())
        {
            $filter['from'] = isset($post['from']) ? $post['from'] : '';
            $filter['to'] = isset($post['to']) ? $post['to'] : '';
            $filter['search_term'] = isset($post['search_term']) ? $post['search_term'] : '';
            $filter['payment_type_id'] = isset($post['payout_type']) ? $post['payout_type'] : '';
            $filter['currency_id'] = isset($post['currency']) ? $post['currency'] : '';

            $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : 1;
            if ($ajaxdata['draw'] == 1 || isset($postdata['withFilters']))
            {
                $ajaxdata['filters']['payment_types'] = $this->withdrawObj->withdrawal_list($data, false, true) + [0=>'All'];
                ksort($ajaxdata['filters']['payment_types']);
                $ajaxdata['filters']['currencies'] = $this->withdrawObj->withdrawal_list($data, false, false, true) + [0=>'All'];
                ksort($ajaxdata['filters']['currencies']);
            }
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->withdrawObj->withdrawal_list($data, true);
            $ajaxdata['data'] = [];
            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->withdrawObj->withdrawal_list($data, true);
                }
                if ($ajaxdata['recordsFiltered'])
                {
                    $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
                    $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $data['order'] = $post['order'][0]['dir'];
                    $ajaxdata['data'] = $this->withdrawObj->withdrawal_list($data);
                }
            }
            $this->statusCode = 200;
            return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
        }
    }

    public function withdrawalDetails ()
    {
        $postdata = Input::all();
        $this->statusCode = 422;
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $op['details'] = $this->withdrawObj->getWithdrawalDetails($postdata);
            if (!empty($op['details']))
            {
                $this->statusCode = 200;
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateWithdrawalStatus ()
    {
        $postdata = Input::all();
        $this->statusCode = 422;
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            if ($this->withdrawObj->updateWithdrawalStatus($postdata))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

}
