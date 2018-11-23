<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Api\APIBase;
use App\Models\Api\Seller\APISupplierReport;
use Input;
use Config;
use Response;
use Lang;
use URL;
use Session;
use DB;

class APISupplierReportsController extends APIBase
{

    public function __construct ()
    {
        parent::__construct();
        $this->reportObj = new APISupplierReport();
    }

    public function payments ()
    {
        $data = $ajaxdata = $filter = array();
        $data['supplier_id'] = $this->supplier_id;
        $post = Input::all();
        $ajaxdata['data'] = array();
        $ajaxdata['draw'] = $post['draw'];
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->reportObj->supplier_payment_report($data, true);
        $filter['from'] = isset($post['from']) && !empty($post['from']) ? $post['from'] : '';
        $filter['to'] = isset($post['to']) && !empty($post['to']) ? $post['to'] : '';
        $filter['term'] = isset($post['term']) && !empty($post['term']) ? $post['term'] : '';
        if ($ajaxdata['recordsTotal'] > 0)
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->reportObj->supplier_payment_report($data, true);
            }
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
                $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                if (isset($post['order']))
                {
                    $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $data['order'] = $post['order'][0]['dir'];
                }
                $ajaxdata['data'] = $this->reportObj->supplier_payment_report($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function transaction_log ()
    {
        $data = $ajaxdata = $filter = array();
        $post = Input::all();
        $data['account_id'] = $this->account_id;
        $filter['from'] = isset($post['from']) && !empty($post['from']) ? $post['from'] : '';
        $filter['to'] = isset($post['to']) && !empty($post['to']) ? $post['to'] : '';
        $filter['term'] = isset($post['term']) && !empty($post['term']) ? $post['term'] : '';
        $filter['ewallet_id'] = isset($post['ewallet_id']) && !empty($post['ewallet_id']) ? $post['ewallet_id'] : '';
        $ajaxdata['data'] = array();
        $ajaxdata['draw'] = $post['draw'];
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->reportObj->getTransactionDetails($data, true);
        if ($ajaxdata['recordsTotal'] > 0)
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->reportObj->getTransactionDetails($data, true);
            }
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
                $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                if (isset($post['order']))
                {
                    $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
                    $data['order'] = $post['order'][0]['dir'];
                }
                $ajaxdata['data'] = $this->reportObj->getTransactionDetails($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

}
