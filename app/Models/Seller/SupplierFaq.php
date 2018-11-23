<?php

class SupplierFaq extends Eloquent
{

    public function view_faq ()
    {
        $res = DB::table(Config::get('tables.FAQS').' as faq')
                ->join(Config::get('tables.ACCOUNT_MST').' as ae', 'ae.account_id', '=', 'faq.created_by')
                ->selectRaw('concat(ae.firstname,\' \',ae.lastname) as fullname,faq.description,faq.title,faq.created_on,faq.id')
                ->where('faq.is_deleted', Config::get('constants.OFF'))
                ->orderby('faq.created_on', 'desc')
                ->get();
        if (!empty($res))
        {
            return $res;
        }
        else
        {
            return false;
        }
    }

}

?>
