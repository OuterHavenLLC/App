<?php
 Class 2FA extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Email() {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "2FA",
    "2FAconfierm",
    "Email"
   ]);
   if(!empty($data["2FA"]) && !empty($data["2FAconfirm"])) {
    $_2FA = md5($data["2FA"]);
    $_2FAconfirm = $data["2FAconfirm"];
    if($_2FA == $_2FAconfirm) {
     $responseType = "UpdateContent";
     // LOAD RETURN VIEW
    } else {
     // LOAD 2FA ERROR MESSAGE WITH .GoToParent BUTTON
     $r = $this->system->Page("XXXX");
    }
   } else {
    $responseType = "GoToView";
    // LOOP THROUGH ORIGINAL FORM DATA
    // ADD 2FA (CODE) AND 2FA (VERIFY) INPUTS
    $r = $this->system->Page("XXXX");
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Phone() {
   $r = $this->system->Page("XXXX");
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>