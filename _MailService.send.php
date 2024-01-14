<?php
 # Mail Service: Sender
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
   require_once("/var/www/html/_send/src/PHPMailer.php");
   require_once("/var/www/html/_send/src/SMTP.php");
   $mail = new PHPMailer();
   $mail->isSMTP();
   $mail->SMTPDebug = SMTP::DEBUG_SERVER;
   $mail->Host = base64_decode($data["Host"]);
   $mail->Port = 587;
   $mail->SMTPAuth = true;
   $mail->SMTPSecure = SMTP::STARTTLS;
   $mail->Username = base64_decode($data["Username"]);
   $mail->Password = base64_decode($data["Password"]);
   $mail->setFrom(base64_decode($data["Username"]), "Do Not Reply");
   $mail->addAddress(base64_decode($data["To"]));
   $mail->Subject = base64_decode($data["Title"]);
   $mail->msgHTML(base64_decode($data["Message"]));
   $mail->AltBody = htmlentities(base64_decode($data["Message"]));
   if(!$mail->send()) {
    echo "<p>Could not send mail: ".$mail->ErrorInfo."</p>";
   }
  } catch(Exception $error) {
   echo "<p>Could not send mail: ".$error->getMessage()."</p>";
  }
 }
?>