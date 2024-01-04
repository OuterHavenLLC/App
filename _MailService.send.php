<?php
 # Mail Service: Sender
 $data = array_merge($_GET, $_POST);
 $i = 0;
 $missingInputs = "";
 $required = [
  "Message",
  "Password",
  "Title",
  "To",
  "Username"
 ];
 $requiredData = $data["RequiredData"] ?? json_encode([]);
 foreach($required as $key) {
  if(empty($requiredData[$key])) {
   $i++;
   $missingInputs.= "$key, ";
  }
 } if($i > 0) {
  echo "<p>The following data is missing: ".substr($missingInputs, 0, -2).".</p>";
 } else {
  $requiredData = json_decode($requiredData);
  try {
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\SMTP;
   require_once(__DIR__."/_send/src/PHPMailer.php");
   require_once(__DIR__."/_send/src/SMTP.php");
   $mail = new PHPMailer();
   $mail->isSMTP();
   $mail->SMTPDebug = SMTP::DEBUG_SERVER;
   $mail->Host = "mail.outerhaven.nyc";
   $mail->Port = 587;
   $mail->SMTPAuth = true;
   $mail->Username = base64_decode($requiredData["Username"]);
   $mail->Password = base64_decode($requiredData["Password"]);
   $mail->setFrom(base64_decode($requiredData["Username"]), "Do Not Reply");
   $mail->addAddress(base64_decode($requiredData["To"]));
   $mail->Subject = base64_decode($requiredData["Title"]);
   $mail->msgHTML(base64_decode($requiredData["Message"]));
   $mail->AltBody = htmlentities(base64_decode($requiredData["Message"]));
   if(!$mail->send()) {
    echo "<p>Could not send mail: ".$mail->ErrorInfo."</p>";
   }
  } catch(Exception $error) {
   echo "<p>Could not send mail: ".$error->getMessage()."</p>";
  }
 }
?>