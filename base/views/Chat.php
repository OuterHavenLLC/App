<?php
 Class Chat extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   // CREATE AND EDIT GROUP CHATS
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $chatID = $data["ID"] ?? "";
   $chatID = $data["Username"] ?? $chatID;
   $group = $data["Group"] ?? 0;
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
    if($group == 1) {
     $active = "Active";
     $displayName = $this->core->Data("Get", ["pf", $id]) ?? [];
     $displayName = $displayName["Title"] ?? "Group Chat";
     $info = "v=".base64_encode("Chat:Information")."&ID=$id";
     $profilePcuture = "";
     $t = $this->core->Member($this->core->ID);
     $to = $displayName;
    } elseif($oneOnOne == 1) {
     $t = $this->core->Member($id);
     $id = md5($id);
     $active = $t["Activity"]["OnlineStatus"];
     $active = ($active == 1) ? "Online" : "Offline";
     $displayName = $t["Personal"]["DisplayName"];
     $info = "v=".base64_encode("Profile:Home")."&CARD=1&Chat=1&UN=".base64_encode($t["Login"]["Username"]);
     $to = $t["Personal"]["DisplayName"];
    } if($group == 1 || $oneOnOne == 1) {
     $accessCode = "Accepted";
     $at1 = base64_encode("Share with $displayName in Chat:.ChatAttachments$id-ATTF");
     $at2 = base64_encode("Added to Chat Message!");
     $r = $this->core->Change([[
      "[Chat.1on1]" => $oneOnOne,
      "[Chat.ActivityStatus]" => $active,
      "[Chat.Attachments]" => base64_encode("v=".base64_encode("Search:Containers")."&AddTo=$at1&Added=$at2&UN=$you&st=XFS"),
      "[Chat.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
      "[Chat.DisplayName]" => $displayName,
      "[Chat.Group]" => $group,
      "[Chat.ID]" => $id,
      "[Chat.List]" => base64_encode("v=".base64_encode("Chat:List")."&1on1=$oneOnOne&Group=$group&ID=$chatID"),
      "[Chat.Profile]" => base64_encode($info),
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
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
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
       }
       $message = (!empty($value["Message"])) ? $this->core->Element([
        "p", base64_decode($vvalue["Message"])
       ]) : "";
       $chat[$key] = [
        "[Message.Attachments]" => $this->core->RenderView($attachments),
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
     "[Chat.1:1]" => base64_encode("v=$search&1on1=1&st=Chat"),
     "[Chat.Groups]" => base64_encode("v=$search&Group=1&st=Chat"),
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
   $attachmentData = $data["rATTF"] ?? "";
   $group = $data["Group"] ?? 0;
   $id = $data["ID"] ?? "";
   $message = $data["Message"] ?? "";
   $check = (!empty($attachments) && empty($message)) ? 1 : 0;
   $check2 = (empty($attachments) && !empty($message)) ? 1 : 0;
   $check3 = (!empty($attachments) && !empty($message)) ? 1 : 0;
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
     $dlc = array_reverse(explode(";", base64_decode($data["rATTF"])));
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
    $messages[$now] = [
     "Attachments" => $attachments,
     "From" => $you,
     "Message" => $message,
     "Read" => 0,
     "Timestamp" => $now,
     "To" => $to
    ];
    $chat["Messages"] = array_unique($messages);
    if($group == 1) {
     $this->core->Data("Save", ["chat", $id, $chat]);
    } elseif($oneOnOne == 1) {
     $this->core->Data("Save", ["chat", md5($you), $chat]);
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
   $btn = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["GroupChat", "ID", "UN"]);
   $id = $data["ID"];
   $r = $this->core->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->core->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->core->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->core->timestamp);
    $r = $this->core->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessage")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->core->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->core->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShare")),
     "id" => "fSub"
    ]]);
   }
   return [
    "Action" => $btn,
    "Front" => $r
   ];
  }
  function ShareGroup(array $a) {
   $btn = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["GroupChat", "ID", "UN"]);
   $id = $data["ID"];
   $r = $this->core->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->core->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->core->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->core->timestamp);
    $r = $this->core->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessageGroup")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->core->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->core->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShareGroup")),
     "id" => "fSub"
    ]]);
   }
   return [
    "Action" => $btn,
    "Front" => $r
   ];
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>