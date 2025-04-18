<?php
 Class Chat extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Attachments(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $r = [
    "Body" => "The Chat Identifier or Username are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $grid = "";
    $messages = $chat["Messages"] ?? [];
    foreach($messages as $key => $message) {
     $attachments = $message["Attachments"] ?? [];
     foreach($attachments as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $efs = $this->core->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
        $grid .= $this->core->Element([
         "button", $this->core->GetAttachmentPreview([
          "DLL" => $efs[$f[1]],
          "T" => $f[0],
          "Y" => $you
         ]), [
          "class" => "FrostedBright OpenCard Rounded",
          "data-view" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
         ]
        ]);
       }
      }
     }
    } if(!empty($grid)) {
     $r = $this->core->Element(["div", $grid, ["class" => "Grid3"]]);
    } else {
     $r = $this->core->Element([
      "h3", "Attachments",
      ["class" => "CenterText UpperCase"]
     ]).$this->core->Element([
      "p", "No attachments, yet...",
      ["class" => "CenterText"]
     ]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Bookmark(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Chat Identifier or Join Command missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($command) && !empty($id)) {
    $command = base64_decode($command);
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $r = [
     "Body" => "You cannot remove the Bookmark for your own Group Chat."
    ];
    if($chat["UN"] != $you) {
     $accessCode = "Accepted";
     $contributors = $chat["Contributors"] ?? [];
     $groupChats = $y["GroupChats"] ?? [];
     $processor = "v=".base64_encode("Chat:Bookmark")."&ID=".base64_encode($id);
     $responseType = "View";
     if($command == "Add Bookmark") {
      if(!in_array($id, $groupChats)) {
       array_push($groupChats, $id);
       array_unique($groupChats);
       $y["GroupChats"] = $groupChats;
      }
      $contributors[$you] = "Member";
      $chat["Contributors"] = $contributors;
      $r = [
       "Attributes" => [
        "class" => "UpdateButton v2",
        "data-processor" => base64_encode("$processor&Command=".base64_encode("Remove Bookmark"))
       ],
       "Text" => "Remove Bookmark"
      ];
     } elseif($command == "Remove Bookmark") {
      $accessCode = "Accepted";
      $newContributors = [];
      $newGroupChats = [];
      foreach($contributors as $member => $role) {
       if($member != $you) {
        $newContributors[$member] = $role;
       }
      } foreach($groupChats as $key => $value) {
       if($id != $value) {
        $newGroupChats[$key] = $value;
       }
      }
      $chat["Contributors"] = $newContributors;
      $y["GroupChats"] = $newGroupChats;
      $r = [
       "Attributes" => [
        "class" => "UpdateButton v2",
        "data-processor" => base64_encode("$processor&Command=".base64_encode("Add Bookmark"))
       ],
       "Text" => "Add Bookmark"
      ];
     }
     $this->core->Data("Save", ["chat", $id, $chat]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $generateID = $data["GenerateID"] ?? 0;
   $id = $data["ID"] ?? base64_encode("");
   $username = $data["Username"] ?? base64_encode("");
   $r = [
    "Body" => "The Chat Identifier or Username are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if((!empty($id) || $generateID == 1) && !empty($username)) {
    $accessCode = "Accepted";
    $description = $data["Description"] ?? base64_encode("");
    $description = base64_decode($description);
    $editorID = md5("ChatEditor-$id".$this->core->timestamp);
    $id = base64_decode($id);
    $id = ($generateID == 1) ? md5("$you-Chat-".uniqid()) : $id;
    $title = $data["Title"] ?? base64_encode("");
    $title = base64_decode($title);
    $username = base64_decode($username);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $description = $chat["Description"] ?? $description;
    $new = (empty($chat)) ? 1 : 0;
    $nsfw = $chat["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $chat["PassPhrase"] ?? "";
    $privacy = $chat["Privacy"] ?? $y["Privacy"]["MSG"];
    $title = $chat["Title"] ?? $title;
    $action = ($new == 1) ? "Create" : "Update";
    $header = ($new == 1) ? "New Group Chat" : "Edit $title";
    $r = $this->core->Change([[
     "[Chat.Author]" => $username,
     "[Chat.Description]" => base64_encode($description),
     "[Chat.EditorID]" => $editorID,
     "[Chat.Header]" => $header,
     "[Chat.ID]" => $id,
     "[Chat.New]" => $new,
     "[Chat.PassPhrase]" => base64_encode($passPhrase),
     "[Chat.Title]" => base64_encode($title),
     "[Chat.Visibility.NSFW]" => $nsfw,
     "[Chat.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("eb169be369e5497344f98d826aea4e7d")]);
    $r = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".Chat$editorID",
      "data-processor" => base64_encode("v=".base64_encode("Chat:Save"))
     ]]),
     "Front" => $r
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
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
   $addTo = $data["AddTo"] ?? "";
   $body = $data["Body"] ?? base64_encode("");
   $card = $data["Card"] ?? 0;
   $chatID = $data["ID"] ?? "";
   $chatID = $data["Username"] ?? $chatID;
   $group = $data["Group"] ?? 0;
   $information = $data["Information"] ?? 0;
   $integrated = $data["Integrated"] ?? 0;
   $oneOnOne = $data["1on1"] ?? 0;
   $paidMessage = $data["PaidMessage"] ?? 0;
   $paidMessages = $data["PaidMessages"] ?? 0;
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
      $bl = $this->core->CheckBlocked([$y, "Group Chats", $id]);
      $_Chat = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Chat;$id"),
       "Integrated" => $integrated
      ]);
      if($_Chat["Empty"] == 0) {
       $accessCode = "Accepted";
       $chat = $_Chat["DataModel"];
       $passPhrase = $post["PassPhrase"] ?? "";
       $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
       $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
       if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
        $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
         "Header" => base64_encode($this->core->Element([
          "h1", "Protected Content", ["class" => "CenterText"]
         ])),
         "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Chat["ListItem"]["Title"]."</em>."),
         "ViewData" => base64_encode(json_encode([
          "AddTo" => $addTo,
          "SecureKey" => base64_encode($passPhrase),
          "ID" => $chatID,
          "VerifyPassPhrase" => 1,
          "v" => base64_encode("Chat:Home")
         ], true))
        ]]);
        $r = $this->core->RenderView($r);
       } elseif($verifyPassPhrase == 1) {
        $accessCode = "Denied";
        $key = $data["Key"] ?? base64_encode("");
        $key = base64_decode($key);
        $r = $this->core->Element(["p", "The Key is missing."]);
        $secureKey = $data["SecureKey"] ?? base64_encode("");
        $secureKey = base64_decode($secureKey);
        if($key != $secureKey) {
         $r = $this->core->Element(["p", "The Keys do not match."]);
        } else {
         $accessCode = "Accepted";
         $r = $this->view(base64_encode("Chat:Home"), ["Data" => [
          "AddTo" => $addTo,
          "ID" => $chatID,
          "ViewProtectedContent" => 1
         ]]);
         $r = $this->core->RenderView($r);
        }
       } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
        $accessCode = "Accepted";
        $active = 0;
        $options = $_Chat["ListItem"]["Options"];
        $contributors = $options["Contributors"] ?? [];
        foreach($contributors as $member => $role) {
         if($member == $you) {
          $active++;
         }
        }
        $blockCommand = ($bl == 0) ? "Block" : "Unblock";
        $bookmarkCommand = ($active == 0) ? "Add " : "Remove ";
        $bookmarkCommand .= "Bookmark";
        $doNotShare = $this->core->RestrictedIDs;
        $delete = (!in_array($id, $doNotShare) && $chat["UN"] == $you) ? 1 : 0;
        $privacy = ($chat["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"]) && $chat["Privacy"] != md5("Private")) ? 1 : 0;
        $share = (!in_array($id, $doNotShare) && ($chat["UN"] == $you || $active == 1)) ? 1 : 0;
        $actions = (!empty($addToData)) ? $this->core->Element([
         "button", "Attach", [
          "class" => "Attach Small v2",
          "data-input" => base64_encode($addToData[1]),
          "data-media" => base64_encode("Chat;$id")
         ]
        ]) : "";
        $actions .= ($chat["UN"] != $you) ? $this->core->Element([
         "button", $blockCommand, [
          "class" => "Small UpdateButton v2",
          "data-processor" => $options["Block"]
         ]
        ]) : $this->core->Element([
         "button", "Edit", [
          "class" => "OpenCard v2",
          "data-view" => $options["Edit"]
         ]
        ]);
        $actions .= ($chat["UN"] != $you && $privacy == 1 && $share == 1) ? $this->core->Element([
         "button", $bookmarkCommand, [
          "class" => "UpdateButton v2",
          "data-processor" => $options["Bookmark"]
         ]
        ]) : "";
        $actions .= ($delete == 1 && $integrated == 1) ? $this->core->Element([
         "button", "Delete", [
          "class" => "CloseCard OpenDialog v2",
          "data-view" => $options["Delete"]
         ]
        ]) : "";
        $actions .= ($privacy == 1 && $share == 1) ? $this->core->Element([
         "button", "Share", [
          "class" => "OpenCard v2",
          "data-view" => $options["Share"]
         ]
        ]) : "";
        $r = $this->core->Change([[
         "[Chat.Attachments]" => base64_encode("v=".base64_encode("Chat:Attachments")."&ID=".base64_encode($id)),
         "[Chat.Body]" => $body,
         "[Chat.Created]" => $this->core->TimeAgo($chat["Created"]),
         "[Chat.Description]" => $_Chat["ListItem"]["Description"],
         "[Chat.ID]" => $id,
         "[Chat.Modified]" => $_Chat["ListItem"]["Modified"],
         "[Chat.Options]" => $actions,
         "[Chat.PaidMessages]" => base64_encode("v=".base64_encode("Chat:Home")."&ID=".base64_encode($id)."&PaidMessages=1"),
         "[Chat.Title]" => $_Chat["ListItem"]["Title"],
        ], $this->core->Extension("5252215b917d920d5d2204dd5e3c8168")]);
       }
      }
     } elseif($oneOnOne == 1) {
      $r = $this->view(base64_encode("Profile:Home"), ["Data" => [
       "Chat" => 1,
       "UN" => $chatID
      ]]);
      $r = $this->core->RenderView($r);
     }
    } elseif($paidMessage == 1) {
     $accessCode = "Accepted";
     $messageID = $data["MessageID"] ?? "";
     $r = $this->core->Element([
      "h1", "Something went wrong..."
     ]).$this->core->Element([
      "p", "The Paid Message Identifier is missing."
     ]);
     if(!empty($messageID)) {
      $attachments = "";
      $chat = $this->core->Data("Get", ["chat", $chatID]) ?? [];
      $messageID = base64_decode($messageID);
      $messages = $chat["Messages"] ?? [];
      $message = $messages[$messageID] ?? [];
      if(!empty($message["Attachments"])) {
       $attachments = $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
        "ID" => base64_encode(implode(";", $message["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
       $attachments = $this->core->RenderView($attachments);
      }
      $t = ($message["From"] == $you) ? $y : $this->core->Member($message["From"]);
      $paid = $message["Paid"] ?? 0;
      $profilePicture = $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:calc(100% - 1em)");
      $text = $message["Message"] ?? "";
      $text = "<strong>@".$message["From"]." Paid ".$message["PaidAmount"]."</strong><br/>$text";
      $text = (!empty($text)) ? $this->core->Element([
       "p", $text
      ]) : "";
      $text = $this->core->Element(["div", $profilePicture, [
       "class" => "Desktop10"
      ]]).$this->core->Element(["div", $text, [
       "class" => "Desktop90"
      ]]);
      $r = $this->core->Change([[
       "[Message.Attachments]" => $attachments,
       "[Message.Class]" => "MSGPaid",
       "[Message.MSG]" => $this->core->PlainText([
        "Data" => $text,
        "Display" => 1
       ]),
       "[Message.Sent]" => $this->core->TimeAgo($message["Timestamp"])
      ], $this->core->Extension("1f4b13bf6e6471a7f5f9743afffeecf9")]);
     }
     $r = [
      "Front" => $r
     ];
    } elseif($paidMessages == 1) {
     $accessCode = "Accepted";
     $chat = $this->core->Data("Get", ["chat", $chatID]) ?? [];
     $extension = $this->core->Extension("PaidMessage");
     $messages = $chat["Messages"] ?? [];
     $r = "";
     foreach($messages as $key => $value) {
      $amount = $value["PaidAmount"] ?? "";
      if($value["Paid"] == 1) {
       $r .= $this->core->Element([
        "div", $this->core->Element([
         "p", "<strong>@".$value["From"]."</strong> paid $amount"
        ]), [
         "class" => "OpenCard PaidMessage",
         "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&ID=$chatID&MessageID=".base64_encode($key)."&PaidMessage=1")
        ]
       ]);
      }
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
      $displayName = $chat["Title"] ?? "Untitled";
      $t = $this->core->Member($this->core->ID);
      $to = $displayName;
     } elseif($oneOnOne == 1) {
      $chat["UN"] = $you;
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
      $extension = "a4c140822e556243e3edab7cae46466d";
      $extension = ($group == 1) ? "5db540d33418852f764419a929277e13" : $extension;
      $r = $this->core->Change([[
       "[Chat.1on1]" => $oneOnOne,
       "[Chat.ActivityStatus]" => $active,
       "[Chat.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$atinput&ID="),
       "[Chat.Body]" => $body,
       "[Chat.Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&AddTo=$at&Added=$at2&CARD=1&UN=".base64_encode($you)."&st=XFS"),
       "[Chat.Files.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
       "[Chat.DisplayName]" => $displayName,
       "[Chat.Group]" => $group,
       "[Chat.ID]" => $id,
       "[Chat.Information]" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=$oneOnOne&Group=$group&ID=$chatID&Information=1&Integrated=$integrated"),
       "[Chat.List]" => base64_encode("v=".base64_encode("Chat:List")."&1on1=$oneOnOne&Group=$group&ID=$chatID"),
       "[Chat.PaidMessage]" => base64_encode("v=".base64_encode("Shop:Pay")."&Shop=".md5($chat["UN"])."&Type=PaidMessage&ViewPairID=".base64_encode("PaidMessage$id")),
       "[Chat.PaidMessages]" => base64_encode("v=".base64_encode("Chat:Home")."&ID=$id&PaidMessages=1"),
       "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:6em;width:calc(100% - 1em)"),
       "[Chat.SecureID]" => $id,
       "[Chat.Send]" => base64_encode("v=".base64_encode("Chat:Save")),
       "[Chat.To]" => $to,
       "[Chat.Type]" => $group
      ], $this->core->Extension($extension)]);
      $r = ($card == 1) ? [
       "Front" => $r
      ] : $r;
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
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
   $r = $this->core->Extension("2ce9b2d2a7f5394df6a71df2f0400873");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $chat = [];
    $extension = $this->core->Extension("1f4b13bf6e6471a7f5f9743afffeecf9");
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
       if(!empty($value["Attachments"])) {
        $attachments = $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
         "ID" => base64_encode(implode(";", $value["Attachments"])),
         "Type" => base64_encode("DLC")
        ]]);
        $attachments = $this->core->RenderView($attachments);
       }
       $message = $value["Message"] ?? "";;
       if($value["From"] != $you) {
        $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
        $profilePicture = $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:calc(100% - 1em)");
        $verified = $t["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        $message = $value["Message"] ?? "";
        $message = ($value["Paid"] == 1) ? "<strong>@".$value["From"].".$verified Paid ".$value["PaidAmount"]."</strong><br/>$message" : "<strong>@".$value["From"]."$verified</strong><br/>$message";
        $message = (!empty($message)) ? $this->core->Element([
         "p", $message
        ]) : "";
        $message = $this->core->Element(["div", $profilePicture, [
         "class" => "Desktop10"
        ]]).$this->core->Element(["div", $message, [
         "class" => "Desktop90"
        ]]);
       } else {
        $message = (!empty($message)) ? $this->core->Element([
         "p", $message
        ]) : "";
       }
       $paid = $value["Paid"] ?? 0;
       $class = ($value["From"] != $you) ? "MSGt" : "MSGy";
       $class = ($paid == 1) ? "MSGPaid" : $class;
       $chat[$key] = [
        "[Message.Attachments]" => $attachments,
        "[Message.Class]" => $class,
        "[Message.MSG]" => $this->core->PlainText([
         "Data" => $message,
         "Display" => 1
        ]),
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
    "AddTopMargin" => "0",
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
     "[Chat.1on1]" => base64_encode("v=$search&1on1=1&Integrated=$integrated&st=MBR-Chat"),
     "[Chat.Groups]" => base64_encode("v=$search&Group=1&Integrated=$integrated&st=MBR-GroupChat"),
     "[Chat.New]" => base64_encode("v=".base64_encode("Chat:Edit")."&GenerateID=1&Username=".base64_encode($you)),
     "[Chat.ID]" => md5($you)
    ], $this->core->Extension("2e1855b9baa7286162fb571c5f80da0f")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function PublicHome(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->core->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "We could not find the Group Chat you were looking for."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($callSign) || !empty($id)) {
    $accessCode = "Accepted";
    $chats = $this->core->DatabaseSet("Chat");
    foreach($chats as $key => $value) {
     $value = str_replace("nyc.outerhaven.chat.", "", $value);
     $chat = $this->core->Data("Get", ["chat", $value]) ?? [];
     $chatCallSign = $this->core->CallSign($chat["Title"]);
     if($callSign == $chatCallSign || $id == $value) {
      $r = $this->view(base64_encode("Chat:Home"), ["Data" => [
       "Group" => 1,
       "ID" => base64_encode($value)
      ]]);
      $r = $this->core->RenderView($r);
     }
    }
   } if($data["pub"] == 1 && $this->core->ID == $you) {
    $r = $this->view(base64_encode("WebUI:OptIn"), []);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKeu) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Chat WHERE Chat_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    $chats = $y["GroupChats"] ?? [];
    $newChats = [];
    foreach($chats as $key => $value) {
     if($id != $value) {
      array_push($newChats, $value);
     }
    }
    $y["GroupChats"] = $newChats;
    $chat = $this->core->Data("Get", ["chat", $id]);
    if(!empty($chat)) {
     $chat["Purge"] = 1;
     $this->core->Data("Save", ["chat", $id, $chat]);
    }
    $translations = $this->core->Data("Get", ["translate", $id]);
    if(!empty($translations)) {
     $translations["Purge"] = 1;
     $this->core->Data("Save", ["translate", $id, $translations]);
    }
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->core->Element([
     "p", "The Blog <em>".$chat["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $attachmentData = $data["Attachments"] ?? "";
   $group = $data["Group"] ?? 0;
   $id = $data["ID"] ?? "";
   $isEditingGroupChat = $data["GroupChatEditor"] ?? 0;
   $message = $data["Message"] ?? "";
   $check = (!empty($attachmentData) && empty($message)) ? 1 : 0;
   $check2 = (empty($attachmentData) && !empty($message)) ? 1 : 0;
   $check3 = (!empty($attachmentData) && !empty($message)) ? 1 : 0;
   $oneOnOne = $data["1on1"] ?? 0;
   $r = [
    "Body" => "A message or attachment are required."
   ];
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($isEditingGroupChat == 1) {
    $description = $data["Description"] ?? "";
    $title = $data["Title"] ?? "";
    $username = $data["Username"] ?? "";
    if(empty($description)) {
     $r = [
      "Body" => "The Description is missing."
     ];
    } elseif(empty($title)) {
     $r = [
      "Body" => "The Title is missing."
     ];
    } elseif(empty($username)) {
     $r = [
      "Body" => "The Author is missing."
     ];
    } else {
     $accessCode = "Accepted";
     $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
     $now = $this->core->timestamp;
     $contributors = $chat["Contributors"] ?? [];
     $created = $chat["Created"] ?? $now;
     $groupChats = $y["GroupChats"] ?? [];
     $messages = $chat["Messages"] ?? [];
     $modifiedBy = $chat["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $passPhrase = $data["PassPhrase"] ?? "";
     $privacy = $data["Privacy"] ?? $y["Privacy"]["MSG"];
     $purge = $chat["Purge"] ?? 0;
     $success = "CloseCard";
     $username = $chat["UN"] ?? $username;
     $chat = [
      "Contributors" => $contributors,
      "Created" => $created,
      "Description" => $description,
      "Group" => 1,
      "Messages" => $messages,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $nsfw,
      "PassPhrase" => $passPhrase,
      "Privacy" => $privacy,
      "Purge" => $purge,
      "Title" => $title,
      "UN" => $username
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO Chat(
      Chat_Created,
      Chat_Description,
      Chat_ID,
      Chat_NSFW,
      Chat_Privacy,
      Chat_Title,
      Chat_Username
     ) VALUES(
      :Created,
      :Description,
      :ID,
      :NSFW,
      :Privacy,
      :Title,
      :Username
     )";
     $sql->query($query, [
      ":Created" => $created,
      ":Description" => $chat["Description"],
      ":ID" => $id,
      ":NSFW" => $chat["NSFW"],
      ":Privacy" => $chat["Privacy"],
      ":Title" => $chat["Title"],
      ":Username" => $username
     ]);
     $sql->execute();
     if(!in_array($id, $groupChats) && $username == $you) {
      array_push($groupChats, $id);
      array_unique($groupChats);
      $y["GroupChats"] = $groupChats;
      $this->core->Data("Save", ["mbr", md5($you), $y]);
     }
     $this->core->Data("Save", ["chat", $id, $chat]);
     $r = [
      "Body" => "The Group Chat for <em>$title</em> has been saved.",
      "Header" => "Done"
     ];
    }
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
     $chat["UN"] = $chat["UN"] ?? $you;
     $chat["Description"] = $chat["Description"] ?? "";
     $chat["Title"] = $chat["Title"] ?? "Group Chat";
     $messages = $chat["Messages"] ?? [];
     $to = "";
    } elseif($oneOnOne == 1) {
     $chat = $this->core->Data("Get", ["chat", md5($you)]) ?? [];
     $messages = $chat["Messages"] ?? [];
     $t = $this->core->Data("Get", ["mbr", $id]) ?? [];
     $to = $t["Login"]["Username"];
     $autoResponse = $t["Personal"]["AutoResponse"] ?? "";
    }
    $autoResponse = ($oneOnOne == 1) ? $autoResponse : "";
    $paid = $data["Paid"] ?? 0;
    $paidAmount = $data["PaidAmount"] ?? "$0.00";
    $messages[$now] = [
     "Attachments" => $attachments,
     "From" => $you,
     "Message" => $message,
     "Paid" => $paid,
     "PaidAmount" => $paidAmount,
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
     if(!empty($autoResponse)) {
      $theirChat = $this->core->Data("Get", ["chat", md5($to)]) ?? [];
      $messages[$now] = [
       "Attachments" => [],
       "From" => $to,
       "Message" => $autoResponse,
       "Paid" => 0,
       "PaidAmount" => $paidAmount,
       "Read" => 0,
       "Timestamp" => $now,
       "To" => $you
      ];
      $this->core->Data("Save", ["chat", md5($to), $theirChat]);
     }
    }
    $r = [
     "Body" => "Your message has been sent.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => $success
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>