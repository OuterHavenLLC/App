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
 $requiredData = $data["RequiredData"] ?? base64_encode(json_encode([]));
 $requiredData = json_decode(base64_decode($requiredData));
 foreach($required as $key) {
  if(empty($requiredData[$key])) {
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
   $mail->Host = base64_decode($requiredData["Host"]);
   $mail->Port = 587;
   $mail->SMTPAuth = true;
   $mail->SMTPSecure = SMTP::STARTTLS;
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