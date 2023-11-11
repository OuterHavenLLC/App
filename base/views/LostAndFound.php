<?php
 Class LostAndFound extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home() {
   $accessCode = "Accepted";
   $r = [
    "Front" => $this->core->Change([[
     "[LostAndFound.Options.Password]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPassword")),
     "[LostAndFound.Options.PIN]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPIN")),
     "[LostAndFound.Options.Username]" => base64_encode("v=".base64_encode("LostAndFound:RecoverUsername"))
    ], $this->core->Extension("65c5bed973a21411e6167bbdc721bbe4")])
   ];
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function RecoverPassword(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->core->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $password = uniqid();
    $you = $this->core->Member($username);
    $you["Login"]["Password"] = md5($password);
    $this->core->Data("Save", ["mbr", md5($username), $you]);
    $r = $this->core->Change([[
     "[Success.Message]" => "Your provisional password is <strong>$password</strong>. We recommend changing this password as soon as possible for your security.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->core->Extension("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->core->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverPassword"
     ])),
     "[LostAndFound.Recovery.Type]" => "Password"
    ], $this->core->Extension("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function RecoverPIN(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->core->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $pin = rand(1000001, 9999999);
    $you = $this->core->Member($username);
    $you["Login"]["PIN"] = md5($pin);
    $this->core->Data("Save", ["mbr", md5($username), $you]);
    $r = $this->core->Change([[
     "[Success.Message]" => "Use <strong>$pin</strong> the next time a PIN is required. We also recommend changing this provisional PIN as soon as possible for your security.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->core->Extension("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->core->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverPIN"
     ])),
     "[LostAndFound.Recovery.Type]" => "PIN"
    ], $this->core->Extension("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function RecoverUsername(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->core->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $r = $this->core->Change([[
     "[Success.Message]" => "Welcome back, <strong>$username</strong>! You may now sign in to your profile.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->core->Extension("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->core->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverUsername"
     ])),
     "[LostAndFound.Recovery.Type]" => "Username"
    ], $this->core->Extension("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>