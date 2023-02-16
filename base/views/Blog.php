<?php
 Class Blog extends GW {
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
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($mbr != $blog["UN"] && $mbr != $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "Are you sure you want to banish $mbr from <em>".$blog["Title"]."</em>?"
      ]),
      "Header" => "Banish $mbr?",
      "Option" => $this->system->Element(["button", "Cancel", [
       "class" => "dBC v2 v2w"
      ]]),
      "Option2" => $this->system->Element(["button", "Banish $mbr", [
       "class" => "BBB dBC dBO v2 v2w",
       "data-type" => "v=".base64_encode("Blog:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"]
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
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
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
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $contributors = $blog["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $blog["Contributors"] = $contributors;
    $this->system->Data("Save", ["blg", $id, $blog]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "$member's Role within <em>".$blog["Title"]."</em> was Changed to $role."
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
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["BLG", "new"]);
   $id = $data["BLG"];
   $new = $data["new"] ?? 0;
   $es = base64_encode("LiveView:EditorSingle");
   $sc = base64_encode("Search:Containers");
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Blog Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($y["Login"]["Username"]."_BLG_".$this->system->timestamp) : $id;
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $atinput = ".BGE_$id-ATTI";
    $at = base64_encode("Set as the Blog Post's Cover Photo:$atinput");
    $atinput = "$atinput .rATT";
    $at2 = base64_encode("All done! Feel free to close this card.");
    $at3 = base64_encode("v=$es&ID=");
    $additionalContent = $this->system->Change([
     [
      "[CP.ContentType]" => "Blog",
      "[CP.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at2&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $coverPhotoSource = $blog["ICO-SRC"] ?? "";
    $description = $this->system->Element(["textarea", $blog["Description"], [
     "maxlen" => 180,
     "name" => "Description",
     "placeholder" => "Description"
    ]]);
    $header = ($new == 1) ? "New Blog" : "Edit ".$blog["Title"];
    $nsfw = $blog["NSFW"] ?? $y["privacy_opt"]["NSFW"];
    $privacy = $blog["Privacy"] ?? $y["privacy_opt"]["Profile"];
    $r = $this->system->Change([[
     "[Blog.Actions.Template]" => $this->system->Select("TPL-BLG", "req v2 v2w", $blog["TPL"]),
     "[Blog.Actions.NSFW]" => $this->system->Select("nsfw", "req v2 v2w", $nsfw),
     "[Blog.Actions.Privacy]" => $this->system->Select("Privacy", "req v2 v2w", $privacy),
     "[Blog.AdditionalContent]" => $additionalContent,
     "[Blog.ATTU]" => $at3,
     "[Blog.CoverPhoto]" => $coverPhotoSource,
     "[Blog.CoverPhoto.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[Blog.Description]" => $description,
     "[Blog.Header]" => $header,
     "[Blog.ID]" => $id,
     "[Blog.New]" => $new,
     "[Blog.Title]" => $blog["Title"]
    ], $this->system->Page("7759aead7a3727dd2baed97550872677")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditBlog$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:Save"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CARD",
    "CallSign",
    "ID",
    "back",
    "lPG",
    "pub"
   ]);
   $bck = ($data["back"] == 1) ? $this->system->Element([
    "button", "Back to Blogs", [
     "class" => "GoToParent LI",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $i = 0;
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Back]" => $bck,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Blog could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $blogs = $this->system->DatabaseSet("BLG") ?? [];
    foreach($blogs as $key => $value) {
     $value = str_replace("c.oh.blg.", "", $value);
     $blog = $this->system->Data("Get", ["blg", $value]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->system->CallSign($blog["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
     }
    }
   } if(!empty($id) || $i > 0) {
    $active = 0;
    $admin = 0;
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $contributors = $blog["Contributorsa"] ?? [];
    $subscribers = $blog["Subscribers"] ?? [];
    $owner = ($blog["UN"] == $you) ? $y : $this->system->Member($blog["UN"]);
    foreach($contributors as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 1;
      if($admin == 0 && $role == "Admin") {
       $admin = 1;
      }
     }
    } if(!empty($blog)) {
     $_IsArtist = $owner["Subscriptions"]["Artist"]["A"] ?? 0;
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $actions = "";
     $admin = ($active == 1 || $admin == 1 || $blog["UN"] == $you) ? 1 : 0;
     $actions .= ($blog["UN"] != $you) ? $this->system->Element([
      "button", "Block <em>".$blog["Title"]."</em>", [
       "class" => "BLK Small v2",
       "data-cmd" => base64_encode("B"),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($blog["Title"])."&content=".base64_encode($id)."&list=".base64_encode("Blogs")."&BC=")
      ]
     ]) : "";
     $actions .= ($blog["UN"] == $you && $pub == 0) ? $this->system->Element([
      "button", "Delete", [
       "class" => "Small dBO dB2C v2",
       "data-type" => "v=".base64_encode("Authentication:DeleteBlog")."&ID=".base64_encode($id)
      ]
     ]) : "";
     $actions .= ($_IsArtist == 1) ? $this->system->Element([
      "button", "Donate", [
       "class" => "Small dBO v2",
       "data-type" => "v=".base64_encode("Profile:Donate")."&UN=".base64_encode($owner["Login"]["Username"])
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Edit", [
       "class" => "Small dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Blog:Edit")."&BLG=$id")
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Invite", [
       "class" => "Small dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Blog:Invite")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Post", [
       "class" => "Small dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=".$blog["ID"]."&new=1")
      ]
     ]) : "";
     $actions .= $this->system->Element(["button", "Share", [
      "class" => "Small dB2O v2",
      "data-type" => base64_encode("v=".base64_encode("Blog:Share")."&ID=".base64_encode($blog["ID"])."&UN=".base64_encode($blog["UN"]))
     ]]);
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $coverPhoto = (!empty($blog["ICO"])) ? $this->system->CoverPhoto(base64_encode($blog["ICO"])) : $coverPhoto;
     $reactions = ($blog["UN"] != $you) ? $this->view(base64_encode("Common:Reactions"), ["Data" => [
      "CRID" => $id,
      "T" => $blog["UN"],
      "Type" => 4
     ]]) : "";
     $search = base64_encode("Search:Containers");
     $subscribe = ($blog["UN"] != $you && $this->system->ID != $you) ? 1 : 0;
     $subscribeText = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
     $subscribe = ($subscribe == 1) ? $this->system->Change([[
      "[Subscribe.ContentID]" => $id,
      "[Subscribe.ID]" => md5($you),
      "[Subscribe.Processor]" => base64_encode("v=".base64_encode("Blog:Subscribe")),
      "[Subscribe.Text]" => $subscribeText,
      "[Subscribe.Title]" => $blog["Title"]
     ], $this->system->Page("489a64595f3ec2ec39d1c568cd8a8597")]) : "";
     $tpl = $blog["TPL"] ?? "02a29f11df8a2664849b85d259ac8fc9";
     $r = $this->system->Change([[
      "[Blog.About]" => "About ".$owner["Personal"]["DisplayName"],
      "[Blog.Actions]" => $actions,
      "[Blog.Back]" => $bck,
      "[Blog.CoverPhoto]" => $coverPhoto,
      "[Blog.Contributors]" => $this->view($search, ["Data" => [
       "ID" => base64_encode($id),
       "Type" => base64_encode("Blog"),
       "st" => "Contributors"
      ]]),
      "[Blog.Contributors.Grid]" => $this->view(base64_encode("Common:MemberGrid"), ["Data" => [
       "List" => $contributors
      ]]),
      "[Blog.Description]" => $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $blog["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Blog.ID]" => $id,
      "[Blog.Posts]" => $this->view($search, ["Data" => [
       "ID" => base64_encode($id),
       "st" => "BGP",
      ]]),
      "[Blog.Reactions]" => $reactions,
      "[Blog.Subscribe]" => $subscribe,
      "[Blog.Title]" => $blog["Title"]
     ], $this->system->Page($tpl)]);
    }
   }
   $r = ($data["CARD"] == 1) ? $this->system->Card([
    "Front" => $r
   ]) : $r;
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Invite(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $fr = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Blog Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $frbtn = "";
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($id);
    $fr = $this->system->Change([[
     "[Content.ID]" => $id,
     "[Content.List]" => $this->system->Select("ListBlogs", "LI req v2 v2w", $id),
     "[Content.Member]" => $data["Member"],
     "[Content.Role]" => $this->system->Select("Role", "LI req v2 v2w")
    ], $this->system->Page("80e444c34034f9345eee7399b4467646")]);
    $frbtn = $this->system->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:SendInvite"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "Title"]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $blogs = $this->system->DatabaseSet("BLG");
    $coverPhoto = "";
    $coverPhotoSource = "";
    $i = 0;
    $now = $this->system->timestamp;
    $title = $data["Title"];
    foreach($blogs as $key => $value) {
     $value = str_replace("c.oh.blg.", "", $value);
     $blog = $this->system->Data("Get", ["blg", $value]) ?? [];
     if($id != $blog["ID"] && $title == $blog["Title"]) {
      $i++;
     }
    } if($i > 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Blog <em>$title</em> is taken."
      ]),
      "Header" => "Error"
     ]);
    } else {
     $accessCode = "Accepted";
     $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
     $author = $blog["UN"] ?? $you;
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $contributors = $blog["Contributors"] ?? [];
     $created = $blog["Created"] ?? $now;
     $illegal = $blog["Illegal"] ?? 0;
     $modifiedBy = $blog["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $posts = $blog["Posts"] ?? [];
     if(!empty($data["rATT"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATT"])));
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
     } if(!in_array($id, $y["Blogs"]) && $new == 1) {
      if($username == $you) {
       array_push($y["Blogs"], $id);
       $y["Blogs"] = array_unique($y["Blogs"]);
       $y["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
      }
     }
     $this->system->Data("Save", ["blg", $id, [
      "Contributors" => $contributors,
      "Created" => $created,
      "ICO" => $coverPhoto,
      "ICO-SRC" => base64_encode($coverPhotoSource),
      "ID" => $id,
      "Illegal" => $illegal,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "Title" => $title,
      "TPL" => $data["TPL-BLG"],
      "Description" => htmlentities($data["Description"]),
      "NSFW" => $data["nsfw"],
      "Privacy" => $data["pri"],
      "Posts" => $posts,
      "UN" => $author
     ]]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Blog <em>$title</em> was $actionTaken!"
      ]),
      "Header" => "Done"
     ]);
     if($new == 1) {
      $this->system->Statistic("BLG");
     } else {
      $this->system->Statistic("BLGu");
     }
    }
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $username = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($username)) {
    $id = base64_decode($id);
    $username = base64_decode($username);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($username != $blog["UN"] && $username != $you) {
     $contributors = $blog["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($username != $member) {
       $newContributors[$member] = $role;
      }
     }
     $blog["Contributors"] = $newContributors;
     $this->system->Data("Save", ["blg", $id, $blog]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr was banished from <em>".$blog["Title"]."</em>."
      ]),
      "Header" => "Done"
     ]);
    }
   }
   return $r;
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $blogs = $y["Blogs"] ?? [];
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $newBlogs = [];
    foreach($blog["Posts"] as $key => $value) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $value]
     ]);
     $this->system->Data("Purge", ["local", $value]);
     $this->system->Data("Purge", ["post", $value]);
     $this->system->Data("Purge", ["react", $value]);
    } foreach($blogs as $key => $value) {
     if($id != $value) {
      array_push($newBlogs, $value);
     }
    }
    $y["Blogs"] = $newBlogs;
    $this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->system->Data("Purge", ["blg", $id]);
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["react", $id]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Blog <em>".$blog["Title"]."</em> was deleted."
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
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
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
    } elseif(empty($blog["ID"])) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Blog does not exist."]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $blog["UN"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr owns <em>".$blog["Title"]."</em>."
      ]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "You are already a contributor."]),
      "Header" => "Error"
     ]);
    } else {
     $active = 0;
     $contributors = $blog["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = $this->system->Dialog([
       "Body" => $this->system->Element([
        "p", "$mbr is already a contributor."
       ]),
       "Header" => "Error"
      ]);
     } else {
      $accessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$mbr] = $role;
      $blog["Contributors"] = $contributors;
      $this->system->SendBulletin([
       "Data" => [
        "BlogID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToBlog"
      ]);
      $this->system->Data("Save", ["blg", $id, $blog]) ?? [];
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
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $accesscode = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $un = $data["UN"];
   $y = $this->you;
   if(!empty($id) && !empty($un)) {
    $id = base64_decode($id);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $un = base64_decode($un);
    $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out <em>".$blog["Title"]."</em> by ".$t["DN"]."!"
     ]).$this->system->Element([
      "div", "[Blog:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Blog",
     "[Share.ContentID]" => "Blog",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $blog["Title"]
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Blog Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to subscribe."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $subscribers = $blog["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $r = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $r = "Unsubscribe";
    }
    $blog["Subscribers"] = $subscribers;
    $this->system->Data("Save", ["blg", $id, $blog]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>