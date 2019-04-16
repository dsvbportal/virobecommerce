@extends('emails.affiliate.maillayout')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="center" class="h1 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 1000; color: #11A018; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="text-align:center;">Recommended</singleline></td>
</tr>
<tr>
    <td align="center" class="title-td" mc:edit="subtitle">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;" width="590">
            <tbody>
                <tr>
                    <td class="small-img line2" height="1" style="font-size: 0px;line-height: 0px;border-collapse: collapse;background-color: #252525;"><img height="1" src="http://digith.com/agency/agency/demo/blue-2/images/spacer.gif" style="border: 0;display: block;-ms-interpolation-mode: bicubic;" width="1" /></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<!--end title-->
<tr>
    <td class="small-img" height="16" style="font-size: 0px;line-height: 0px;border-collapse: collapse;"><img height="1" src="http://digith.com/agency/agency/demo/blue-2/images/spacer.gif" style="border: 0;display: block;-ms-interpolation-mode: bicubic;" width="1" /></td>
</tr>
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">	 
	<p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Hi,</p>
	<br>
	 <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">You won’t believe what I found!</p>
	 <br>
	 <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">There is this awesome website called virob.com that basically pays you for shopping online. Just visit virob.com & search for products.</p>
	 <br>
	 <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">You’ve got nothing to lose by trying it!.</p>
	 <br>	 
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">You are referred by <?php echo $full_name; ?>.</p>
	<br>
	<a href="<?php echo $referral_url; ?>"><?php echo $referral_url; ?></a>
	<br><br>
    <p style="font-family: 'Playfair Display', Arial; font-weight: 1000; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Enjoy the savings!</p>
</multiline></td>
</tr>
<!--end content-->
<tr>
    <td height="38"></td>
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
                     <tr><td> </td></tr>
                     <tr><td> </td></tr>
                     
                    
                    </tbody>
                </table>
            </multiline>
    </td>
</tr>
</tbody>
</table>
</td>
</tr>
<!--end 2 columns-->
</tbody>
</table>



@stop