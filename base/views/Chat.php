<?php
 Class Chat extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $generateID = $data["GenerateID"] ?? "";
   $id = $data["ID"] ?? "";
   $username = $data["Username"] ?? "";
   $r = [
    "Body" => "The Chat Identifier or Username are missing."
   ];
   if((!empty($id) || $generateID == 1) && !empty($username)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $id = ($generateID == 1) ? md5("$you-Chat-".uniqid()) : $id;
    $username = base64_decode($username);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $new = (empty($chat)) ? 1 : 0;
    $action = ($new == 1) ? "Create" : "Update";
    $r = $this->core->Element([
     "h1", "Chat Editor"
    ]).$this->core->Element([
     "p", "Create or edit a Group Chat."
    ]).$this->core->Element([
     "p", "ID: $id"
    ]).$this->core->Element([
     "p", "Username: $username"
    ]);
    $r = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton",
      "data-form" => ".ChatEditor$id",
      "data-processor" => base64_encode("#")
     ]]),
     "Front" => $r
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $chatID = $data["ID"] ?? "";
   $chatID = $data["Username"] ?? $chatID;
   $group = $data["Group"] ?? 0;
   $information = $data["Information"] ?? 0;
   $oneOnOne = $data["1on1"] ?? 0;
   $r = [
    "Body" => "The Chat Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($chatID)) {
    $id = base64_decode($chatID);
    $r = [
     "Body" => "The Chat Type is missing."
    ];
    if($information == 1) {
     $accessCode = "Accepted";
     $r = $this->core->Element([
      "h1", "Something went wrong..."
     ]).$this->core->Element([
      "p", "Chat Information is only viewable for Group Chats."
     ]);
     if($group == 1) {
      $r = $this->core->Element([
       "h1", "Chat Information"
      ]).$this->core->Element([
       "p", "Soon you will see chat information and a full history of attachments in a grid. If you created the Group Chat, you will also be able to edit it. If the chat is a Group Chat and visible to the public, you will also be able to share it."
      ]);
     } elseif($oneOnOne == 1) {
      $r = $this->view(base64_encode("Profile:Home"), ["Data" => [
       "Chat" => 1,
       "UN" => $chatID
      ]]);
      $r = $this->core->RenderView($r);
     }
    } else {
     $check = 1;
     $r = [
      "Body" => "The Group Chat has not been created."
     ];
     if($group == 1) {
      $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
      $accessCode = (!empty($chat)) ? "Accepted" : "Denied";
      $active = "Active";
      $check = (!empty($chat)) ? 1 : 0;
      $profilePcuture = "";
      $t = $this->core->Member($this->core->ID);
      $to = $chat["Title"] ?? "Group Chat";
     } elseif($oneOnOne == 1) {
      $t = $this->core->Member($id);
      $id = md5($id);
      $active = $t["Activity"]["OnlineStatus"];
      $active = ($active == 1) ? "Online" : "Offline";
      $displayName = $t["Personal"]["DisplayName"];
      $to = $t["Personal"]["DisplayName"];
     } if(($check == 1 && $group == 1) || $oneOnOne == 1) {
      $accessCode = "Accepted";
      $atinput = ".ChatAttachments$id-Attachments";
      $at = base64_encode("Share with $displayName in Chat:$atinput");
      $atinput = "$atinput .rATT";
      $at2 = base64_encode("Added to Chat Message!");
      $r = $this->core->Change([[
       "[Chat.1on1]" => $oneOnOne,
       "[Chat.ActivityStatus]" => $active,
       "[Chat.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$atinput&ID="),
       "[Chat.Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&AddTo=$at&Added=$at2&CARD=1&UN=".base64_encode($you)."&st=XFS"),
       "[Chat.Files.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
       "[Chat.DisplayName]" => $displayName,
       "[Chat.Group]" => $group,
       "[Chat.ID]" => $id,
       "[Chat.Information]" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=$oneOnOne&Group=$group&ID=$chatID&Information=1"),
       "[Chat.List]" => base64_encode("v=".base64_encode("Chat:List")."&1on1=$oneOnOne&Group=$group&ID=$chatID"),
       "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:6em;width:calc(100% - 1em)"),
       "[Chat.SecureID]" => $id,
       "[Chat.Send]" => base64_encode("v=".base64_encode("Chat:Save")),
       "[Chat.To]" => $to,
       "[Chat.Type]" => $group
      ], $this->core->Page("a4c140822e556243e3edab7cae46466d")]);
      $r = ($card == 1) ? [
       "Front" => $r
      ] : $r;
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Join(array $a) {
   // JOIN OR LEAVE THE ACTIVE CHAT (IF NOT YOURS)
  }
  function List(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $group = $data["Group"] ?? 0;
   $id = $data["ID"] ?? "";
   $oneOnOne = $data["1on1"] ?? 0;
   $r = $this->core->Page("2ce9b2d2a7f5394df6a71df2f0400873");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $chat = [];
    $extension = $this->core->Page("1f4b13bf6e6471a7f5f9743afffeecf9");
    $id = base64_decode($id);
    if($group == 1) {
     $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
     $chat = $chat["Messages"] ?? [];
     $to = "";
    } elseif($oneOnOne == 1) {
     $t = $this->core->Member($id);
     $theirChat = $this->core->Data("Get", ["chat", md5($id)]) ?? [];
     $theirChat = $theirChat["Messages"] ?? [];
     $yourChat = $this->core->Data("Get", ["chat", md5($you)]) ?? [];
     $yourChat = $yourChat["Messages"] ?? [];
     $chat = array_merge($theirChat, $yourChat);
     $to = $t["Login"]["Username"];
    } if($group == 1 || $oneOnOne == 1) {
     $accessCode = "Accepted";
     foreach($chat as $key => $value) {
      $check = 1;
      $check2 = 1;
      if($oneOnOne == 1) {
       $check = ($value["From"] == $to && $value["To"] == $you) ? 1 : 0;
       $check2 = ($value["From"] == $you && $to == $value["To"]) ? 1 : 0;
      } if($group == 1 || $check == 1 || $check2 == 1) {
       $attachments = "";
       $class = ($value["From"] != $you) ? "MSGt" : "MSGy";
       $liveView = base64_encode("LiveView:InlineMossaic");
       if(!empty($value["Attachments"])) {
        $attachments = $this->view($liveView, ["Data" => [
         "ID" => base64_encode(implode(";", $value["Attachments"])),
         "Type" => base64_encode("DLC")
        ]]);
        $attachments = $this->core->RenderView($attachments);
       }
       $message = (!empty($value["Message"])) ? $this->core->Element([
        "p", $value["Message"]
       ]) : "";
       $chat[$key] = [
        "[Message.Attachments]" => $attachments,
        "[Message.Class]" => $class,
        "[Message.MSG]" => $message,
        "[Message.Sent]" => $this->core->TimeAgo($value["Timestamp"])
       ];
      }
     }
    }
   } if(!empty($chat)) {
    $r = "";
    ksort($chat);
    foreach($chat as $key => $value) {
     $message = $extension;
     $r .= $this->core->Change([$value, $message]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Menu(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $integrated = $data["Integrated"] ?? 0;
   $r = [
    "Body" => "Unknown Error."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } else {
    $accessCode = "Accepted";
    $search = base64_encode("Search:Containers");
    $r = $this->core->Change([[
     "[Chat.1on1]" => base64_encode("v=$search&1on1=1&Integrated=$integrated&st=Chat"),
     "[Chat.Groups]" => base64_encode("v=$search&Group=1&Integrated=$integrated&st=GroupChat"),
     "[Chat.ID]" => md5($you)
    ], $this->core->Page("2e1855b9baa7286162fb571c5f80da0f")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $attachmentData = $data["Attachments"] ?? "";
   $group = $data["Group"] ?? 0;
   $id = $data["ID"] ?? "";
   $message = $data["Message"] ?? "";
   $check = (!empty($attachmentData) && empty($message)) ? 1 : 0;
   $check2 = (empty($attachmentData) && !empty($message)) ? 1 : 0;
   $check3 = (!empty($attachmentData) && !empty($message)) ? 1 : 0;
   $oneOnOne = $data["1on1"] ?? 0;
   $r = [
    "Body" => "A message or attachment are required."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && ($check == 1 || $check2 == 1 || $check3 == 1)) {
    $accessCode = "Accepted";
    $attachments = [];
    $chat = [];
    $now = $this->core->timestamp;
    if(!empty($attachmentData)) {
     $dlc = array_reverse(explode(";", base64_decode($attachmentData)));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($attachments, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
     $attachments = array_unique($attachments);
    } if($group == 1) {
     $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
     $chat["Description"] = $chat["Description"] ?? "";
     $chat["Title"] = $chat["Title"] ?? "Group Chat";
     $messages = $chat["Messages"] ?? [];
     $to = "";
    } elseif($oneOnOne == 1) {
     $chat = $this->core->Data("Get", ["chat", md5($you)]) ?? [];
     $chat["UN"] = $chat["UN"] ?? $you;
     $messages = $chat["Messages"] ?? [];
     $t = $this->core->Data("Get", ["mbr", $id]) ?? [];
     $to = $t["Login"]["Username"];
    }
    $paid = $data["PaidChat"] ?? 0;
    $messages[$now] = [
     "Attachments" => $attachments,
     "From" => $you,
     "Message" => $message,
     "Paid" => $paid,
     "Read" => 0,
     "Timestamp" => $now,
     "To" => $to
    ];
    $chat["Messages"] = $messages;
    if($group == 1) {
     $this->core->Data("Save", ["chat", $id, $chat]);
    } elseif($oneOnOne == 1) {
     $this->core->Data("Save", ["chat", md5($you), $chat]);
     $this->core->SendBulletin([
      "Data" => [
       "From" => $you
      ],
      "To" => $to,
      "Type" => "NewMessage"
     ]);
    }
    $r = [
     "Body" => "Your message has been sent.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function Share(array $a) {
   $accessCode = "Denied";
   $action = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["GroupChat", "ID", "UN"]);
   $id = $data["ID"];
   $r = $this->core->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->core->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($this->core->PlainText([
     "Data" => $id,
     "HTMLDencode" => 1
    ]));
    $sid = md5($this->core->timestamp);
    $r = $this->core->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessage")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->core->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $action = $this->core->Element(["button", "Send", [
     "class" => "BB v2",
     "data-form" => ".ShareMessage$sid",
     "data-processor" => base64_encode("v=".base64_encode("Chat:SaveShare"))
    ]]);
   }
   return [
    "Action" => $action,
    "Front" => $r
   ];
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>