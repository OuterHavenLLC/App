<?php
 Class LostAndFound extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(): string {
   return $this->core->JSONResponse([
    "Card" => [
     "Front" => [
      "ChangeData" => [
       "[LostAndFound.Options.Password]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPassword")),
       "[LostAndFound.Options.PIN]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPIN")),
       "[LostAndFound.Options.Username]" => base64_encode("v=".base64_encode("LostAndFound:RecoverUsername"))
      ],
      "ExtensionID" => "65c5bed973a21411e6167bbdc721bbe4"
     ]
    ]
   ]);
  }
  function RecoverPassword(array $data): string {
   $_AddTopMargin = "0";
   $_ResponseType = "GoToView";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("LostAndFound");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $r = "A valid Email address is required.";
    } elseif($i == 0) {
     $r = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverPassword"));
     $viewData["ParentView"] = base64_decode($parentView);
     $viewData["Email"] = base64_encode($email);
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $r = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $r = $this->core->RenderView($r);
    } if($_AccessCode != "Accepted") {
     $r = $this->core->Change([[
      "[Error.Message]" => $r,
      "[Error.ParentView]" => base64_decode($parentView)
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-67ac610803c33")]);
    }
   } elseif($step == 3) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $email = $data["Email"] ?? base64_encode("");
    $email = base64_decode($email);
    $members = $this->core->DatabaseSet("Member");
    $username = "";
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
      $username = $member["Login"]["Username"];
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $r = "A valid Email address is required.";
    } elseif($i == 0) {
     $r = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $password = uniqid();
     $you = $this->core->Member($username);
     $you["Login"]["Password"] = md5($password);
     $this->core->Data("Save", ["mbr", md5($username), $you]);
     $r = $this->core->Change([[
      "[Success.Message]" => "Your provisional password is <strong>$password</strong>. We recommend changing this password as soon as possible for your security.",
      "[Success.ViewPairID]" => base64_decode($parentView),
     ], $this->core->Extension("d4449b01c6da01613cff89e6cf723ad1")]);
    }
   } else {
    $parentView = "RecoverPassword";
    $_ResponseType = "View";
    $r = $this->core->Change([[
     "[LostAndFound.Recovery.ParentView]" => $parentView,
     "[LostAndFound.Recovery.ParentView.Encoded]" => base64_encode($parentView),
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPassword")."&Step=".base64_encode(2)),
     "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
      "Step" => base64_encode(3)
     ], true)),
     "[LostAndFound.Recovery.Type]" => "Password"
    ], $this->core->Extension("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => $_AddTopMargin,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $_ResponseType
   ]);
  }
  function RecoverPIN(array $data): string {
   $_AddTopMargin = "0";
   $_ResponseType = "GoToView";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("LostAndFound");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $r = "A valid Email address is required.";
    } elseif($i == 0) {
     $r = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverPIN"));
     $viewData["ParentView"] = base64_decode($parentView);
     $viewData["Email"] = base64_encode($email);
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $r = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $r = $this->core->RenderView($r);
    } if($_AccessCode != "Accepted") {
     $r = $this->core->Change([[
      "[Error.Message]" => $r,
      "[Error.ParentView]" => base64_decode($parentView)
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-67ac610803c33")]);
    }
   } elseif($step == 3) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $email = $data["Email"] ?? base64_encode("");
    $email = base64_decode($email);
    $members = $this->core->DatabaseSet("Member");
    $username = "";
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
      $username = $member["Login"]["Username"];
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $r = "A valid Email address is required.";
    } elseif($i == 0) {
     $r = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $pin = rand(000000, 999999);
     $you = $this->core->Member($username);
     $you["Login"]["PIN"] = md5($pin);
     $this->core->Data("Save", ["mbr", md5($username), $you]);
     $r = $this->core->Change([[
      "[Success.Message]" => "Use <strong>$pin</strong> the next time a PIN is required. We also recommend changing this provisional PIN as soon as possible for your security.",
      "[Success.ViewPairID]" => base64_decode($parentView),
     ], $this->core->Extension("d4449b01c6da01613cff89e6cf723ad1")]);
    } if($_AccessCode != "Accepted") {
     $r = $this->core->Change([[
      "[Error.Message]" => $r,
      "[Error.ParentView]" => base64_decode($parentView)
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-67ac610803c33")]);
    }
   } else {
    $parentView = "RecoverPIN";
    $_ResponseType = "View";
    $r = $this->core->Change([[
     "[LostAndFound.Recovery.ParentView]" => $parentView,
     "[LostAndFound.Recovery.ParentView.Encoded]" => base64_encode($parentView),
     "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPIN")."&Step=".base64_encode(2)),
     "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
      "Step" => base64_encode(3)
     ], true)),
     "[LostAndFound.Recovery.Type]" => "PIN"
    ], $this->core->Extension("84e04efba2e596a97d2ba5f2762dd60b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => $_AddTopMargin,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $_ResponseType
   ]);
  }
  function RecoverUsername(array $data): string {
   $_AccessCode = "Denied";
   $_AddTopMargin = "0";
   $_Dialog = "";
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("LostAndFound");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AddTopMargin = "1";
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $message = "A valid Email address is required.";
    } elseif($i == 0) {
     $message = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverUsername"));
     $viewData["ParentView"] = base64_decode($parentView);
     $viewData["Email"] = base64_encode($email);
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $_View = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $_View = $this->core->RenderView($_View);
    } if($_AccessCode != "Accepted") {
     $_Dialog = [
      "Body" => $message
     ];
     $_View = "";
    }
   } elseif($step == 3) {
    $_AddTopMargin = "1";
    $email = $data["Email"] ?? base64_encode("");
    $email = base64_decode($email);
    $members = $this->core->DatabaseSet("Member");
    $username = "";
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     if($emailIsTaken == 1 && $i == 0) {
      $i++;
      $username = $member["Login"]["Username"];
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $message = "A valid Email address is required.";
    } elseif($i == 0) {
     $message = "The email address is not in use.";
    } else {
     $_AccessCode = "Accepted";
     $_View = [
      "ChangeData" => [
       "[Success.Message]" => "Welcome back, <strong>$username</strong>! You may now sign in to your profile.",
       "[Success.ViewPairID]" => base64_decode($parentView),
      ],
      "ExtensionID" => "d4449b01c6da01613cff89e6cf723ad1"
     ];
    } if($_AccessCode != "Accepted") {
     $_Dialog = [
      "Body" => $message
     ];
     $_View = "";
    }
   } else {
    $_ResponseType = "View";
    $parentView = "RecoverUsername";
    $_View = [
     "ChangeData" => [
      "[LostAndFound.Recovery.ParentView]" => $parentView,
      "[LostAndFound.Recovery.ParentView.Encoded]" => base64_encode($parentView),
      "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverUsername")."&Step=".base64_encode(2)),
      "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
       "Step" => base64_encode(3)
      ], true)),
      "[LostAndFound.Recovery.Type]" => "Username"
     ],
     "ExtensionID" => "84e04efba2e596a97d2ba5f2762dd60b"
    ];
   }
   return $this->core->JSONResponse([//PURGE: 45787465-6e73-496f-ae42-794d696b65-67ac610803c33
    "AddTopMargin" => $_AddTopMargin,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>