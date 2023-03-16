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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["2FAReturn"]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $r = $this->system->Element([
     "h2", "Done"
    ]);
   } else {
    $r = $this->system->Element(["button", "Back", [
     "class" => "GoToParent LI header",
     "data-type" => "LostAndFound"
    ]]).$this->system->Element([
     "h2", "Recover Password"
    ]);
   }
   return $r;
  }
  function RecoverPIN(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["2FAReturn"]);
   $isBackFrom2FA = $data["2FAReturn"] ?? 0;
   if($isBackFrom2FA == 1) {
    $r = $this->system->Element([
     "h2", "Done"
    ]);
   } else {
    $r = $this->system->Element(["button", "Back", [
     "class" => "GoToParent LI header",
     "data-type" => "LostAndFound"
    ]]).$this->system->Element([
     "h2", "Recover PIN"
    ]);
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
    // BEGIN TEMP
    $r = $this->system->Element([
     "h2", "Done", ["class" => "CenterText UpperCase"]
    ]).$this->system->Element([
     "p", "Welcome back, <strong>[LostAndFound.Username]</strong>! You may now sign in to your profile.", ["class" => "CenterText"]
    ]);
    // END TEMP
    $r = $this->system->Change([[
     "[LostAndFound.Username]" => $username
    ], $r]);
    #], $this->system->Page("XXXX")]);
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