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
   $data = $this->system->FixMissing($data, [
    "2FA",
    "2FAconfirm",
    "BirthMonth",
    "BirthYear",
    "Email",
    "Name",
    "Password",
    "Password2",
    "PIN",
    "PIN2",
    "ReturnView",
    "Username",
    "ViewPairID"
   ]);
   $required = [
    "BirthMonth",
    "BirthYear",
    "Email",
    "Name",
    "Password",
    "Password2",
    "PIN",
    "PIN2",
    "ReturnView",
    "Username"
   ];
   $ck = ($data["Password"] == $data["Password2"]) ? 1 : 0;
   $ck = ($ck == 1 && $data["PIN"] == $data["PIN2"]) ? 1 : 0;
   $i = 0;
   $inputs = [];
   $r = $this->system->Change([[
    "[2FA.Error.Message]" => "Something went wrong...",
    "[2FA.Error.ViewPairID]" => "SignUp"
   ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
   foreach($required as $key) {
    if(!empty($data[$key])) {
     $i++;
     $inputs[$key] = $data[$key] ?? "";
    }
   } if($data["Password"] != $data["Password2"]) {
    $r = $this->system->Change([[
     "[2FA.Error.Message]" => "Your Passwords must match.",
     "[2FA.Error.ViewPairID]" => "SignUp"
    ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
   } elseif($data["PIN"] != $data["PIN2"]) {
    $r = $this->system->Change([[
     "[2FA.Error.Message]" => "Your PINs must match.",
     "[2FA.Error.ViewPairID]" => "SignUp"
    ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
   } if($ck == 1 && $i == count($required)) {
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
      "[2FA.Error.Message]" => "The email <strong>$email</strong> is already in use.",
      "[2FA.Error.ViewPairID]" => $data["ViewPairID"]
     ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
     $members = $this->system->DatabaseSet("MBR") ?? [];
     foreach($members as $key => $value) {
      $value = str_replace("c.oh.mbr.", "", $value);
      $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
      if($email == $member["Personal"]["Email"]) {
       $emailIsRegistered++;
      }
     } if($emailIsRegistered == 0) {
      $_VerificationCode = uniqid("OH-");
      $_SecureVerificationCode = md5($_VerificationCode);
      $_Inputs = "";
      foreach($inputs as $key => $value) {
       if($key != "ViewPairID") {
        $_Inputs.="<input name=\"$key\" type=\"hidden\" value=\"$value\"/>\r\n";
       }
      }
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
       "[2FA.Inputs]" => $_Inputs,
       "[2FA.Step2]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:FirstTime")),
       "[2FA.ReturnView]" => $data["ReturnView"],
       "[2FA.ViewPairID]" => $data["ViewPairID"]
      ], $this->system->Page("e0513cfec7f3f4505d30c0c854e9dac2")]);
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
        "BirthMonth" => $data["BirthMonth"],
        "BirthYear" => $data["BirthYear"],
        "Email" => $data["Email"],
        "Name" => $data["Name"],
        "Password" => $data["Password"],
        "Password2" => $data["Password2"],
        "PIN" => $data["PIN"],
        "PIN2" => $data["PIN2"],
        "Username" => $data["Username"]
       ]]);
      }
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>