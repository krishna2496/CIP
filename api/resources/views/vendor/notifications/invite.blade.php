<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Optimy</title>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			font-family: Verdana, Geneva, sans-serif;
			background: #f2f2f2;
			font-size: 12px;
			color: #414141;
		}

		a {
			border: 0;
			outline: none;
			text-decoration: none;
			cursor: pointer;
		}

		a,
		a:hover {
			transition: all .3s;
			-o-transition: all .3s;
			-ms-transition: all .3s;
			-moz-transition: all .3s;
			-webkit-transition: all .3s;
		}

		img {
			border: 0;
			outline: none;
		}

		@media only screen and (max-width:600px) {
			.main-table {
				width: 90%;
			}

			.inner-table {
				width: 80%;
			}

			.title_text {
				font-size: 25px !important;
				line-height: 35px !important;
			}
		}

		@media only screen and (max-width:374px) {
			.inner-table {
				width: 90%;
			}

			.button_text {
				color: #f88634;
				font-size: 17px;
				font-family: Verdana, Geneva, sans-serif;
			}

		}
	</style>
</head>

<body>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f2f2f2" align="center" style="background:#f2f2f2;">
		<tr>
			<td height="70" style="font-size:0; line-height:0;" align="left" valign="top"></td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFF" style="background:#FFFFFF;" class="main-table">
					<tr>
						<td height="3" style="background:#757575;" align="left" valign="top"></td>
					</tr>
					<tr>
						<td height="50" style="font-size:0; line-height:0;" align="left" valign="top"></td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<table width="500" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFF" style="background:#FFFFFF;" class="inner-table">
								<tr>
									<td>
										<img src="{{ $company_logo }}" height="50" width="150" alt="Logo" />
									</td>
								</tr>
								<tr>
									<td height="40" style="font-size:0; line-height:0;"></td>
								</tr>
								<tr>
									<td style="font-family: Verdana,Geneva,sans-serif;  color: #414141; font-size:15px; line-height: 19px;">
										<p>Hi {{ $first_name }} {{ $last_name }},</p>

										<p>{{ $customer_name }} has invited you to use {{ $site_name }}.</p>

										<p>Your account has been created. Below is your information:</p>

										<p>Login: {{ $email }}<br/>
										Password: {{ $password }}</p>

										<p>Use the button below to set up your account and get started:</p>
									</td>
								</tr>
								<tr>
									<td align="center">
										<table cellpadding="0" cellspacing="0" border="0" class="button" style="border: 2px solid #3B6CD1; border-radius:5px; -ms-border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius: 5px; color:#ffffff; font-size:17px; background-color: #3B6CD1; height: 40px; ">
											<tr>
												<td style=" width:20px;"></td>
												<td class="button_text" style="color:#ffffff; font-size:17px; font-family: Verdana,Geneva,sans-serif;" valign="middle"><a href="{{ $mail_url }}" title="Set up account" style="display:inline-block; color:#ffffff; font-size:17px; vertical-align: middle; display: block;">Set up account</a></td>
												<td style=" width: 20px;"></td>
											</tr>
                                        </table>
									</td>
								</tr>
								<tr>
									<td height="25" style="font-size:0; line-height:0;"></td>
								</tr>
								<tr>
									<td style="font-family: Verdana,Geneva,sans-serif;  color: #414141; font-size:15px; line-height: 19px;">To create a new password, click on change password in your user account.</td>
								</tr>
								<tr>
									<td height="45" style="font-size:0; line-height:0;"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFF" style="background:#FFFFFF;" class="main-table">
					<tr>
						<td style="border-top:3px solid #757575; font-size:0; line-height:0;"></td>
					</tr>
					<tr>
						<td height="40" style="font-size:0; line-height:0;"></td>
					</tr>
					<tr>
						<td style="font-family: Verdana,Geneva,sans-serif;  color: #757575; font-size:13px; line-height: 17px; align:center; text-align:center">&copy; {{date('Y')}} Optimy, {{ trans('mail.other_text.ALL_RIGHTS_RESERVED', [], $language_code) }}</td>
					</tr>
					<tr>
						<td height="40" style="font-size:0; line-height:0;"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="70" style="font-size:0; line-height:0;"></td>
		</tr>
	</table>
</body>

</html>