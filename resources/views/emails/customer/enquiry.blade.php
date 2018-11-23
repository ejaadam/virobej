@extends('emails.maillayout')
@section('content')
<style>
    table{
        border-collapse: collapse;
        border: none;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        border-radius:3px;
        margin-left: 30px;
		
    }
    table tr{
        padding:2px 5px;
        border-radius:3px;
    }
    table tr th,table tr td{
        padding:5px;
    }
    table tr td img{
        border-radius:3px;
    }
	  table.bordered tr{
        border:1px #cccccc solid;padding:5px;
    }
    
  
</style>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi  <?php echo 'hi'?></strong>,</singleline></td>
</tr>
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">   
   <p> Thanks for writing to us. We have received your query and will get back to you within the next 24 hours.</p>  
   <p> to know  your enquiry status in future pls use code: #."{{$enquiry_code}}"</p>      
   <p> [This is an auto-generated response. Please do not reply to this email.] </p>
 </p>
<b>Thanks,</b>
<b></b>
 
   
</multiline>
</td>
</tr>
       
       
      
       
       
        
        
    </tbody>
</table>
@stop



