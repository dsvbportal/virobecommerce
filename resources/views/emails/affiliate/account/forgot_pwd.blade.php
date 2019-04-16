@extends('emails.affiliate.maillayout')
@section('content')
<tr><td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear <?php echo $name;?>,</td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr><td bgcolor="#d7d7d7" height="1"></td></tr>
<tr><td width="40">&nbsp;</td></tr>
<tr>
	<td align="center" style="font-size:25px;color:#000">Password Reset Notification</td>
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
	<tr>
		<td>				
		<p>We received a request to reset your password for your Virob affiliate account. We’re here to help! Simply click on the button to set a new password.</p>		
		</td>
	</tr>	
	<tr>
		<td width="40">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<a href="{{$forgotpwd_link}}" style="background-color:#007fcb;padding:10px 17px;color:white;text-decoration: none; border-radius:5px">Set a New Password.</a>
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
		<p>If you didn’t ask to change your password, don’t worry! Your password is still safe and you can delete this email.</p>		
		<p>Virob support teams will never ask you to send or confirm your password or personal information via email.If you believe an unauthorized person accessed your account, please contact at help@virob.com.</p>
		</td>
	</tr>	
</tr>
@stop