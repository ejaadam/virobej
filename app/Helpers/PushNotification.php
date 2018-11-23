<?php

/**
 * Description of PushNotification
 *
 * @author Admin
 */
class PushNotification
{

    /**
     * @param int|array $id notification to whom single id or array of ids
     * @param string $title title of the notification
     * @param string $body body of the notification
     * @param string $click_action action link of the notification
     * @param string $icon icon of the notification
     *
     * @return array|false response of notification or false
     */
    public static function send ($id, $title, $body, $click_action = '', $icon = '')
    {
        $id = is_array($id) ? array_filter($id) : [$id];
        if (!empty($id))
        {
            $Settings = Config::get('services.google');
            $fcm_registration_ids = DB::table(Config::get('tables.DEVICE_LOG').' as dl')
                    ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', function($ap)
                    {
                        $ap->on('ap.account_id', '=', 'dl.account_id')
                        ->where('ap.send_notification', '=', Config::get('constants.ACTIVE'));
                    })
                    ->where('dl.status', Config::get('constants.ACTIVE'))
                    ->whereIn('dl.account_id', $id)
                    ->lists('dl.fcm_registration_id');
            $registatoin_ids = array_values(array_filter($fcm_registration_ids));
            if (!empty($registatoin_ids))
            {
                $message_data = [];
                $message_data['data'] = [
                    'notification'=>[
                        'title'=>$title,
                        'body'=>$body,
                        'click_action'=>!empty($click_action) ? $click_action : '',
                        'icon'=>!empty($icon) ? $icon : '',
                        'color'=>'#111111',
                        'sound'=>true,
                        'vibrate'=>true,
                    ]
                ];
                $notifications = [];
                $notifications['account_ids'] = implode(',', array_filter($id));
                $notifications['created_on'] = date('Y-m-d H:i:s');
                $notifications['data'] = json_encode($message_data['data']);
                $message_data['data']['id'] = DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS'))
                        ->insertGetId($notifications);
                $message_data['registration_ids'] = $registatoin_ids;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $Settings['fcm_url']);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: key='.$Settings['api_key'], 'Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
                $result = curl_exec($ch);
                if ($result === FALSE)
                {
                    die('Curl failed: '.curl_error($ch));
                }
                curl_close($ch);
                return $result;
            }
        }
        return false;
    }

}
