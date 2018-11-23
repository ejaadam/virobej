<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        <meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>
    </head>
    <body>
        <table id="example9" class="table table-bordered" border="1">
            <tr><th colspan="8" align="center"> {{$title}} - {{date("d-M-Y")}}</th></tr>
            <thead>
                <tr>
                    <th style="mso-number-format:'@';">Date</th>
                    <th style="mso-number-format:'@';">From</th>
                    <th style="mso-number-format:'@';">To</th>
                    <th style="mso-number-format:'@';">Transaction Details</th>
                    <th style="mso-number-format:'@';">Amount</th>
                    <th style="mso-number-format:'@';">Commission</th>
                    <th style="mso-number-format:'@';">Status</th>
                    <th style="mso-number-format:'@';">Verified On</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($commissions))
                @foreach($commissions as $commission)
                <tr>
                    <td style="mso-number-format:'@';">{{date('d-M-Y H:i:s', strtotime($commission->created_date))}}</td>
                    <td style="mso-number-format:'@';">{{$commission->from_full_name}}
                        @if($commission->from_uname != '' && !empty($commission->from_uname))
                        {{'('.$commission->from_uname.')'}}
                        @endif
                        @if(isset($commission->district_name) && $commission->district_name != '' && !empty($commission->district_name))
                        {{'District : '.$commission->district_name}}
                        @endif
                    </td>
                    <td style="mso-number-format:'@';"> {{$commission->to_full_name.'('.$commission->to_uname.')'}}</td>
                    <td style="mso-number-format:'@';">
                        @if(isset($commission->remark) && $commission->remark != '' && !empty($commission->remark))
                        {{$commission->remark}}
                        @endif
                        {{'<b>Transaction Id: '.$commission->transaction_id.'</b>'}}
                    </td>
                    <td style="mso-number-format:'\0022{{$commission->currency}}\0022 #,##0.00';">{{number_format($commission->amount,2,'.',',')}}</td>
                    <td style="mso-number-format:'\0022{{$commission->currency}}\0022 #,##0.00';">{{number_format($commission->commission_amount,2,'.',',')}}</td>
                    <td style="mso-number-format:'@';">{{$commission->status_name}}</td>
                    <td style="mso-number-format:'@';">{{(!empty($commission->confirmed_date)?date('d-M-Y H:i:s', strtotime($commission->confirmed_date)):'-')}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        </div>
        </div>
    </body>
</html>
