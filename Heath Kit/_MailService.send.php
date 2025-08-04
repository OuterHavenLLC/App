<?php
 # Mail Service: Sender
 require_once("vendor/autoload.php");
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
 use PHPMailer\PHPMailer\SMTP;
 $data = array_merge($_GET, $_POST);
 $i = 0;
 $missingInputs = "";
 $required = [
  "Host",
  "Message",
  "Password",
  "Title",
  "To",
  "Username"
 ];
 foreach($required as $key) {
  if(empty($data[$key])) {
   $i++;
   $missingInputs .= "$key, ";
  }
 } if($i > 0) {
  die("<p>The following data is missing: ".substr($missingInputs, 0, -2).".</p>");
 } else {
  try {
   $mail = new PHPMailer(true);
   $mail->isSMTP();
   $mail->SMTPDebug = SMTP::DEBUG_SERVER;
   $mail->Host = base64_decode($data["Host"]);
   $mail->Port = 587;
   $mail->SMTPAuth = true;
   $mail->SMTPSecure = "tls";
   $mail->Username = base64_decode($data["Username"]);
   $mail->Password = base64_decode($data["Password"]);
   $mail->setFrom(base64_decode($data["Username"]), "Outer Haven");
   $mail->addAddress(base64_decode($data["To"]));
   $mail->Subject = base64_decode($data["Title"]);
   $mail->msgHTML(base64_decode($data["Message"]));
   $mail->AltBody = htmlentities(base64_decode($data["Message"]));
   if(!$mail->send()) {
    die("<p>Could not send mail: ".$mail->ErrorInfo."</p>");
   }
  } catch(Exception $error) {
   die("<p>Could not send mail: ".$error->getMessage()."</p>");
  }
 }
?>