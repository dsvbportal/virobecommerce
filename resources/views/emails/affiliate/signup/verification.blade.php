@extends('emails.affiliate.maillayout')
@section('content')
<tr><td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear <?php echo $fullname;?>,</td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr><td bgcolor="#d7d7d7" height="1"></td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr>
	<td align="center" style="font-size:25px;color:#000">Welcome to <?php echo $pagesettings->site_name;?> Influencer Programme</td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
	<td bgcolor="#d7d7d7" height="1"></td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
<td align="center">
	<p>To make your Virob account more secure and to receive important messages please confirm that you want to use this as your Virob account email address. Once it’s done you will be able to start selling and agreeing to Virob’s terms of service.</p>	
	<p style="text-align: center;">
		<a target="_blank" style="background:none repeat scroll 0 0 #00cf00;font-weight:bold;color:#fff;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 20px;text-transform:uppercase;display:inline-block" href="<?php echo $activate_link;?>">VERIFY NOW</a>
	</p>
	<p style="word-wrap: break-word">(OR)  paste this link to your browser.<br/> <?php echo $activate_link;?></p>
</td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
	<td bgcolor="#d7d7d7" height="1"></td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
<td align="center">
	<p>If this is not your Virob account, don’t worry, someone probably just typed the wrong email address. Please contact support@virob.com for any queries regarding this. </p>
</td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
@stop