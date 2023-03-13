<?php
 Class TwoFactorAuthentication extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Email(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "2FA",
    "2FAconfierm",
    "Email",
    "ReturnView",
    "ViewPairID"
   ]);
   $ck = (!empty($data["2FA"]) && !empty($data["2FAconfirm"])) ? 1 : 0;
   // BEGIN TEMP
   $r = $this->sytem->Element([
    "h1", "Error", ["class" => "CenterText UpperCase"]
   ]).$this->sytem->Element([
    "p", "[LostAndFound.Error.Message]", ["class" => "CenterText"]
   ]).$this->sytem->Element([
    "div", "&nbsp;", ["class" => "Desktop33"]
   ]).$this->sytem->Element([
    "div", $this->system->Element(["button", "Try Again", [
     "class" => "BB v2 v2w"
    ]]), ["class" => "Desktop33"]
   ]).$this->sytem->Element([
    "div", "&nbsp;", ["class" => "Desktop33"]
   ]);
   $r = $this->system->Element(["div", $r, ["class" => "InnerMargin"]]);
   //END TEMP
   $r = $this->system->Change([[
    "[2FA.Error.Message]" => "An email address is required in order for us to continue the verification process.",
    "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
   ], $r]);
   #], $this->system->Page("XXXX")]);
   if(empty($data["Email"]) && $ck == 0) {
    $accessCode = "Accepted";
    $r = $this->sytem->Element([
     "h1", "Check Your Email", ["class" => "CenterText UpperCase"]
    ]).$this->sytem->Element([
     "p", "We sent a verification code to $email, please enter it below."
    ]);
   } elseif($ck == 1) {
    $accessCode = "Accepted";
    $_2FA = md5($data["2FA"]);
    $_2FAconfirm = $data["2FAconfirm"];
    if($_2FA == $_2FAconfirm) {
     // LOAD RETURN VIEW WITH "2FAreturn" PARAMETER
    } else {
     // BEGIN TEMP
     $r = $this->sytem->Element([
      "h1", "Error", ["class" => "CenterText UpperCase"]
     ]).$this->sytem->Element([
      "p", "[LostAndFound.Error.Message]", ["class" => "CenterText"]
     ]).$this->sytem->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]).$this->sytem->Element([
      "div", $this->system->Element(["button", "Try Again", [
       "class" => "BB v2 v2w"
      ]]), ["class" => "Desktop33"]
     ]).$this->sytem->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]);
     $r = $this->system->Element(["div", $r, ["class" => "InnerMargin"]]);
     //END TEMP
     $r = $this->system->Change([[
      "[2FA.Error.Message]" => "",
      "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
     ], $r]);
     #], $this->system->Page("XXXX")]);
    }
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "GoToView"
   ]);
  }
  function Phone(array $a) {
   $r = $this->system->Page("XXXX");
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>