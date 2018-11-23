<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\SupplierFeedback;
use Config;
use Lang;
use Redirect;

class SupplierFeedbackController extends SupplierBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->feedback = new SupplierFeedback();
    }

    public function feedbacks_details ($feedback_id)
    {
        $op = array();
        //$data['company_id'] = $this->company_id;
        $data['feedback_detail'] = $this->feedback->feedbacks_details($feedback_id);
        $str = View::make('supplier.feedback.feedbacks_details', $data)->render();
        if (!empty($str))
        {
            $op['contents'] = $str;
            $op['status'] = 'OK';
        }
        else
        {
            $op['status'] = 'ERR';
        }
        return Response::json($op);
    }

    public function my_feedbacks ()
    {
        return $this->feedback_lists(Config::get('constants.ACCOUNT_TYPE.SUPPLIER'));
    }

    public function feedback_lists ($account_type = 1)
    {

        $data = array();
        $data['show_submit_feedback'] = false;
        $data['title'] = 'Member Feedbacks';
        if ($account_type != 1)
        {
            $data['show_submit_feedback'] = true;
            $data['title'] = 'My Feedbacks';
        }
        $submit = '';
        $post = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if ($post)
        {
            $data['search_term'] = $post['term'];

            $data['account_type_id'] = $account_type;
            if (isset($post['feedback_type']))
            {
                $data['status'] = $post['feedback_type'];
            }

            if (isset($post['submit']))
            {
                $submit = $post['submit'];
            }
            else
            {
                $submit = ' ';
            }
        }

        if (Request::ajax())
        {


            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->feedback->feedbacks_list($data, true);

            $data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
            $data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            if (isset($postdata['order']))
            {
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
            }

            $ajaxdata['data'] = $this->feedback->feedbacks_list($data);
            $ajaxdata['draw'] = $post['draw'];
            $ajaxdata['url'] = URL::to('/');
            return Response::json($ajaxdata);
        }
        else
        {
            //$data['feedback_list'] = $this->feedback->feedbacks_list($data);
            return View::make('supplier.feedback.feedbacks_list', $data);
        }
    }

    public function reply_feedback ($feedback_id)
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $postdata = Input::all();
        $data['feedback_id'] = $feedback_id;
        $str = View::make('supplier.feedback.feedback_reply', $data)->render();
        if (!empty($str))
        {
            $op['contents'] = $str;
            $op['msg'] = Lang::get('general.updated_successfully');
            $op['status'] = 'OK';
        }
        return Response::json($op);
    }

    public function save_reply_feedback ($feedback_id)
    {
        $op = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $data = array();
        $data['replied_account_types'] = Config::get('constants.ACCOUNT_TYPE.SUPPLIER');
        $data['relative_account_id'] = 0;
        $data['feedback_id'] = $feedback_id;
        $data['reply_comments'] = $postdata['description'];
        $data['status'] = Config::get('constants.ACTIVE');
        $data['created_on'] = date('Y-m-d H:i:s');
        $res = $this->feedback->save_reply_feedback($data);
        if ($res > 0)
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.your_comments_replied_successfully');
        }

        return Response::json($op);
    }

    public function replied_feedback ($feedback_id)
    {
        $res['replied'] = $this->feedback->feedback_replied_details($feedback_id);
        $res['feedback_detail'] = $this->feedback->feedbacks_details($feedback_id);
        $str = View::make('supplier.feedback.feedback_replied', $res)->render();
        if (!empty($str))
        {
            $op['contents'] = $str;
            $op['status'] = 'OK';
        }
        else
        {
            $op['status'] = 'ERR';
        }
        return Response::json($op);
    }

    public function submit_feedback ()
    {
        $op = '';
        $op = array();
        $status = 422;
        $op['status'] = 'failed';
        $op['msg'] = Lang::get('general.feedback_submitted_failed');
        $postdata = Input::all();
        $validator = Validator::make($postdata, Config::get('validations.MY_FEEDBACK.RULES'), Config::get('validations.MY_FEEDBACK.MESSAGES'));
        if (!$validator->fails())
        {
            if (!empty($postdata))
            {
                $data['subject'] = $postdata['subject'];
                $data['description'] = $postdata['description'];
                $data['account_type_id'] = Config::get('constants.ACCOUNT_TYPE.SUPPLIER');
                $data['account_id'] = $this->account_id;
                //$data['feedback_type'] = Config::get('constants.ON'); //1- general feed back
                $data['created_on'] = $currentdate = date('Y-m-d H:i:s');
                $res = $this->feedback->save_feedback($data);
                if ($res)
                {
                    $status = 200;
                    $op['status'] = 'OK';
                    $op['msg'] = Lang::get('general.feedback_submitted_successfully');
                }
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $status);
    }

    public function rating_lists ()
    {
        return View::make('supplier.ratings.rating_list');
    }

    public function rating_details ($product_id)
    {
        $op = array();
        $wdata = array();
        $wdata['product_id'] = $product_id;
        $wdata['supplier_id'] = $this->supplier_id;
        $data['rating_detail'] = $this->feedback->rating_detail($wdata);
        $data['account_id'] = $this->account_id;
        $str = View::make('supplier.ratings.rating_details', $data)->render();
        if (!empty($str))
        {
            $op['contents'] = $str;
            $op['status'] = 'OK';
        }
        else
        {
            $op['status'] = 'ERR';
        }
        return Response::json($op);
    }

    public function status_update ($status, $id)
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.we_cound_not_able_to_process_your_request');
        $postdata = Input::all();
        $status_arr = array(
            'publish'=>array(
                'val'=>Config::get('constants.QUIZ_LEVEL_STATUS_PUBLISHED'),
                'next_status'=>'unpublish',
                'status_label'=>'<span class="label label-success" id="active"> Publish </span>'),
            'unpublish'=>array(
                'val'=>Config::get('constants.QUIZ_LEVEL_STATUS_UNPUBLISHED'),
                'next_status'=>'publish',
                'status_label'=>'<span class="label label-danger" id="inactive"> Unpublish </span>')
        );
        if (!empty($id))
        {
            $wdata = '';
            $wdata['id'] = $id;
            $updata['status_id'] = $status_arr[$status]['val'];
            $response = $this->feedback->rating_status_update($wdata, $updata);
            if (!empty($response))
            {
                $op['data']['next_status'] = $status_arr[$status]['next_status'];
                $op['data']['status_label'] = $status_arr[$status]['status_label'];
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.rating.status.'.$updata['status_id']);
            }
        }
        return Response::json($op);
    }

    public function delete_rating ($id)
    {

        if (!empty($id))
        {
            $wdata = '';
            $data = '';
            $op = '';
            $op['status'] = 'ERR';
            $wdata = array();
            $data = array();
            $wdata['id'] = $id;
            $wdata['supplier_id'] = $this->supplier_id;
            $data['is_deleted'] = Config::get('constants.ON');
            $res = $this->feedback->delete_ratings($wdata, $data);
            if (!empty($res))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.product.rating_deleted');
            }

            return Response::json($op);
        }
    }

}
