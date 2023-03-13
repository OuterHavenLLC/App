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
    $r = "
<div class=\"ParentPageRecoverUsername\">
 <button class=\"GoToParent LI header\" data-type=\"LostAndFound\">Back</button>
 <div class=\"InnerMargin\">
  <h2>Recover Username</h2>
  <p>Please enter your email address below. Once your email is verified, we will give you your username.</p>
  <input class=\"req\" name=\"Email\" placeholder=\"mike@outerhaven.nyc\" type=\"email\"/>
  <input name=\"ReturnView\" type=\"hidden\" value=\"[LostAndFound.Recovery.ReturnView]\"/>
  <input name=\"ViewPairID\" type=\"hidden\" value=\"RecoverUsername\"/>
  <button class=\"BBB SendData v2\" data-form=\".ParentPageRecoverUsername\" data-processor=\"[LostAndFound.Recovery.Processor]\">Verify</button>
 </div>
</div>
    ";
    $r = $this->system->Change([[
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
     "[LostAndFound.Recovery.ReturnView]" => base64_encode(json_encode([
      "Group" => "LostAndFound",
      "View" => "RecoverUsername"
     ], true))
    ], $r]);
    #], $this->system->Page("XXXX")]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>