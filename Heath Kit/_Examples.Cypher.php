<?php
 Class Cypher {
  function __construct() {
   try {
    $this->key = "@SuperS3cretK3y!"; # REPLACE WITH A SECURE KEY
   } catch(PDOException $error) {
    return "Failed to initialize Cypher... ".$error->getMessage();
   }
  }
  function Decrypt($data) {
   $data = explode(" ", $data);
   $i = 0;
   $x = count($data);
   $y = $x - 1;
   $c = (is_numeric($y) && is_numeric($data[$y])) ? $data[$y] - 50 : $y - 50;
   $r = chr($c);
   $re = "";
   while($i < $y) {
    $a[$i] = $data[$i] + $r;
    $re .= chr($a[$i]);
    $i++;
   }
   $data = $re ?? base64_encode("");
   $data = base64_decode($re);
   if(!empty($data)) {
    $data = explode("::", base64_decode($data));
    return unserialize(base64_decode($data[0]));
   } else {
    return "";
   }
  }
  function Encrypt($data) {
   $data = base64_encode(base64_encode(serialize($data))."::".$this->key);
   $data = base64_encode($data);
   $i = 0;
   $length = strlen($data);
   $r = rand(0, 1);
   while($i < $length) {
    $ch[$i] = (ord($data[$i]) - $r);
    $i++;
   }
   return str_replace(".", " ", implode(".", $ch).".".(ord($r) + 50));
  }
  function MailCredentials() {
   # FOR USE WITH THE CORE SendEmail() OBJECT
   return [
    "Host" => base64_encode("mail.example.com"),
    "Password" => base64_encode("P@ssw0rd!"),
    "Username" => base64_encode("noreply@example.com")
   ];
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>