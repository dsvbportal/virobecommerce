@extends('emails.franchisee.merchant_maillayout')
@section('content')
<tbody>
                           <tr>
      							<!-- Header Top Border // -->
      							<td style="background-color:#00c22a;font-size:1px;line-height:3px;" height="3">&nbsp;</td>
							</tr>
      						<tr>
      						<td style="padding-bottom: 20px;" valign="top" align="center">
      								<!-- Hero Image // -->
      							<a href="#" target="_blank" style="text-decoration:none;">
      							<img src="<?php echo URL::asset('resources/assets/img-email/PIN-code.png');?>" alt="" style="width:100%; max-width:600px; height:auto; display:block;" width="600" border="0">
      							</a>
      							</td>
      						</tr>
      						<tr>
      							<td style="padding-bottom:5px;padding-left:20px;padding-right:20px;"  valign="top" align="center">
      								<!-- Main Title Text // -->
      								<h2 style="color:#000000; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
      									Dear {{$full_name}}
      								</h2>
      							</td>
      						</tr>
      						<tr>
      							<td style="padding-bottom:30px;padding-left:20px;padding-right:20px;" valign="top" align="center">
      								<!-- Sub Title Text // -->
      								<h4 style="color:#999999; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
      								<b>	Congratulations!!
      								</h4>
      							</td>
      						</tr>
      						<tr>
      							<td style="padding-left:20px;padding-right:20px;" valign="top" align="center">

      								<table width="100%" cellspacing="0" cellpadding="0" border="0">
      									<tbody><tr>
      										<td style="padding-bottom:20px;" valign="top" align="center">
      											<!-- Description Text// -->
      											<p style="color:#666666; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding-bottom:20px; margin:0">
                                                  <b> Your Account Created successfully.</b></p>
												<p> To complete your registration please log on with following details</p>
												<p> and update your business informations.</p>
												<p>Account Email/Username :<b>{{$email}}</b></p>
                                                <p>Account Password :<b>{{$password}}</p>	
										 <center><p><a href="{{$login_link}}" style="background-color:#00c484;padding:10px;color:white;text-decoration: none; text-align: center;border-radius:5px" title="Verify Email Address"><b>Click Here to Login </b></a></p></center>
                                               
      										</td>
      									</tr>
      								</tbody></table>

      								<table width="100%" cellspacing="0" cellpadding="0" border="0">
      									<tbody>
@stop
