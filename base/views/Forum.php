<?php
 Class Forum extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Banish(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Forum Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "Are you sure you want to banish $mbr from <em>".$forum["Title"]."</em>?"
      ]),
      "Header" => "Banish $mbr?",
      "Option" => $this->system->Element(["button", "Cancel", [
       "class" => "dBC v2 v2w"
      ]]),
      "Option2" => $this->system->Element(["button", "Banish $mbr", [
       "class" => "BBB dBC dBO v2 v2w",
       "data-type" => "v=".base64_encode("Forum:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"]
      ]])
     ]);
    }
   }
   return $r;
  }
  function ChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $manifest[$member] = $role;
    $this->system->Data("Save", ["pfmanifest", $id, $manifest]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "$member's Role within <em>".$forum["Title"]."</em> was Changed to $role."
     ]),
     "Header" => "Done"
    ]);
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
   $bck = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "new"]);
   $fr = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "Something went wrong on our end."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $frbtn = "";
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $now = $this->system->timestamp;
   $y = $this->you;
   if($y["Login"]["Username"] == $this->system->ID) {
    $fr = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
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
    $additionalContent = $this->system->Change([
     [
      "[CP.ContentType]" => "Forum",
      "[CP.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $fr = $this->system->Change([[
     "[Forum.About]" => $about,
     "[Forum.AdditionalContent]" => $additionalContent,
     "[Forum.Created]" => $created,
     "[Forum.Description]" => $description,
     "[Forum.Header]" => $header,
     "[Forum.ICO]" => $coverPhoto,
     "[Forum.ICO.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[Forum.ID]" => $id,
     "[Forum.NSFW]" => $this->system->Select("nsfw", "LI req v2 v2w", $nsfw),
     "[Forum.New]" => $new,
     "[Forum.Privacy]" => $this->system->Select("Privacy", "LI req v2 v2w", $privacy),
     "[Forum.Title]" => $title,
     "[Forum.Type]" => $this->system->Select("PFType", "LI req v2 v2w", $type)
    ], $this->system->Page("8304362aea73bddb2c12eb3f7eb226dc")]);
    $frbtn = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditForum$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:Save"))
    ]]);
   }
   return $this->system->Card([
    "Back" => $bck,
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function Home(array $a) {
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
   $r = $this->system->Change([[
    "[Error.Back]" => $bck,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Forum could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
    $r = $this->system->Change([[
     "[Error.Back]" => $bck,
     "[Error.Header]" => "Private Forum",
     "[Error.Message]" => "<em>".$forum["Title"]."</em> is invite-only."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
    if($active == 1 || $ck == 1 || $forum["Type"] == "Public") {
     $_BlockCommand = ($bl == 0) ? "B" : "U";
     $_BlockText = ($bl == 0) ? "Block" : "Unblock";
     $_BlockText .= " <em>".$forum["Title"]."</em>";
     $_JoinCommand = ($active == 0) ? "Join" : "Leave";
     $_SonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
     $actions = ($active == 1 && $ck == 0) ? $this->view(base64_encode("Common:Reactions"), ["Data" => [
      "CRID" => $id,
      "T" => $forum["UN"],
      "Type" => 4
     ]]) : "";
     $actions .= ($bl == 0 && $ck == 0) ? $this->system->Element(["button", $_BlockText, [
      "class" => "BLK GoToParent dB2C Small v2 v2w",
      "data-cmd" => base64_encode($_BlockCommand),
      "data-type" => ".OHCC;$lpg",
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($f["Title"])."&content=".base64_encode($f["ID"])."&list=".base64_encode("Forums")."&BC=")
     ]]) : "";
     $actions .= ($active == 1 || $ck == 1) ? $this->system->Element([
      "button", "Chat", [
       "class" => "Small dB2C v2 v2w",
       "onclick" => "FST('N/A', 'v=".base64_encode("Chat:Home")."&GroupChat=1&to=".base64_encode($id)."', '".md5("Chat$id")."');"
      ]
     ]) : "";
     $actions .= ($forum["UN"] == $you && $pub == 0) ? $this->system->Element([
      "button", "Delete", [
       "class" => "Small dBO dB2C v2",
       "data-type" => "v=".base64_encode("Authentication:DeleteForum")."&ID=".base64_encode($id)
      ]
     ]) : "";
     $actions .= ($admin == 1) ? $this->system->Element(["button", "Edit", [
      "class" => "Small dB2O v2 v2w",
      "data-type" => base64_encode("v=".base64_encode("Forum:Edit")."&ID=$id")
     ]]) : "";
     $actions .= ($active == 1 || $ck == 1 || $forum["Type"] == "Public") ? $this->system->Element([
      "button", "Post", [
       "class" => "Small dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$id&new=1")
      ]
     ]) : "";
     $actions .= ($forum["Type"] == "Public") ? $this->system->Element([
      "button", "Share", [
       "class" => "Small dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Forum:Share")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $coverPhoto = (!empty($forum["ICO"])) ? base64_encode($forum["ICO"]) : $coverPhoto;
     $invite = ($active == 1 && $forum["ID"] == $_SonsOfLiberty) ? $this->system->Element([
      "button", "Invite", [
       "class" => "BB dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Forum:Invite")."&ID=".base64_encode($forum["ID"]))
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
     $search = base64_encode("Search:Containers");
     $r = $this->system->Change([[
      "[Forum.About]" => $forum["About"],
      "[Forum.Actions]" => $actions,
      "[Forum.Administrators]" => $this->view($search, ["Data" => [
       "Admin" => base64_encode($forum["UN"]),
       "ID" => base64_encode($id),
       "st" => "Forums-Admin"
      ]]),
      "[Forum.Back]" => $bck,
      "[Forum.Contributors]" => $this->view($search, ["Data" => [
       "ID" => base64_encode($id),
       "Type" => base64_encode("Forum"),
       "st" => "Contributors"
      ]]),
      "[Forum.Contributors.Featured]" => $this->view(base64_encode("Common:MemberGrid"), ["Data" => [
       "List" => $manifest
      ]]),
      "[Forum.CoverPhoto]" => $this->system->CoverPhoto($coverPhoto),
      "[Forum.Description]" => $this->system->PlainText([
       "Data" => $forum["Description"],
       "HTMLDncode" => 1
      ]),
      "[Forum.Invite]" => $invite,
      "[Forum.Join]" => $join,
      "[Forum.Stream]" => $this->view($search, ["Data" => [
       "ID" => base64_encode($id),
       "st" => "Forums-Posts"
      ]]),
      "[Forum.Title]" => $forum["Title"]
     ], $this->system->Page("4159d14e4e8a7d8936efca6445d11449")]);
    }
   }
   $r = ($data["CARD"] == 1) ? $this->system->Card(["Front" => $r]) : $r;
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Invite(array $a) {
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Forum Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $r = $this->system->Change([[
     "[Content.ID]" => $id,
     "[Content.List]" => $this->system->Select("ListForums", "LI req v2 v2w", $id),
     "[Content.Member]" => $data["Member"],
     "[Content.Role]" => $this->system->Select("Role", "LI req v2 v2w")
    ], $this->system->Page("80e444c34034f9345eee7399b4467646")]);
    $button = $this->system->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Forum:SendInvite"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function LeaveOrJoin(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $command = $data["Command"] ?? "";
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CallSign",
    "ID"
   ]);
   $callSign = $data["CallSign"] ?? "";
   $callSign = $this->system->CallSign($callSign);
   $id = $data["ID"] ?? "";
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "We could not find the Forum you were looking for."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   if(!empty($callSign) || !empty($id)) {
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
   }
   $r = ($y["Login"]["Username"] == $this->system->ID && $data["pub"] == 1) ? $this->view(base64_encode("WebUI:OptIn"), []) : $r;
   return $r;
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
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
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
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Forum <em>$title</em> was $actionTaken."
     ]),
     "Header" => "Done"
    ]);
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($mbr != $forum["UN"] && $mbr != $y["Login"]["Username"]) {
     $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
     $newManifest = [];
     foreach($manifest as $member => $role) {
      if($forum["UN"] != $member && $mbr != $member) {
       $newManifest[$member] = $role;
      }
     }
     $this->system->Data("Save", ["pfmanifest", $id, $newManifest]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr was banished from <em>".$forum["Title"]."</em>."
      ]),
      "Header" => "Done"
     ]);
    }
   }
   return $r;
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $all = $data["all"] ?? 0;
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN", "all"]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($this->system->ID == $y["Login"]["Username"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
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
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Forum ($id, temp) was deleted.".json_encode($y["Forums"], true)
     ]),
     "Header" => "Done"
    ]);
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
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
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
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Member $mbr does not exist."
      ]),
      "Header" => "Error"
     ]);
    } elseif(empty($forum["ID"])) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Forum does not exist."]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $forum["UN"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr owns <em>".$forum["Title"]."</em>."
      ]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "You are already a member of this forum."
      ]),
      "Header" => "Error"
     ]);
    } else {
     $active = 0;
     $manifest = $this->system->Data("Get", ["pfmanifest", $forum["ID"]]) ?? [];
     foreach($manifest as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = $this->system->Dialog([
       "Body" => $this->system->Element([
        "p", "$mbr is already an active member of <em>".$forum["Title"]."</em>."
       ]),
       "Header" => "Error"
      ]);
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
      $r = $this->system->Dialog([
       "Body" => $this->system->Element([
        "p", "$mbr was notified of your invitation."
       ]),
       "Header" => "Invitation Sent"
      ]);
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $ec = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($id);
    $r = $this->system->Change([[
     "[Error.Header]" => "Error",
     "[Error.Message]" => "The Forum cannot be shared."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
    if($id != "cb3e432f76b38eaa66c7269d658bd7ea") {
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
    }
   }
   return $this->system->Card(["Front" => $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>