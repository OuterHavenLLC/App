<?php
 Class Blog extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $data) {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($member)) {
    $id = base64_decode($id);
    $blog = $this->core->Data("Get", ["blg", $id]);
    $member = base64_decode($member);
    $_Dialog = [
     "Body" => "You cannot banish yourself."
    ];
    if($member != $blog["UN"] && $member != $you) {
     $_Dialog = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $member", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-type" => base64_encode("v=".base64_encode("Blog:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $member from <em>".$blog["Title"]."</em>?",
      "Header" => "Banish $member?"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function ChangeMemberRole(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $_AccessCode = "Accepted";
    $blog = $this->core->Data("Get", ["blg", $id]);
    $contributors = $blog["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $blog["Contributors"] = $contributors;
    $this->core->Data("Save", ["blg", $id, $blog]);
    $_Dialog = [
     "Body" => "$member's Role within <em>".$blog["Title"]."</em> was Changed to $role.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $action = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["BLG", "new"]);
   $id = $data["BLG"];
   $new = $data["new"] ?? 0;
   $es = base64_encode("LiveView:EditorSingle");
   $sc = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($you."_BLG_".uniqid()) : $id;
    $blog = $this->core->Data("Get", ["blg", $id]);
    $author = $blog["UN"] ?? $you;
    $coverPhotoSource = $blog["ICO-SRC"] ?? "";
    $description = $blog["Description"] ?? "";
    $header = ($new == 1) ? "New Blog" : "Edit ".$blog["Title"];
    $nsfw = $blog["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $blog["PassPhrase"] ?? "";
    $privacy = $blog["Privacy"] ?? $y["Privacy"]["Posts"];
    $template = $blog["TPL"] ?? "";
    $templateOptions = $this->core->DatabaseSet("Extensions");
    $templates = [];
    $title = $blog["Title"] ?? "";
    foreach($templateOptions as $key => $value) {
     $value = str_replace("nyc.outerhaven.extension.", "", $value);
     $template = $this->core->Data("Get", ["extension", $value]) ?? [];
     if($template["Category"] == "BlogTemplate") {
      $templates[$value] = $template["Title"];
     }
    }
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".EditBlog$id",
      "data-processor" => base64_encode("v=".base64_encode("Blog:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Blog.Attachments]" => "",
       "[Blog.Description]" => base64_encode($description),
       "[Blog.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
       "[Blog.Header]" => $header,
       "[Blog.ID]" => $id,
       "[Blog.New]" => $new,
       "[Blog.PassPhrase]" => base64_encode($passPhrase),
       "[Blog.Title]" => base64_encode($title),
       "[Blog.Template]" => $template,
       "[Blog.Templates]" => json_encode($templates, true),
       "[Blog.Visibility.NSFW]" => $nsfw,
       "[Blog.Visibility.Privacy]" => $privacy
      ],
      "ExtensionID" => "7759aead7a3727dd2baed97550872677"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The requested Blog could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CallSign",
    "back",
    "lPG"
   ]);
   $addTo = $data["AddTo"] ?? "";
   $back = ($data["back"] == 1) ? $this->core->Element([
    "button", "Back to Blogs", [
     "class" => "GoToParent LI",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $card = $data["CARD"] ?? "";
   $i = 0;
   $id = $data["ID"] ?? "";
   $public = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($public == 1) {
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
     $_IsVIP = $owner["Subscriptions"]["VIP"]["A"] ?? 0;
     $_IsSubscribed = ($_IsArtist == 1 || $_IsVIP == 1) ? 1 : 0;
     $accessCode = "Accepted";
     $passPhrase = $blog["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Card = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Blog["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $id,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Blog:Home")
       ], true))
      ]]);
      $_Card = [
       "Front" => $this->core->RenderView($_Card)
      ];
      $_Dialog = "";
      $_View = "";
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = [
       "Body" => "The Key is missing."
      ];
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $_Dialog = "";
       $_View = "";
      } else {
       $_Dialog = "";
       $_View = $this->view(base64_encode("Blog:Home"), ["Data" => [
        "AddTo" => $addTo,
        "ID" => $id,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $actions = "";
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $admin = ($active == 1 || $admin == 1 || $blog["UN"] == $you) ? 1 : 0;
      $blockCommand = ($bl == 0) ? "Block" : "Unblock";
      $chat = $this->core->Data("Get", ["chat", $id]);
      $actions = (!empty($addToData)) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode("Blog;$id")
       ]
      ]) : "";
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
      $actions .= ($blog["UN"] == $you && $public == 0) ? $this->core->Element([
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
      $actions .= ($_IsSubscribed == 1 && $admin == 1) ? $this->core->Element([
       "button", "Edit", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Edit"]
       ]
      ]).$this->core->Element([
       "button", "Invite", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Invite"]
       ]
      ]).$this->core->Element([
       "button", "Post", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Post"]
       ]
      ]) : "";
      $actions .= $this->core->Element(["button", "Share", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Share"]
      ]]);
      $extensionID = $blog["TPL"] ?? "02a29f11df8a2664849b85d259ac8fc9";
      $search = base64_encode("Search:Containers");
      $_View = [
       "ChangeData" => [
        "[Blog.About]" => "About ".$owner["Personal"]["DisplayName"],
        "[Blog.Actions]" => $actions,
        "[Blog.Back]" => $back,
        "[Blog.CoverPhoto]" => $_Blog["ListItem"]["CoverPhoto"],
        "[Blog.Contributors]" => base64_encode("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Blog")."&st=Contributors"),
        "[Blog.Contributors.Grid]" => base64_encode("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
        "[Blog.Description]" => $_Blog["ListItem"]["Description"],
        "[Blog.ID]" => $id,
        "[Blog.Posts]" => base64_encode("v=$search&ID=".base64_encode($id)."&st=BGP"),
        "[Blog.Subscribe]" => $options["Subscribe"],
        "[Blog.Title]" => $_Blog["ListItem"]["Title"],
        "[Blog.Votes]" => $options["Vote"]
       ],
       "ExtensionID" => $extensionID
      ];
      $_Card = ($card == 1) ? [
       "Front" => $_View
      ] : "";
      $_View = ($card === 0) ? $_View : "";
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Invite(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $action = "";
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
    $contentOptions = $y["Blogs"] ?? [];
    foreach($contentOptions as $key => $value) {
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $content[$value] = $blog["Title"];
    }
    $_Card = [
     "Action" => $this->core->Element(["button", "Send Invite", [
      "class" => "CardButton SendData",
      "data-form" => ".Invite$id",
      "data-processor" => base64_encode("v=".base64_encode("Blog:SendInvite"))
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
  function Purge(array $data) {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
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
     "Body" => "The PINs do not match. ($key, ".md5($key).", $secureKey)"
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $blogs = $y["Blogs"] ?? [];
    $blog = $this->core->Data("Get", ["blg", $id]);
    $blogPosts = $blog["Posts"] ?? [];
    $newBlogs = [];
    $passPhrase = base64_encode($key);
    $securePassPhrase = base64_encode($secureKey);
    foreach($blogPosts as $key => $value) {
     $blogPost = $this->core->Data("Get", ["bp", $value]);
     if(!empty($blogPost)) {
      $this->view(base64_encode("BlogPost:Purge"), ["Data" => [
       "Key" => $passPhrase,
       "ID" => base64_encode($value),
       "SecureKey" => $securePassPhrase
      ]]);
     }
    } foreach($blogs as $key => $value) {
     if($id != $value) {
      array_push($newBlogs, $value);
     }
    }
    $y["Blogs"] = $newBlogs;
    $blog = $this->core->Data("Get", ["blg", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Blogs WHERE Blog_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($blog)) {
     $blog["Purge"] = 1;
     $this->core->Data("Save", ["blg", $id, $blog]);
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
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_View = $this->core->Element([
     "p", "The Blog <em>".$blog["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
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
     $_AccessCode = "Accepted";
     $blog = $this->core->Data("Get", ["blg", $id]);
     $author = $blog["UN"] ?? $you;
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $contributors = $blog["Contributors"] ?? [];
     $contributors[$author] = "Admin";
     $created = $blog["Created"] ?? $now;
     $illegal = $blog["Illegal"] ?? 0;
     $modifiedBy = $blog["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $passPhrase = $data["PassPhrase"] ?? "";
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
     $purge = $blog["Purge"] ?? 0;
     $posts = $blog["Posts"] ?? [];
     $subscribers = $blog["Subscribers"] ?? [];
     foreach($subscribers as $key => $value) {
      $this->core->SendBulletin([
       "Data" => [
        "BlogID" => $id
       ],
       "To" => $value,
       "Type" => "BlogUpdate"
      ]);
     } if(!empty($data["CoverPhoto"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["CoverPhoto"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc) && $i == 0) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         $t = $this->core->Member($f[0]);
         $efs = $this->core->Data("Get", [
          "fs",
          md5($t["Login"]["Username"])
         ]) ?? [];
         $fileName = $efs["Files"][$f[1]]["Name"] ?? "";
         if(!empty($fileName)) {
          $coverPhoto = $f[0]."/$fileName";
          $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
          $i++;
         }
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
      "Description" => htmlentities($data["Description"]),
      "NSFW" => $nsfw,
      "PassPhrase" => $passPhrase,
      "Privacy" => $privacy,
      "Posts" => $posts,
      "Purge" => $purge,
      "Title" => $title,
      "TPL" => $data["TPL-BLG"],
      "UN" => $author
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO Blogs(
      Blog_Created,
      Blog_Description,
      Blog_ID,
      Blog_NSFW,
      Blog_Privacy,
      Blog_Title,
      Blog_Username
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
      ":Description" => $blog["Description"],
      ":ID" => $id,
      ":NSFW" => $blog["NSFW"],
      ":Privacy" => $blog["Privacy"],
      ":Title" => $blog["Title"],
      ":Username" => $author
     ]);
     $sql->execute();
     $this->core->Data("Save", ["blg", $id, $blog]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $_Dialog = [
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
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $data) {
   $_Dialog = [
    "Body" => "The Article Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $username = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $id = base64_decode($id);
    $username = base64_decode($username);
    $blog = $this->core->Data("Get", ["blg", $id]);
    $_Dialog = [
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
     $_Dialog = [
      "Body" => "$member was banished from <em>".$blog["Title"]."</em>.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SendInvite(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $member = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($member)) {
    $blog = $this->core->Data("Get", ["blg", $id]);
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
    } elseif(empty($blog["ID"])) {
     $_Dialog = [
      "Body" => "The Blog does not exist."
     ];
    } elseif($blog["UN"] == $member) {
     $_Dialog = [
      "Body" => "$member owns <em>".$blog["Title"]."</em>."
     ];
    } elseif($member == $you) {
     $_Dialog = [
      "Body" => "You are already a contributor."
     ];
    } else {
     $active = 0;
     $contributors = $blog["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($member == $member) {
       $active++;
      }
     } if($active == 1) {
      $_Dialog = [
       "Body" => "$member is already a contributor."
      ];
     } else {
      $_AccessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$member] = $role;
      $blog["Contributors"] = $contributors;
      $this->core->SendBulletin([
       "Data" => [
        "BlogID" => $id,
        "Member" => $member,
        "Role" => $role
       ],
       "To" => $member,
       "Type" => "InviteToBlog"
      ]);
      $this->core->Data("Save", ["blg", $id, $blog]);
      $_Dialog = [
       "Body" => "$member was notified of your invitation.",
       "Header" => "Invitation Sent"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $_ResponseType = "N/A";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $_ResponseType = "UpdateText";
    $blog = $this->core->Data("Get", ["blg", $id]);
    $subscribers = $blog["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $_View = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $_View = "Unsubscribe";
    }
    $blog["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["blg", $id, $blog]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>