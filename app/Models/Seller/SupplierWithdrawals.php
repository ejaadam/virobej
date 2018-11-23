<?php

class SupplierWithdrawals extends Eloquent
{

    public function withdrawal_wallet_balance_list ($arr = array())
    {
        $res = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE').' as wbt')
                ->join(Config::get('tables.WALLET').' as wa', 'wa.wallet_id', '=', 'wbt.wallet_id')
                ->join(Config::get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wbt.currency_id')
                ->where('wa.withdrawal_status', Config::get('constants.ON'))
                ->where('wbt.account_id', $arr['account_id'])
                ->selectRaw('wbt.current_balance,ci.currency');
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            return $res->get();
        }
    }

}

?>
