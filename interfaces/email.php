<?php

	global $email_from, $admin_email;
	$email_from = 'Kaputa Support <support@kaputa.lk>';//'"Kaputa.LK" <admin@kaputa.lk>';
	$admin_email = 'Kaputa Team <kaputa.team@gmail.com>';//'"Kaputa.LK Admin Team" <kaputa.team@gmail.com>';

	function send_email($to, $data, $template, $subject = 'No Subject', $attachments = false){
		global $email_from, $admin_email;
		$boundary = uniqid('np');
		$headers = 'From: '.$email_from.
				"\r\n".'To: '.$to.
				/*"\r\n".'Reply-To: '.$admin_email.*/
				"\r\n".'MIME-Version: 1.0'.
				"\r\n".'Content-Type: multipart/alternative; boundary='.$boundary."\r\n";
		ob_start();
		//
		echo render_view('email/'.$template, $data);
		//
		$message = ob_get_clean();
		$message = strip_tags($message).
				"\r\n\r\n--".$boundary."\r\n".
				'Content-type: text/html;charset=utf-8'.
				"\r\n\r\n".$message.
				"\r\n\r\n--".$boundary.'--';
		$mail_sent = @mail($to, $subject, $message, $headers);
		//echo $message;
		return $mail_sent;
	}

?>
