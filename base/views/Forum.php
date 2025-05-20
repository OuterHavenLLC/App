<?php
 Class Forum extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($member)) {
    $_Dialog = [
     "Body" => "You cannot banish yourself."
    ];
    $id = base64_decode($id);
    $forum = $this->core->Data("Get", ["pf", $id]);
    $member = base64_decode($member);
    if($member != $forum["UN"] && $member != $y["Login"]["Username"]) {
     $_Dialog = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $member", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Forum:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $member from <em>".$forum["Title"]."</em>?",
      "Header" => "Banish $member?"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_DIalog
   ]);
  }
  function ChangeMemberRole(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $pin = $data["PIN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $_AccessCode = "Accepted";
    $forum = $this->core->Data("Get", ["pf", $id]);
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $manifest[$member] = $role;
    $this->core->Data("Save", ["pfmanifest", $id, $manifest]);
    $_Dialog = [
     "Body" => "$member's Role within <em>".$forum["Title"]."</em> was Changed to $role.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_DIalog,
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? $this->core->UUID("ForumBy$you") : $id;
    $forum = $this->core->Data("Get", ["pf", $id]);
    $about = $forum["About"] ?? "";
    $author = $forum["UN"] ?? $you;
    $ca = base64_encode("Chat:Attachments");
    $coverPhoto = $forum["CoverPhoto"] ?? "";
    $created = $forum["Created"] ?? $now;
    $description = $forum["Description"] ?? "";
    $es = base64_encode("LiveView:EditorSingle");
    $header = ($new == 1) ? "New Forum" : "Edit ".$forum["Title"];
    $nsfw = $forum["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $forum["PassPhrase"] ?? "";
    $privacy = $forum["Privacy"] ?? $y["Privacy"]["Forums"];
    $sc = base64_encode("Search:Containers");
    $title = $forum["Title"] ?? "My Forum";
    $type = $forum["Type"] ?? $y["Privacy"]["ForumsType"];
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-encryption" => "AES",
      "data-form" => ".EditForum$id",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Forum:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Forum.Attachments]" => "",
       "[Forum.Chat]" => $this->core->AESencrypt("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
       "[Forum.Header]" => $header,
       "[Forum.ID]" => $id
      ],
      "ExtensionID" => "8304362aea73bddb2c12eb3f7eb226dc"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ForumInformation$id",
       [
        [
         "Attributes" => [
          "name" => "Created",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $created
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
          "name" => "About",
          "placeholder" => "Tell us about your Forum..."
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "About"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($about)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Description",
          "placeholder" => "Describe yout Forum..."
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
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Pass Phrase"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "47f9082fc380ca62d531096aa1d110f1" => "Private",
          "3d067bedfe2f4677470dd6ccf64d05ed" => "Public"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Forum Type"
         ],
         "Name" => "Type",
         "Title" => "Forum Type",
         "Type" => "Select",
         "Value" => $type
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
  function EditTopics(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The requested Forum could not be found."
    ];
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$id")
    ]);
    if($_Forum["Empty"] == 0) {
     $_Dialog = "";
     $forum = $_Forum["DataModel"];
     $topicList = "";
     $topics = $forum["Topics"] ?? [];
     foreach($topics as $topicID => $info) {
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "Forum" => base64_encode($id),
       "Topic" => base64_encode($topicID),
       "v" => base64_encode("Forum:PurgeTopic")
      ], true);
      $topicList .= $this->core->Change([[
       "[Clone.ID]" => $topicID,
       "[Topic.Default]" => $info["Default"],
       "[Topic.Delete]" => $this->core->Element(["button", "Delete Topic", [
        "class" => "OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData))
       ]]),
       "[Topic.Description]" => $info["Description"],
       "[Topic.ID]" => $topicID,
       "[Topic.NSFW]" => $info["NSFW"],
       "[Topic.Title]" => $info["Title"]
      ], $this->core->Extension("5f5acd280261747ae18830eb70ce719c")]);
     }
     $_Commands = [
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".ParentForumInformation$id",
        [
         [
          "Attributes" => [
           "name" => "ID",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $id
         ]
        ]
       ]
      ]
     ];
     $_View = [
      "ChangeData" => [
       "[Forum.ID]" => $id,
       "[Topics.Clone]" => base64_encode($this->core->Change([[
        "[Topic.Default]" => 0,
        "[Topic.Delete]" => $this->core->Element(["button", "Delete Topic", [
         "class" => "Delete v2 v2w",
         "data-target" => ".DeleteTopic[Clone.ID]"
        ]]),
        "[Topic.Description]" => "",
        "[Topic.ID]" => "",
        "[Topic.NSFW]" => 0,
        "[Topic.Title]" => ""
       ], $this->core->Extension("5f5acd280261747ae18830eb70ce719c")])),
       "[Topics.List]" => $topicList,
       "[Topics.Save]" => $this->core->AESencrypt("v=".base64_encode("Forum:SaveTopics"))
      ],
      "ExtensionID" => "4a40ab9e976c8d00bb4ebc8c953cd3ca"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The requested Forum could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $card = $data["CARD"] ?? 0;
   $id = $data["ID"] ?? "";
   $lpg = $data["lPG"] ?? "";
   $b2 = $data["b2"] ?? "Forums";
   $b2 = $this->core->Element(["em", $b2]);
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI header",
    "data-type" => $lpg
   ]]) : "";
   $public = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$id")
    ]);
    if($_Forum["Empty"] == 0) {
     $forum = $_Forum["DataModel"];
     $passPhrase = $forum["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Forum["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $data["ID"],
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Forum:Home")
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
       $_View = $this->view(base64_encode("Forum:Home"), ["Data" => [
        "AddTo" => $addTo,
        "ID" => $data["ID"],
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $active = 0;
      $admin = 0;
      $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
      $notAnon = ($this->core->ID != $you) ? 1 : 0;
      $options = $_Forum["ListItem"]["Options"];
      $title = $_Forum["ListItem"]["Title"];
      foreach($manifest as $member => $role) {
       if($active == 0 && $member == $you) {
        $active = 1;
        if($admin == 0 && $role == "Admin") {
         $admin = 1;
        }
       }
      }
      $check = ($admin == 1 || $forum["UN"] == $you) ? 1 : 0;
      $doNotShare = $this->core->RestrictedIDs;
      $_Dialog = [
       "Body" => "<em>$title</em> is invite-only.",
       "Header" => "Private Forum"
      ];
      if($active == 1 || $check == 1 || $forum["Type"] == "Public") {
       $_Dialog = "";
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $actions = (!in_array($forum["ID"], $doNotShare)) ? $this->core->Element([
        "button", "Attach", [
         "class" => "Attach Small v2",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode($forum["ID"])
        ]
       ]) : "";
       $actions .= (!empty($chat) && ($active == 1 || $check == 1)) ? $this->core->Element([
        "button", "Chat", [
         "class" => "OpenCard Small v2 v2w",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($id)."&Integrated=1")
        ]
       ]) : "";
       $actions .= (!in_array($forum["ID"], $doNotShare) && $forum["UN"] == $you && $public == 0) ? $this->core->Element([
        "button", "Delete", [
         "class" => "CloseCard OpenDialog Small v2",
         "data-encryption" => "AES",
         "data-view" => $options["Delete"]
        ]
       ]) : "";
       $actions .= ($admin == 1) ? $this->core->Element(["button", "Edit", [
        "class" => "OpenCard Small v2 v2w",
         "data-encryption" => "AES",
        "data-view" => $options["Edit"]
       ]]) : "";
       $actions .= ($active == 1 || $check == 1 || $forum["Type"] == "Public") ? $this->core->Element([
        "button", "Post", [
         "class" => "OpenCard Small v2 v2w",
         "data-encryption" => "AES",
         "data-view" => $options["Post"]
        ]
       ]) : "";
       $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
        "button", "Share", [
         "class" => "OpenCard Small v2 v2w",
         "data-encryption" => "AES",
         "data-view" => $options["Share"]
        ]
       ]) : "";
       $createTopicAction = (empty($forum["Topics"])) ? "Create a Topic" : "Manage Topics";
       $createTopic = ($forum["UN"] == $you) ? $this->core->Element([
        "button", $createTopicAction, [
         "class" => "BigButton GoToView",
         "data-encryption" => "AES",
         "data-type" => "ForumTopics$id;".$this->core->AESencrypt("v=".base64_encode("Forum:EditTopics")."&ID=".$data["ID"])
        ]
       ]) : "";
       $invite = (!in_array($forum["ID"], $doNotShare) && $active == 1) ? $this->core->Element([
        "button", "Invite", [
         "class" => "OpenCard v2",
         "data-encryption" => "AES",
         "data-view" => $options["Invite"]
        ]
       ]) : "";
       $joinCommand = ($active == 0) ? "Join" : "Leave";
       $join = ($check == 0 && $forum["Type"] == "Public") ? $this->core->Element([
        "button", $joinCommand, [
         "class" => "BBB UpdateButton v2 v2w",
         "data-processor" => base64_encode("v=".base64_encode("Forum:Join")."&Command=$joinCommand&ID=$id")
        ]
       ]) : "";
       $search = base64_encode("Search:Containers");
       $blocked = $this->core->CheckBlocked([$y, "Forums", $id]);
       $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
       $purgeRenderCode = ($check == 0) ? "PURGE" : "DO NOT PURGE";
       $_Commands = [
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Administrators$id",
          $this->core->AESencrypt("v=$search&Admin=".base64_encode($forum["UN"])."&ID=".base64_encode($id)."&st=Forums-Admin")
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Contributors$id",
          $this->core->AESencrypt("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Forum")."&st=Contributors")
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".FeaturedContributors$id",
          $this->core->AESencrypt("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($manifest, true)))
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Posts$id",
          $this->core->AESencrypt("v=$search&ID=".base64_encode($id)."&st=Forums-Posts")
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".TopicsList$id",
          $this->core->AESencrypt("v=".base64_encode("Forum:Topics")."&ID=".base64_encode($id))
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Vote$id",
          $options["Vote"]
         ]
        ]
       ];
       $_View = [
        "ChangeData" => [
         "[Forum.About]" => $forum["About"],
         "[Forum.Actions]" => $actions,
         "[Forum.Back]" => $back,
         "[Forum.Block]" => $options["Block"],
         "[Forum.Block.Text]" => $blockCommand,
         "[Forum.CoverPhoto]" => $_Forum["ListItem"]["CoverPhoto"],
         "[Forum.CreateTopic]" => $createTopic,
         "[Forum.EditTopics]" => $this->core->AESencrypt("v=".base64_encode("Forum:EditTopics")."&ID=".base64_encode($id)),
         "[Forum.Description]" => $_Forum["ListItem"]["Description"],
         "[Forum.ID]" => $id,
         "[Forum.Invite]" => $invite,
         "[Forum.Join]" => $join,
         "[Forum.Title]" => $title,
         "[PurgeRenderCode]" => $purgeRenderCode
        ],
        "ExtensionID" => "4159d14e4e8a7d8936efca6445d11449"
       ];
      }
     }
    }
   } if(empty($_Dialog)) {
    $_Card = ($card == 1) ? [
     "Front" => $_View
    ] : "";
    $_View = ($card == 0) ? $_View : "";
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Invite(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $content = [];
    $contentOptions = $y["Forums"] ?? [];
    foreach($contentOptions as $key => $value) {
     $forum = $this->core->Data("Get", ["pf", $value]);
     $content[$value] = $forum["Title"];
    }
    $_Card = [
     "Action" => $this->core->Element(["button", "Send Invite", [
      "class" => "CardButton CloseCard SendData",
      "data-form" => ".Invite$id",
      "data-processor" => base64_encode("v=".base64_encode("Forum:SendInvite"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Invite.Content]" => json_encode($content, true),
       "[Invite.ID]" => $id,
       "[Invite.Member]" => $member
      ],
      "ExtensionID" => "80e444c34034f9345eee7399b4467646"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Join(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier or Join Command are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($command) && !empty($id)) {
    $_Dialog = [
     "Body" => "You cannot leave your own Forum."
    ];
    if($forum["UN"] != $you) {
     $_Dialog = "";
     $forum = $this->core->Data("Get", ["pf", $id]);
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
     $processor = "v=".base64_encode("Forum:Join")."&ID=$id";
     if($command == "Join") {
      $manifest[$you] = "Member";
      $_View = [
       "Attributes" => [
        "class" => "BBB UpdateButton v2 v2w",
        "data-processor" => base64_encode("$processor&Command=Leave")
       ],
       "Text" => "Leave"
      ];
     } elseif($command == "Leave") {
      $newManifest = [];
      foreach($manifest as $member => $role) {
       if($member != $you) {
        $newManifest[$member] = $role;
       }
      }
      $manifest = $newManifest;
      $_View = [
       "Attributes" => [
        "class" => "BBB UpdateButton v2 v2w",
        "data-processor" => base64_encode("$processor&Command=Join")
       ],
       "Text" => "Join"
      ];
     }
     $this->core->Data("Save", ["pfmanifest", $id, $manifest]);
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Public(array $data): string {
   $_Dialog = [
    "Body" => "We could not find the Forum you were looking for."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->core->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($callSign) || !empty($id)) {
    $forums = $this->core->DatabaseSet("Forum");
    foreach($forums as $key => $value) {
     $forum = str_replace("nyc.outerhaven.pf.", "", $value);
     $forum = $this->core->Data("Get", ["pf", $forum]);
     $forumCallSign = $this->core->CallSign($forum["Title"]);
     if($callSign == $forumCallSign || $id == $forum["ID"]) {
      $_View = $this->view(base64_encode("Forum:Home"), ["Data" => [
       "ID" => $forum["ID"]
      ]]);
      $_View = $this->core->RenderView($r_View);
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
    "Body" => "The Forum Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Forum was not found."
    ];
    $id = base64_decode($id);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$id")
    ]);
    if($_Forum["Empty"] == 0) {
     $_AccessCode = "Accepted";
     $_Dialog = "";
     $forum = $_Forum["DataModel"];
     $forumPosts = $forum["Posts"] ?? [];
     $forums = $y["Forums"] ?? [];
     $newForums = [];
     $passPhrase = base64_encode($key);
     $securePassPhrase = base64_encode($secureKey);
     foreach($forumPosts as $key => $value) {
      $forumPost = $this->core->Data("Get", ["post", $value]);
      if(!empty($forumPost)) {
       $sql = New SQL($this->core->cypher->SQLCredentials());
       $sql->query("DELETE FROM ForumPosts WHERE ForumPost_ID=:ID", [
        ":ID" => $value
       ]);
       $sql->execute();
       $this->view(base64_encode("ForumPost:Purge"), ["Data" => [
        "Key" => $passPhrase,
        "ID" => base64_encode($value),
        "SecureKey" => $securePassPhrase
       ]]);
      }
     } foreach($forums as $key => $value) {
      if($id != $value) {
       $newForums[$key] = $value;
      }
     }
     $chat = $this->core->Data("Get", ["chat", $id]);
     if(!empty($chat)) {
      $chat["Purge"] = 1;
      $this->core->Data("Save", ["chat", $id, $chat]);
     }
     $conversation = $this->core->Data("Get", ["conversation", $id]);
     if(!empty($conversation)) {
      $conversation["Purge"] = 1;
      $this->core->Data("Save", ["conversation", $id, $conversation]);
     }
     $forum = $this->core->Data("Get", ["pf", $id]);
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $sql->query("DELETE FROM Forum WHERE Forum_ID=:ID", [
      ":ID" => $id
     ]);
     $sql->execute();
     if(!empty($forum)) {
      $forum["Purge"] = 1;
      $this->core->Data("Save", ["pf", $id, $forum]);
     }
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
     if(!empty($manifest)) {
      $manifest["Purge"] = 1;
      $this->core->Data("Save", ["pfmanifest", $id, $manifest]);
     }
     $translations = $this->core->Data("Get", ["translate", $id]);
     if(!empty($translations)) {
      $translations["Purge"] = 1;
      $this->core->Data("Save", ["translate", $id, $translations]);
     }
     $votes = $this->core->Data("Get", ["votes", $id]);
     if(!empty($votes)) {
      $votes["Purge"] = 1;
      $this->core->Data("Save", ["votes", $id, $votes]);
     }
     $y["Forums"] = $newForums;
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $_View = [
      "ChangeData" => [
       "[Forum.Title]" => $forum["Title"]
      ],
      "Extension" => $this->core->AESencrypt($this->core->Element([
       "p", "The Forum <em>[Forum.Title]</em> and dependencies were marked for purging.",
       ["class" => "CenterText"]
      ]).$this->core->Element([
       "button", "Okay", ["class" => "CloseDialog v2 v2w"]
      ]))
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function PurgeTopic(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $forumID = $data["Forum"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $topicID = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($forumID) && !empty($topicID)) {
    $_Dialog = [
     "Body" => "The Forum was not found."
    ];
    $forumID = base64_decode($forumID);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$forumID")
    ]);
    if($_Forum["Empty"] == 0) {
     $_Dialog = [
      "Body" => "You do not have permission to delete this topic.",
      "Header" => "Forbidden"
     ];
     $forum = $_Forum["DataModel"];
     $owner = $forum["UN"] ?? "";
     $topicID = base64_decode($topicID);
     $topics = $forum["Topics"] ?? [];
     $topic = $topics[$topicID] ?? [];
     $topicIsDefault = $topic["Default"] ?? 0;
     if(empty($topic)) {
      $_Dialog = [
       "Body" => "The topic was not found."
      ];
     } elseif($owner == $you) {
      $_Dialog = "";
      $defaultTopics = 0;
      $migrateTo = "";
      foreach($topics as $id => $info) {
       if($info["Default"] == 1) {
        $defaultTopics++;
        if($id != $topicID && $info["Default"] == 1) {
         $migrateTo = $id;
        }
       }
      } if($defaultTopics == 1 && $topicIsDefault == 1) {
       $_View = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "p", "Please make another topic the default before deleting this one.",
         ["class" => "CenterText"]
        ]).$this->core->Element([
         "button", "Okay", ["class" => "CloseDialog v2 v2w"]
        ]))
       ];
      } else {
       $newTopics = [];
       foreach($topic["Posts"] as $key => $post) {
        array_push($topics[$migrateTo]["Posts"], $post);
       } foreach($topics as $id => $info) {
        if($id != $topicID) {
         $newTopics[$id] = $info;
        }
       }
       $forum["Topics"] = $newTopics;
       $this->core->Data("Save", ["pf", $forumID, $forum]);
       $_View = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "p", "The topic <em>".$topic["Title"]."</em> was purged from <em>".$forum["Title"]."</em>.",
         ["class" => "CenterText"]
        ]).$this->core->Element([
         "button", "Okay", ["class" => "CloseDialog v2 v2w"]
        ]).$this->core->Element([
         "script", "$('.DeleteTopic$topicID').remove();"
        ]))
       ];
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "About",
    "Crweated",
    "Description",
    "ID",
    "Type"
   ]);
   $id = $data["ID"];
   $new = $data["New"] ?? 0;
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $actionTaken = ($new == 1) ? "published" : "updated";
    $coverPhoto = $data["CoverPhoto"] ?? "";
    if($new == 1) {
     array_push($y["Forums"], $id);
     $y["Forums"] = array_unique($y["Forums"]);
     $manifest = [];
     $manifest[$y["Login"]["Username"]] = "Admin";
     $points = $this->core->config["PTS"]["NewContent"] ?? 0;
     $y["Points"] = $y["Points"] + $points;
     $y["Activity"]["LastActive"] = $now;
     $this->core->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
     $this->core->Data("Save", ["pfmanifest", $id, $manifest]);
    } if(!empty($data["rATTI"])) {
     $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
     $i = 0;
     foreach($dlc as $dlc) {
      if(!empty($dlc) && $i == 0) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $t = $this->core->Member($f[0]);
        $efs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
        $fileName = $efs["Files"][$f[1]]["Name"] ?? "";
        if(!empty($fileName)) {
         $coverPhoto = $f[0]."/$fileName";
         $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
         $i++;
        }
       }
      }
     }
    }
    $forum = $this->core->Data("Get", ["pf", $id]);
    $created = $forum["Created"] ?? $this->core->timestamp;
    $illegal = $forum["Illegal"] ?? 0;
    $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $owner = $forum["UN"] ?? $you;
    $posts = $forum["Posts"] ?? [];
    $passPhrase = $data["PassPhrase"] ?? "";
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
    $purge = $forum["Purge"] ?? 0;
    $title = $data["Title"] ?? "Untitled";
    $topics = $forum["Topics"] ?? [];
    $type = $data["Type"] ?? md5("Private");
    $forum = [
     "About" => $data["About"],
     "Created" => $created,
     "Description" => $this->core->PlainText([
      "Data" => $data["Description"],
      "HTMLEncode" => 1
     ]),
     "CoverPhoto" => $coverPhoto,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "NSFW" => $nsfw,
     "PassPhrase" => $passPhrase,
     "Posts" => $posts,
     "Privacy" => $privacy,
     "Purge" => $purge,
     "Title" => $title,
     "Topics" => $topics,
     "Type" => $type,
     "UN" => $owner
    ];
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO Forums(
     Forum_Created,
     Forum_Description,
     Forum_ID,
     Forum_NSFW,
     Forum_Privacy,
     Forum_Title,
     Forum_Username
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
     ":Description" => $forum["Description"],
     ":ID" => $id,
     ":NSFW" => $forum["NSFW"],
     ":Privacy" => $forum["Privacy"],
     ":Title" => $forum["Title"],
     ":Username" => $owner
    ]);
    $sql->execute();
    $this->core->Data("Save", ["pf", $id, $forum]);
    $_Dialog = [
     "Body" => "The Forum <em>$title</em> was $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($member)) {
    $_Dialog = [
     "Body" => "You cannot banish yourself."
    ];
    $id = base64_decode($id);
    $forum = $this->core->Data("Get", ["pf", $id]);
    $member = base64_decode($member);
    if($member != $forum["UN"] && $member != $y["Login"]["Username"]) {
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
     $newManifest = [];
     foreach($manifest as $member => $role) {
      if($forum["UN"] != $member && $member != $member) {
       $newManifest[$member] = $role;
      }
     }
     $this->core->Data("Save", ["pfmanifest", $id, $newManifest]);
     $_Dialog = [
      "Body" => "$member was banished from <em>".$forum["Title"]."</em>.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveTopics(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "At least one Topic is required."
    ];
    $topicID = $data["TopicID"] ?? [];
    if(!empty($topicID)) {
     $_Dialog = [
      "Body" => "The Forum could not be loaded."
     ];
     $_Forum = $this->core->GetContentData([
      "ID" => base64_encode("Forum;$id")
     ]);
     if($_Forum["Empty"] == 0) {
      $defaultTopics = 0;
      $forum = $_Forum["DataModel"];
      $now = $this->core->timestamp;
      $topics = [];
      for($i = 0; $i < count($topicID); $i++) {
       $topicIdentifier = $topicID[$i] ?? "NoID";
       $previousTopicIdentifier = $data["PreviousID"][$i] ?? $topicID[$i];
       $_Topic = $forum["Topics"][$previousTopicIdentifier] ?? [];
       $created = $_Topic["Created"] ?? $now;
       $default = $_Topic["Default"] ?? 0;
       $default = $data["Default"][$i] ?? $default;
       $description = $_Topic["Description"] ?? "";
       $description = $data["Description"][$i] ?? $description;
       $modifiedBy = $_Topic["ModifiedBy"] ?? [];
       $modifiedBy[$now] = $you;
       $nsfw = $data["NSFW"][$i] ?? 0;
       $nsfw = $_Topic["NSFW"] ?? $nsfw;
       $posts = $_Topic["Posts"] ?? [];
       $title = $data["Title"][$i] ?? "Untitled";
       $title = $_Topic["Title"] ?? $title;
       $topicIdentifier = $topicID[$i] ?? $previousTopicIdentifier;
       if($default == 1) {
        $defaultTopics++;
       }
       $topics[$topicIdentifier] = [
        "Created" => $created,
        "Default" => $default,
        "Description" => $description,
        "Modified" => $now,
        "ModifiedBy" => $modifiedBy,
        "NSFW" => $nsfw,
        "Posts" => $posts,
        "Title" => $title
       ];
      } if($defaultTopics == 0) {
       $topics[0]["Default"] = 1;
       $_Dialog = [
        "Body" => "A default Topic is required."
       ];
      } else {
       $forum["Topics"] = $topics;
       $this->core->Data("Save", ["pf", $id, $forum]);
       $_Dialog = [
        "Body" => "The Topic list was updated.",
        "Header" => "Done"
       ];
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SendInvite(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $i = 0;
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($member)) {
    $forum = $this->core->Data("Get", ["pf", $id]);
    $members = $this->core->DatabaseSet("Member");
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $value]);
      if($member == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $_Dialog = [
      "Body" => "The Member $member does not exist."
     ];
    } elseif(empty($forum["ID"])) {
     $_Dialog = [
      "Body" => "The Forum does not exist."
     ];
    } elseif($forum["UN"] == $member) {
     $_Dialog = [
      "Body" => "$member owns <em>".$forum["Title"]."</em>."
     ];
    } elseif($member == $you) {
     $_Dialog = [
      "Body" => "You are already a member of this forum."
     ];
    } else {
     $active = 0;
     $manifest = $this->core->Data("Get", [
      "pfmanifest",
      $forum["ID"]
     ]);
     foreach($manifest as $member => $role) {
      if($member == $member) {
       $active++;
      }
     } if($active == 1) {
      $_Dialog = [
       "Body" => "$member is already an active member of <em>".$forum["Title"]."</em>."
      ];
     } else {
      $role = $data["Role"] ?? 0;
      $role = ($role == 1) ? "Member" : "Admin";
      $manifest[$member] = $role;
      $this->core->Data("Save", [
       "pfmanifest",
       $forum["ID"],
       $manifest
      ]);
      $this->core->SendBulletin([
       "Data" => [
        "ForumID" => $id,
        "Member" => $member,
        "Role" => $role
       ],
       "To" => $member,
       "Type" => "InviteToForum"
      ]);
      $_Dialog = [
       "Body" => "$member was notified of your invitation.",
       "Header" => "Invitation Sent"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Topic(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $forumID = $data["Forum"] ?? "";
   $topicID = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($forumID)) {
    $_Dialog = [
     "Body" => "The Forum could not be found."
    ];
    $action = "";
    $forumID = base64_decode($forumID);
    $blocked = $this->core->CheckBlocked([$y, "Forums", $forumID]);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$forumID")
    ]);
    $topicID = base64_decode($topicID);
    if($_Forum["Empty"] == 0 && $blocked == 0) {
     $_Dialog = "";
     $_Extension = $this->core->Element([
      "h1", "[Topic.Title]"
     ]).$this->core->Element([
      "div", "[Topic.View]", ["class" => "NONAME"]
     ]).$this->core->Element([
      "div", "[Topic.Post]", ["class" => "NONAME"]
     ]).$this->core->Element([
      "div", "[Topic.Back]", ["class" => "NONAME"]
     ]);
     $_View = $this->view(base64_encode("Search:Containers"), ["Data" => [
      "Forum" => $forumID,
      "Topic" => $topicID,
      "st" => "Forums-Topic"
     ]]);
     $forum = $_Forum["DataModel"];
     $manifest = $this->core->Data("Get", ["pfmanifest", $forumID]);
     $now = $this->core->timestamp;
     $yourRole = $manifest[$you] ?? "";
     $topic = $forum["Topics"][$topicID] ?? [];
     $post = (!empty($yourRole)) ? $this->core->Element([
      "button", "Say Something", [
       "class" => "BigButton OpenCard",
       "data-view" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$forumID&Topic=$topicID&new=1")
      ]
     ]) : "";
     $_View = [
      "ChangeData" => [
       "[Topic.Back]" => $this->core->Element([
        "div", "&nbsp;", ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Back", [
         "class" => "GoToParent v2 v2w",
         "data-type" => "TopicsList$forumID"
        ]]), ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", "&nbsp;", ["class" => "Desktop33"]
       ]),
       "[Topic.Post]" => $post,
       "[Topic.Title]" => $topic["Title"],
       "[Topic.View]" => $this->core->RenderView($_View)
      ],
      "Extension" => $this->core->AESencrypt($_Extension)
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Topics(array $data): string {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Forum could not be loaded."
    ];
    $action = "";
    $id = base64_decode($id);
    $_Forum = $this->core->GetContentData([
     "ID" => base64_encode("Forum;$id")
    ]);
    if($_Forum["Empty"] == 0) {
     $_Dialog = [
      "Body" => "This Forum currently has no discussion topics.",
      "Header" => "No Topics"
     ];
     $forum = $_Forum["DataModel"];
     $topics = $forum["Topics"] ?? [];
     if(!empty($topics)) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Search:Containers"), ["Data" => [
       "Forum" => $id,
       "st" => "Forums-Topics"
      ]]);
      $_View = $this->core->RenderView($_View);
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>