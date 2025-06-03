<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol . $_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
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
   $_AccessCode = "Denied";
   $_Dialog = "";
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("LostAndFound");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
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
     $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
     $viewData = json_decode(base64_decode($viewData), true);
     $viewData["Step"] = base64_encode(3);
     $viewData["Email"] = base64_encode($email);
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ParentView"] = $parentView;
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverUsername"));
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
     $password = uniqid();
     $you = $this->core->Member($username);
     $you["Login"]["Password"] = md5($password);
     $this->core->Data("Save", ["mbr", md5($username), $you]);
     $_View = [
      "ChangeData" => [
       "[Success.Message]" => "Your provisional password is <strong>$password</strong>. We recommend changing this password as soon as possible for your security.",
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
    $parentView = "RecoverPassword";
    $timestamp = $this->core->timestamp;
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".LostAndFoundEmail$timestamp",
       [
        [
         "Attributes" => [
          "class" => "EmptyOnSuccess req",
          "name" => "Email",
          "placeholder" => "johnny.test@outerhaven.nyc",
          "type" => "email"
         ],
         "Options" => [
         ],
         "Type" => "Text",
         "Value" => ""
        ]
       ]
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[LostAndFound.ID]" => $timestamp,
      "[LostAndFound.Recovery.ParentView]" => $parentView,
      "[LostAndFound.Recovery.ParentView.Encoded]" => base64_encode($parentView),
      "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPassword")."&Step=".base64_encode(2)),
      "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
       "Step" => base64_encode(3)
      ], true)),
      "[LostAndFound.Recovery.Type]" => "Password"
     ],
     "ExtensionID" => "84e04efba2e596a97d2ba5f2762dd60b"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function RecoverPIN(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = "";
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("LostAndFound");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
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
     $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
     $viewData = json_decode(base64_decode($viewData), true);
     $viewData["Step"] = base64_encode(3);
     $viewData["Email"] = base64_encode($email);
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ParentView"] = $parentView;
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverUsername"));
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
     $pin = rand(000000, 999999);
     $you = $this->core->Member($username);
     $you["Login"]["PIN"] = md5($pin);
     $this->core->Data("Save", ["mbr", md5($username), $you]);
     $_View = [
      "ChangeData" => [
       "[Success.Message]" => "Use <strong>$pin</strong> the next time a PIN is required. We also recommend changing this provisional PIN as soon as possible for your security.",
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
    $parentView = "RecoverPIN";
    $timestamp = $this->core->timestamp;
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".LostAndFoundEmail$timestamp",
       [
        [
         "Attributes" => [
          "class" => "EmptyOnSuccess req",
          "name" => "Email",
          "placeholder" => "johnny.test@outerhaven.nyc",
          "type" => "email"
         ],
         "Options" => [
         ],
         "Type" => "Text",
         "Value" => ""
        ]
       ]
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[LostAndFound.ID]" => $timestamp,
      "[LostAndFound.Recovery.ParentView]" => $parentView,
      "[LostAndFound.Recovery.ParentView.Encoded]" => base64_encode($parentView),
      "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverPIN")."&Step=".base64_encode(2)),
      "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
       "Step" => base64_encode(3)
      ], true)),
      "[LostAndFound.Recovery.Type]" => "PIN"
     ],
     "ExtensionID" => "84e04efba2e596a97d2ba5f2762dd60b"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function RecoverUsername(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = "";
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $data["Data"] ?? [];
   $i = 0;
   $parentView = $viewData["ParentView"] ?? base64_encode("RecoverUsername");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $data = $this->core->DecodeBridgeData($data);
    $email = $data["Email"] ?? "";
    $members = $this->core->DatabaseSet("Member");
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
     $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
     $viewData = json_decode(base64_decode($viewData), true);
     $viewData["Step"] = base64_encode(3);
     $viewData["Email"] = base64_encode($email);
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ParentView"] = $parentView;
     $data["ReturnView"] = base64_encode(base64_encode("LostAndFound:RecoverUsername"));
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
    $timestamp = $this->core->timestamp;
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".LostAndFoundEmail$timestamp",
       [
        [
         "Attributes" => [
          "class" => "EmptyOnSuccess req",
          "name" => "Email",
          "placeholder" => "johnny.test@outerhaven.nyc",
          "type" => "email"
         ],
         "Options" => [
         ],
         "Type" => "Text",
         "Value" => ""
        ]
       ]
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[LostAndFound.ID]" => $timestamp,
      "[LostAndFound.Recovery.ParentView]" => base64_decode($parentView),
      "[LostAndFound.Recovery.ParentView.Encoded]" => $parentView,
      "[LostAndFound.Recovery.Processor]" => base64_encode("v=".base64_encode("LostAndFound:RecoverUsername")."&Step=".base64_encode(2)),
      "[LostAndFound.Recovery.ViewData]" => base64_encode(json_encode([
       "Step" => base64_encode(3)
      ], true)),
      "[LostAndFound.Recovery.Type]" => "Username"
     ],
     "ExtensionID" => "84e04efba2e596a97d2ba5f2762dd60b"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
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