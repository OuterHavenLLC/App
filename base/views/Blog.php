<?php
 Class Blog extends GW {
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
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($mbr)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $blog["UN"] && $mbr != $you) {
     $r = [
      "Actions" => [
       $this->system->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->system->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-type" => base64_encode("v=".base64_encode("Blog:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$blog["Title"]."</em>?",
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
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $contributors = $blog["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $blog["Contributors"] = $contributors;
    $this->system->Data("Save", ["blg", $id, $blog]);
    $r = [
     "Body" => "$member's Role within <em>".$blog["Title"]."</em> was Changed to $role.",
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
   $action = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["BLG", "new"]);
   $id = $data["BLG"];
   $new = $data["new"] ?? 0;
   $es = base64_encode("LiveView:EditorSingle");
   $sc = base64_encode("Search:Containers");
   $r = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($you."_BLG_".uniqid()) : $id;
    $action = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditBlog$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:Save"))
    ]]);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $atinput = ".BGE_$id-ATTI";
    $at = base64_encode("Set as the Blog Post's Cover Photo:$atinput");
    $atinput = "$atinput .rATT";
    $at2 = base64_encode("All done! Feel free to close this card.");
    $coverPhotoSource = $blog["ICO-SRC"] ?? "";
    $description = $blog["Description"] ?? "";
    $header = ($new == 1) ? "New Blog" : "Edit ".$blog["Title"];
    $nsfw = $blog["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $blog["Privacy"] ?? $y["Privacy"]["Posts"];
    $template = $blog["TPL"] ?? "";
    $templateOptions = $this->system->DatabaseSet("PG") ?? [];
    $templates = [];
    $title = $blog["Title"] ?? "";
    foreach($templateOptions as $key => $value) {
     $value = str_replace("c.oh.pg.", "", $value);
     $t = $this->system->Data("Get", ["pg", $value]) ?? [];
     if($t["Category"] == "TPL-BLG") {
      $templates[$value] = $t["Title"];
     }
    }
    $r = $this->system->Change([[
     "[Blog.AdditionalContent]" => $this->system->Change([
      [
       "[Extras.ContentType]" => "Blog",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => "N/A",
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=$id")
      ], $this->system->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Blog.Header]" => $header,
     "[Blog.ID]" => $id,
     "[Blog.Inputs]" => $this->system->RenderInputs([
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
        "ContainerClass" => "BGE_$id-ATTI"
       ],
       "Type" => "Text",
       "Value" => $coverPhotoSource
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
       "Value" => $description
      ]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ]).$this->system->RenderInputs([
      [
       "Attributes" => [],
       "OptionGroup" => $templates,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Template"
       ],
       "Name" => "TPL-BLG",
       "Type" => "Select",
       "Value" => $template
      ]
     ])
    ], $this->system->Page("7759aead7a3727dd2baed97550872677")]);
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
   $r = [
    "Body" => "The requested Blog could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $accessCode = "Accepted";
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
     $accessCode = "Accepted";
     $_IsArtist = $owner["Subscriptions"]["Artist"]["A"] ?? 0;
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $actions = "";
     $admin = ($active == 1 || $admin == 1 || $blog["UN"] == $you) ? 1 : 0;
     $actions .= ($blog["UN"] != $you) ? $this->system->Element([
      "button", "Block <em>".$blog["Title"]."</em>", [
       "class" => "Small UpdateButton v2",
       "data-cmd" => base64_encode("B"),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($blog["Title"])."&content=".base64_encode($id)."&list=".base64_encode("Blogs")."&BC=")
      ]
     ]) : "";
     $actions .= ($blog["UN"] == $you && $pub == 0) ? $this->system->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteBlog")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($_IsArtist == 1) ? $this->system->Element([
      "button", "Donate", [
       "class" => "OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Profile:Donate")."&UN=".base64_encode($owner["Login"]["Username"]))
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Edit", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Blog:Edit")."&BLG=$id")
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Invite", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Blog:Invite")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->system->Element([
      "button", "Post", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=".$blog["ID"]."&new=1")
      ]
     ]) : "";
     $actions .= $this->system->Element(["button", "Share", [
      "class" => "OpenCard Small v2",
      "data-view" => base64_encode("v=".base64_encode("Blog:Share")."&ID=".base64_encode($blog["ID"])."&UN=".base64_encode($blog["UN"]))
     ]]);
     $contributors = base64_encode(json_encode($contributors, true));
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $coverPhoto = (!empty($blog["ICO"])) ? $this->system->CoverPhoto(base64_encode($blog["ICO"])) : $coverPhoto;
     $search = base64_encode("Search:Containers");
     $votes = ($blog["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $votes = base64_encode("v=$votes&ID=$id&Type=4");
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
      "[Blog.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Blog")."&st=Contributors"),
      "[Blog.Contributors.Grid]" => base64_encode("v=".base64_encode("Common:MemberGrid")."&List=$contributors"),
      "[Blog.Description]" => $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $blog["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Blog.ID]" => $id,
      "[Blog.Posts]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=BGP"),
      "[Blog.Subscribe]" => $subscribe,
      "[Blog.Title]" => $blog["Title"],
      "[Blog.Votes]" => $votes
     ], $this->system->Page($tpl)]);
     $r = ($data["CARD"] == 1) ? [
      "Front" => $r
     ] : $r;
    }
   }
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
   $action = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $action = $this->system->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:SendInvite"))
    ]]);
    $content = [];
    $contentOptions = $y["Blogs"] ?? [];
    foreach($contentOptions as $key => $value) {
     $blog = $this->Data("Get", ["blg", $value]) ?? [];
     $content[$blog["ID"]] = $blog["Title"];
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
       "Name" => "ListBlogs",
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "Title"]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
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
     $r = [
      "Body" => "The Blog <em>$title</em> is taken."
     ];
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
     $r = [
      "Body" => "The Blog <em>$title</em> was $actionTaken!",
      "Header" => "Done"
     ];
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
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $username = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $id = base64_decode($id);
    $username = base64_decode($username);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($username != $blog["UN"] && $username != $you) {
     $accessCode = "Accepted";
     $contributors = $blog["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($username != $member) {
       $newContributors[$member] = $role;
      }
     }
     $blog["Contributors"] = $newContributors;
     $this->system->Data("Save", ["blg", $id, $blog]);
     $r = [
      "Body" => "$mbr was banished from <em>".$blog["Title"]."</em>.",
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
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
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
     $this->system->Data("Purge", ["votes", $value]);
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
    $r = [
     "Body" => "The Blog <em>".$blog["Title"]."</em> was deleted.",
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
    "Body" => "The Blog Identifier is missing."
   ];
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
     $r = [
      "Body" => "The Member $mbr does not exist."
     ];
    } elseif(empty($blog["ID"])) {
     $r = [
      "Body" => "The Blog does not exist."
     ];
    } elseif($mbr == $blog["UN"]) {
     $r = [
      "Body" => "$mbr owns <em>".$blog["Title"]."</em>."
     ];
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = [
      "Body" => "You are already a contributor."
     ];
    } else {
     $active = 0;
     $contributors = $blog["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = [
       "Body" => "$mbr is already a contributor."
      ];
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
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Share(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $accesscode = "Denied";
   $id = $data["ID"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $un = $data["UN"];
   $y = $this->you;
   if(!empty($id) && !empty($un)) {
    $accessCode = "Accepted";
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
    $r = [
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
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
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