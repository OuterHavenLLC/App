<?php
 Class Forum extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $accessCode = "Accepted";
     $r = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Forum:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$forum["Title"]."</em>?",
      "Header" => "Banish $mbr?"
     ];
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
  function ChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $r = [
    "Body" => "p", "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $manifest[$member] = $role;
    $this->core->Data("Save", ["pfmanifest", $id, $manifest]);
    $r = [
     "Body" => "$member's Role within <em>".$forum["Title"]."</em> was Changed to $role.",
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
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($y["Login"]["Username"]."_FORUM_".$now) : $id;
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $about = $forum["About"] ?? "";
    $author = $forum["UN"] ?? $you;
    $ca = base64_encode("Chat:Attachments");
    $coverPhoto = $forum["ICO-SRC"] ?? "";
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
    $r = $this->core->Change([[
     "[Forum.Attachments]" => "",
     "[Forum.About]" => base64_encode($about),
     "[Forum.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
     "[Forum.Created]" => $created,
     "[Forum.Description]" => base64_encode($description),
     "[Forum.Header]" => $header,
     "[Forum.ID]" => $id,
     "[Forum.New]" => $new,
     "[Forum.NSFW]" => $nsfw,
     "[Forum.PassPhrase]" => base64_encode($passPhrase),
     "[Forum.Privacy]" => $privacy,
     "[Forum.Title]" => base64_encode($title),
     "[Forum.Type]" => $type
    ], $this->core->Extension("8304362aea73bddb2c12eb3f7eb226dc")]);
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditForum$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:Save"))
    ]]);
    $r = [
     "Action" => $action,
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
  function EditTopics(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $id = base64_decode($id);
    $bl = $this->core->CheckBlocked([$y, "Forums", $id]);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Forum;$id")
    ]);
    $r = [
     "Body" => "The requested Forum could not be found."
    ];
    if($_Forum["Empty"] == 0) {
     $accessCode = "Accepted";
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
     $r = $this->core->Change([[
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
      "[Topics.Save]" => base64_encode("v=".base64_encode("Forum:SaveTopics"))
     ], $this->core->Extension("4a40ab9e976c8d00bb4ebc8c953cd3ca")]);
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $card = $data["CARD"] ?? 0;
   $id = $data["ID"] ?? "";
   $lpg = $data["lPG"] ?? "";
   $b2 = $data["b2"] ?? "Forums";
   $b2 = $this->core->Element(["em", $b2]);
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI header",
    "data-type" => ".OHCC;$lpg"
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Forum could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $id = base64_decode($id);
    $bl = $this->core->CheckBlocked([$y, "Forums", $id]);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Forum;$id")
    ]);
    if($_Forum["Empty"] == 0) {
     $accessCode = "Accepted";
     $forum = $_Forum["DataModel"];
     $passPhrase = $forum["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
       $r = $this->view(base64_encode("Forum:Home"), ["Data" => [
        "AddTo" => $addTo,
        "ID" => $data["ID"],
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Denied";
      $active = 0;
      $admin = 0;
      $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
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
      $ck = ($admin == 1 || $forum["UN"] == $you) ? 1 : 0;
      $doNotShare = $this->core->RestrictedIDs;
      $r = [
       "Body" => "<em>$title</em> is invite-only.",
       "Header" => "Private Forum"
      ];
      if($active == 1 || $ck == 1 || $forum["Type"] == "Public") {
       $accessCode = "Accepted";
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $actions = (!in_array($forum["ID"], $doNotShare)) ? $this->core->Element([
        "button", "Attach", [
         "class" => "Attach Small v2",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode($forum["ID"])
        ]
       ]) : "";
       $actions .= ($bl == 0 && $ck == 0) ? $this->core->Element([
        "button", "Block", [
         "class" => "CloseCard Small UpdateButton v2 v2w",
         "data-view" => $options["Block"]
        ]
       ]) : "";
       $actions .= (!empty($chat) && ($active == 1 || $ck == 1)) ? $this->core->Element([
        "button", "Chat", [
         "class" => "OpenCard Small v2 v2w",
         "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($id)."&Integrated=1")
        ]
       ]) : "";
       $actions .= (!in_array($forum["ID"], $doNotShare) && $forum["UN"] == $you && $pub == 0) ? $this->core->Element([
        "button", "Delete", [
         "class" => "CloseCard OpenDialog Small v2",
         "data-view" => $options["Delete"]
        ]
       ]) : "";
       $actions .= ($admin == 1) ? $this->core->Element(["button", "Edit", [
        "class" => "OpenCard Small v2 v2w",
        "data-view" => $options["Edit"]
       ]]) : "";
       $actions .= ($active == 1 || $ck == 1 || $forum["Type"] == "Public") ? $this->core->Element([
        "button", "Post", [
         "class" => "OpenCard Small v2 v2w",
         "data-view" => $options["Post"]
        ]
       ]) : "";
       $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
        "button", "Share", [
         "class" => "OpenCard Small v2 v2w",
         "data-view" => $options["Share"]
        ]
       ]) : "";
       $createTopicAction = (empty($forum["Topics"])) ? "Create a Topic" : "Manage Topics";
       $createTopic = ($forum["UN"] == $you) ? $this->core->Element([
        "button", $createTopicAction, [
         "class" => "BigButton GoToView",
         "data-type" => "ForumTopics$id;".base64_encode("v=".base64_encode("Forum:EditTopics")."&ID=".$data["ID"])
        ]
       ]) : "";
       $invite = (!in_array($forum["ID"], $doNotShare) && $active == 1) ? $this->core->Element([
        "button", "Invite", [
         "class" => "OpenCard v2",
         "data-view" => $options["Invite"]
        ]
       ]) : "";
       $joinCommand = ($active == 0) ? "Join" : "Leave";
       $join = ($ck == 0 && $forum["Type"] == "Public") ? $this->core->Element([
        "button", $joinCommand, [
         "class" => "BBB UpdateButton v2 v2w",
         "data-processor" => base64_encode("v=".base64_encode("Forum:Join")."&Command=$joinCommand&ID=$id")
        ]
       ]) : "";
       $search = base64_encode("Search:Containers");
       $r = $this->core->Change([[
        "[Forum.About]" => $forum["About"],
        "[Forum.Actions]" => $actions,
        "[Forum.Administrators]" => base64_encode("v=$search&Admin=".base64_encode($forum["UN"])."&ID=".base64_encode($id)."&st=Forums-Admin"),
        "[Forum.Back]" => $back,
        "[Forum.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Forum")."&st=Contributors"),
        "[Forum.Contributors.Featured]" => base64_encode("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($manifest, true))),
        "[Forum.CoverPhoto]" => $_Forum["ListItem"]["CoverPhoto"],
        "[Forum.CreateTopic]" => $createTopic,
        "[Forum.EditTopics]" => base64_encode("v=".base64_encode("Forum:EditTopics")."&ID=".base64_encode($id)),
        "[Forum.Description]" => $_Forum["ListItem"]["Description"],
        "[Forum.ID]" => $id,
        "[Forum.Invite]" => $invite,
        "[Forum.Join]" => $join,
        "[Forum.Stream]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=Forums-Posts"),
        "[Forum.Title]" => $title,
        "[Forum.Topics]" => base64_encode("v=".base64_encode("Forum:Topics")."&ID=".base64_encode($id)),
        "[Forum.Votes]" => $options["Vote"]
       ], $this->core->Extension("4159d14e4e8a7d8936efca6445d11449")]);
      }
     }
    }
   }
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
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
  function Invite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $action = $this->core->Element(["button", "Send Invite", [
     "class" => "CardButton CloseCard SendData",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:SendInvite"))
    ]]);
    $content = [];
    $contentOptions = $y["Forums"] ?? [];
    foreach($contentOptions as $key => $value) {
     $forum = $this->core->Data("Get", ["pf", $value]) ?? [];
     $content[$value] = $forum["Title"];
    }
    $r = $this->core->Change([[
     "[Invite.Content]" => json_encode($content, true),
     "[Invite.ID]" => $id,
     "[Invite.Member]" => $member
    ], $this->core->Extension("80e444c34034f9345eee7399b4467646")]);
    $r = [
     "Action" => $action,
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
  function Join(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Forum Identifier or Join Command are missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($command) && !empty($id)) {
    $r = [
     "Body" => "You cannot leave your own Forum."
    ];
    if($forum["UN"] != $you) {
     $accessCode = "Accepted";
     $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
     $processor = "v=".base64_encode("Forum:Join")."&ID=$id";
     $responseType = "View";
     if($command == "Join") {
      $manifest[$you] = "Member";
      $r = [
       "Attributes" => [
        "class" => "BBB UpdateButton v2 v2w",
        "data-processor" => base64_encode("$processor&Command=Leave")
       ],
       "Text" => "Leave"
      ];
     } elseif($command == "Leave") {
      $accessCode = "Accepted";
      $newManifest = [];
      foreach($manifest as $member => $role) {
       if($member != $you) {
        $newManifest[$member] = $role;
       }
      }
      $manifest = $newManifest;
      $r = [
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
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function PublicHome(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->core->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "We could not find the Forum you were looking for."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($callSign) || !empty($id)) {
    $accessCode = "Accepted";
    $forums = $this->core->DatabaseSet("Forum");
    foreach($forums as $key => $value) {
     $forum = str_replace("nyc.outerhaven.pf.", "", $value);
     $forum = $this->core->Data("Get", ["pf", $forum]) ?? [];
     $forumCallSign = $this->core->CallSign($forum["Title"]);
     if($callSign == $forumCallSign || $id == $forum["ID"]) {
      $r = $this->view(base64_encode("Forum:Home"), ["Data" => [
       "ID" => $forum["ID"]
      ]]);
      $r = $this->core->RenderView($r);
     }
    }
   } if($this->core->ID  == $you) {
    $r = $this->view(base64_encode("WebUI:OptIn"), []);
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
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
    "Body" => "The Forum Identifier is missing."
   ];
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $id = base64_decode($id);
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Forum;$id")
    ]);
    $r = [
     "Body" => "The Forum was not found."
    ];
    if($_Forum["Empty"] == 0) {
     $accessCode = "Accepted";
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
     $r = $this->core->Element([
      "p", "The Forum <em>".$forum["Title"]."</em> and dependencies were marked for purging.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
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
    "ResponseType" => "Dialog"
   ]);
  }
  function PurgeTopic(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $forumID = $data["Forum"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $topicID = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($forumID) && !empty($topicID)) {
    $forumID = base64_decode($forumID);
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Forum;$forumID")
    ]);
    $r = [
     "Body" => "The Forum was not found."
    ];
    if($_Forum["Empty"] == 0) {
     $forum = $_Forum["DataModel"];
     $owner = $forum["UN"] ?? "";
     $r = [
      "Body" => "You do not have permission to delete this topic.",
      "Header" => "Forbidden"
     ];
     $topicID = base64_decode($topicID);
     $topics = $forum["Topics"] ?? [];
     $topic = $topics[$topicID] ?? [];
     $topicIsDefault = $topic["Default"] ?? 0;
     if(empty($topic)) {
      $r = [
       "Body" => "The topic was not found."
      ];
     } elseif($owner == $you) {
      $accessCode = "Accepted";
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
       $r = $this->core->Element([
        "p", "Please make another topic the default before deleting this one.",
        ["class" => "CenterText"]
       ]).$this->core->Element([
        "button", "Okay", ["class" => "CloseDialog v2 v2w"]
       ]);
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
       $r = $this->core->Element([
        "p", "The topic <em>".$topic["Title"]."</em> was purged from <em>".$forum["Title"]."</em>.",
        ["class" => "CenterText"]
       ]).$this->core->Element([
        "button", "Okay", ["class" => "CloseDialog v2 v2w"]
       ]).$this->core->Element([
        "script", "$('.DeleteTopic$topicID').remove();"
       ]);
      }
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
    "ResponseType" => "Dialog"
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
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
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "published" : "updated";
    $coverPhoto = "";
    $coverPhotoSource = "";
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
        $efs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]) ?? [];
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
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
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
     "ICO" => $coverPhoto,
     "ICO-SRC" => base64_encode($coverPhotoSource),
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
    $r = [
     "Body" => "The Forum <em>$title</em> was $actionTaken.",
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
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($mbr)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
     $newManifest = [];
     foreach($manifest as $member => $role) {
      if($forum["UN"] != $member && $mbr != $member) {
       $newManifest[$member] = $role;
      }
     }
     $this->core->Data("Save", ["pfmanifest", $id, $newManifest]);
     $r = [
      "Body" => "$mbr was banished from <em>".$forum["Title"]."</em>.",
      "Header" => "Done"
     ];
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
  function SaveTopics(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $r = [
     "Body" => "At least one Topic is required."
    ];
    $topicID = $data["TopicID"] ?? [];
    if(!empty($topicID)) {
     $_Forum = $this->core->GetContentData([
      "Blacklisted" => 0,
      "ID" => base64_encode("Forum;$id")
     ]);
     $r = [
      "Body" => "The Forum could not be loaded."
     ];
     if($_Forum["Empty"] == 0) {
      $accessCode = "Accepted";
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
       $accessCode = "Denied";
       $topics[0]["Default"] = 1;
       $r = [
        "Body" => "A default Topic is required."
       ];
      } else {
       $forum["Topics"] = $topics;
       $this->core->Data("Save", ["pf", $id, $forum]);
       $r = [
        "Body" => "The Topic list was updated.",
        "Header" => "Done"
       ];
      }
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
    "ResponseType" => "Dialog"
   ]);
  }
  function SendInvite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($mbr)) {
    $forum = $this->core->Data("Get", ["pf", $id]);
    $members = $this->core->DatabaseSet("Member");
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
      if($mbr == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = [
      "Body" => "The Member $mbr does not exist."
     ];
    } elseif(empty($forum["ID"])) {
     $r = [
      "Body" => "The Forum does not exist."
     ];
    } elseif($forum["UN"] == $mbr) {
     $r = [
      "Body" => "$mbr owns <em>".$forum["Title"]."</em>."
     ];
    } elseif($mbr == $you) {
     $r = [
      "Body" => "You are already a member of this forum."
     ];
    } else {
     $active = 0;
     $manifest = $this->core->Data("Get", [
      "pfmanifest",
      $forum["ID"]
     ]) ?? [];
     foreach($manifest as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = [
       "Body" => "$mbr is already an active member of <em>".$forum["Title"]."</em>."
      ];
     } else {
      $accessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $manifest[$mbr] = $role;
      $this->core->Data("Save", [
       "pfmanifest",
       $forum["ID"],
       $manifest
      ]) ?? [];
      $this->core->SendBulletin([
       "Data" => [
        "ForumID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToForum"
      ]);
      $r = [
       "Body" => "$mbr was notified of your invitation.",
       "Header" => "Invitation Sent"
      ];
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
    "ResponseType" => "Dialog"
   ]);
  }
  function Topic(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $forumID = $data["Forum"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $topicID = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($forumID)) {
    $action = "";
    $forumID = base64_decode($forumID);
    $bl = $this->core->CheckBlocked([$y, "Forums", $forumID]);
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Forum;$forumID")
    ]);
    $r = [
     "Body" => "The Forum could not be loaded."
    ];
    $topicID = base64_decode($topicID);
    if($_Forum["Empty"] == 0) {
     $accessCode = "Accepted";
     $forum = $_Forum["DataModel"];
     $manifest = $this->core->Data("Get", ["pfmanifest", $forumID]) ?? [];
     $now = $this->core->timestamp;
     $yourRole = $manifest[$you] ?? "";
     $topic = $forum["Topics"][$topicID] ?? [];
     $posts = $topic["Posts"] ?? [];
     $r = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "No Posts",
      "[Error.Message]" => "This Topic currently has no posts."
     ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
     if(!empty($posts)) {
      $posts = array_reverse($posts);
      $r = $this->view(base64_encode("Search:Containers"), ["Data" => [
       "Forum" => $forumID,
       "Topic" => $topicID,
       "st" => "Forums-Topic"
      ]]);
      $r = $this->core->Element([
       "h1", $topic["Title"]
      ]).$this->core->RenderView($r);
     }
     $r .= (!empty($yourRole)) ? $this->core->Element([
      "button", "Say Something", [
       "class" => "BigButton OpenCard",
       "data-view" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$forumID&Topic=$topicID&new=1")
      ]
     ]) : "";
     $r .= $this->core->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
     ]).$this->core->Element([
      "div", $this->core->Element(["button", "Back", [
       "class" => "GoToParent v2 v2w",
       "data-type" => "TopicsList$forumID"
      ]]), ["class" => "Desktop33"]
     ]).$this->core->Element([
      "div", "&nbsp;", ["class" => "Desktop33"]
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
  function Topics(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $action = "";
    $id = base64_decode($id);
    $bl = $this->core->CheckBlocked([$y, "Forums", $id]);
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Forum;$id")
    ]);
    $r = [
     "Body" => "The Forum could not be loaded."
    ];
    if($_Forum["Empty"] == 0) {
     $accessCode = "Accepted";
     $forum = $_Forum["DataModel"];
     $topics = $forum["Topics"] ?? [];
     $r = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "No Topics",
      "[Error.Message]" => "This Forum currently has no discussion topics."
     ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
     if(!empty($topics)) {
      $r = $this->view(base64_encode("Search:Containers"), ["Data" => [
       "Forum" => $id,
       "st" => "Forums-Topics"
      ]]);
      $r = $this->core->RenderView($r);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>