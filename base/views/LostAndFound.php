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
     "h2", "Recover Username"
    ]).$this->system->Element([
     "p", "Please enter your email address below. Once your email is verified, we will give you your username."
    ])."
<div class=\"RecoverUsername\">
 <input class=\"req\" name=\"Email\" placeholder=\"mike@outerhaven.nyc\" type=\"email\"/>
</div>
    ".$this->system->Element(["button", "Verify", [
     "class" => "BB v2",
     "data-form" => ".RecoverUsername",
     "data-processor" => base64_encode("v=".base64_encode("2FA:Email"))
    ]]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>