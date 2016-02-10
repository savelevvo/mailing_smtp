<!DOCTYPE html>
<html>
<head>
	<title>Рассылка</title>
	<style type="text/css">
		.ok{
			color: green;
			cursor: pointer;
		}
		.error{
			color: #CC0000;
			cursor: pointer;
		}
	</style>
</head>
<body>
<form name="message-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <p>Тема:<br/>
        <input type="text" name="subject" /></p>
    <p>Сообщение:<br/>
        <textarea name="message" cols="60" rows="8"></textarea><br />
        <input type="submit" /></p>
</form>
<?php
require_once("./lib/PHPExcel/PHPExcel.php");
require_once("./lib/PHPExcel/PHPExcel/IOFactory.php");
require_once("./lib/PHPMailer/class.phpmailer.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){

	$file = "db.xlsx";

	if(file_exists($file)){
		if(!empty($_POST['subject']) && !empty($_POST['message'])){

			function clearData($data){
				$data = stripslashes($data);
				$data = strip_tags($data);
				$data = trim($data);
				return $data;
			}

			$xls = PHPExcel_IOFactory::load("$file");
			$xls->setActiveSheetIndex(0);
			$sheet = $xls->getActiveSheet();

			for($i=2; $i<=$sheet->getHighestRow(); $i++){
				$cell[0] = $sheet->getCellByColumnAndRow(0, $i);
				$cell[1] = $sheet->getCellByColumnAndRow(1, $i);
				$cell[2] = $sheet->getCellByColumnAndRow(2, $i);
				$cell[3] = $sheet->getCellByColumnAndRow(7, $i);
				$cell[4] = $sheet->getCellByColumnAndRow(5, $i);

				$last_name   = $cell[0]->getValue();
				$first_name  = $cell[1]->getValue();
				$middle_name = $cell[2]->getValue();
				$email 	     = $cell[3]->getValue();
				$gender	     = $cell[4]->getValue();

                $fullName = "";
                    if(!empty($last_name))   $fullName .= $last_name." ";
                    if(!empty($first_name))  $fullName .= $first_name." ";
                    if(!empty($middle_name)) $fullName .= $middle_name;

                if(!empty($email) && preg_match("/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/", $email)){
                    if(!empty($last_name) || !empty($first_name) || !empty($middle_name)){
                        $mail 			= new PHPMailer();
                        $mail->IsSMTP();
                        $mail->CharSet 	= 'UTF-8';
                        $mail->Host     = "";
                        $mail->Username = '';
                        $mail->Password = '';
                        $mail->SMTPAuth = "";
                        $mail->Port 	= 0;

                        $hello			= (!empty($gender)) ? (($gender === "Мужской") ? "Уважаемый " : "Уважаемая ") : ("") ;
                        $message 		= $hello.$fullName."!\n".clearData($_POST['message']);
                        $mail->From		= "";
                        $mail->FromName	= "";
                        $mail->Subject  = clearData($_POST['subject']);
                        $mail->Body    	= $message;
                        $mail->AddAddress($email);

                        if(!$mail->Send())
                            echo "Message was not sent. Mailer error: $mail->ErrorInfo <br/>";
                        else
                            echo "<div class=\"ok\">&#9745; $email</div>";
                    }else
                        echo "<div class=\"error\">&#9746; Строка $i: Введите ФИО ($email)</div>";
                }else
                    echo "<div class=\"error\">&#9746; Строка $i: Некорректный e-mail ($fullName)</div>";
			}
		}else
			echo "<div class=\"error\">Ошибка: Заполните все поля формы!</div>";
	}else
		echo "<div class=\"error\">Ошибка: Файл \"$file\" не существует!</div>";
}
?>
</body>
</html>
