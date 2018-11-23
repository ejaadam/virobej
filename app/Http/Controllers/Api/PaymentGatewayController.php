<?php

namespace App\Http\Controllers\Api;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\Api\CommonModel;

class PaymentGatewayController extends \App\Http\Controllers\BaseController
{

    public function __construct ()
    {
        parent::__construct();
		$this->commObj = new CommonModel();
		$postdata = request()->all();
		Log::info('PG response url: '.$this->request->fullUrl().' from '. $this->request->header('User-Agent').', Data:'.json_encode($postdata)); 
    }

		
    private function checkNull ($value)
    {
        return ($value == null) ? '' : $value;
    }
	
	/* function decrypt($crypt, $key, $type)
	{   
		$enc = MCRYPT_RIJNDAEL_128;
		$mode = MCRYPT_MODE_CBC;
		$iv = "0123456789abcdef";
		$crypt = base64_decode($crypt);
		$padtext = mcrypt_decrypt($enc, base64_decode($key) , $crypt, $mode, $iv);
		$pad = ord($padtext
			{
			strlen($padtext) - 1});
			if ($pad > strlen($padtext)) return false;
			if (strspn($padtext, $padtext
				{
				strlen($padtext) - 1}, strlen($padtext) - $pad) != $pad)
					{
					$text = "Error";
					}

				$text = substr($padtext, 0, -1 * $pad);
				return $text;
	}
	
	public function response_data_formation ($data)
	{		
		//return $data['response'];
		$data['response'] = str_replace("[","{",$data['response']);
		$data['response'] = str_replace("]","\"}",$data['response']);
		$data['response'] = str_replace(": ",": \"",$data['response']);
		$data['response'] = str_replace(", ","\", ",$data['response']);		
		$data['response'] = (string)$data['response'];			
		
		$merchant_key = 'oqUl4D0LqA4plZw4reAX/K3UKJoQdet0k/N6X6K4Y5k=';
		$return_elements = array();
		$post = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];   
		$txn_response = $post['txn_response'] = isset($post['txn_response']) ? $post['txn_response'] : '';
		//return $txn_response = decrypt($post['txn_response'], $merchant_key, 256);
		$txn_response_arr = explode('|', $txn_response);		
		$return_elements['txn_response']['ag_id'] 			= isset($txn_response_arr[0]) ? $txn_response_arr[0] : '';
		$return_elements['txn_response']['me_id'] 			= isset($txn_response_arr[1]) ? $txn_response_arr[1] : '';
		$return_elements['txn_response']['order_no'] 		= isset($txn_response_arr[2]) ? $txn_response_arr[2] : '';
		$return_elements['txn_response']['amount'] 			= isset($txn_response_arr[3]) ? $txn_response_arr[3] : '';		   
		$return_elements['txn_response']['country'] 		= isset($txn_response_arr[4]) ? $txn_response_arr[4] : '';
		$return_elements['txn_response']['currency'] 		= isset($txn_response_arr[5]) ? $txn_response_arr[5] : '';
		$return_elements['txn_response']['txn_date'] 		= isset($txn_response_arr[6]) ? $txn_response_arr[6] : '';
		$return_elements['txn_response']['txn_time'] 		= isset($txn_response_arr[7]) ? $txn_response_arr[7] : '';
		$return_elements['txn_response']['ag_ref'] 			= isset($txn_response_arr[8]) ? $txn_response_arr[8] : '';
		$return_elements['txn_response']['pg_ref'] 			= isset($txn_response_arr[9]) ? $txn_response_arr[9] : '';
		$return_elements['txn_response']['status'] 			= isset($txn_response_arr[10]) ? $txn_response_arr[10] : '';
		$return_elements['txn_response']['txn_type'] 		= isset($txn_response_arr[11]) ? $txn_response_arr[11] : '';
		$return_elements['txn_response']['res_code'] 		= isset($txn_response_arr[11]) ? $txn_response_arr[11] : '';
		$return_elements['txn_response']['res_message'] 	= isset($txn_response_arr[12]) ? $txn_response_arr[12] : '';
		
		$pg_details = $post['pg_details'] = isset($post['pg_details']) ? $post['pg_details'] : '';
		//$pg_details 						= decrypt($post['pg_details'], $merchant_key, 256);
		$pg_details_arr	= explode('|', $pg_details);
		$return_elements['pg_details']['pg_id'] 			= isset($pg_details_arr[0]) ? $pg_details_arr[0] : '';
		$return_elements['pg_details']['pg_name'] 			= isset($pg_details_arr[1]) ? $pg_details_arr[1] : '';
		$return_elements['pg_details']['paymode'] 			= isset($pg_details_arr[2]) ? $pg_details_arr[2] : '';
		$return_elements['pg_details']['emi_months'] 		= isset($pg_details_arr[3]) ? $pg_details_arr[3] : '';

		//Fraud Details
		$fraud_details = $post['fraud_details']	= isset($post['fraud_details']) ? $post['fraud_details'] : '';
		//$fraud_details = decrypt($post['fraud_details'], $merchant_key, 256);
		$fraud_details_arr					= explode('|', $fraud_details);
		$return_elements['fraud_details']['fraud_action'] 	= isset($fraud_details_arr[0]) ? $fraud_details_arr[0] : '';
		$return_elements['fraud_details']['fraud_message'] 	= isset($fraud_details_arr[1]) ? $fraud_details_arr[1] : '';
		$return_elements['fraud_details']['score'] 			= isset($fraud_details_arr[2]) ? $fraud_details_arr[2] : '';

		//Other Details
		$other_details = $post['other_details']	= isset($post['other_details']) ? $post['other_details'] : '';
		//$other_details 						= decrypt($post['other_details'], $merchant_key, 256);
		$other_details_arr					= explode('|', $other_details);
		$return_elements['other_details']['udf_1'] 			= isset($other_details_arr[0]) ? $other_details_arr[0] : '';
		$return_elements['other_details']['udf_2'] 			= isset($other_details_arr[1]) ? $other_details_arr[1] : '';
		$return_elements['other_details']['udf_3'] 			= isset($other_details_arr[2]) ? $other_details_arr[2] : '';
		$return_elements['other_details']['udf_4'] 			= isset($other_details_arr[3]) ? $other_details_arr[3] : '';
		$return_elements['other_details']['udf_5'] 			= isset($other_details_arr[4]) ? $other_details_arr[4] : '';
		
		return json_encode($return_elements);		
	} */
	
	
	public function dataFeed ($payment_type, $id = null)
    {			
        $data = $this->request->all();		
		
		/* if (isset($data['response']) && !empty($data['response'])) 
		{
			$data['response'] = $this->response_data_formation($data);
			//print_r($data['response']);exit;
		}	 */
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);		
        if (isset($data['response']) && !empty($data['response']))
        {
			Log::info('Datafeed url: '.$this->request->fullUrl().', Postdata:'.$data['response']); 
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];                              
        }	
		
		//print_r($payment_type);print_r($data);exit;
        switch ($payment_type)
        {
			case 'safexpay':
                if (isset($data['status']))
                {					
                    $pgr['id'] = !empty($id) ? base64_decode($id) : $data['udf1'];
                    $pgr['payment_status'] = $data['txn_response']['status'] == 'Successful' ? 'CONFIRMED' : 'FAILED';		
                }
                break;           
            case 'pay-u':
                if (isset($data['status']))
                {					
                    $pgr['id'] = !empty($id) ? base64_decode($id) : $data['udf1'];
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';		
                }
                break;           
        }
		//print_r($pgr);exit;	
        $pgr['response'] = $data;		
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';						
        return $this->getResponse($pgr['id'], $pgr, true);
    }
	
	public function success ($payment_type, $id)
    {
        $data = $this->request->all();
		/* if (isset($data['response']) && !empty($data['response'])) 
		{
			$data['response'] = $this->response_data_formation($data);
			//print_r($data['response']);exit;
		} */
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
        if (isset($data['response']) && !empty($data['response']))
        {
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];
        }
        switch ($payment_type)
        {
			case 'safexpay':
                if (isset($data['txn_response']['status']))
                {					
                    $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['udf1']);
                    $pgr['payment_status'] = $data['txn_response']['status'] == 'Successful' ? 'CONFIRMED' : 'FAILED';	
					//print_r($pgr);exit;	
                }
                break;       
            case 'pay-u':
                if (isset($data['status']))
                {
					$pgr['id'] = base64_decode((!empty($id)) ? $id : $data['udf1']);                   
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';

                }
                break;
            case 'pay-dollar':
                if (isset($data['successcode']) && ((isset($data['Ref']) && $data['Ref'] != 'TestDatafeed') || !empty($id)))
                {
                    $gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                    $pgr['payment_status'] = $data['successcode'] == 0 ? 'CONFIRMED' : ($data['successcode'] == 1 ? 'FAILED' : 'CANCELLED');
                    $pgr['id'] = base64_decode(!empty($id) ? $id : $data['Ref']);
                }
                break;
            case 'cashfree':
                $settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                if ($data['signature'] == base64_encode(hash_hmac('sha256', $data['orderId'].$data['orderAmount'].$data['referenceId'].$data['txStatus'].$data['paymentMode'].$data['txMsg'].$data['txTime'], $settings->secretkey, true)))
                {
                    if (isset($data['txStatus']))
                    {
                        $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['orderId']);
                        if ($data['txStatus'] == 'SUCCESS')
                        {
                            $pgr['payment_status'] = 'CONFIRMED';
                        }
                        elseif ($data['txStatus'] == 'CANCELED')
                        {
                            $pgr['payment_status'] = 'CANCELED';
                        }
                        else
                        {
                            $pgr['payment_status'] = 'FAILED';
                        }
                    }
                }
                break;
        }
        $pgr['response'] = $data;
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';		
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function checkSum ($payment_type)
    {
        $op = [];
        switch ($payment_type)
        {
            case 'pay-u':
                $data = $this->request->all();
                $key = 'gtKFFx';
                $salt = 'eCwWELxi';
                $op['payment_hash'] = strtolower(hash('sha512', $key.'|'.$this->checkNull($this->request->txnid).'|'.$this->checkNull($data['amount']).'|'.$this->checkNull($data['productinfo']).'|'.$this->checkNull($data['firstname']).'|'.$this->checkNull($data['email']).'|'.$this->checkNull($data['udf1']).'|'.$this->checkNull($data['udf2']).'|'.$this->checkNull($data['udf3']).'|'.$this->checkNull($data['udf4']).'|'.$this->checkNull($data['udf5']).'||||||'.$salt));
				
                $op['get_merchant_ibibo_codes_hash'] = strtolower(hash('sha512', $key.'|get_merchant_ibibo_codes|default|'.$salt));
                $op['vas_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|vas_for_mobile_sdk|default|'.$salt));
                $op['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|payment_related_details_for_mobile_sdk|default|'.$salt));
                $op['verify_payment_hash'] = strtolower(hash('sha512', $key.'|verify_payment|'.$this->request->txnid.'|'.$salt));
				//print_r($op);exit;
                if ($data['user_credentials'] != NULL && $data['user_credentials'] != '')
                {
                    $op['delete_user_card_hash'] = strtolower(hash('sha512', $key.'|delete_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['get_user_cards_hash'] = strtolower(hash('sha512', $key.'|get_user_cards|'.$data['user_credentials'].'|'.$salt));
                    $op['edit_user_card_hash'] = strtolower(hash('sha512', $key.'|edit_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['save_user_card_hash'] = strtolower(hash('sha512', $key.'|save_user_card|'.$data['user_credentials'].'|'.$salt));
                    $op['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $key.'|payment_related_details_for_mobile_sdk|'.$data['user_credentials'].'|'.$salt));
                }
                $op['send_sms_hash'] = strtolower(hash('sha512', $key.'|send_sms|'.$data['udf3'].'|'.$salt));
                if ($data['offerKey'] != NULL && !empty($data['offerKey']))
                {
                    $op['check_offer_status_hash'] = strtolower(hash('sha512', $key.'|check_offer_status|'.$data['offerKey'].'|'.$salt));
                }
                if ($data['cardBin'] != NULL && !empty($data['cardBin']))
                {
                    $op['check_isDomestic_hash'] = strtolower(hash('sha512', $key.'|check_isDomestic|'.$data['cardBin'].'|'.$salt));
                }
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                break;
            case 'cashfree':
               /*  $op['status'] = 'ERROR';
                $data = $this->request->all();
                $appId = '3135c5ca65a7ec9f54b3830313'; //replace it with your appId
                $secretKey = '4ca06060c5c649754e055317725a0067e23cb9db'; //replace it with your secret key
                Log::info('Cashfree Input : '.json_encode($data).' from '.$this->request->header('User-Agent'));
                //$gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                //$gateway_settings=$gateway_settings->status?$gateway_settings->live:$gateway_settings->sandbox;
                if (isset($data['orderId']) && isset($data['orderAmount']) && isset($data['customerEmail']) && isset($data['customerPhone']))
                {
                    $op['status'] = 'OK';
                    $op['orderId'] = $data['orderId'];
                    $checksumData = '';
                    if (stripos($this->request->header('User-Agent'), 'Android') === false)
                    {
                        $checksumData = 'appId='.$appId.'&orderId='.$data['orderId'].'&orderAmount='.$data['orderAmount'].'&customerEmail='.$data['customerEmail'].'&customerPhone='.$data['customerPhone'].'&orderCurrency='.$data['orderCurrency'];
                    }
                    else
                    {
                        ksort($data);
                        foreach ($data as $key=> $value)
                        {
                            $checksumData .= $key.$value;
                        }
                    }
                    $op['checksum'] = base64_encode(hash_hmac('sha256', $checksumData, $secretKey, true));
                    DB::table($this->config->get('tbl.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $data['orderId'])
                            ->update(['checksum'=>$op['checksum']]);
                }
                $this->statusCode = $this->config->get('httperr.SUCCESS'); */
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function _return ($payment_type, $id)
    {
        $data = $this->request->all();		
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);		
        switch ($payment_type)
        {
            case 'pay-u':
                if (isset($data['status']))
                {
                    $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['udf1']);
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
					//print_r($pgr);exit;
                }
                break;
            case 'pay-dollar':
                /* if (isset($data['successcode']) && ((isset($data['Ref']) && $data['Ref'] != 'TestDatafeed') || !empty($id)))
                {
                    $gateway_settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                    $pgr['payment_status'] = $data['successcode'] == 0 ? 'CONFIRMED' : ($data['successcode'] == 1 ? 'FAILED' : 'CANCELLED');
                    $pgr['id'] = base64_decode(!empty($id) ? $id : $data['Ref']);
                }
                bre */ak;
            case 'cashfree':
                /* $settings = $this->commonObj->getPaymentGateWayDeatils($payment_type);
                if ($data['signature'] == base64_encode(hash_hmac('sha256', $data['orderId'].$data['orderAmount'].$data['referenceId'].$data['txStatus'].$data['paymentMode'].$data['txMsg'].$data['txTime'], $settings->secretKey, true)))
                {
                    if (isset($data['txStatus']))
                    {
                        $pgr['id'] = base64_decode((!empty($id)) ? $id : $data['orderId']);
                        if ($data['txStatus'] == 'SUCCESS')
                        {
                            $pgr['payment_status'] = 'CONFIRMED';
                        }
                        elseif ($data['txStatus'] == 'CANCELED')
                        {
                            $pgr['payment_status'] = 'CANCELED';
                        }
                        else
                        {
                            $pgr['payment_status'] = 'FAILED';
                        }
                    }
                } */
                break;
        }
        $pgr['response'] = $data;
        $pgr['payment_status'] = (!empty($pgr['payment_status'])) ? $pgr['payment_status'] : 'FAILED';
        return $this->getResponse($pgr['id'], $pgr);
    }    

    public function failure ($payment_type, $id)
    {
        $data = $this->request->all();
		
        $pgr = $op = [];
        $pgr['id'] = base64_decode($id);
		
        if (isset($data['response']) && !empty($data['response']))
        {
            $data = is_string($data['response']) ? json_decode($data['response'], true) : $data['response'];
        }
		//print_r($data);exit;
        switch ($payment_type)
        {
            case 'pay-u':
                $gateway_settings = $this->commObj->getPaymentGateWayDetails($payment_type);					
                If (isset($data['additionalCharges']))
                {
                    $retHashSeq = $data['additionalCharges'].'|'.$gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
                }
                else
                {
                    $retHashSeq = $gateway_settings->salt.'|'.$data['status'].'|||||||||||'.$data['email'].'|'.$data['firstname'].'|'.$data['productinfo'].'|'.$data['amount'].'|'.$data['txnid'].'|'.$gateway_settings->key;
                }				
                $hash = hash('sha512', $retHashSeq);
                if ($hash != $data['hash'])
                {
                    $pgr['payment_status'] = 'FAILED';
                }
                else
                {
                    $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
                }		
				$pgr['response'] = $data;					
                break;
            case 'pay-dollar':
                $pgr['payment_status'] = $data['status'] == 'success' ? 'CONFIRMED' : 'FAILED';
                break;
        }		
		//print_r($pgr);exit;
        return $this->getResponse($pgr['id'], $pgr);
    }

    public function cancelled ($payment_type, $id)
    {
        $pgr = [];
        $pgr['id'] = base64_decode($id);
        //$pgr['payment_type'] = $payment_type;
        $pgr['response'] = $this->request->all();
        switch ($payment_type)
        {
            case 'pay-u':
                $pgr['payment_status'] = 'CANCELLED';
                break;
        }
        return $this->getResponse($pgr['id'], $pgr);
    }

    

    private function getResponse ($id, $pgr = array(), $is_repeated = false)
    {	
        $op = [];		
        if ($details = $this->commObj->getPGRdetails($id))
        {				
            $data = [];           		
            $res = $this->commObj->saveResponse($pgr, $data, $is_repeated);								
            $this->statusCode = $res['status'];
			Log::info('PG Response: '.json_encode($res)); 
            return $this->response->json($res, $this->statusCode, $this->headers, $this->options);
            
        }
        else
        {
            return $this->config->get('app.is_api') ? $this->response->json($res, $this->statusCode, $this->headers, $this->options) : app()->abort(404);
        }
    }

}
