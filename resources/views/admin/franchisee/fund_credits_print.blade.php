<?php /* ?> @include('user.decimal_value')<?php */?>
<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css" media="print">
    table tr td{
        border-collapse:collapse;
        padding:5px 5px;
    }
    .noprint{
        display:none;
    }
</style>
<?php
foreach ($data as $key=> $val)
    $$key = $val;
?>
<div class="rightbox">
    <div class="homeMsg" style="text-align:left; height:auto;">
        <h1>Support Center Fund Credits - <?php echo date("d-M-Y");?></h1><br/>
        <?php
        $err = Session::get('err');
        $msgClass = Session::get('msgClass');
        if (isset($err))
        {
            if ($err != '')
            {
                echo '<div class="'.$msgClass.'">'.$err.'</div>';
            }
        }
        ?>
        <table id="example1" border="1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Code</th>
                    <th>Username</th>
                    <th>Fullname</th>
                    <th>Country</th>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Payment Type</th>
                    <th>Auto Credit</th>
                    <th>Credited By</th>
                    <th>Payment Status</th>
                    <th>Status</th>
                    <th>Updated On</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($user_fund_credits))
                @foreach ($user_fund_credits as $val)
                <tr>
                    <td class="text-center" nowrap>{{ date('d-M-Y H:i:s', strtotime($val->requested_date))}}</td>
                    <td>    <?php
                        $payout_name = strtr($val->payout_types, array(
                            'Direct'=>'',
                            'Transfer'=>'',
                            'Wallet'=>'',
                            'Credit/Debit Card'=>'',
                            '/Net Banking'=>'',
                            '-'=>'',
                            ' '=>''));
                        $payout_name = strtolower($payout_name).'_trans_id';
                        ?>
                        {{$val->transaction_id}}<br />
                        @if(isset($val->$payout_name))
                        PG : @if( $val->payment_type == Config::get('constants.PAYPAL'))
                        {{ 'AF'.$val->transaction_id}}
                        @else
                        {{ $val->$payout_name}}
                        @endif
                        @endif    </td>
                    <td>{{ $val->uname}} </td>
                    <td class="text-left">{{ $val->full_name}} </td>
                    <td class="text-center">{{ $val->country}} </td>
                    <td class="text-center">{{ $val->currency}} </td>
                    <td class="text-right"><?php $usercommonObj->amount_with_decimal($val->amount);?></td>
                    <td><?php
                        $search_replace = array(
                            'Direct'=>'',
                            'Transfer'=>'');
                        $payout_name = strtr($val->payout_types, $search_replace);
                        ?>{{ $payout_name}}</td>
                    <td>{{ ($val->auto_credit == 0) ? 'No':'Yes' }}</td>
                    <td>{{ ($val->status == 1) ? ($val->credited_by == 0) ? 'User(Auto)': 'Admin(Manual)' : ''}}</td>
                    <td class="text-center">{{ $payment_status_arr[$val->payment_status]}} </td>
                    <td class="text-center">{{ $status_arr[$val->status]}}</td>
                    <td class="text-center" nowrap>
                        <?php
                        if ($val->status == 0)
                        {
                            if ($val->released_date != '0000-00-00 00:00:00' && !empty($val->released_date))
                                $update_date = $val->released_date;
                            else
                                $update_date = $val->requested_date;
                        }else if ($val->status == 1)
                        {
                            $update_date = $val->approved_date;
                        }
                        else if ($val->status == 3)
                        {
                            $update_date = $val->cancelled_date;
                        }
                        else if ($val->status == 5)
                        {
                            $update_date = $val->refund_date;
                        }
                        else
                        {
                            $update_date = $val->updated_date;
                        }
                        ?>
                        {{ ($update_date != '0000-00-00 00:00:00') ? date('d-M-Y H:i:s', strtotime($update_date)) : '' }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<button class="noprint" onclick="myFunction()">Print</button>
