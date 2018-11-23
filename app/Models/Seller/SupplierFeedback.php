<?php

class SupplierFeedback extends Eloquent
{

    public function feedbacks_list ($arr = array(), $count = false)
    {

        $res = DB::table(Config::get('tables.ACCOUNT_FEEDBACK').' as a')//feedbacks
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'a.account_id')
                ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.account_id', '=', 'mst.account_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mst.account_id')
                ->where('a.is_deleted', Config::get('constants.OFF'))
                ->where('sup.supplier_id', $arr['supplier_id'])
                ->where('mst.account_type_id', $arr['account_type_id'])
                ->selectRaw('a.*,mst.uname,concat(um.firstname,um.lastname) as full_name,sup.company_name,mst.account_type_id,a.status');
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['search_term']) && !empty($arr['search_term']))
        {
            $a = $arr['search_term'];
            $res->where(function($res1) use ($a)
            {
                $res1->where('um.firstname', 'LIKE', '%'.$a.'%');
                $res1->orWhere('um.lastname', 'LIKE', '%'.$a.'%');
                $res1->orWhere('mst.uname', 'LIKE', '%'.$a.'%');
                $res1->orwhere('a.subject', 'LIKE', '%'.$a.'%');
            });
        }
        if (isset($arr['status']) && !empty($arr['status']))
        {

            $res1->where('a.status', 'LIKE', '%'.$arr['status'].'%');
        }
        if (isset($arr['dep_id']) && !empty($arr['dep_id']))
        {
            $res->where('a.department_id', $arr['dep_id']);
        }
        if (!empty($from))
        {
            $query->whereRaw('a.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (!empty($to))
        {
            $query->whereRaw('a.created_on', '<=', date('Y-m-d', strtotime($to)));
        }

        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('a.created_on', 'desc');
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            return $res->get();
        }
    }

    public function feedbacks_details ($feedback_id)
    {
        if (!empty($feedback_id))
        {
            $wdata = array();
            $wdata['feedback_id'] = $feedback_id;
            return DB::table(Config::get('tables.ACCOUNT_FEEDBACK').' as a')
                            ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'a.account_id')
                            ->join(Config::get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mst.account_id')
                            //->where('um.company_id', $arr['company_id'])
                            ->where('a.feedback_id', $wdata['feedback_id'])
                            ->selectRaw('a.*, concat(um.firstname,um.lastname) as full_name,mst.uname,mst.email')
                            ->first();
        }
    }

    public function save_feedback ($data = array())
    {
        if (!empty($data))
        {
            $data['status'] = Config::get('constants.ACTIVE'); //replied->status 0 and new->status 1.
            DB::table(Config::get('tables.ACCOUNT_FEEDBACK'))
                    ->insert($data);
            return true;
        }
        return false;
    }

    public function feedback_replied_details ($feedback_id)
    {
        if (!empty($feedback_id))
        {
            $wdata = array();
            $wdata['feedback_id'] = $feedback_id;
            return DB::table(Config::get('tables.FEEDBACK_REPLIES').' as fr')
                            ->join(Config::get('tables.ACCOUNT_FEEDBACK').' as a', 'a.feedback_id', '=', 'fr.feedback_id')
                            ->join(Config::get('tables.ACCOUNT_TYPES').' as act', 'act.id', '=', 'fr.replied_account_types')
                            ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'a.account_id')
                            ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.account_id', '=', 'mst.account_id')
                            ->join(Config::get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mst.account_id')
                            ->where('fr.feedback_id', $wdata['feedback_id'])
                            ->orderBy('act.account_type_name', 'ASC')
                            //->where('fr.replied_account_types', Config::get('constants.ACCOUNT_TYPE.ADMIN'))
                            ->selectRaw('fr.*,mst.uname,concat(um.firstname,um.lastname) as full_name,mst.email,a.created_on as created,a.subject,a.description,act.id,act.account_type_name')
                            ->get();
        }
        return false;
    }

    public function save_reply_feedback ($data = array())
    {
        $res = DB::table(Config::get('tables.FEEDBACK_REPLIES'))
                ->insertGetId($data);
        if (!empty($res))
        {
            $data1['status'] = Config::get('constants.INACTIVE'); //replied->status 0 and new->status 1.
            DB::table(Config::get('tables.ACCOUNT_FEEDBACK'))
                    ->where('feedback_id', $data['feedback_id'])
                    ->update($data1);
            return true;
        }
        return false;
    }

    public function rating_detail ($wdata = array())
    {

        $res = DB::table(Config::get('constants.TW_RATINGS').' as rat')
                ->join(Config::get('constants.TW_ACCOUNT_EXTRAS').' as um', 'um.account_id', '=', 'rat.account_id')
                ->join(Config::get('constants.TW_SUPPLIER_PRODUCT_ITEMS').' as item', 'item.supplier_id', '=', 'rat.supplier_id')
                ->where('item.supplier_id', $wdata['supplier_id'])
                ->where('item.product_id', $wdata['product_id'])
                ->selectRaw('item.product_name,item.description,concat(um.firstname,um.lastname) as full_name,rat.description as descriptions,rat.rating,rat.created_on')
                ->first();
        if (!empty($res))
        {
            return $res;
        }
        return false;
    }

    public function rating_status_update ($data, $updata)
    {
        if (!empty($data) && !empty($updata))
        {
            return DB::table(Config::get('constants.TW_RATINGS'))
                            ->where('id', $data['id'])
                            ->update($updata);
        }
        return false;
    }

    public function delete_ratings ($wdata, $data)
    {
        $data = array();
        $data['is_deleted'] = Config::get('constants.ON');
        $res = DB::table(Config::get('constants.TW_RATINGS'))
                ->where('supplier_id', $wdata['supplier_id'])
                ->where('id', $wdata['id'])
                ->update($data);
        if ($res)
        {
            return $res;
        }
        else
        {
            return false;
        }
    }

}
