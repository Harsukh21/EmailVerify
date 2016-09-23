1.) This code is only working in live serve, not working in local system

2.) 25 port must be open in your hosting server

3.) if you check 25 port open or not please follow above step

	step -1 => login in your cpanel by ssh
			   ssh username@yourIP

	setp -2 => then after enter your ssh user password.

	step -3 => type  like that........
			   telnet gmail-smtp-in.l.google.com 25

				if geting respons ok your 25 port working fine
				if geting error then tell your hosting provider