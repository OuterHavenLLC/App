<?php
 Class LostAndFound extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home() {
   $r = $this->system->Change([[
    "[LostAndFound.Options.Password]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPassword")),
    "[LostAndFound.Options.PIN]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPIN")),
    "[LostAndFound.Options.Username]" => base64_encode("v=".base64_encode("LostAndFound:RecoverUsername"))
   ], $this->system->Page("65c5bed973a21411e6167bbdc721bbe4")]);
   return $this->system->Card([
    "Front" => $r
   ]);
  }
  function RecoverPassword(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->system->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $password = uniqid();
    $you = $this->system->Member($username);
    $you["Login"]["Password"] = md5($password);
    $this->system->Data("Save", ["mbr", md5($username), $you]);
    $r = $this->system->Change([[
     "[Success.Message]" => "Your provisional password is <strong>$password</strong>. We recommend changing this password as soon as possible for your security.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->system->Page("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->system->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverPassword"
     ])),
     "[LostAndFound.Recovery.Type]" => "Password"
    ], $this->system->Page("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $r;
  }
  function RecoverPIN(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->system->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $pin = rand(1000001, 9999999);
    $you = $this->system->Member($username);
    $you["Login"]["PIN"] = md5($pin);
    $this->system->Data("Save", ["mbr", md5($username), $you]);
    $r = $this->system->Change([[
     "[Success.Message]" => "Use <strong>$pin</strong> the next time a PIN is required. We also recommend changing this provisional PIN as soon as possible for your security.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->system->Page("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->system->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverPIN"
     ])),
     "[LostAndFound.Recovery.Type]" => "PIN"
    ], $this->system->Page("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $r;
  }
  function RecoverUsername(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "2FAReturn",
    "Email"
   ]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $email = base64_decode($data["Email"]);
    $username = "";
    $x = $this->system->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     $memberEmail = $member["Personal"]["Email"];
     if($email == $memberEmail) {
      $username = $member["Login"]["Username"];
     }
    }
    $r = $this->system->Change([[
     "[Success.Message]" => "Welcome back, <strong>$username</strong>! You may now sign in to your profile.",
     "[Success.ViewPairID]" => "LostAndFound",
    ], $this->system->Page("d4449b01c6da01613cff89e6cf723ad1")]);
   } else {
    $r = $this->system->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverUsername"
     ])),
     "[LostAndFound.Recovery.Type]" => "Username"
    ], $this->system->Page("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>