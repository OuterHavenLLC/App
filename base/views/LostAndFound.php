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
    $r = $this->system->Element(["button", "Back", [
     "class" => "GoToParent LI header",
     "data-type" => "LostAndFound"
    ]]).$this->system->Element([
     "h2", "Recover Password"
    ]);
   } else {
    $r = $this->system->Element([
     "h2", "Done"
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
    $r = $this->system->Element(["button", "Back", [
     "class" => "GoToParent LI header",
     "data-type" => "LostAndFound"
    ]]).$this->system->Element([
     "h2", "Recover PIN"
    ]);
   } else {
    $r = $this->system->Element([
     "h2", "Done"
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
    $r = $this->system->Element(["button", "Back", [
     "class" => "GoToParent LI header",
     "data-type" => "LostAndFound"
    ]]).$this->system->Element([
     "h2", "Recover Username"
    ]);
   } else {
    $r = $this->system->Element([
     "h2", "Done"
    ]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>