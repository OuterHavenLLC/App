<?php
 Class Chat extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Attachments(array $data): string {
   $_Dialog = [
    "Body" => "The Chat Identifier or Username are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]);
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
     $_View = $this->core->Element(["div", $grid, ["class" => "Grid3"]]);
    } else {
     $_View = $this->core->Element([
      "h3", "Attachments",
      ["class" => "CenterText UpperCase"]
     ]).$this->core->Element([
      "p", "No attachments, yet...",
      ["class" => "CenterText"]
     ]);
    }
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Bookmark(array $data): string {
   $_Dialog = [
    "Body" => "The Chat Identifier or Join Command missing."
   ];
   $data = $data["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($command) && !empty($id)) {
    $_Dialog = [
     "Body" => "You cannot remove the Bookmark for your own Group Chat."
    ];
    $command = base64_decode($command);
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]);
    if($chat["UN"] != $you) {
     $_Dialog = "";
     $contributors = $chat["Contributors"] ?? [];
     $groupChats = $y["GroupChats"] ?? [];
     $processor = "v=".base64_encode("Chat:Bookmark")."&ID=".base64_encode($id);
     if($command == "Add Bookmark") {
      if(!in_array($id, $groupChats)) {
       array_push($groupChats, $id);
       array_unique($groupChats);
       $y["GroupChats"] = $groupChats;
      }
      $contributors[$you] = "Member";
      $chat["Contributors"] = $contributors;
      $_View = [
       "Attributes" => [
        "class" => "UpdateButton v2",
        "data-processor" => base64_encode("$processor&Command=".base64_encode("Remove Bookmark"))
       ],
       "Text" => "Remove Bookmark"
      ];
     } elseif($command == "Remove Bookmark") {
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
      $_View = [
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
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Chat Identifier or Username are missing."
   ];
   $data = $data["Data"] ?? [];
   $generateID = $data["GenerateID"] ?? 0;
   $id = $data["ID"] ?? base64_encode("");
   $username = $data["Username"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if((!empty($id) || $generateID == 1) && !empty($username)) {
    $_Dialog = "";
    $description = $data["Description"] ?? base64_encode("");
    $description = base64_decode($description);
    $editorID = md5("ChatEditor-$id".$this->core->timestamp);
    $id = base64_decode($id);
    $id = ($generateID == 1) ? md5("$you-Chat-".uniqid()) : $id;
    $title = $data["Title"] ?? base64_encode("");
    $title = base64_decode($title);
    $username = base64_decode($username);
    $chat = $this->core->Data("Get", ["chat", $id]);
    $description = $chat["Description"] ?? $description;
    $new = (empty($chat)) ? 1 : 0;
    $nsfw = $chat["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $chat["PassPhrase"] ?? "";
    $privacy = $chat["Privacy"] ?? $y["Privacy"]["MSG"];
    $title = $chat["Title"] ?? $title;
    $action = ($new == 1) ? "Create" : "Update";
    $header = ($new == 1) ? "New Group Chat" : "Edit $title";
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".Chat$editorID",
      "data-processor" => base64_encode("v=".base64_encode("Chat:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Chat.Header]" => $header,
       "[Chat.ID]" => $id
      ],
      "ExtensionID" => "eb169be369e5497344f98d826aea4e7d"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ChatInformation$id",
       [
        [
         "Attributes" => [
          "name" => "GroupChatEditor",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => 1
        ],
        [
         "Attributes" => [
          "name" => "ID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $id
        ],
        [
         "Attributes" => [
          "name" => "New",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $new
        ],
        [
         "Attributes" => [
          "name" => "Username",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $username
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Title",
          "placeholder" => "Title",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Title"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($title)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Description",
          "placeholder" => "Description"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($description)
        ],
        [
         "Attributes" => [
          "name" => "PassPhrase",
          "placeholder" => "Pass Phrase",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Pass Phrase"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".NSFW$id",
       [
        "Filter" => "NSFW",
        "Name" => "NSFW",
        "Title" => "Content Status",
        "Value" => $nsfw
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".Privacy$id",
       [
        "Value" => $privacy
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Chat Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
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
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($chatID)) {
    $_Dialog = [
     "Body" => "The Chat Type is missing."
    ];
    $id = base64_decode($chatID);
    if($information == 1) {
     $_Dialog = [
      "Body" => "The Chat Type is missing."
     ];
     if($group == 1) {
      $_Dialog = [
       "Body" => "We could lnot locate the requested Chat.",
       "Header" => "Not Found"
      ];
      $_Chat = $this->core->GetContentData([
       "Blacklisted" => 0,
       "ID" => base64_encode("Chat;$id"),
       "Integrated" => $integrated
      ]);
      $blocked = $this->core->CheckBlocked([$y, "Group Chats", $id]);
      if($_Chat["Empty"] == 0) {
       $_Dialog = "";
       $chat = $_Chat["DataModel"];
       $passPhrase = $post["PassPhrase"] ?? "";
       $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
       $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
       if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
        $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
        $_View = $this->core->RenderView($_View);
       } elseif($verifyPassPhrase == 1) {
        $_Dialog = "";
        $key = $data["Key"] ?? base64_encode("");
        $key = base64_decode($key);
        $secureKey = $data["SecureKey"] ?? base64_encode("");
        $secureKey = base64_decode($secureKey);
        if($key == $secureKey) {
         $_View = $this->view(base64_encode("Chat:Home"), ["Data" => [
          "AddTo" => $addTo,
          "ID" => $chatID,
          "ViewProtectedContent" => 1
         ]]);
         $_View = $this->core->RenderView($_View, 1);
         $_Commands = $_View["Commands"];
         $_View = $_View["View"];
        }
       } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
        $_Dialog = "";
        $active = 0;
        $options = $_Chat["ListItem"]["Options"];
        $contributors = $options["Contributors"] ?? [];
        foreach($contributors as $member => $role) {
         if($member == $you) {
          $active++;
         }
        }
        $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
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
        $actions .= ($chat["UN"] == $you) ? $this->core->Element([
         "button", "Edit", [
          "class" => "OpenCard v2",
          "data-encryption" => "AES",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        $actions .= ($chat["UN"] != $you && $privacy == 1 && $share == 1) ? $this->core->Element([
         "button", $bookmarkCommand, [
          "class" => "UpdateButton v2",
          "data-encryption" => "AES",
          "data-processor" => $options["Bookmark"]
         ]
        ]) : "";
        $actions .= ($delete == 1 && $integrated == 1) ? $this->core->Element([
         "button", "Delete", [
          "class" => "Red CloseCard OpenDialog v2",
          "data-encryption" => "AES",
          "data-view" => $options["Delete"]
         ]
        ]) : "";
        $actions .= ($privacy == 1 && $share == 1) ? $this->core->Element([
         "button", "Share", [
          "class" => "OpenCard v2",
          "data-encryption" => "AES",
          "data-view" => $options["Share"]
         ]
        ]) : "";
        $purgeRenderCode = ($chat["UN"] == $you) ? "PURGE" : "DO NOT PURGE";
        $_Commands = [
         [
          "Name" => "UpdateContentRecursiveAES",
          "Parameters" => [
           ".Attachments$id",
           $this->core->AESencrypt("v=".base64_encode("Chat:Attachments")."&ID=".base64_encode($id)),
           15000
         ]
        ];
        $_View = [
         "ChangeData" => [
         "[Chat.Actions]" => $actions,
         "[Chat.Block]" => $options["Block"],
         "[Chat.Block.Text]" => $blockCommand,
         "[Chat.Body]" => $body,
         "[Chat.Created]" => $this->core->TimeAgo($chat["Created"]),
         "[Chat.Description]" => $_Chat["ListItem"]["Description"],
         "[Chat.ID]" => $id,
         "[Chat.Modified]" => $_Chat["ListItem"]["Modified"],
         "[Chat.PaidMessages]" => $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&ID=".base64_encode($id)."&PaidMessages=1"),
         "[Chat.Title]" => $_Chat["ListItem"]["Title"],
         "[PurgeRenderCode]" => $purgeRenderCode
         ],
         "ExtensionID" => "5252215b917d920d5d2204dd5e3c8168"
        ];
       }
      }
     } elseif($oneOnOne == 1) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Profile:Home"), ["Data" => [
       "Chat" => 1,
       "UN" => $chatID
      ]]);
      $_View = $this->core->RenderView($_View);
     }
    } elseif($paidMessage == 1) {;
     $_Dialog = [
      "Body" => "The Paid Message Identifier is missing."
     ];
     $messageID = $data["MessageID"] ?? "";
     if(!empty($messageID)) {
      $_Dialog = "";
      $chat = $this->core->Data("Get", ["chat", $chatID]);
      $messageID = base64_decode($messageID);
      $messages = $chat["Messages"] ?? [];
      $message = $messages[$messageID] ?? [];
      $attachments = $message["Attachments"] ?? "";
      if(!empty($attachments)) {
       array_push($_Commands, [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Attachments$chatID$messageID",
         $this->core->AESencrypt("v=".base64_encode("Chat:Attachments")."&ID=".base64_encode(implode(";", $attachments)))
        ]
       ]);
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
      $_Card = [
       "Front" => [
        "ChangeData" => [
         "[Message.ChatID]" => $chatID,
         "[Message.Class]" => "MSGPaid",
         "[Message.Message]" => $this->core->PlainText([
          "Data" => $text,
          "Display" => 1
         ]),
         "[Message.Message.ID]" => $messageID,
         "[Message.Sent]" => $this->core->TimeAgo($message["Timestamp"])
        ],
        "ExtensionID" => "1f4b13bf6e6471a7f5f9743afffeecf9"
       ]
      ];
     }
    } elseif($paidMessages == 1) {
     $_Dialog = "";
     $_View = "";
     $chat = $this->core->Data("Get", ["chat", $chatID]);
     $extension = $this->core->Extension("PaidMessage");
     $messages = $chat["Messages"] ?? [];
     foreach($messages as $key => $value) {
      $amount = $value["PaidAmount"] ?? "";
      if($value["Paid"] == 1) {
       $_View .= $this->core->Element([
        "div", $this->core->Element([
         "p", "<strong>@".$value["From"]."</strong> paid $amount"
        ]), [
         "class" => "OpenCard PaidMessage",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&ID=$chatID&MessageID=".base64_encode($key)."&PaidMessage=1")
        ]
       ]);
      }
     }
     $_View = [
      "ChangeData" => [],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    } else {
     $_Dialog = [
      "Body" => "The Group Chat has not been created."
     ];
     $check = 1;
     if($group == 1) {
      $chat = $this->core->Data("Get", ["chat", $id]);
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
      $_Dialog = "";
      $_Commands = [
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".ChatMessage$id",
         [
          [
           "Attributes" => [
            "name" => "1on1",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $oneOnOne
          ],
          [
           "Attributes" => [
            "name" => "Group",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $group
          ],
          [
           "Attributes" => [
            "name" => "ID",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $id
          ],
          [
           "Attributes" => [
            "class" => "EmptyOnSuccess",
            "name" => "Message",
            "placeholder" => "Say something...",
            "type" => "text"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => ""
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".PaidMessage$id",
         [
          [
           "Attributes" => [],
           "OptionGroup" => [
            "5.00" => "$5.00",
            "10.00" => "$10.00",
            "15.00" => "$15.00",
            "20.00" => "20.00",
            "25.00" => "$25.00",
            "30.00" => "$30.00",
            "50.00" => "$50.00",
            "100.00" => "$100.00",
            "500.00" => "$500.00",
            "1000.00" => "$1,000.00"
           ],
           "Options" => [
            "Header" => 1,
            "HeaderText" => "Choose an Amount"
           ],
           "Name" => "Amount",
           "Title" => "Amount",
           "Type" => "Select",
           "Value" => "5.00"
          ],
          [
           "Attributes" => [
            "name" => "Form",
            "type" => "hidden"
           ],
           "Type" => "Text",
           "Value" => "ChatMessage$id"
          ],
          [
           "Attributes" => [
            "name" => "ViewPairID",
            "type" => "hidden"
           ],
           "Type" => "Text",
           "Value" => "PaidMessage$id"
          ]
         ]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Information$id",
         $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&1on1=$oneOnOne&Group=$group&ID=$chatID&Information=1&Integrated=$integrated")
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".ChatBody$id",
         $this->core->AESencrypt("v=".base64_encode("Chat:List")."&1on1=$oneOnOne&Group=$group&ID=$chatID"),
         3000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".ChatPaidMessages$id",
         $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&ID=$id&PaidMessages=1"),
         3000
        ]
       ],
      ];
      $_View = [
       "ChangeData" => [
       "[Chat.ActivityStatus]" => $active,
       "[Chat.Body]" => $body,
       "[Chat.DisplayName]" => $displayName,
       "[Chat.ID]" => $id,
       "[Chat.PaidMessage]" => base64_encode("v=".base64_encode("Shop:Pay")."&Shop=".md5($chat["UN"])."&Type=PaidMessage&ViewPairID=".base64_encode("PaidMessage$id")),
       "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:6em;width:calc(100% - 1em)"),
       "[Chat.Send]" => base64_encode("v=".base64_encode("Chat:Save")),
       "[Chat.To]" => $to,
       "[Chat.Type]" => $group
       ],
       "ExtensionID" => "5db540d33418852f764419a929277e13"
      ];
      $_Card = ($card == 1) ? [
       "Front" => $_View
      ] : "";
      $_View = ($card == 0) ? $_View : "";
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function List(array $data): string {
   $_View = [
    "ChangeData" => [],
    "ExtensionID" => "2ce9b2d2a7f5394df6a71df2f0400873"
   ];
   $data = $data["Data"] ?? [];
   $group = $data["Group"] ?? 0;
   $id = $data["ID"] ?? "";
   $oneOnOne = $data["1on1"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $chat = [];
    $id = base64_decode($id);
    if($group == 1) {
     $chat = $this->core->Data("Get", ["chat", $id]);
     $chat = $chat["Messages"] ?? [];
     $to = "";
    } elseif($oneOnOne == 1) {
     $t = $this->core->Member($id);
     $to = $t["Login"]["Username"];
     $theirChat = $this->core->Data("Get", ["chat", md5($to)]);
     $theirChat = $theirChat["Messages"] ?? [];
     $yourChat = $this->core->Data("Get", ["chat", md5($you)]);
     $yourChat = $yourChat["Messages"] ?? [];
     $chat = array_merge($theirChat, $yourChat);
    } if($group == 1 || $oneOnOne == 1) {
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
    ksort($chat);
    $_Extension = $this->core->Extension("1f4b13bf6e6471a7f5f9743afffeecf9");
    $_View = "";
    foreach($chat as $key => $info) {
     $_View .= $this->core->Change([
      $info,
      $_Extension
     ]);
    }
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function Menu(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "You must sign in to continue."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $integrated = $data["Integrated"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $id = md5($you);
    $search = base64_encode("Search:Containers");
    $_Dialog = "";
    $_Commands = [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".GroupChat$id",
       $this->core->AESencrypt("v=$search&Group=1&Integrated=$integrated&st=MBR-GroupChat")
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".OneOnOneChat$id",
       $this->core->AESencrypt("v=$search&1on1=1&Integrated=$integrated&st=MBR-Chat")
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[Chat.New]" => $this->core->AESencrypt("v=".base64_encode("Chat:Edit")."&GenerateID=1&Username=".base64_encode($you)),
      "[Chat.ID]" => $id
     ],
     "ExtensionID" => "2e1855b9baa7286162fb571c5f80da0f"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "1",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Public(array $data): string {
   $data = $data["Data"] ?? [];
   $_Dialog = [
    "Body" => "We could not find the Group Chat you were looking for."
   ];
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->core->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($callSign) || !empty($id)) {
    $_Dialog = "";
    $chats = $this->core->DatabaseSet("Chat");
    foreach($chats as $key => $value) {
     $value = str_replace("nyc.outerhaven.chat.", "", $value);
     $chat = $this->core->Data("Get", ["chat", $value]);
     $chatCallSign = $this->core->CallSign($chat["Title"]);
     if($callSign == $chatCallSign || $id == $value) {
      $_View = $this->view(base64_encode("Chat:Home"), ["Data" => [
       "Group" => 1,
       "ID" => base64_encode($value)
      ]]);
      $_View = $this->core->RenderView($_View);
     }
    }
   } if($this->core->ID  == $you) {
    $_View = $this->view(base64_encode("WebUI:Gateway"), []);
    $_View = $this->core->RenderView($_View);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKeu) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
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
    $_View = $this->core->Element([
     "p", "The Blog <em>".$chat["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "Success" => "CloseDialog",
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "A message or attachment are required."
   ];
   $data = $data["Data"] ?? [];
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
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($isEditingGroupChat == 1) {
    $description = $data["Description"] ?? "";
    $title = $data["Title"] ?? "";
    $username = $data["Username"] ?? "";
    if(empty($description)) {
     $_Dialog = [
      "Body" => "The Description is missing."
     ];
    } elseif(empty($title)) {
     $_Dialog = [
      "Body" => "The Title is missing."
     ];
    } elseif(empty($username)) {
     $_Dialog = [
      "Body" => "The Author is missing."
     ];
    } else {
     $_AccessCode = "Accepted";
     $chat = $this->core->Data("Get", ["chat", $id]);
     $now = $this->core->timestamp;
     $contributors = $chat["Contributors"] ?? [];
     $coverPhoto = $data["CoverPhoto"] ?? "";
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
      "CoverPhoto" => $coverPhoto,
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
     $_Dialog = [
      "Body" => "The Group Chat for <em>$title</em> has been saved.",
      "Header" => "Done"
     ];
    }
   } elseif(!empty($id) && ($check == 1 || $check2 == 1 || $check3 == 1)) {
    $_AccessCode = "Accepted";
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
     $chat = $this->core->Data("Get", ["chat", $id]);
     $chat["UN"] = $chat["UN"] ?? $you;
     $chat["Description"] = $chat["Description"] ?? "";
     $chat["Title"] = $chat["Title"] ?? "Group Chat";
     $messages = $chat["Messages"] ?? [];
     $to = "";
    } elseif($oneOnOne == 1) {
     $chat = $this->core->Data("Get", ["chat", md5($you)]);
     $messages = $chat["Messages"] ?? [];
     $t = $this->core->Data("Get", ["mbr", $id]);
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
      $theirChat = $this->core->Data("Get", ["chat", md5($to)]);
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
    $_Dialog = [
     "Body" => "Your message has been sent.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => $success
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>