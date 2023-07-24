<?php
 Class Forum extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Banish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $accessCode = "Accepted";
     $r = [
      "Actions" => [
       $this->system->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->system->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Forum:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$forum["Title"]."</em>?",
      "Header" => "Banish $mbr?"
     ];
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN", "Member"]);
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
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $manifest[$member] = $role;
    $this->system->Data("Save", ["pfmanifest", $id, $manifest]);
    $r = [
     "Body" => "$member's Role within <em>".$forum["Title"]."</em> was Changed to $role.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, ["ID", "new"]);
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $now = $this->system->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($y["Login"]["Username"]."_FORUM_".$now) : $id;
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $about = $forum["About"] ?? "";
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
    $r = $this->system->Change([[
     "[Forum.About]" => $about,
     "[Forum.AdditionalContent]" => $this->system->Change([
      [
       "[Extras.ContentType]" => "Forum",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => "N/A",
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=N/A&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=$id")
      ], $this->system->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Forum.Header]" => $header,
     "[Forum.ID]" => $id,
     "[Forum.Inputs]" => $this->system->RenderInputs([
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
        "name" => "new",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $new
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTI",
        "data-a" => "#ATTL$id-ATTI",
        "data-u" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
        "name" => "rATTI",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Forum$id-ATTI"
       ],
       "Type" => "Text",
       "Value" => $coverPhoto
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
       "Value" => $title
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
       "Value" => $about
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
       "Value" => $description
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        md5("Private") => "Private",
        md5("Publid") => "Publid"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Forum Type"
       ],
       "Name" => "PageCategory",
       "Title" => "Forum Type",
       "Type" => "Select",
       "Value" => $type
      ]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->system->Page("8304362aea73bddb2c12eb3f7eb226dc")]);
    $action = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditForum$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:Save"))
    ]]);
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, [
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
   $b2 = $this->system->Element(["em", $b2]);
   $bck = $data["back"] ?? 0;
   $bck = ($bck == 1) ? $this->system->Element(["button", "Back to $b2", [
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
   $bl = $this->system->CheckBlocked([$y, "Forums", $id]);
   if(!empty($id) && $bl == 0) {
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $active = 0;
    $admin = 0;
    $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
    $notAnon = ($this->system->ID != $you) ? 1 : 0;
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
     $accessCode = "Accepted";
     $_JoinCommand = ($active == 0) ? "Join" : "Leave";
     $_SonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
     $actions = ($bl == 0 && $ck == 0) ? $this->system->Element(["button", "Block", [
      "class" => "Block CloseCard GoToParent Small v2 v2w",
      "data-cmd" => base64_encode("B"),
      "data-type" => ".OHCC;$lpg",
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($f["Title"])."&content=".base64_encode($f["ID"])."&list=".base64_encode("Forums")."&BC=")
     ]]) : "";
     $actions .= ($active == 1 || $ck == 1) ? $this->system->Element([
      "button", "Chat", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&GroupChat=1&to=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($forum["UN"] == $you && $pub == 0) ? $this->system->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteForum")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($admin == 1) ? $this->system->Element(["button", "Edit", [
      "class" => "OpenCard Small v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Forum:Edit")."&ID=$id")
     ]]) : "";
     $actions .= ($active == 1 || $ck == 1 || $forum["Type"] == "Public") ? $this->system->Element([
      "button", "Post", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$id&new=1")
      ]
     ]) : "";
     $actions .= ($forum["Type"] == "Public") ? $this->system->Element([
      "button", "Share", [
       "class" => "OpenCard Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Forum:Share")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $coverPhoto = (!empty($forum["ICO"])) ? base64_encode($forum["ICO"]) : $coverPhoto;
     $invite = ($active == 1 && $forum["ID"] == $_SonsOfLiberty) ? $this->system->Element([
      "button", "Invite", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Forum:Invite")."&ID=".base64_encode($forum["ID"]))
      ]
     ]) : "";
     $join = ($ck == 0 && $f["Type"] == "Public") ? $this->system->Change([[
      "[Forum.Join.Command]" => $_JoinCommand,
      "[Forum.Join.ID]" => $id,
      "[Forum.Join.Processor]" => base64_encode("v=".base64_encode("Forum:LeaveOrJoin")),
      "[Forum.Join.Text]" => $_JoinCommand,
      "[Forum.Join.Username]" => $you,
      "[Forum.Title]" => $forum["Title"]
     ], $this->system->Page("4c3a04a91734ce56bef85d474294202d")]) : "";
     $manifest = base64_encode(json_encode($manifest, true));
     $search = base64_encode("Search:Containers");
     $votes = ($active == 1 && $ck == 0) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $r = $this->system->Change([[
      "[Forum.About]" => $forum["About"],
      "[Forum.Actions]" => $actions,
      "[Forum.Administrators]" => base64_encode("v=$search&Admin=".base64_encode($forum["UN"])."&ID=".base64_encode($id)."&st=Forums-Admin"),
      "[Forum.Back]" => $bck,
      "[Forum.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Forum")."&st=Contributors"),
      "[Forum.Contributors.Featured]" => base64_encode("v=".base64_encode("Common:MemberGrid")."&List=$manifest"),
      "[Forum.CoverPhoto]" => $this->system->CoverPhoto($coverPhoto),
      "[Forum.Description]" => $this->system->PlainText([
       "Data" => $forum["Description"],
       "HTMLDncode" => 1
      ]),
      "[Forum.ID]" => $id,
      "[Forum.Invite]" => $invite,
      "[Forum.Join]" => $join,
      "[Forum.Stream]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=Forums-Posts"),
      "[Forum.Title]" => $forum["Title"],
      "[Forum.Votes]" => base64_encode("v=$votes&ID=$id&Type=4")
     ], $this->system->Page("4159d14e4e8a7d8936efca6445d11449")]);
    }
   }
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $content = [];
    $contentOptions = $y["Forums"] ?? [];
    $id = base64_decode($id);
    foreach($contentOptions as $key => $value) {
     $forum = $this->Data("Get", ["pf", $value]) ?? [];
     $content[$forum["ID"]] = $forum["Title"];
    }
    $r = $this->system->Change([[
     "[Invite.ID]" => $id,
     "[Invite.Inputs]" => $this->system->RenderInputs([
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
        "name" => "Member",
        "placeholder" => $this->system->ID,
        "type" => "text"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $data["Member"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => $content,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Invite To"
       ],
       "Name" => "ListForums",
       "Type" => "Select",
       "Value" => $id
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Role"
       ],
       "Name" => "Role",
       "Type" => "Select",
       "Value" => 1
      ]
     ])
    ], $this->system->Page("80e444c34034f9345eee7399b4467646")]);
    $action = $this->system->Element(["button", "Send Invite", [
     "class" => "CardButton CloseCard SendData",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:SendInvite"))
    ]]);
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function LeaveOrJoin(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($command) && !empty($id)) {
    $accessCode = "Accepted";
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $ck = ($forum["UN"] == $you) ? 1 : 0;
    $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
    $responseType = "ReplaceContent";
    if($ck == 0 && $command == "Join") {
     $manifest[$you] = "Member";
     $r = $this->system->Element([
      "p", "You've joined <em>".$forum["Title"]."</em>!"
     ]);
    } elseif($ck == 0 && $command == "Leave") {
     $newManifest = [];
     foreach($manifest as $member => $role) {
      if($member != $you) {
       $newManifest[$member] = $role;
      }
     }
     $manifest = $newManifest;
     $r = $this->system->Element([
      "p", "Sorry to see you go, we hope to see you again!"
     ]);
    }
    #$this->system->Data("Save", ["pfmanifest", $id, $manifest]);
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
  function PublicHome(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CallSign",
    "ID"
   ]);
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->system->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "We could not find the Forum you were looking for."
   ];
   if(!empty($callSign) || !empty($id)) {
    $accessCode = "Accepted";
    $forums = $this->system->DatabaseSet("PF") ?? [];
    foreach($forums as $key => $value) {
     $forum = str_replace("c.oh.pf.", "", $value);
     $forum = $this->system->Data("Get", ["pf", $forum]) ?? [];
     $forumCallSign = $this->system->CallSign($forum["Title"]);
     if($callSign == $forumCallSign || $id == $forum["ID"]) {
      $r = $this->view(base64_encode("Forum:Home"), ["Data" => [
       "ID" => $forum["ID"]
      ]]);
     }
    }
   } if($y["Login"]["Username"] == $this->system->ID && $data["pub"] == 1) {
    $r = $this->view(base64_encode("WebUI:OptIn"), []);
    $r = $this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "About",
    "Crweated",
    "Description",
    "ID",
    "PFType",
    "nsfw",
    "pri"
   ]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $now = $this->system->timestamp;
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
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
     $points = $this->system->core["PTS"]["NewContent"] ?? 0;
     $y["Points"] = $y["Points"] + $points;
     $y["Activity"]["LastActive"] = $now;
     $this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
     $this->system->Data("Save", ["pfmanifest", $id, $manifest]);
    } if(!empty($data["rATTI"])) {
     $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
     $i = 0;
     foreach($dlc as $dlc) {
      if($i == 0 && !empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $t = $this->system->Member($f[0]);
        $efs = $this->system->Data("Get", [
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
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $created = $forum["Created"] ?? $this->system->timestamp;
    $illegal = $forum["Illegal"] ?? 0;
    $posts = $forum["Posts"] ?? [];
    $title = $data["Title"] ?? "My Forum";
    $this->system->Data("Save", ["pf", $id, [
     "About" => $data["About"],
     "Created" => $created,
     "Description" => $this->system->PlainText([
      "Data" => $data["Description"],
      "HTMLEncode" => 1
     ]),
     "ICO" => $coverPhoto,
     "ICO-SRC" => base64_encode($coverPhotoSource),
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "NSFW" => $data["nsfw"],
     "Posts" => $posts,
     "Privacy" => $data["pri"],
     "Title" => $title,
     "UN" => $y["Login"]["Username"],
     "Type" => $data["PFType"]
    ]]);
    $actionTaken = ($new == 1) ? "published" : "updated";
    $r = [
     "Body" => "The Forum <em>$title</em> was $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($mbr)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
     $newManifest = [];
     foreach($manifest as $member => $role) {
      if($forum["UN"] != $member && $mbr != $member) {
       $newManifest[$member] = $role;
      }
     }
     $this->system->Data("Save", ["pfmanifest", $id, $newManifest]);
     $r = [
      "Body" => "$mbr was banished from <em>".$forum["Title"]."</em>.",
      "Header" => "Done"
     ];
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN", "all"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->system->ID == $y["Login"]["Username"]) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $forums = $y["Forums"] ?? [];
    $newForums = [];
    foreach($forum["Posts"] as $key => $value) {
     if(!empty($this->system->Data("Get", ["conversation", $value]))) {
      #$this->view(base64_encode("Conversation:SaveDelete"), [
      # "Data" => ["ID" => $value]
      #]);
     }
     $this->system->Data("Purge", ["local", $value]);
     #$this->system->Data("Purge", ["post", $value]);
     $this->system->Data("Purge", ["react", $value]);
    } if(!empty($this->system->Data("Get", ["conversation", $id]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    }
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["pfmanifest", $id]);
    $this->system->Data("Purge", ["pf", $id]);
    $this->system->Data("Purge", ["react", $id]);
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
    #$this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
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
   if(!empty($id) && !empty($mbr)) {
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $members = $this->system->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     if($i == 0) {
      $t = $this->system->Data("Get", ["mbr", $value]) ?? [];
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
    } elseif($mbr == $forum["UN"]) {
     $r = [
      "Body" => "$mbr owns <em>".$forum["Title"]."</em>."
     ];
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = [
      "Body" => "You are already a member of this forum."
     ];
    } else {
     $active = 0;
     $manifest = $this->system->Data("Get", ["pfmanifest", $forum["ID"]]) ?? [];
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
      $this->system->Data("Save", [
       "pfmanifest",
       $forum["ID"],
       $manifest
      ]) ?? [];
      $this->system->SendBulletin([
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
   return $this->system->JSONResponse([
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $ec = "Denied";
   $id = $data["ID"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($id);
    $r = [
     "Body" => "The Forum cannot be shared."
    ];
    if($id != "cb3e432f76b38eaa66c7269d658bd7ea") {
     $accessCode = "Accepted";
     $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
     $body = $this->system->PlainText([
      "Data" => $this->system->Element([
       "p", "Check out <em>".$forum["Title"]."</em>!"
      ]).$this->system->Element([
       "div", "[Forum:$id]",
       ["class" => "NONAME"]
      ]),
      "HTMLEncode" => 1
     ]);
     $body = base64_encode($body);
     $r = $this->system->Change([[
      "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Forum",
      "[Share.ContentID]" => "Forum",
      "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
      "[Share.ID]" => $id,
      "[Share.Link]" => "",
      "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
      "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
      "[Share.Title]" => $forum["Title"]
     ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
     $r = [
      "Front" => $r
     ];
    }
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
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