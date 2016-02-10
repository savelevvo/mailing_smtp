<!DOCTYPE html>
<html>
<head>
	<title>Рассылка</title>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$file = "./names_list.txt";//Список рассылки
	if(file_exists($file)){
		if(!empty($_POST['subject']) && !empty($_POST['message'])){
			function clearData($data){
				$data = stripslashes($data);
				$data = strip_tags($data);
				$data = trim($data);
				return $data;
			}

			$string = file_get_contents($file);
			$array = explode("\n", $string);

			require("class.phpmailer.php");
			
			foreach ($array as $key){
				$new_array = explode(" - ", $key);
				$mail 			= new PHPMailer();
				$mail->IsSMTP();
				$mail->CharSet 	= 'UTF-8';
				$mail->Host     = "";
				$mail->Username = '';
				$mail->Password = '';
				$mail->SMTPAuth = "";
				$mail->Port 	= 587;

				/**************************************/
				$name 	 		= $new_array[0];
				$email 	 		= $new_array[1];
				$message 		= "Уважаемый ".$name."!\n".clearData($_POST['message']);
				$mail->From		= "";
				$mail->FromName	= "";
				$mail->Subject  = clearData($_POST['subject']);
				$mail->Body    	= $message;
				$mail->AddAddress($email);

				if(!$mail->Send()) {
					echo 'Message was not sent.';
					echo 'Mailer error: ' . $mail->ErrorInfo;
				}
			}
		}else
			echo "<p><b>Заполните все поля формы!</b></p>";
	}else
		echo "<p><b>Файл не существует!</b></p>";
}
?>

<form name="message-form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<p>Тема:<br/>
	<input type="text" name="subject" /></p>
	<p>Сообщение:<br/>
	<textarea name="message" cols="60" rows="8"></textarea><br />
	<input type="submit" /></p>
</form>

</body>
</html>
