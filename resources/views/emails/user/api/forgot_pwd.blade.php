@extends('emails.user.maillayout')
@section('content')

<tr>
	<tr>
		<td>		
		<h1>Reset Your Password</h1>
		<p>Hello,</p>
		<p>We're sorry you forgot your password. Don't worry, it happens to everyone.</p>
		<!--p>Simply click the button below and create a new password.</p-->
		</td>
	</tr>
	<!--tr>
		<td style="padding:0px 0px 0px 200px;">
			<h3><a href="{{$forgotpwd_link}}" style="background-color:#007fcb;padding:10px 17px;color:white;text-decoration: none; border-radius:5px">Reset Password</a></h3>
		</td>
	</tr-->
	<tr> 
		<td>
			<!--p style="color:#990000;">Alternatively, you can create a new password by entering OTP (One Time Password) -</p-->
			<p style="color:#990000;">You can create a new password by entering OTP (One Time Password) -</p>
			<h1 style="text-align:center;color:#90f;">{{$code}}</h1>
			
			<p>After setting your password if you continue to have trouble logging in,</p>
			<p>Here are a few helpful hints:</p>
			<ol>
			<li>Make sure your "caps lock" is not on.</li>
			<li>Remember that your password is "CaSe SeNsItiVe".</li>
			<li>The security setting on your browser should be set to "medium".</li>
			<li>You may want to clear your temporary internet files (cache), then try to log in again.</li>
			</ol>
			<p>Virob support teams will never ask you to send or confirm your password or personal information via email. If you believe an unauthorized person accessed your account, please contact help@Virob.com.</p> 

		</td>	
	</tr>
</tr>
@stop
