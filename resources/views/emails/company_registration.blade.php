@extends('emails.maillayout')
@section('content')

<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">Dear <?php echo $company_name;?>,</singleline></td>
</tr>
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Thank you for registering with <?php echo $pagesettings->site_name;?>.</p>
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Your Account Detail are as follows.</p>
</multiline>
</td>
</tr>
<!--end content-->
<tr>
    <td height="24"></td>
</tr>
<!--start 2 columns-->
<tr>
    <td>
        <table align="left"  border="0" cellpadding="0" cellspacing="0" class="col2" style="border-collapse: collapse;border: none;mso-table-lspace: 0pt;mso-table-rspace: 0pt;" width="0">
            <tbody>
                <tr>
                    <td align="left" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
            <multiline label="content2">
                <table  class="wrapper last">
                    <tbody>
                        <tr><td>Account Email/Username : <?php echo $acc_email;?> </td></tr>
                        <tr><td>Account ID : <?php echo $user_name;?> </td></tr>
                        <tr><td>Password  : <?php echo $password;?> </td></tr>
                    </tbody>
                </table>
            </multiline>
    </td>
</tr>
</tbody>
</table>
</td>
</tr>

</tbody>
</table>
@stop
