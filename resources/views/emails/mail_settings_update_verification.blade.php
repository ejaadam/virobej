@extends('emails.maillayout')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi</strong>,</singleline></td>
</tr>
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p>Greetings</p>
</multiline>
</td>
</tr>
<tr>
    <td>
        <p>Verification code: {{$v_code}}</p>
    </td>
</tr>
</tbody>
</table>
@stop
