<?php
 Class Blog extends OH {
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
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($mbr)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself."
    ];
    if($mbr != $blog["UN"] && $mbr != $you) {
     $r = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-type" => base64_encode("v=".base64_encode("Blog:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$blog["Title"]."</em>?",
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
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $contributors = $blog["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $blog["Contributors"] = $contributors;
    $this->core->Data("Save", ["blg", $id, $blog]);
    $r = [
     "Body" => "$member's Role within <em>".$blog["Title"]."</em> was Changed to $role.",
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
   $action = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["BLG", "new"]);
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
   if($this->core->ID == $you) {
    $r = $this->core->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->core->Extension("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($you."_BLG_".uniqid()) : $id;
    $additionalContent = $this->view(base64_encode("WebUI:AdditionalContent"), [
     "ID" => $id
    ]);
    $additionalContent = $this->core->RenderView($additionalContent);
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditBlog$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:Save"))
    ]]);
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $author = $blog["UN"] ?? $you;
    $coverPhotoSource = $blog["ICO-SRC"] ?? "";
    $description = $blog["Description"] ?? "";
    $header = ($new == 1) ? "New Blog" : "Edit ".$blog["Title"];
    $nsfw = $blog["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $blog["Privacy"] ?? $y["Privacy"]["Posts"];
    $template = $blog["TPL"] ?? "";
    $templateOptions = $this->core->DatabaseSet("Extensions");
    $templates = [];
    $title = $blog["Title"] ?? "";
    foreach($templateOptions as $key => $value) {
     $value = str_replace("nyc.outerhaven.extension.", "", $value);
     $value = $this->core->Data("Get", ["extension", $value]) ?? [];
     if($value["Category"] == "BlogTemplate") {
      $templates[$key] = $value["Title"];
     }
    }
    $r = $this->core->Change([[
     "[Blog.AdditionalContent]" => $additionalContent["Extension"],
     "[Blog.CoverPhoto]" => $coverPhotoSource,
     "[Blog.CoverPhoto.LiveView]" => $additionalContent["LiveView"]["CoverPhoto"],
     "[Blog.Description]" => base64_encode($description),
     "[Blog.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
     "[Blog.Header]" => $header,
     "[Blog.ID]" => $id,
     "[Blog.New]" => $new,
     "[Blog.Title]" => base64_encode($title),
     "[Blog.Template]" => $template,
     "[Blog.Templates]" => json_encode($templates, true),
     "[Blog.Visibility.NSFW]" => $nsfw,
     "[Blog.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("7759aead7a3727dd2baed97550872677")]);
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
    "CallSign",
    "ID",
    "back",
    "lPG"
   ]);
   $bck = ($data["back"] == 1) ? $this->core->Element([
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
    $blogs = $this->core->DatabaseSet("Blog");
    foreach($blogs as $key => $value) {
     $value = str_replace("nyc.outerhaven.blg.", "", $value);
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($blog["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
      break;
     }
    }
   } if(!empty($id) || $i > 0) {
    $bl = $this->core->CheckBlocked([$y, "Blogs", $id]);
    $_Blog = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Blog;$id")
    ]);
    $active = 0;
    $admin = 0;
    $blog = $_Blog["DataModel"];
    $contributors = $blog["Contributors"] ?? [];
    $options = $_Blog["ListItem"]["Options"];
    $owner = ($blog["UN"] == $you) ? $y : $this->core->Member($blog["UN"]);
    foreach($contributors as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 1;
      if($admin == 0 && $role == "Admin") {
       $admin = 1;
      }
     }
    } if($_Blog["Empty"] == 0) {
     $_IsArtist = $owner["Subscriptions"]["Artist"]["A"] ?? 0;
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $accessCode = "Accepted";
     $actions = "";
     $admin = ($active == 1 || $admin == 1 || $blog["UN"] == $you) ? 1 : 0;
     $blockCommand = ($bl == 0) ? "Block" : "Unblock";
     $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
     $actions .= ($blog["UN"] != $you) ? $this->core->Element([
      "button", $blockCommand, [
       "class" => "Small UpdateButton v2",
       "data-processor" => $options["Block"]
      ]
     ]) : "";
     $actions .= (!empty($chat)) ? $this->core->Element([
      "button", "Chat", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Chat"]
      ]
     ]) : "";
     $actions .= ($blog["UN"] == $you && $pub == 0) ? $this->core->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2",
       "data-view" => $options["Delete"]
      ]
     ]) : "";
     $actions .= ($_IsArtist == 1) ? $this->core->Element([
      "button", "Donate", [
       "class" => "OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Profile:Donate")."&UN=".base64_encode($owner["Login"]["Username"]))
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->core->Element([
      "button", "Edit", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Edit"]
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->core->Element([
      "button", "Invite", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Invite"]
      ]
     ]) : "";
     $actions .= ($_IsBlogger == 1 && $admin == 1) ? $this->core->Element([
      "button", "Post", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Post"]
      ]
     ]) : "";
     $actions .= $this->core->Element(["button", "Share", [
      "class" => "OpenCard Small v2",
      "data-view" => $options["Share"]
     ]]);
     $search = base64_encode("Search:Containers");
     $extension = $blog["TPL"] ?? "02a29f11df8a2664849b85d259ac8fc9";
     $r = $this->core->Change([[
      "[Blog.About]" => "About ".$owner["Personal"]["DisplayName"],
      "[Blog.Actions]" => $actions,
      "[Blog.Back]" => $bck,
      "[Blog.CoverPhoto]" => $_Blog["ListItem"]["CoverPhoto"],
      "[Blog.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Blog")."&st=Contributors"),
      "[Blog.Contributors.Grid]" => base64_encode("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
      "[Blog.Description]" => $_Blog["ListItem"]["Description"],
      "[Blog.ID]" => $id,
      "[Blog.Posts]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=BGP"),
      "[Blog.Subscribe]" => base64_encode("v=".base64_encode("WebUI:SubscribeSection")."&ID=$id&Type=Blog"),
      "[Blog.Title]" => $_Blog["ListItem"]["Title"],
      "[Blog.Votes]" => $options["Vote"]
     ], $this->core->Extension($extension)]);
     $r = ($data["CARD"] == 1) ? [
      "Front" => $r
     ] : $r;
    }
   }
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
   $action = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $r = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
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
     "class" => "CardButton SendData",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Blog:SendInvite"))
    ]]);
    $content = [];
    $contentOptions = $y["Blogs"] ?? [];
    foreach($contentOptions as $key => $value) {
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $content[$value] = $blog["Title"];
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
   if(md5($key) != $y["Login"]["PIN"]) {
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
    $blogs = $y["Blogs"] ?? [];
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $newBlogs = [];
    foreach($blog["Posts"] as $key => $value) {
     $blogPost = $this->core->Data("Get", ["bp", $value]);
     if(!empty($blogPost)) {
      $blogPost["Purge"] = 1;
      $this->core->Data("Save", ["bp", $value, $blogPost]);
     }
     $conversation = $this->core->Data("Get", ["conversation", $value]);
     if(!empty($conversation)) {
      $conversation["Purge"] = 1;
      $this->core->Data("Save", ["conversation", $value, $conversation]);
     }
     $this->core->Data("Purge", ["translate", $value]);
     $this->core->Data("Purge", ["votes", $value]);
    } foreach($blogs as $key => $value) {
     if($id != $value) {
      array_push($newBlogs, $value);
     }
    }
    $y["Blogs"] = $newBlogs;
    $blog = $this->core->Data("Get", ["blg", $id]);
    if(!empty($blog)) {
     $blog["Purge"] = 1;
     $this->core->Data("Save", ["chat", $id, $blog]);
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
    $this->core->Data("Purge", ["translate", $id]);
    $this->core->Data("Purge", ["votes", $id]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->core->Element([
     "p", "The Blog <em>".$blog["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $blogs = $this->core->DatabaseSet("Blog");
    $coverPhoto = "";
    $coverPhotoSource = "";
    $i = 0;
    $now = $this->core->timestamp;
    $title = $data["Title"] ?? "";
    foreach($blogs as $key => $value) {
     $value = str_replace("nyc.outerhaven.blg.", "", $value);
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     if($id != $blog["ID"] && $title == $blog["Title"]) {
      $i++;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Blog <em>$title</em> is taken."
     ];
    } else {
     $accessCode = "Accepted";
     $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
     $author = $blog["UN"] ?? $you;
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $contributors = $blog["Contributors"] ?? [];
     $contributors[$author] = "Admin";
     $created = $blog["Created"] ?? $now;
     $illegal = $blog["Illegal"] ?? 0;
     $modifiedBy = $blog["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
     $purge = $blog["Purge"] ?? 0;
     $posts = $blog["Posts"] ?? [];
     if(!empty($data["CoverPhoto"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["CoverPhoto"])));
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
     } if(!in_array($id, $y["Blogs"]) && $new == 1) {
      if($author == $you) {
       array_push($y["Blogs"], $id);
       $y["Blogs"] = array_unique($y["Blogs"]);
       $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
      }
     }
     $blog = [
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
      "NSFW" => $nsfw,
      "Privacy" => $privacy,
      "Purge" => $purge,
      "Posts" => $posts,
      "UN" => $author
     ];
     $this->core->Data("Save", ["blg", $id, $blog]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $r = [
      "Body" => "The Blog <em>$title</em> was $actionTaken!",
      "Header" => "Done"
     ];
     if($new == 1) {
      $this->core->Statistic("Save Blog");
     } else {
      $this->core->Statistic("Update Blog");
     }
    }
   }
   return $this->core->JSONResponse([
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
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $username = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $id = base64_decode($id);
    $username = base64_decode($username);
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
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
     $this->core->Data("Save", ["blg", $id, $blog]);
     $r = [
      "Body" => "$mbr was banished from <em>".$blog["Title"]."</em>.",
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
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($mbr)) {
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
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
    } elseif(empty($blog["ID"])) {
     $r = [
      "Body" => "The Blog does not exist."
     ];
    } elseif($blog["UN"] == $mbr) {
     $r = [
      "Body" => "$mbr owns <em>".$blog["Title"]."</em>."
     ];
    } elseif($mbr == $you) {
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
      $this->core->SendBulletin([
       "Data" => [
        "BlogID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToBlog"
      ]);
      $this->core->Data("Save", ["blg", $id, $blog]) ?? [];
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
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
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
    $this->core->Data("Save", ["blg", $id, $blog]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>