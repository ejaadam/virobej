@extends('emails.user.maillayout')
@section('content')
<tr>
    <td>
        <table  style="width:100%;">
            <tr>
                <td colspan="2" style="text-align:center;">
                    <p style="margin:20px 0px 0px 0px;"><img src="{{asset('assets/emails/img/tick_mark.png')}}" alt="Tick Mark" style="width:60px;height:75px;"></p>
                    <h2 style="margin:0px;"><b>Success!</b></h2>
                    <p style="margin:0px;">Withdrawal ID {{$withdraw_id}} at {{$paytype}} in 3-5 business days</p>
                </td>
            </tr>
            <tr style="">
                <td style="text-align:left;width:50%;padding-top:20px;">Date : {{$date}}</td>
                <td style="text-align:right;width:50%;padding-top:20px;">Amount : {{$amount}}</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td>
        <p>For further queries, feel free to get in touch with <a href="mailto:support@paygyft.com" title="support@paygyft.com">support team</a>  or <a href="#" title="FAQ's">FAQ’s.</a></p>
        <p>Happy Shopping with {{$siteConfig->site_name}} !</p>
    </td>
</tr>
@stop
