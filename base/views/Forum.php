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
   $data = $this->core->FixMissing($data, ["ID", "new"]);
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"];
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
    $atinput = ".Forum$id-ATTI";
    $at = base64_encode("Set as the Forum's Cover Photo:$atinput");
    $atinput = "$atinput .rATT";
    $at2 = base64_encode("All done! Feel free to close this card.");
    $ca = base64_encode("Chat:Attachments");
    $coverPhoto = $forum["ICO-SRC"] ?? "";
    $created = $forum["Created"] ?? $now;
    $description = $forum["Description"] ?? "";
    $es = base64_encode("LiveView:EditorSingle");
    $header = ($new == 1) ? "New Forum" : "Edit ".$forum["Title"];
    $nsfw = $forum["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $forum["Privacy"] ?? $y["Privacy"]["Forums"];
    $sc = base64_encode("Search:Containers");
    $title = $forum["Title"] ?? "My Forum";
    $type = $forum["Type"] ?? $y["Privacy"]["ForumsType"];
    $r = $this->core->Change([[
     "[Forum.About]" => $about,
     "[Forum.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Forum",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".base64_encode($you)),
       "[Extras.DesignView.Origin]" => "NA",
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&UN=".base64_encode($you)),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Forum.About]" => base64_encode($about),
     "[Forum.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
     "[Forum.CoverPhoto]" => $coverPhoto,
     "[Forum.CoverPhoto.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[Forum.Created]" => $created,
     "[Forum.Description]" => base64_encode($description),
     "[Forum.Header]" => $header,
     "[Forum.ID]" => $id,
     "[Forum.New]" => $new,
     "[Forum.Title]" => base64_encode($title),
     "[Forum.Type]" => $type
    ], $this->core->Page("8304362aea73bddb2c12eb3f7eb226dc")]);
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
   $data = $this->core->FixMissing($data, [
    "CARD",
    "ID",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $id = $data["ID"];
   $lpg = $data["lPG"];
   $b2 = $data["b2"] ?? "Forums";
   $b2 = $this->core->Element(["em", $b2]);
   $bck = $data["back"] ?? 0;
   $bck = ($bck == 1) ? $this->core->Element(["button", "Back to $b2", [
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
   $bl = $this->core->CheckBlocked([$y, "Forums", $id]);
   if(!empty($id)) {
    $id = base64_decode($id);
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $active = 0;
    $admin = 0;
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
    $notAnon = ($this->core->ID != $you) ? 1 : 0;
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 1;
      if($admin == 0 && $role == "Admin") {
       $admin = 1;
      }
     }
    }
    $ck = ($admin == 1 || $forum["UN"] == $you) ? 1 : 0;
    $r = [
     "Body" => "<em>".$forum["Title"]."</em> is invite-only.",
     "Header" => "Private Forum"
    ];
    if($active == 1 || $ck == 1 || $forum["Type"] == "Public") {
     $_SonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
     $accessCode = "Accepted";
     $blockCommand = ($bl == 0) ? "Block" : "Unblock";
     $actions = ($bl == 0 && $ck == 0) ? $this->core->Element([
      "button", $blockCommand, [
       "class" => "CloseCard GoToParent Small UpdateButton v2 v2w",
       "data-type" => ".OHCC;$lpg",
       "data-processor" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($forum["ID"])."&List=".base64_encode("Forums"))
      ]
     ]) : "";
     $actions .= (!empty($chat) && ($active == 1 || $ck == 1)) ? $this->core->Element([
      "button", "Chat", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($id)."&Integrated=1")
      ]
     ]) : "";
     $actions .= ($forum["UN"] == $you && $pub == 0) ? $this->core->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteForum")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($admin == 1) ? $this->core->Element(["button", "Edit", [
      "class" => "OpenCard Small v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Forum:Edit")."&ID=$id")
     ]]) : "";
     $actions .= ($active == 1 || $ck == 1 || $forum["Type"] == "Public") ? $this->core->Element([
      "button", "Post", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$id&new=1")
      ]
     ]) : "";
     $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
      "button", "Share", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($id)."&Type=".base64_encode("Forum")."&Username=".base64_encode($forum["UN"]))
      ]
     ]) : "";
     $coverPhoto = $this->core->PlainText([
      "Data" => "[Media:CP]",
      "Display" => 1
     ]);
     $coverPhoto = (!empty($forum["ICO"])) ? base64_encode($forum["ICO"]) : $coverPhoto;
     $invite = ($active == 1 && $forum["ID"] != $_SonsOfLiberty) ? $this->core->Element([
      "button", "Invite", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Forum:Invite")."&ID=".base64_encode($forum["ID"]))
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
     $votes = ($active == 1 && $ck == 0) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $r = $this->core->Change([[
      "[Forum.About]" => $forum["About"],
      "[Forum.Actions]" => $actions,
      "[Forum.Administrators]" => base64_encode("v=$search&Admin=".base64_encode($forum["UN"])."&ID=".base64_encode($id)."&st=Forums-Admin"),
      "[Forum.Back]" => $bck,
      "[Forum.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Forum")."&st=Contributors"),
      "[Forum.Contributors.Featured]" => base64_encode("v=".base64_encode("Common:MemberGrid")."&List=".base64_encode(json_encode($manifest, true))),
      "[Forum.CoverPhoto]" => $this->core->CoverPhoto($coverPhoto),
      "[Forum.Description]" => $this->core->PlainText([
       "Data" => $forum["Description"],
       "HTMLDncode" => 1
      ]),
      "[Forum.ID]" => $id,
      "[Forum.Invite]" => $invite,
      "[Forum.Join]" => $join,
      "[Forum.Stream]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=Forums-Posts"),
      "[Forum.Title]" => $forum["Title"],
      "[Forum.Votes]" => base64_encode("v=$votes&ID=$id&Type=4")
     ], $this->core->Page("4159d14e4e8a7d8936efca6445d11449")]);
    }
   }
   $r = ($data["CARD"] == 1) ? [
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
    ], $this->core->Page("80e444c34034f9345eee7399b4467646")]);
    $r = [
     "Action" => $action,
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
    $forums = $this->core->DatabaseSet("PF") ?? [];
    foreach($forums as $key => $value) {
     $forum = str_replace("c.oh.pf.", "", $value);
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
      if($i == 0 && !empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $t = $this->core->Member($f[0]);
        $efs = $this->core->Data("Get", [
         "fs",
         md5($t["Login"]["Username"])
        ]) ?? [];
        $coverPhoto = $f[0]."/".$efs["Files"][$f[1]]["Name"];
        $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
        $i++;
       }
      }
     }
    }
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $created = $forum["Created"] ?? $this->core->timestamp;
    $illegal = $forum["Illegal"] ?? 0;
    $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $posts = $forum["Posts"] ?? [];
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
    $title = $data["Title"] ?? "My Forum";
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
     "Posts" => $posts,
     "Privacy" => $privacy,
     "Title" => $title,
     "UN" => $you,
     "Type" => $type
    ];
    $this->core->Data("Save", ["pf", $id, $forum]);
    $actionTaken = ($new == 1) ? "published" : "updated";
    $r = [
     "Body" => "The Forum <em>$title</em> was $actionTaken.",
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
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $all = $data["all"] ?? 0;
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN", "all"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $y["Login"]["Username"]) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $forums = $y["Forums"] ?? [];
    $newForums = [];
    /*foreach($forum["Posts"] as $key => $value) {
     if(!empty($this->core->Data("Get", ["conversation", $value]))) {
      $this->view(base64_encode("Conversation:SaveDelete"), [
       "Data" => ["ID" => $value]
      ]);
     }
     $this->core->Data("Purge", ["local", $value]);
     $this->core->Data("Purge", ["post", $value]);
     $this->core->Data("Purge", ["votes", $value]);
    } if(!empty($this->core->Data("Get", ["conversation", $id]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    }
    $this->core->Data("Purge", ["chat", $id]);
    $this->core->Data("Purge", ["local", $id]);
    $this->core->Data("Purge", ["pfmanifest", $id]);
    $this->core->Data("Purge", ["pf", $id]);
    $this->core->Data("Purge", ["react", $id]);*/
    foreach($forums as $key => $value) {
     if($id != $value) {
      $newForums[$key] = $value;
     }
    }
    $r = [
     "Body" => "The Forum ($id, temp) was deleted.".json_encode($y["Forums"], true),
     "Header" => "Done"
    ];
    $y["Forums"] = $newForums;
    #$this->core->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
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
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $members = $this->core->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
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
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>