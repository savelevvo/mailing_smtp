<!DOCTYPE html>
<html>
<head>
	<title>Рассылка</title>
</head>
<body>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(!empty($_POST['subject']) && !empty($_POST['message'])){
		define("HOST", "localhost");
		define("USER", "root");
		define("PASSWORD", "");
		define("DB", "mail_db");

		$connect = mysql_connect(HOST, USER, PASSWORD) or die (mysql_error());
		mysql_select_db(DB, $connect);
		mysql_set_charset('utf8', $connect);
		$query = mysql_query("SELECT name, email FROM `main_table`") or die (mysql_error());

		function clearData($data){
			$data = stripslashes($data);
			$data = strip_tags($data);
			$data = trim($data);
			return $data;
		}

		require("class.phpmailer.php");
	
		while ($res = mysql_fetch_assoc($query)){
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->CharSet 	= 'UTF-8';
			$mail->Host 	= "";
			$mail->Username	= '';
			$mail->Password	= '';
			$mail->SMTPAuth	= "";
			$mail->Port		= 587;

			$mail->From		= "";
			$mail->FromName = "";
			$mail->Subject  = clearData($_POST['subject']);
			$mail->Body     = "Уважаемый ".$res['name']."!\n".clearData($_POST['message']);
			$mail->AddAddress($res['email']);

			if(!$mail->Send()) {
				echo 'Message was not sent.';
				echo 'Mailer error: ' . $mail->ErrorInfo;
			}
		}
		mysql_close($connect);
	}else
		echo "<p><b>Заполните все поля формы!</b></p>";
}
?>

<form name="message-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<p>Тема:<br />
	<input type="text" name="subject" /></p>
	<p>Сообщение:<br />
	<textarea name="message" cols="60" rows="8"></textarea><br />
	<input type="submit" /></p>
</form>

</body>
</html>
