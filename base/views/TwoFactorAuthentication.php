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
   $r = $this->system->Change([[
    "[2FA.Error.Message]" => "An email address is required in order for us to continue the verification process.",
    "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
   ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
   if(!empty($data["Email"]) && $ck == 0) {
    $accessCode = "Accepted";
    $email = $data["Email"];
    $emailIsRegistered = 0;
    $r = $this->system->Change([[
     "[2FA.Error.Message]" => "The email <strong>$email</strong> is not registered to any Member.",
     "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
    ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
    $members = $this->system->DatabaseSet("MBR") ?? [];
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     if($email == $member["Personal"]["Email"]) {
      $emailIsRegistered++;
     }
    } if($emailIsRegistered > 0) {
     $_VerificationCode = uniqid("OH-");
     $_SecureVerificationCode = md5($_VerificationCode);
     $this->system->SendEmail([
      "Message" => $this->system->Element([
       "p", "Use the code below to verify your email address:"
      ]).$this->system->Element([
       "h4", $_VerificationCode
      ]),
      "Title" => "Your Verification Code: $_VerificationCode",
      "To" => $email
     ]);
     $r = $this->system->Change([[
      "[2FA.Confirm]" => $_SecureVerificationCode,
      "[2FA.Email]" => $email,
      "[2FA.Step2]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:Email")),
      "[2FA.ReturnView]" => $data["ReturnView"],
      "[2FA.ViewPairID]" => $data["ViewPairID"]
     ], $this->system->Page("ab9d092807adfadc3184c8ab844a1406")]);
    }
   } elseif($ck == 1) {
    $accessCode = "Accepted";
    $_VerificationCode = md5($data["2FA"]);
    $_SecureVerificationCode = $data["2FAconfirm"];
    $r = $this->system->Change([[
     "[2FA.Error.Message]" => "The code you entered does not match the one we sent you.",
     "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
    ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
    if($_VerificationCode == $_SecureVerificationCode) {
     $r = $this->system->Change([[
      "[2FA.Error.Message]" => "The Return View ID is missing.",
      "[2FA.Error.ViewPairID]" => "LostAndFound"
     ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
     if(!empty($data["ReturnView"])) {
      $returnView = base64_decode($data["ReturnView"]);
      $returnView = json_decode($returnView, true);
      $r = $this->view(base64_encode($returnView["Group"].":".$returnView["View"]), ["Data" => [
       "2FAReturn" => 1,
       "Email" => base64_encode($data["Email"])
      ]]);
     }
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
  function FirstTime(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   // 2FA FOR NEW MEMBERS
   return "OK";
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>