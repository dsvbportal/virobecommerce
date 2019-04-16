@extends('emails.affiliate.maillayout')
@section('content')
<tr><td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear <?php echo $fullname;?>,</td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr><td bgcolor="#d7d7d7" height="1"></td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr>
	<td align="center" style="font-size:25px;color:#000">Welcome to <?php echo $pagesettings->site_name;?> Affiliate Platform</td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
	<td align="center" style="font-size:16px;color:#666666">Congratulations! to becoming a part of the <?php echo $pagesettings->site_name;?> Community
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
	<td align="center" style="font-size:13px;color:#929292"><p style="font-size:18px;color:#2dcc00">Let's start an amazing journey together!</p> </td>
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
	<td align="center" style="white-space: no-wordwrap "><b style="color:#066fc5">Your account details are :</b>
	<br />
	<p><b>Username:</b> <?php echo $username;?></p>
	<p><b>Email/Login Id:</b> <?php echo $email;?></p>
	</td>
</tr>
<tr>
	<td align="center">You are invited by - <?php echo $referral_fullname.' ('.$referral_name.')';?>
	<p>Mobile Number - <?php echo $referral_contact;?> </p>
	<p>Email - <?php echo $referral_email;?> </p></p>                            
	</td>
</tr>
<tr>
<td align="center">
	<p>We just need to verify this Email Address belongs to you.</p>
	<p>In order to verify your Email ID,please click on the link below:</p>
	<p style="text-align: center;">
		<a target="_blank" style="background:none repeat scroll 0 0 #00cf00;font-weight:bold;color:#fff;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 20px;text-transform:uppercase;display:inline-block" href="<?php echo $email_verify_link;?>">CLICK HERE TO VERIFY YOUR EMAIL ID</a>
	</p>
	<p style="word-wrap: break-word">(OR) Copy and paste the below link in your browser.<br/> <?php echo $email_verify_link;?></p>
</td>
</tr>
@stop