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
    "2FAconfirm",
    "Email",
    "ReturnView",
    "ViewPairID"
   ]);
   $ck = (!empty($data["2FA"]) && !empty($data["2FAconfirm"])) ? 1 : 0;
   // BEGIN TEMP
   $r = $this->system->Element([
    "h1", "Error", ["class" => "CenterText UpperCase"]
   ]).$this->system->Element([
    "p", "[2FA.Error.Message]", ["class" => "CenterText"]
   ]).$this->system->Element([
    "div", "&nbsp;", ["class" => "Desktop33"]
   ]).$this->system->Element([
    "div", $this->system->Element(["button", "Try Again", [
     "class" => "BB GoToParent v2",
     "data-type" => $data["ViewPairID"]
    ]]), ["class" => "Desktop33"]
   ]).$this->system->Element([
    "div", "&nbsp;", ["class" => "Desktop33"]
   ]);
   $r = $this->system->Element(["div", $r, ["class" => "InnerMargin"]]);
   //END TEMP
   $r = $this->system->Change([[
    "[2FA.Error.Message]" => "An email address is required in order for us to continue the verification process.",
    "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
   ], $r]);
   #], $this->system->Page("XXXX")]);
   if(!empty($data["Email"]) && $ck == 0) {
    $accessCode = "Accepted";
    $email = $data["Email"];
    $emailIsRegistered = 0;
    if($emailIsRegistered == 1) {
     // BEGIN TEMP
     $r = $this->system->Element([
      "h1", "Error", ["class" => "CenterText UpperCase"]
     ]).$this->system->Element([
      "p", "[2FA.Error.Message]", ["class" => "CenterText"]
     ]).$this->system->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]).$this->system->Element([
      "div", $this->system->Element(["button", "Try Again", [
       "class" => "BB GoToParent v2",
       "data-type" => $data["ViewPairID"]
      ]]), ["class" => "Desktop33"]
     ]).$this->system->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]);
     $r = $this->system->Element(["div", $r, ["class" => "InnerMargin"]]);
     //END TEMP
     $r = $this->system->Change([[
      "[2FA.Error.Message]" => "The email <em>$email</em> is not registered to any Member.",
      "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
     ], $r]);
     #], $this->system->Page("XXXX")]);
     // CHECK THAT EMAIL IS IN USE, ADD ERROR HANDLING
    } else {
     $_VerificationCode = uniqid("OH-");
     $_SecureVerificationCode = md5($_VerificationCode);
     // BEGIN TEMP
     $r="
<div class=\"ParentPage2FAStep1\">
 <div class=\"InnerMargin\">
  <h1 class=\"CenterText\">Check Your Email</h1>
  <p class=\"CenterText\">We sent a verification code to <strong>[2FA.Email]</strong>, please enter it below.</p>
  <div class=\"Desktop75\">
   [2FA.Inputs]
   <input name=\"2FA\" placeholder=\"OH-A1B2C3x4y5z\" type=\"text\" value=\"[2FA.Temp]\"/>
   <input name=\"2FAconfirm\" type=\"hidden\" value=\"[2FA.Confirm]\"/>
   <input name=\"ReturnView\" type=\"hidden\" value=\"[2FA.ReturnView]\"/>
   <input name=\"ViewPairID\" type=\"hidden\" value=\"2FAStep1\"/>
   <div class=\"Desktop50\">
    <button class=\"BB GoToParent v2 v2w\" data-type=\"[2FA.ViewPairID]\">Back</button>
   </div>
   <div class=\"Desktop50\">
    <button class=\"BB SendData v2 v2w\" data-form=\".ParentPage2FAStep1\" data-processor=\"[2FA.Step2]\">Confirm</button>
   </div>
  </div>
 </div>
</div>
     ";
     // END TEMP
     $r = $this->system->Change([[
      "[2FA.Temp]" => $_VerificationCode,
      "[2FA.Confirm]" => $_SecureVerificationCode,
      "[2FA.Email]" => $email,
      "[2FA.Step2]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
      "[2FA.ReturnView]" => $data["ReturnView"],
      "[2FA.ViewPairID]" => $data["ViewPairID"]
     ], $r]);
     #], $this->system->Page("XXXX")]);
    }
   } elseif($ck == 1) {
    $accessCode = "Accepted";
    $_VerificationCode = md5($data["2FA"]);
    $_SecureVerificationCode = $data["2FAconfirm"];
    if($_VerificationCode == $_SecureVerificationCode) {
     // LOAD RETURN VIEW WITH 2FAreturn PARAMETER
    } else {
     // BEGIN TEMP
     $r = $this->system->Element([
      "h1", "Error", ["class" => "CenterText UpperCase"]
     ]).$this->system->Element([
      "p", "[LostAndFound.Error.Message]", ["class" => "CenterText"]
     ]).$this->system->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]).$this->system->Element([
      "div", $this->system->Element(["button", "Try Again", [
       "class" => "BB v2 v2w",
       "data-type" => "[2FA.ViewPairID]"
      ]]), ["class" => "Desktop33"]
     ]).$this->system->Element([
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
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   // 2FA VIA SMS
   $r = $this->system->Page("XXXX");
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>